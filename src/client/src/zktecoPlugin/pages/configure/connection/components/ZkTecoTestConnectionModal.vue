<template>
  <oxd-dialog class="orangehrm-dialog-modal" @update:show="onCancel">
    <div class="orangehrm-modal-header">
      <oxd-text type="card-title">
        {{ $t('admin.connection_status') }}
      </oxd-text>
    </div>
    <oxd-divider/>

    <div class="orangehrm-ldap-test">
      <oxd-text tag="p" class="orangehrm-ldap-test-title">
        Status
      </oxd-text>
      <div
          class="orangehrm-ldap-test-row"
      >
        <oxd-text class="orangehrm-ldap-test-content">
          {{ status ? status.toUpperCase() : '' }}
        </oxd-text>
        <oxd-text :class="getClass()">
          {{ getText() }}
        </oxd-text>
      </div>
    </div>
  </oxd-dialog>
</template>

<script>
import {OxdDialog} from '@ohrm/oxd';

export default {
  name: 'ZkTecoTestConnectionModal',
  components: {
    'oxd-dialog': OxdDialog,
  },
  props: {
    status: {
      type: String,
      default: () => '',
    },
  },
  emits: ['close'],
  methods: {
    getClass() {
      return this.status === 'success'
          ? 'orangehrm-ldap-test-value --success'
          : 'orangehrm-ldap-test-value --error';
    },
    onCancel() {
      this.$emit('close');
    },
    getText() {
      return this.status === 'success'
          ? 'Connected successfully.'
          : 'Error connecting.';
    },
  },
};
</script>

<style lang="scss" scoped>
.orangehrm-ldap-test {
  margin-bottom: 0.75rem;

  &-title {
    font-size: 14px;
    font-weight: 700;
    margin-bottom: 0.2rem;
  }

  &-value {
    &.--success {
      color: $oxd-feedback-success-color;
    }

    &.--error {
      color: $oxd-feedback-danger-color;
    }
  }

  &-row {
    width: 100%;
    display: flex;
    font-size: 14px;
    margin-bottom: 0.2rem;
  }

  &-content {
    flex: 1;
  }
}
</style>
