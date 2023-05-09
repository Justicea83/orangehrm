<template>
  <div>
    <oxd-form-row>
      <oxd-grid :cols="3" class="orangehrm-full-width-grid">
        <oxd-grid-item>
          <employee-autocomplete
            label="Employee"
            required
            :rules="rules.employee"
          />
        </oxd-grid-item>
        <oxd-grid-item>
          <supervisor-auto-complete
            label="Supervisor"
            required
            :rules="rules.supervisor"
          />
        </oxd-grid-item>
        <oxd-grid-item>
          <onboarding-type-dropdown required label="Type" :rules="rules.type" />
        </oxd-grid-item>
      </oxd-grid>
    </oxd-form-row>

    <oxd-form-row>
      <oxd-grid :cols="3" class="orangehrm-full-width-grid">
        <oxd-grid-item>
          <date-input label="Start Date" :rules="rules.startDate" required />
        </oxd-grid-item>
        <oxd-grid-item>
          <date-input label="End Date" :rules="rules.endDate" required />
        </oxd-grid-item>
        <oxd-grid-item>
          <date-input label="Due Date" :rules="rules.dueDate" required />
        </oxd-grid-item>
      </oxd-grid>
    </oxd-form-row>

    <oxd-form-row>
      <oxd-input-field
        type="textarea"
        :label="$t('general.note')"
        :placeholder="$t('general.add_note')"
        label-icon="pencil-square"
        :rules="rules.notes"
      />
    </oxd-form-row>

    <!--    <oxd-form-actions>
      <required-text />
      <oxd-button
        display-type="ghost"
        :label="$t('general.cancel')"
        @click="onCancel"
      />
      <submit-button />
    </oxd-form-actions>-->
  </div>
</template>

<script>
import OnboardingTypeDropdown from '@/orangehrmPimPlugin/components/OnboardingTypeDropdown';
import EmployeeAutocomplete from '@/core/components/inputs/EmployeeAutocomplete';
import SupervisorAutoComplete from '@/orangehrmPerformancePlugin/components/SupervisorAutoComplete';

export default {
  name: 'OnboardingDetails',
  components: {
    OnboardingTypeDropdown,
    EmployeeAutocomplete,
    SupervisorAutoComplete,
  },
  props: {
    activity: {
      type: Object,
      required: true,
    },
    rules: {
      type: Object,
      required: true,
    },
  },
};
</script>

<style scoped></style>
