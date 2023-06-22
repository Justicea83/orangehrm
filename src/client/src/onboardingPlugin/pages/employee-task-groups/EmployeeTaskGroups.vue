<template>
  <task-groups
    api-url="/api/v2/task-management/employee-assignments"
    :show-actions="false"
    :theme="theme"
    :user-id="userId"
  />
</template>

<script>
import TaskGroups from '@/onboardingPlugin/pages/task-groups/TaskGroups';
export default {
  name: 'EmployeeTaskGroups',
  components: {
    TaskGroups,
  },
  props: {
    theme: {
      type: Object,
      default: null,
    },
    userId: {
      type: String,
      required: true,
    },
  },
};
</script>

<style scoped></style>
