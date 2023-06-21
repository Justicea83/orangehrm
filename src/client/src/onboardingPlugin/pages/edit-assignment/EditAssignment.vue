<template>
  <create-onboarding
    v-if="selectedTask"
    mode="edit"
    :assignment="selectedTask"
  />
</template>

<script>
import {APIService} from '@/core/util/services/api.service';
import CreateOnboarding from '@/onboardingPlugin/pages/create/CreateOnboarding';
import {navigate} from '@/core/util/helper/navigation';

export default {
  name: 'EditAssignment',
  components: {
    CreateOnboarding,
  },
  props: {
    assignmentId: {
      type: String,
      default: null,
    },
  },
  setup() {
    const http = new APIService(
      window.appGlobal.baseUrl,
      '/api/v2/task-management/task-assignments',
    );
    return {
      http,
    };
  },
  data() {
    return {
      selectedTask: null,
    };
  },
  mounted() {
    this.loadData();
  },
  methods: {
    loadData() {
      this.http.get(this.assignmentId).then((results) => {
        const {data} = results.data;
        if (data.submittedAt) {
          this.$toast
            .warn({
              message: 'The assignment has already been submitted',
              title: 'Assignment Submitted',
            })
            .then(() => {
              navigate('/taskManagement/viewTaskGroups');
            });
        } else {
          this.selectedTask = data;
        }
      });
    },
  },
};
</script>

<style scoped></style>
