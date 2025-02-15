<template>
  <div v-if="!loading && tasks.length === 0" class="oxd-table-filter">
    <div class="oxd-table-filter-header">
      <div class="oxd-table-filter-header-title">
        <oxd-text
          class="oxd-table-filter-title cursor-pointer text-center"
          tag="h5"
        >
          No Tasks Assigned to you.
        </oxd-text>
      </div>
    </div>
  </div>
  <div class="flex relative h-full">
    <div
      ref="list"
      :class="{'w-2/3 mr-3': showDetails, 'w-full': !showDetails}"
    >
      <assignment
        v-for="task in tasks"
        :key="task.id"
        :task-group="task"
        :class="{active: task.isActive}"
        @open-details="openAssignmentDetail"
        @toggle-active="toggleActive"
      >
        <template #exportOptions>
          <task-progress
            :fill-color="theme['--oxd-primary-one-color'] ?? null"
            :progress="task.progress"
          />
        </template>
      </assignment>
    </div>
    <div class="fixed top-[8.4rem] right-3 h-screen w-1/4">
      <assignment-detail
        v-if="selectedTask && showDetails"
        :task-group="selectedTask"
        @on-close="onClose"
        @on-toggle-mark-complete="onToggleMarkComplete"
        @on-submit="onSubmit"
      />
    </div>
    <delete-confirmation
      ref="deleteDialog"
      confirm-button-text="Yes, Submit"
      :with-confirmation-icon="false"
      :message="submitConfirmation"
    ></delete-confirmation>
  </div>
</template>

<script>
import {APIService} from '@/core/util/services/api.service';
import Assignment from '@/onboardingPlugin/pages/my-assignments/components/Assignment';
import AssignmentDetail from '@/onboardingPlugin/pages/my-assignments/components/AssignmentDetail';
import DeleteConfirmationDialog from '@/core/components/dialogs/DeleteConfirmationDialog';
import TaskProgress from '@/onboardingPlugin/pages/task-groups/components/TaskProgress';
import {OxdText} from '@ohrm/oxd';

export const ACTION_COMPLETE = 'complete_assignment';
export const ACTION_SUBMIT = 'submit';

export default {
  name: 'MyAssignments',
  components: {
    OxdText,
    TaskProgress,
    Assignment,
    AssignmentDetail,
    'delete-confirmation': DeleteConfirmationDialog,
  },
  props: {
    theme: {
      type: Object,
      required: true,
    },
  },
  setup() {
    const http = new APIService(
      window.appGlobal.baseUrl,
      '/api/v2/task-management/my-assignments',
    );
    return {
      http,
    };
  },
  data() {
    return {
      meta: null,
      tasks: [],
      selectedTask: null,
      showDetails: true,
      loading: false,
      submitConfirmation:
        "The will be submitted and can't be edited again. Are you sure you want to continue?",
    };
  },
  mounted() {
    this.loadData();
  },
  methods: {
    toggleActive(taskGroup) {
      const tasks = [...this.tasks];
      tasks.map((task) => {
        task.isActive = task.id === taskGroup.id;
        return task;
      });
      this.tasks = tasks;
      this.selectedTask = taskGroup;

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
      this.loading = true;
      this.http
        .getAll()
        .then((results) => {
          const {meta, data} = results.data;
          this.meta = meta;
          this.tasks = data.map((task, index) => ({
            ...task,
            isActive: index === 0,
          }));
          this.selectedTask = data[0];
        })
        .finally(() => {
          this.loading = false;
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
          const tasks = [...this.tasks];
          const taskIndex = tasks.findIndex(
            (task) => task.id === this.selectedTask?.id,
          );
          tasks[taskIndex].completed = true;
          tasks[taskIndex].progress = 100;
          tasks[taskIndex].taskGroups = tasks[taskIndex].taskGroups.map(
            (task) => ({
              ...task,
              isCompleted: true,
            }),
          );
          this.tasks = tasks;
          this.selectedTask = {...tasks[taskIndex]};
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
          const tasks = [...this.tasks];
          const taskIndex = tasks.findIndex(
            (task) => task.id === this.selectedTask?.id,
          );
          tasks[taskIndex].submittedAt = new Date().toISOString();
          this.selectedTask = {...tasks[taskIndex]};

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
