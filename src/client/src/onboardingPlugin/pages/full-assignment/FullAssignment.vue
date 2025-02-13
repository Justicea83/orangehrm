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
        @progress-changed="progressChanged()"
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
    <div class="fixed mt-1 right-3 h-screen w-1/4">
      <assignment-detail
        v-if="selectedTask && showDetails"
        :is-owner="isOwner"
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
import TaskProgress from '@/onboardingPlugin/pages/task-groups/components/TaskProgress';
import {ACTION_SUBMIT, ACTION_COMPLETE} from '../my-assignments/MyAssignments';
import DeleteConfirmationDialog from '@/core/components/dialogs/DeleteConfirmationDialog.vue';

export default {
  name: 'FullAssignment',
  components: {
    'delete-confirmation': DeleteConfirmationDialog,
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
      submitConfirmation:
        "The will be submitted and can't be edited again. Are you sure you want to continue?",
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
    progressChanged(progress) {
      if (progress) {
        this.selectedTask = {
          ...this.selectedTask,
          completed: progress === 100,
          progress: progress,
        };
      }
    },
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
          this.selectedTask = {
            ...this.selectedTask,
            completed: true,
            progress: 100,
            taskGroups: this.selectedTask.taskGroups.map((group) => {
              return {...group, isCompleted: true};
            }),
          };
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
          this.selectedTask = {
            ...this.selectedTask,
            submittedAt: new Date().toISOString(),
          };
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
