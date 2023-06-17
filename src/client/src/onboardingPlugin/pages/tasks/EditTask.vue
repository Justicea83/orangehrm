<template>
  <div class="orangehrm-background-container">
    <div class="orangehrm-card-container">
      <oxd-text tag="h6" class="orangehrm-main-title"> Edit Task </oxd-text>

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
import {APIService} from '@/core/util/services/api.service';
import {
  required,
  shouldNotExceedCharLength,
} from '@/core/util/validation/rules';
import OnboardingTypeDropdown from '@/orangehrmPimPlugin/components/OnboardingTypeDropdown';
import {navigate} from '@/core/util/helper/navigation';
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
  type: null,
};

export default {
  name: 'EditTask',

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
    taskId: {
      type: String,
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
        taskId: [required],
        type: [required],
        notes: [shouldNotExceedCharLength(1000)],
      },
    };
  },

  created() {
    this.isLoading = true;
    this.http
      .get(this.taskId)
      .then((response) => {
        const {data} = response.data;
        this.task.title = data.title;
        this.task.notes = data.notes;
        this.task.type = {
          id: data.taskType?.id,
          label: data.taskType?.name,
        };

        // Fetch list data for unique test
        return this.http.getAll({limit: 0});
      })
      .then((response) => {
        const {data} = response.data;
        this.rules.title.push((v) => {
          const index = data.findIndex((item) => item.title === v);
          if (index > -1) {
            const {id} = data[index];
            return id !== this.taskId
              ? this.$t('general.already_exists')
              : true;
          } else {
            return true;
          }
        });
      })
      .finally(() => {
        this.isLoading = false;
      });
  },

  methods: {
    onSave() {
      this.isLoading = true;
      const payload = {
        ...this.task,
        type: this.task?.type?.id,
      };
      this.http
        .update(this.taskId, {
          ...payload,
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
      navigate('/taskManagement/viewTasks');
    },
  },
};
</script>

<style scoped></style>
