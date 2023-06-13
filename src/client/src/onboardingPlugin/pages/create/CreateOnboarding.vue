<template>
  <div>
    <div class="orangehrm-background-container">
      <div class="orangehrm-card-container">
        <oxd-text tag="h6" class="orangehrm-main-title">
          Create a boarding event
        </oxd-text>

        <oxd-divider />

        <oxd-form
          ref="form"
          :loading="isLoading"
          @submitValid="onCreateOnboarding"
        >
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

import Stepper from '@/core/components/stepper/Stepper';
import {APIService} from '@/core/util/services/api.service';

export default {
  name: 'CreateOnboarding',
  components: {
    Stepper,
    OnboardingDetails,
    OnboardingTasks,
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
      isLoading: true,
      activity: {...initialActivity},
      fetchedTasks: {},
      tasksData: {},
      rules: {
        onboardingDetails: {
          name: [required, shouldNotExceedCharLength(100)],
          employee: [required],
          supervisor: [required],
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
          title: 'ELP Details',
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

      // TODO payload ready for submission
      console.log(payload);
    },
    onCreateOnboarding() {
      console.log('subbb');
    },
    beforeChange({index, ref}) {
      const {validate} = this.$refs.form;
      if (index > 0) {
        validate();
      }
      // TODO change this back
      /*setTimeout(() => {
        if (index >= 0 && this.$refs.form.isFromInvalid) {
          ref.changeTab(index - 1);
        }
      }, 10);*/
    },
  },
};
</script>

<style scoped></style>
