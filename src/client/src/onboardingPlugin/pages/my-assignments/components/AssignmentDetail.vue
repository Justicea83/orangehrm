<template>
  <div class="orangehrm-background-container">
    <div class="orangehrm-paper-container">
      <detail-tools
        :task-group="taskGroup"
        @on-toggle-mark-complete="onToggleMarkComplete"
        @on-submit="onSubmit"
        @on-close="onClose"
      />
      <oxd-divider />
      <div class="flex flex-col p-3">
        <oxd-text type="subtitle-1 w-full">
          <h2>{{ taskGroup?.name }}</h2>
        </oxd-text>

        <detail-item title="Creator" :sub-title="taskGroup?.creator?.name" />
        <detail-item
          title="Supervisor"
          :sub-title="taskGroup?.supervisor?.name"
        />
        <detail-item
          title="Start Date"
          :sub-title="formatDate(taskGroup?.startDate)"
        />
        <detail-item
          title="End Date"
          :expiring="
            !taskGroup.isCompleted && isDeadlineApproaching(taskGroup.endDate)
          "
          :sub-title="formatDate(taskGroup?.endDate)"
        />
        <detail-item
          title="Due Date"
          :expiring="
            !taskGroup.isCompleted && isDeadlineApproaching(taskGroup.dueDate)
          "
          :sub-title="formatDate(taskGroup?.dueDate)"
        />

        <detail-item
          v-if="taskGroup.submittedAt"
          title="Submitted On"
          :sub-title="formatDate(taskGroup?.submittedAt)"
        />

        <div class="flex flex-col mt-3">
          <h5 class="text-sm text-gray-400">Notes</h5>

          <oxd-text type="card-body">
            <h4>
              {{ taskGroup?.notes }}
            </h4>
          </oxd-text>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import DetailTools from '@/onboardingPlugin/pages/my-assignments/components/DetailTools';
import {OxdText} from '@ohrm/oxd';
import DetailItem from '@/onboardingPlugin/pages/my-assignments/components/DetailItem';
import moment from 'moment';

export default {
  name: 'AssignmentDetail',
  components: {
    DetailTools,
    OxdText,
    DetailItem,
  },
  props: {
    taskGroup: {
      type: Object,
      required: true,
    },
  },
  emits: ['onToggleMarkComplete', 'onClose', 'onSubmit'],
  computed: {
    formatDate() {
      return (date) => {
        return moment(date).format('DD MMM');
      };
    },
  },
  methods: {
    isDeadlineApproaching(date) {
      const dateComponent = date.split(' ')[0];
      return new Date(dateComponent) < new Date();
    },
    onToggleMarkComplete() {
      this.$emit('onToggleMarkComplete');
    },
    onSubmit() {
      this.$emit('onSubmit');
    },
    onClose() {
      this.$emit('onClose');
    },
  },
};
</script>

<style scoped></style>
