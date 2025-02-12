<template>
  <oxd-input-field
    :type="multiple ? 'multiselect' : 'select'"
    label="Report Mode"
    :options="options"
  />
</template>

<script>
import {OxdInputField} from '@ohrm/oxd';

export default {
  name: 'ReportModeDropdown',
  components: {
    OxdInputField,
  },
  props: {
    multiple: {
      type: Boolean,
      required: false,
      default: false,
    },
  },
  data() {
    return {
      options: [
        {
          id: 'daily',
          label: 'Daily',
        },
        // {
        //   id: 'monthly',
        //   label: 'Monthly',
        // },
        // {
        //   id: 'yearly',
        //   label: 'Yearly',
        // },
      ],
    };
  },
};
</script>

<style scoped lang="scss"></style>
