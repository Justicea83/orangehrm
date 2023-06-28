<template>
  <oxd-input-field
    v-model="selectedOptions"
    type="multiselect"
    label="Task Type"
    :options="options"
    :rules="rules"
    :required="required"
  />
</template>

<script>
import {onBeforeMount, ref} from 'vue';
import {APIService} from '@/core/util/services/api.service';
import {OxdInputField} from '@ohrm/oxd';

export default {
  name: 'TaskTypeDropdown',
  components: {
    OxdInputField,
  },
  props: {
    rules: {
      type: Array,
      required: false,
      default: Array.from([]),
    },
    selected: {
      type: Array,
      required: false,
      default: () => Array.from([]),
    },
    required: {
      type: Boolean,
      required: false,
      default: false,
    },
  },
  emits: ['optionsChanged'],
  setup() {
    const options = ref([]);
    const selectedOptions = ref([]);

    const http = new APIService(
      window.appGlobal.baseUrl,
      '/api/v2/task-management/task-types',
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
      selectedOptions,
    };
  },
  watch: {
    selectedOptions(options) {
      this.$emit('optionsChanged', options);
    },
    selected(options) {
      this.selectedOptions = options;
    },
  },
};
</script>

<style scoped></style>
