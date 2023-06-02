<template>
  <div class="orangehrm-background-container">
    <div class="orangehrm-card-container">
      <oxd-text tag="h6" class="orangehrm-main-title"> Edit Task </oxd-text>

      <oxd-divider />

      <oxd-form :loading="isLoading" @submitValid="onSave">
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
          <jobtitle-dropdown
            v-model="task.jobTitleId"
            :rules="rules.jobTitleId"
            required
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
import JobtitleDropdown from '@/orangehrmPimPlugin/components/JobtitleDropdown';
import {navigate} from '@/core/util/helper/navigation';

const initialTask = {
  title: '',
  notes: '',
  jobTitleId: null,
  type: null,
};

export default {
  name: 'EditTask',

  components: {
    'jobtitle-dropdown': JobtitleDropdown,
    OnboardingTypeDropdown,
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
      '/api/v2/onboarding/tasks',
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
          id: data.type,
          label: data.type === 0 ? 'Onboarding' : 'Offboarding',
        };
        this.task.jobTitleId = data.jobTitle?.id ? data.jobTitle : null;

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
      const payload = {
        ...this.task,
        jobTitleId: this.task?.jobTitleId?.id,
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
        });
    },
    onCancel() {
      navigate('/admin/viewTaskList');
    },
  },
};
</script>

<style scoped></style>
