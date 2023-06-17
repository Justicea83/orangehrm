<template>
  <h3
    :class="{
      'text-red-400': !isCompleted && isDeadlineApproaching(date),
    }"
  >
    {{ date }}
  </h3>
</template>

<script lang="ts">
export default {
  name: 'DeadlineDate',
  props: {
    date: {
      type: String,
      required: true,
    },
    isCompleted: {
      type: Boolean,
      default: false,
    },
  },
  methods: {
    isDeadlineApproaching(date: string): boolean {
      const dateComponent = date.split(' ')[0];
      return new Date(dateComponent) < new Date();
    },
  },
};
</script>

<style scoped></style>
