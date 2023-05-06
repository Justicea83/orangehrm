<template>
  <oxd-input-field
    type="select"
    label="Task Type"
    :options="options"
    :rules="rules"
    :required="required"
  />
</template>

<script>
export default {
  name: 'OnboardingTypeDropdown',
  props: {
    rules: {
      type: Array,
      required: false,
      default: Array.from([]),
    },
    required: {
      type: Boolean,
      required: false,
      default: false,
    },
  },
  data() {
    return {
      options: [
        {
          id: 0,
          label: 'Onboarding',
        },
        {
          id: 1,
          label: 'Offboarding',
        },
      ],
    };
  },
};
</script>

<style scoped></style>
