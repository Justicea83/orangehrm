<template>
  <h1>Full Assignment {{ assignmentId }}</h1>
</template>

<script>
export default {
  name: 'FullAssignment',
  props: {
    assignmentId: {
      type: String,
      default: null,
    },
  },
};
</script>

<style scoped></style>
