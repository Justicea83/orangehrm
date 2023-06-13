<template>
  <div class="orangehrm-background-container">
    <div class="orangehrm-paper-container">
      <detail-tools
        @on-toggle-mark-complete="onToggleMarkComplete"
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
          :sub-title="formatDate(taskGroup?.endDate)"
        />
        <detail-item
          title="Due Date"
          :sub-title="formatDate(taskGroup?.dueDate)"
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
emits: ['onToggleMarkComplete', 'onClose'],
  computed: {
    formatDate() {
      return (date) => {
        return moment(date).format('DD MMM');
      };
    },
  },
  methods: {
    onToggleMarkComplete() {
      this.$emit('onToggleMarkComplete');
    },
    onClose() {
      this.$emit('onClose');
    },
  },
};
</script>

<style scoped></style>
