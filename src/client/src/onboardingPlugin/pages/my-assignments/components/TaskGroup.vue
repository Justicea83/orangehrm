<template>
  <div>
    <datatable
      alternating
      :headers="headers"
      :items="taskList"
      :rows-per-page="10"
    >
      <template #expand="item">
        <div class="p-2">
          {{ item.task.notes }}
        </div>
      </template>
      <template #item-isCompleted="item">
        <check-badge-icon
          class="h-6 cursor-pointer"
          :class="{
            'text-green-600': item.isCompleted,
            'text-gray-400': !item.isCompleted,
          }"
          @click="toggleComplete(item)"
        />
      </template>
      <template #item-dueDate="item">
        <h3>{{ formatDate(item.dueDate) }}</h3>
      </template>
    </datatable>
  </div>
</template>

<script lang="ts">
import 'vue3-easy-data-table/dist/style.css';
import Vue3EasyDataTable, {Header} from 'vue3-easy-data-table';
import {CheckBadgeIcon} from '@heroicons/vue/24/solid';
import {APIService} from '@/core/util/services/api.service';
import {Task} from '@/onboardingPlugin/models';
import moment from 'moment';

export default {
  name: 'TaskGroup',
  components: {
    Datatable: Vue3EasyDataTable,
    CheckBadgeIcon,
  },
  props: {
    taskList: {
      type: Array,
      required: true,
    },
    taskGroupId: {
      type: Number,
      required: true,
    },
  },
  setup() {
    const http = new APIService(
      (window as any).appGlobal.baseUrl,
      '/api/v2/onboarding/task-groups',
    );
    return {
      http,
    };
  },
  data() {
    return {
      checkedItems: [],
      sortDefinition: null,
      showDrawer: false,
    };
  },
  computed: {
    headers(): Header[] {
      return [
        {value: 'isCompleted', text: ''},
        {value: 'task.title', text: 'Title'},
        {value: 'dueDate', text: 'Due Date', sortable: true},
      ];
    },
    taskListModel: {
      get() {
        return this.taskList;
      },
      set(value: Task[]) {
        this.$emit('update:prop', value);
      },
    },
  },
  methods: {
    toggleComplete(row: Task) {
      this.http
        .request({
          url: '/api/v2/onboarding/task-groups/actions',
          method: 'put',
          data: {
            action: 'toggle_complete',
            groupAssignmentId: this.taskGroupId,
            taskGroupId: row.id,
          },
        })
        .then(() => {
          const tableData: Task[] = [...this.taskList];
          const indexById = tableData.findIndex(
            (task: Task) => task.id === row.id,
          );
          tableData[indexById].isCompleted = !tableData[indexById].isCompleted;

          this.taskListModel = tableData;
        });
    },
    formatDate(date: string): string {
      return moment(date).format('DD MMM');
    },
  },
};
</script>

<style scoped></style>
