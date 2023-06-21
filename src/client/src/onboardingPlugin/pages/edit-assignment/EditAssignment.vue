<template>
  <h1>Edit Assignment {{ assignmentId }}</h1>
</template>

<script>
export default {
  name: 'EditAssignment',
  props: {
    assignmentId: {
      type: String,
      default: null,
    },
  },
};
</script>

<style scoped></style>
