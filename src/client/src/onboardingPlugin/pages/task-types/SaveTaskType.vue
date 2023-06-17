<template>
  <div class="orangehrm-background-container">
    <div class="orangehrm-card-container">
      <oxd-text tag="h6" class="orangehrm-main-title">Add Task Type</oxd-text>

      <oxd-divider />

      <oxd-form :loading="isLoading" @submit-valid="onSave">
        <oxd-form-row>
          <oxd-input-field
            v-model="taskType.name"
            autofocus
            label="Name"
            :rules="rules.name"
            required
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

const initialTaskType = {
  name: '',
};

export default {
  name: 'SaveTaskType',
  components: {
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
      '/api/v2/task-management/task-types',
    );
    return {
      http,
    };
  },

  data() {
    return {
      isLoading: false,
      taskType: {...initialTaskType},
      rules: {
        name: [required, shouldNotExceedCharLength(100)],
      },
    };
  },
  methods: {
    onSave() {
      this.isLoading = true;
      this.http
        .create({
          ...this.taskType,
        })
        .then(() => {
          return this.$toast.saveSuccess();
        })

        .then(() => {
          this.onCancel();
        })
        .catch(() => {
          this.isLoading = false;
        });
    },
    onCancel() {
      navigate('/taskManagement/viewTaskTypes');
    },
  },
};
</script>

<style scoped></style>
