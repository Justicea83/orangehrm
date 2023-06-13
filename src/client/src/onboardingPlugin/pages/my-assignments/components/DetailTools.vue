<template>
  <div class="flex justify-between p-2">
    <oxd-button
      label="Mark Complete"
      class="!flex !flex-row !justify-center !items-center"
      size="small"
      display-type="ghost"
      @click="onToggleMarkComplete"
    >
      <template #icon>
        <check-badge-icon class="h-4 mr-1" />
      </template>
    </oxd-button>
    <oxd-icon-button name="arrow-bar-right" @click="onClose" />
  </div>
</template>

<script>
import {OxdIconButton, OxdButton} from '@ohrm/oxd';
import {CheckBadgeIcon} from '@heroicons/vue/24/solid';

export default {
  name: 'DetailTools',
  components: {
    OxdIconButton,
    OxdButton,
    CheckBadgeIcon,
  },
  emits: ['onToggleMarkComplete', 'onClose'],
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
