<template>
  <h3
    :class="{
      'text-red-400': !isCompleted,
      'text-green-400': isCompleted,
    }"
  >
    {{ isCompleted ? 'Yes' : 'No' }}
  </h3>
</template>

<script>
export default {
  name: 'CompleteCell',
  props: {
    isCompleted: {
      type: Boolean,
      required: true,
    },
  },
};
</script>

<style scoped></style>
