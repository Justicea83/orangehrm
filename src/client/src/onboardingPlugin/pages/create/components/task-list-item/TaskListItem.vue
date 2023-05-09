<template>
  <li>
    <div class="flex flex-col list-item">
      <h3 class="text-sm">{{ task.title }}</h3>
      <div class="flex items-center justify-end">
        <h3 class="text-xs text-gray-500">2022-03-21</h3>
        <button
          type="button"
          class="hover:bg-gray-300 w-6 h-6 rounded-md grid place-content-center"
          @click="dateButtonClick"
        >
          <clock-icon class="w-4 h-4 text-gray-500" />
        </button>
      </div>
    </div>
  </li>
</template>

<script>
import {ClockIcon} from '@heroicons/vue/24/solid';

export default {
  name: 'TaskListItem',
  components: {
    ClockIcon,
  },
  props: {
    task: {
      type: Object,
      required: true,
    },
  },
  methods: {
    dateButtonClick() {

      //this.$refs.dateInput.click()
    },
  },
};
</script>

<style src="./task-list-item.scss" scoped lang="scss"></style>
