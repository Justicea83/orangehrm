<template>
  <div class="orangehrm-background-container">
    <div class="orangehrm-card-container">
      <oxd-text tag="h6" class="orangehrm-main-title"> Add Task </oxd-text>

      <oxd-divider />

      <oxd-form :loading="isLoading" @submit-valid="onSave">
        <oxd-form-row>
          <oxd-input-field
            v-model="task.title"
            autofocus
            label="Task Title"
            :rules="rules.title"
            required
          />
        </oxd-form-row>

        <oxd-form-row>
          <onboarding-type-dropdown
            v-model="task.type"
            required
            label="Task Type"
            :rules="rules.type"
          />
        </oxd-form-row>

        <oxd-form-row>
          <oxd-input-field
            v-model="task.notes"
            type="textarea"
            :label="$t('general.note')"
            :placeholder="$t('general.add_note')"
            label-icon="pencil-square"
            :rules="rules.notes"
          />
        </oxd-form-row>

        <oxd-divider />

        <oxd-form-actions>
          <required-text />
          <oxd-button
            display-type="ghost"
            :label="$t('general.cancel')"
            @click="onCancel"
          />
          <submit-button />
        </oxd-form-actions>
      </oxd-form>
    </div>
  </div>
</template>

<script>
import {
  required,
  shouldNotExceedCharLength,
} from '@/core/util/validation/rules';
import {navigate} from '@/core/util/helper/navigation';
import OnboardingTypeDropdown from '@/orangehrmPimPlugin/components/OnboardingTypeDropdown';
import {APIService} from '@/core/util/services/api.service';
import {
  OxdForm,
  OxdFormRow,
  OxdFormActions,
  OxdButton,
  OxdDivider,
  OxdText,
  OxdInputField,
} from '@ohrm/oxd';
import SubmitButton from '@ohrm/components/buttons/SubmitButton.vue';
import RequiredText from '@ohrm/components/labels/RequiredText.vue';

const initialTask = {
  title: '',
  notes: '',
  jobTitleId: null,
  type: null,
};

export default {
  name: 'SaveTask',
  components: {
    OnboardingTypeDropdown,
    OxdForm,
    OxdFormActions,
    OxdFormRow,
    OxdDivider,
    OxdButton,
    OxdText,
    OxdInputField,
    SubmitButton,
    RequiredText,
  },
  props: {
    allowedFileTypes: {
      type: Array,
      required: true,
    },
    maxFileSize: {
      type: Number,
      required: true,
    },
  },

  setup() {
    const http = new APIService(
      window.appGlobal.baseUrl,
      '/api/v2/task-management/tasks',
    );
    return {
      http,
    };
  },

  data() {
    return {
      isLoading: false,
      task: {...initialTask},
      rules: {
        title: [required, shouldNotExceedCharLength(100)],
        type: [required],
        notes: [shouldNotExceedCharLength(1000)],
      },
    };
  },
  methods: {
    onSave() {
      this.isLoading = true;
      const payload = {
        ...this.task,
        jobTitleId: this.task?.jobTitleId?.id,
        type: this.task?.type?.id,
      };
      this.http
        .create({
          ...payload,
        })
        .then(() => {
          return this.$toast.saveSuccess();
        })
        .then(() => {
          this.onCancel();
        })
        .catch(() => (this.isLoading = false));
    },
    onCancel() {
      navigate('/taskManagement/viewTasks');
    },
  },
};
</script>

<style scoped></style>
