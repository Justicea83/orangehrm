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
            @before-mount="beforeMount"
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
      isLoading: false,
      activity: {...initialActivity},
      fetchedTasks: {},
      tasksData: {},
      rules: {
        onboardingDetails: {
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
  methods: {
    tasksChanged(tasks) {
      console.log(tasks);
    },
    complete() {
      console.log('done');
    },
    onCreateOnboarding() {
      console.log('subbb');
    },
    beforeChange({index, ref}) {
      const {validate} = this.$refs.form;
      if (index > 0) {
        validate();
      }
      // TODO uncomment after testing
      /*setTimeout(() => {
        if (index >= 0 && this.$refs.form.isFromInvalid) {
          ref.changeTab(index - 1);
        }
      }, 10);*/
    },
    beforeMount(index) {
      if (index === 1) {
        const activityId = this.activityType?.id;
        if (activityId === null) {
          return;
        }

        if (this.fetchedTasks[activityId]) {
          this.tasksData = this.fetchedTasks[activityId];
          return;
        }

        this.http
          .request({
            params: {
              taskType: this.activityType?.id,
            },
          })
          .then(({data}) => {
            this.fetchedTasks[activityId] = data;
            this.tasksData = data;
          });
      }
    },
  },
};
</script>

<style scoped></style>
