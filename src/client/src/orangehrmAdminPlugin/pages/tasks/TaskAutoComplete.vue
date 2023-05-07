<template>
  <oxd-input-field
    type="autocomplete"
    label="Task Title"
    :clear="false"
    :create-options="loadTasks"
  >
    <template #option="{data}">
      <span>{{ data.label }}</span>
    </template>
  </oxd-input-field>
</template>

<script>
import {APIService} from '@ohrm/core/util/services/api.service';

export default {
  name: 'TaskAutoComplete',
  props: {
    params: {
      type: Object,
      default: () => ({}),
    },
    apiPath: {
      type: String,
      default: '/api/v2/onboarding/tasks',
    },
  },
  setup(props) {
    const http = new APIService(window.appGlobal.baseUrl, props.apiPath);
    return {
      http,
    };
  },
  methods: {
    async loadTasks(searchParams) {
      return new Promise(resolve => {
        if (searchParams.trim()) {
          this.http
            .getAll({
              title: searchParams.trim(),
            })
            .then(({data}) => {
              const formattedData = data.data.map(task => {
                return {
                  id: task.id,
                  label: task.title,
                  _task: task,
                };
              });
              resolve(formattedData);
            });
        } else {
          resolve([]);
        }
      });
    },
  },
};
</script>

<style scoped></style>
