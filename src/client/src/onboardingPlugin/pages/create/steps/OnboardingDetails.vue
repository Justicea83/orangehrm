<template>
  <div>
    <oxd-form-row>
      <oxd-grid :cols="3" class="orangehrm-full-width-grid">
        <oxd-grid-item>
          <employee-autocomplete
            v-model="activityModel.employee"
            label="Employee"
            name="employee"
            required
            :rules="rules.employee"
          />
        </oxd-grid-item>
        <oxd-grid-item>
          <employee-autocomplete
            v-model="activityModel.supervisor"
            label="Supervisor"
            name="supervisor"
            required
            :rules="rules.supervisor"
          />
        </oxd-grid-item>
        <oxd-grid-item>
          <onboarding-type-dropdown
            v-model="activityModel.type"
            required
            name="type"
            label="Type"
            :rules="rules.type"
          />
        </oxd-grid-item>
      </oxd-grid>
    </oxd-form-row>

    <oxd-form-row>
      <oxd-grid :cols="3" class="orangehrm-full-width-grid">
        <oxd-grid-item>
          <date-input
            v-model="activityModel.startDate"
            label="Start Date"
            name="startDate"
            :rules="rules.startDate"
            required
          />
        </oxd-grid-item>
        <oxd-grid-item>
          <date-input
            v-model="activityModel.endDate"
            label="End Date"
            name="endDate"
            :rules="rules.endDate"
            required
          />
        </oxd-grid-item>
        <oxd-grid-item>
          <date-input
            v-model="activityModel.dueDate"
            label="Due Date"
            name="dueDate"
            :rules="rules.dueDate"
            required
          />
        </oxd-grid-item>
      </oxd-grid>
    </oxd-form-row>

    <oxd-form-row>
      <oxd-input-field
        v-model="activityModel.notes"
        type="textarea"
        name="notes"
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

export default {
  name: 'OnboardingDetails',
  components: {
    OnboardingTypeDropdown,
    EmployeeAutocomplete,
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
  computed: {
    activityModel: {
      get() {
        return this.activity;
      },
      set(value) {
        this.$emit('update:prop', value);
      },
    },
  },
};
</script>

<style scoped></style>
