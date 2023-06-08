<template>
  <oxd-input-field
    type="select"
    label="Task Type"
    :options="options"
    :rules="rules"
    :required="required"
  />
</template>

<script>
import {onBeforeMount, ref} from 'vue';
import {APIService} from '@/core/util/services/api.service';

export default {
  name: 'OnboardingTypeDropdown',
  props: {
    rules: {
      type: Array,
      required: false,
      default: Array.from([]),
    },
    required: {
      type: Boolean,
      required: false,
      default: false,
    },
  },
  setup() {
    const options = ref([]);
    const http = new APIService(
      window.appGlobal.baseUrl,
      '/api/v2/onboarding/task-types',
    );
    onBeforeMount(() => {
      http.getAll().then(({data}) => {
        options.value = data.data.map((item) => {
          return {
            id: item.id,
            label: item.name,
          };
        });
      });
    });
    return {
      options,
    };
  },
};
</script>

<style scoped></style>
