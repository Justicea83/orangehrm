<template>
  <div>
    <oxd-form-row>
      <oxd-input-field
        v-model="activityModel.name"
        autofocus
        label="Title"
        :rules="rules.name"
        required
      />
    </oxd-form-row>
    <oxd-form-row>
      <oxd-grid :cols="3" class="orangehrm-full-width-grid">
        <oxd-grid-item>
          <employee-autocomplete
            v-model="activityModel.employee"
            :value="activityModel.employee"
            label="Employee"
            name="employee"
            :required="false"
          />
        </oxd-grid-item>
        <oxd-grid-item>
          <employee-autocomplete
            v-model="activityModel.supervisor"
            label="Supervisor"
            name="supervisor"
            :required="false"
          />
        </oxd-grid-item>
        <oxd-grid-item>
          <task-type-dropdown
            required
            name="type"
            :selected="activityModel.types"
            label="Type"
            :rules="rules.type"
            @options-changed="optionsChanged"
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
  </div>
</template>

<script>
import EmployeeAutocomplete from '@/core/components/inputs/EmployeeAutocomplete';
import TaskTypeDropdown from '@/onboardingPlugin/pages/create/components/TaskTypeDropdown';
import {OxdFormRow, OxdInputField, OxdGrid, OxdGridItem} from '@ohrm/oxd';
import DateInput from '@ohrm/components/inputs/DateInput.vue';

export default {
  name: 'OnboardingDetails',
  components: {
    EmployeeAutocomplete,
    TaskTypeDropdown,
    OxdFormRow,
    OxdInputField,
    OxdGrid,
    OxdGridItem,
    DateInput,
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
  emits: ['update:prop'],
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
  methods: {
    optionsChanged(options) {
      this.activityModel.type = options;
    },
  },
};
</script>

<style scoped></style>
