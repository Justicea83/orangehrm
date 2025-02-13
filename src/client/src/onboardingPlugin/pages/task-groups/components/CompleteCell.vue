<template>
  <h3
    :class="{
      'text-red-400': !isComplete,
      'text-green-400': isComplete,
    }"
  >
    {{ isComplete ? 'Yes' : 'No' }}
  </h3>
</template>

<script>
export default {
  name: 'CompleteCell',
  props: {
    isComplete: {
      type: Boolean,
      required: true,
    },
  },
};
</script>

<style scoped></style>
