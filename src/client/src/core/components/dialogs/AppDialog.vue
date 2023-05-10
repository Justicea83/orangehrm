<template>
  <oxd-dialog
    v-if="show"
    class="orangehrm-dialog-modal"
    @update:show="onCancel"
  >
    <div class="orangehrm-dialog-header-container">
      <oxd-text type="card-title">
        {{ title }}
      </oxd-text>
    </div>
    <oxd-divider
      class="orangehrm-dialog-horizontal-margin orangehrm-clear-margins"
    />

    <div class="mt-3 px-4">
      <slot name="body"></slot>
    </div>

    <div class="orangehrm-dialog-horizontal-padding">
      <oxd-form-actions>
        <oxd-button
          display-type="ghost"
          :label="cancelButtonText"
          @click="show = false"
        />
        <submit-button
          :label="submitButtonText"
          button-type="button"
          @click="onSubmit"
        />
      </oxd-form-actions>
    </div>
  </oxd-dialog>
</template>

<script>
import Dialog from '@ohrm/oxd/core/components/Dialog/Dialog';

export default {
  name: 'AppDialog',
  components: {
    'oxd-dialog': Dialog,
  },
  props: {
    title: {
      type: String,
      required: true,
    },
    cancelButtonText: {
      type: String,
      default: 'Cancel',
    },
    submitButtonText: {
      type: String,
      default: 'Submit',
    },
  },
  emits: ['submit'],
  data() {
    return {
      show: false,
    };
  },
  methods: {
    showDialog() {
      this.show = true;
    },
    onCancel() {
      this.show = false;
    },
    onSubmit() {
      this.$emit('submit');
      this.show = false;
    },
  },
};
</script>

<style scoped></style>
