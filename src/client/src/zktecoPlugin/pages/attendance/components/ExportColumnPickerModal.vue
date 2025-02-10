<template>
  <oxd-dialog class="orangehrm-dialog-modal" @update:show="onCancel">
    <div class="orangehrm-modal-header">
      <oxd-text type="card-title"> Select Columns</oxd-text>
    </div>

    <oxd-divider />

    <oxd-form :loading="isLoading" @submit="onSave">
      <oxd-form-row>
        <oxd-grid :cols="3" class="orangehrm-full-width-grid">
          <oxd-grid-item
            v-for="column in columns"
            :key="column"
            class="spacing-y"
            @change="checkChange($event, column)"
          >
            <oxd-checkbox-input :option-label="columnMappings[column] ?? ''" />
          </oxd-grid-item>
        </oxd-grid>
      </oxd-form-row>

      <oxd-divider />

      <oxd-form-actions>
        <oxd-button
          type="button"
          display-type="ghost"
          :label="$t('general.cancel')"
          @click="onCancel"
        />
        <submit-button :disabled="selectedColumns.length === 0" />
      </oxd-form-actions>
    </oxd-form>
  </oxd-dialog>
</template>

<script lang="ts">
import {
  OxdForm,
  OxdFormRow,
  OxdFormActions,
  OxdDialog,
  OxdDivider,
  OxdButton,
  OxdText,
  OxdGrid,
  OxdGridItem,
  OxdCheckboxInput,
} from '@ohrm/oxd';
import SubmitButton from '@/core/components/buttons/SubmitButton';

export default {
  name: 'ExportColumnPickerModal',
  components: {
    'oxd-dialog': OxdDialog,
    OxdText,
    OxdForm,
    OxdDivider,
    OxdFormActions,
    OxdFormRow,
    SubmitButton,
    OxdButton,
    OxdCheckboxInput,
    OxdGrid,
    OxdGridItem,
  },
  props: {
    columns: {
      type: Array,
      required: true,
      default: () => [],
    },
  },
  emits: ['close', 'onExport'],
  data() {
    return {
      selectedColumns: [],
      columnMappings: {
        emp_code: 'Employee Code',
        first_name: 'First Name',
        last_name: 'Last Name',
        nick_name: 'Nickname',
        gender: 'Gender',
        dept_code: 'Department Code',
        dept_name: 'Department Name',
        position_code: 'Position Code',
        company_code: 'Company Code',
        company_name: 'Company Name',
        position_name: 'Position Name',
        att_date: 'Attendance Date',
        weekday: 'Day of the Week',
        check_in: 'Check-in Time',
        check_out: 'Check-out Time',
        total_time: 'Total Work Time',
        hourly_rate: 'Hourly Rate',
        total_comp: 'Total Compensation',
        currency: 'Currency',
      },
    };
  },
  methods: {
    onSave() {
      this.$emit('onExport', [...new Set(this.selectedColumns)]);
    },
    onCancel() {
      this.comment = null;
      this.$emit('close', true);
    },
    checkChange(event, column) {
      if (event.target.checked) {
        this.selectedColumns.push(column);
      } else {
        this.selectedColumns = this.selectedColumns.filter(
          (item) => item !== column,
        );
      }
    },
  },
};
</script>

<style scoped lang="scss">
.spacing-y {
  margin: 0 0 1rem;
}
</style>
