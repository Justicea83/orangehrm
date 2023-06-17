<template>
  <li @click="listItemClick">
    <div class="flex flex-col list-item">
      <h3 class="text-sm">{{ task.title }}</h3>
      <div v-if="task.dueDate" class="flex items-center justify-end">
        <h3 class="text-xs text-gray-500">{{ task.dueDate }}</h3>
        <clock-icon class="ml-1 w-4 h-4 text-gray-500" />
      </div>
    </div>
    <app-dialog
      ref="appDialog"
      title="Change Due date"
      submit-button-text="Done"
      @submit="onDialogSubmit"
    >
      <template #body>
        <div>
          <oxd-form-row>
            <oxd-input-field
              :value="task.title"
              autofocus
              label="Task Title"
              disabled
            />
          </oxd-form-row>

          <oxd-form-row>
            <oxd-input-field
              :value="task.notes"
              type="textarea"
              :label="$t('general.note')"
              :placeholder="$t('general.add_note')"
              label-icon="pencil-square"
              disabled
            />
          </oxd-form-row>

          <oxd-form-row>
            <date-input
              v-model="taskModel.dueDate"
              label="Due Date"
              :rules="rules.dueDate"
            />
          </oxd-form-row>
        </div>
      </template>
    </app-dialog>
  </li>
</template>

<script>
import {ClockIcon} from '@heroicons/vue/24/solid';
import AppDialog from '@/core/components/dialogs/AppDialog';
import {
  endDateShouldBeAfterStartDate,
  validDateFormat,
} from '@/core/util/validation/rules';

export default {
  name: 'TaskListItem',
  components: {
    AppDialog,
    ClockIcon,
  },
  props: {
    task: {
      type: Object,
      required: true,
    },
  },
  emits: ['taskDetailChanged'],
  data() {
    return {
      dueDate: null,
      userValidDateFormat: 'yyyy-mm-dd',
      rules: {
        dueDate: [
          validDateFormat(this.userValidDateFormat),
          endDateShouldBeAfterStartDate(
            () => new Date().toISOString().split('T')[0],
            'Due Date should be after today',
          ),
        ],
      },
    };
  },
  computed: {
    taskModel: {
      get() {
        return this.task;
      },
      set(value) {
        this.$emit('update:prop', value);
      },
    },
    taskDueDate() {
      return this.task.dueDate;
    },
  },
  watch: {
    taskDueDate() {
      this.$emit('taskDetailChanged');
    },
  },
  methods: {
    listItemClick() {
      this.$refs.appDialog.showDialog();
    },
    onDialogSubmit() {
      this.$emit('taskDetailChanged');
    },
  },
};
</script>

<style src="./task-list-item.scss" scoped lang="scss"></style>
