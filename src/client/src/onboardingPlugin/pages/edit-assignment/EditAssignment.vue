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
        this.selectedTask = data;
        console.clear();
        console.log(data);
      });
    },
  },
};
</script>

<style scoped></style>
