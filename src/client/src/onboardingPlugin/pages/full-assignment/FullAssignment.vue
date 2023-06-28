<template>
  <div class="flex relative h-full">
    <div
      ref="list"
      :class="{'w-2/3 mr-3': showDetails, 'w-full': !showDetails}"
    >
      <assignment
        v-if="selectedTask"
        :task-group="selectedTask"
        :class="{active: true}"
        :is-owner="isOwner"
        @open-details="openAssignmentDetail"
        @toggle-active="toggleActive"
      >
        <template #exportOptions>
          <task-progress
            :fill-color="theme['--oxd-primary-one-color'] ?? null"
            :progress="selectedTask.progress"
          />
        </template>
      </assignment>
    </div>
    <div class="fixed top-15 right-3 h-screen w-1/4">
      <assignment-detail
        v-if="selectedTask && showDetails"
        :is-owner="isOwner"
        :task-group="selectedTask"
        @on-close="onClose"
        @on-toggle-mark-complete="onToggleMarkComplete"
        @on-submit="onSubmit"
      />
    </div>
  </div>
</template>

<script>
import {APIService} from '@/core/util/services/api.service';
import Assignment from '@/onboardingPlugin/pages/my-assignments/components/Assignment';
import AssignmentDetail from '@/onboardingPlugin/pages/my-assignments/components/AssignmentDetail';
import TaskProgress from '@/onboardingPlugin/pages/task-groups/components/TaskProgress';
import {ACTION_SUBMIT, ACTION_COMPLETE} from '../my-assignments/MyAssignments';

export default {
  name: 'FullAssignment',
  components: {
    TaskProgress,
    Assignment,
    AssignmentDetail,
  },
  props: {
    assignmentId: {
      type: String,
      default: null,
    },
    creatorId: {
      type: String,
      default: null,
    },
    theme: {
      type: Object,
      required: true,
    },
  },
  setup() {
    const http = new APIService(
      window.appGlobal.baseUrl,
      '/api/v2/task-management/task-assignments',
    );
    return {
      http,
    };
  },
  data() {
    return {
      selectedTask: null,
      showDetails: true,
    };
  },
  computed: {
    isOwner() {
      if (!this.creatorId || !this.selectedTask) {
        return false;
      }

      return (
        this.creatorId.toString() === this.selectedTask?.creator?.id?.toString()
      );
    },
  },
  mounted() {
    this.loadData();
  },
  methods: {
    toggleActive() {
      this.$nextTick(() => {
        const activeElement = this.$refs.list.querySelector('.active');
        if (activeElement) {
          activeElement.scrollIntoView({
            behavior: 'smooth',
            block: 'center',
            inline: 'nearest',
          });
        }
      });
    },
    onClose() {
      this.showDetails = false;
    },
    openAssignmentDetail(data) {
      this.selectedTask = data;
      this.showDetails = true;
    },
    loadData() {
      this.http.get(this.assignmentId).then((results) => {
        const {data} = results.data;
        this.selectedTask = {...data, isActive: true};
      });
    },
    allTasksComplete() {
      return this.selectedTask.taskGroups.every((task) => task.isCompleted);
    },
    onToggleMarkComplete() {
      this.http
        .request({
          url: '/api/v2/task-management/task-groups/actions',
          method: 'PUT',
          data: {
            action: ACTION_COMPLETE,
            groupAssignmentId: this.selectedTask?.id,
          },
        })
        .then(() => {
          const tasks = this.tasks;
          const taskIndex = tasks.findIndex(
            (task) => task.id === this.selectedTask?.id,
          );
          tasks[taskIndex].completed = true;
          tasks[taskIndex].taskGroups = tasks[taskIndex].taskGroups.map(
            (task) => ({
              ...task,
              isCompleted: true,
            }),
          );
          this.tasks = tasks;
          this.$toast.generalSuccess('Assignment completed successfully');
        });
    },
    submitAssignment() {
      if (!this.allTasksComplete()) {
        this.$toast.unexpectedError(
          'An assignment can only be submitted after all subtasks are completed',
        );
        return;
      }
      this.http
        .request({
          url: '/api/v2/task-management/task-groups/actions',
          method: 'PUT',
          data: {
            action: ACTION_SUBMIT,
            groupAssignmentId: this.selectedTask?.id,
          },
        })
        .then(() => {
          const tasks = this.tasks;
          const taskIndex = tasks.findIndex(
            (task) => task.id === this.selectedTask?.id,
          );
          tasks[taskIndex].submittedAt = new Date().toISOString();

          this.tasks = tasks;
          this.$toast.generalSuccess('Assignment submitted successfully');
        });
    },
    onSubmit() {
      this.$refs.deleteDialog.showDialog().then((confirmation) => {
        if (confirmation === 'ok') {
          this.submitAssignment();
        }
      });
    },
  },
};
</script>

<style scoped></style>
