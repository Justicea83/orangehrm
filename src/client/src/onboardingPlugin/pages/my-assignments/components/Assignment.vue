<template>
  <div class="oxd-table-filter">
    <div class="oxd-table-filter-header">
      <div class="oxd-table-filter-header-title">
        <oxd-text
          class="oxd-table-filter-title cursor-pointer"
          tag="h5"
          @click="openMenu"
        >
          {{ taskGroup?.name }}
        </oxd-text>
      </div>
      <div class="oxd-table-filter-header-options">
        <div class="--toggle">
          <slot name="toggleOptions"></slot>
        </div>
        <div class="--export">
          <slot name="exportOptions"></slot>
        </div>
        <div class="--toggle">
          <oxd-icon-button
            :name="taskGroup.isActive ? 'caret-up-fill' : 'caret-down-fill'"
            @click="toggleFilters"
          />
        </div>
      </div>
    </div>
    <div v-show="taskGroup.isActive" class="oxd-table-filter-area mt-2">
      <task-group
        :task-list="taskGroup.taskGroups"
        :task-group-id="taskGroup.id"
        :completed="taskGroup.completed"
        :submitted-at="taskGroup.submittedAt"
      />
    </div>
  </div>
</template>

<script>
import {OxdIconButton, OxdText} from '@ohrm/oxd';
import TaskGroup from '@/onboardingPlugin/pages/my-assignments/components/TaskGroup';

export default {
  name: 'Assignment',
  components: {
    OxdIconButton,
    OxdText,
    TaskGroup,
  },
  props: {
    taskGroup: {
      type: Object,
      required: true,
    },
  },
  emits: ['open-details', 'toggleActive'],

  methods: {
    openMenu() {
      this.$emit('open-details', this.taskGroup);
      this.toggleFilters()
    },
    toggleFilters() {
      this.$emit('toggleActive', this.taskGroup);
    },
  },
};
</script>

<style src="./assignment.scss" lang="scss" scoped></style>

<style lang="scss">
.oxd-table-filter-header-options {
  & .oxd-icon-button {
    font-size: 12px;
    width: 24px !important;
    height: 24px !important;
    min-width: unset;
    min-height: unset;
    margin-right: 5px;
  }
  & .oxd-button {
    padding-right: 5px;
    padding-left: 5px;
    min-width: unset;
    margin-right: 5px;
  }
}
</style>
