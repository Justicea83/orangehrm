<template>
  <div
    class="grid"
    :class="{'grid-cols-3 gap-4': showDetails, 'grid-cols-2': !showDetails}"
  >
    <div class="col-span-2">
      <assignment
        v-for="task in tasks"
        :key="task.id"
        :task-group="task"
        @open-details="openAssignmentDetail"
      />
    </div>
    <div>
      <assignment-detail
        v-if="selectedTask && showDetails"
        :task-group="selectedTask"
        @on-close="onClose"
      />
    </div>
  </div>
</template>

<script>
import {APIService} from '@/core/util/services/api.service';
import Assignment from '@/onboardingPlugin/pages/my-assignments/components/Assignment';
import AssignmentDetail from '@/onboardingPlugin/pages/my-assignments/components/AssignmentDetail';

export default {
  name: 'MyAssignments',
  components: {
    Assignment,
    AssignmentDetail,
  },
  setup() {
    const http = new APIService(
      window.appGlobal.baseUrl,
      '/api/v2/onboarding/my-assignments',
    );
    return {
      http,
    };
  },
  data() {
    return {
      meta: null,
      tasks: [],
      selectedTask: null,
      showDetails: false,
    };
  },
  mounted() {
    this.loadData();
  },
  methods: {
    onClose() {
      this.showDetails = false;
    },
    openAssignmentDetail(data) {
      console.log(data);
      this.selectedTask = data;
      this.showDetails = true;
    },
    loadData() {
      this.http.getAll().then((results) => {
        const {meta, data} = results.data;
        this.meta = meta;
        this.tasks = data;
        this.selectedTask = data[0];
      });
    },
  },
};
</script>

<style scoped></style>
