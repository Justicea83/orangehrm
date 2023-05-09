<template>
  <div>
    <div class="orangehrm-background-container">
      <div class="orangehrm-card-container">
        <oxd-text tag="h6" class="orangehrm-main-title">
          Create an ELP Event
        </oxd-text>

        <oxd-divider />

        <oxd-form :loading="isLoading" @submitValid="onCreateOnboarding">
          <stepper :tabs="tabs" :start-index="1" @completed="complete">
            <template #0>
              <onboarding-details
                :rules="rules.onboardingDetails"
                :activity="activity"
              />
            </template>
            <template #1>
              <onboarding-tasks :activity="activity" />
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
  endDate: null,
  tasks: [],
};

import Stepper from '@/core/components/stepper/Stepper';

export default {
  name: 'CreateOnboarding',
  components: {
    Stepper,
    OnboardingDetails,
    OnboardingTasks,
  },
  data() {
    return {
      isLoading: false,
      activity: {...initialActivity},
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
          tasks: [required],
          notes: [required],
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
  mounted() {
    console.log('called from here');
  },
  methods: {
    complete() {
      console.log('done');
    },
    onCreateOnboarding() {
      console.log('subbb');
    },
  },
};
</script>

<style scoped></style>
