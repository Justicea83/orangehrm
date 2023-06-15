<template>
  <div>
    <div class="orangehrm-background-container">
      <div class="orangehrm-card-container">
        <oxd-text tag="h6" class="orangehrm-main-title">
          Create a task assignment
        </oxd-text>

        <oxd-divider />

        <oxd-form ref="form" :loading="isLoading">
          <stepper
            :tabs="tabs"
            @after-change="beforeChange"
            @completed="complete"
          >
            <template #0>
              <onboarding-details
                :rules="rules.onboardingDetails"
                :activity="activity"
              />
            </template>
            <template #1>
              <onboarding-tasks
                :activity="activity"
                :type="activityType"
                :data="tasksData"
                @tasks-changed="tasksChanged"
              />
            </template>
          </stepper>
        </oxd-form>
      </div>
    </div>
  </div>
</template>

<script>
import {
  endDateShouldBeAfterStartDate,
  required,
  shouldNotExceedCharLength,
  startDateShouldBeBeforeEndDate,
  validDateFormat,
} from '@/core/util/validation/rules';
import OnboardingDetails from '@/onboardingPlugin/pages/create/steps/OnboardingDetails';
import OnboardingTasks from '@/onboardingPlugin/pages/create/steps/OnboardingTasks';
import {OxdForm, OxdText, OxdDivider} from '@ohrm/oxd';
import Stepper from '@/core/components/stepper/Stepper';
import {APIService} from '@/core/util/services/api.service';
import {navigate} from '@/core/util/helper/navigation';

const initialActivity = {
  employee: null,
  supervisor: null,
  dueDate: null,
  startDate: null,
  type: null,
  endDate: null,
  tasks: [],
  name: null,
};

export default {
  name: 'CreateOnboarding',
  components: {
    Stepper,
    OnboardingDetails,
    OnboardingTasks,
    OxdText,
    OxdForm,
    OxdDivider,
  },
  setup() {
    const http = new APIService(
      window.appGlobal.baseUrl,
      '/api/v2/onboarding/tasks',
    );
    return {
      http,
    };
  },
  data() {
    return {
      isLoading: false,
      activity: {...initialActivity},
      fetchedTasks: {},
      tasksData: {},
      rules: {
        onboardingDetails: {
          name: [required, shouldNotExceedCharLength(100)],
          startDate: [
            required,
            validDateFormat(this.userDateFormat),
            startDateShouldBeBeforeEndDate(
              () => this.activity.endDate,
              this.$t(
                'performance.review_period_start_date_should_be_before_end_date',
              ),
            ),
          ],
          endDate: [
            required,
            validDateFormat(this.userDateFormat),
            endDateShouldBeAfterStartDate(
              () => this.activity.startDate,
              this.$t(
                'performance.review_period_end_date_should_be_after_start_date',
              ),
            ),
          ],
          dueDate: [
            required,
            validDateFormat(),
            endDateShouldBeAfterStartDate(
              () => this.activity.endDate,
              this.$t(
                'performance.due_date_should_be_after_review_period_end_date',
              ),
            ),
          ],
          type: [required],
        },
      },
      tabs: [
        {
          title: 'Task Details',
          id: 1,
        },
        {
          title: 'Tasks',
          id: 2,
        },
      ],
    };
  },
  computed: {
    activityType() {
      return this.activity.type;
    },
  },
  watch: {
    activityType(newType) {
      if (newType.length) {
        const activityIds = newType?.map((t) => t.id).join(',');

        this.http
          .request({
            params: {
              taskTypes: activityIds,
            },
          })
          .then(({data}) => {
            this.tasksData = data;
          });
      }
    },
  },
  methods: {
    tasksChanged(tasks) {
      this.activity.tasks = tasks;
    },
    complete() {
      if (!this.activity.tasks || this.activity.tasks.length === 0) {
        this.$toast.unexpectedError('Tasks cannot be empty!');
        return;
      }

      this.loading = true;

      const {
        dueDate,
        endDate,
        startDate,
        notes,
        type,
        tasks,
        employee,
        name,
        supervisor,
      } = this.activity;

      const payload = {
        dueDate,
        endDate,
        startDate,
        notes,
        name,
        types: type?.map((t) => t.id).join(','),
        supervisorId: supervisor?.id,
        employeeId: employee?.id,
        tasks: tasks.map((task) => {
          const {dueDate, id} = task;
          return {
            dueDate: dueDate ?? null,
            id,
          };
        }),
      };

      this.http
        .request({
          url: '/api/v2/onboarding/task-assignments',
          method: 'POST',
          data: payload,
        })
        .then(() => {
          this.$toast.saveSuccess();
          navigate('/onboarding/viewTaskGroups');
        })
        .catch(() => {
          this.loading = false;
        });
    },
    beforeChange({index, ref}) {
      const {validate} = this.$refs.form;
      if (index > 0) {
        validate();
      }
      setTimeout(() => {
        if (index >= 0 && this.$refs.form.isFromInvalid) {
          ref.changeTab(index - 1);
        }
      }, 10);
    },
  },
};
</script>

<style scoped></style>
