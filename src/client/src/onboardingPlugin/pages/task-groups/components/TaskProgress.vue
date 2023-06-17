<template>
  <circle-progress
    :size="size"
    :border-width="5"
    :border-bg-width="5"
    :fill-color="fillColor"
    :percent="progress"
  />
</template>

<script>
import 'vue3-circle-progress/dist/circle-progress.css';
import CircleProgress from 'vue3-circle-progress';

export default {
  name: 'TaskProgress',
  components: {
    CircleProgress,
  },
  props: {
    progress: {
      type: Number,
      required: true,
    },
    size: {
      type: Number,
      default: 30,
    },
    fillColor: {
      type: String,
      default: '#288feb',
    },
  },
};
</script>

<style scoped lang="scss"></style>
