<template>
  <div class="wizard-wrapper">
    <wizard
      :start-index="startIndex"
      :custom-tabs="tabs"
      @change="stepChanged"
      @complete:wizard="completed"
    >
      <div
        v-for="(key, index) in tabs.length"
        :key="index"
        class="stepper-content"
      >
        <div v-show="currentStep === index" class="stepper-tab">
          <slot :name="index"></slot>
        </div>
      </div>
    </wizard>
  </div>
</template>

<script>
import Wizard from 'form-wizard-vue3';

export default {
  name: 'Stepper',
  components: {
    Wizard,
  },
  props: {
    startIndex: {
      type: Number,
      default: 0,
      validator: (value) => value >= 0,
    },
    tabs: {
      type: Array,
      default: Array.from([
        {
          title: 'Step 1',
        },
        {
          title: 'Step 2',
        },
      ]),
    },
  },
  emits: ['completed'],
  data() {
    return {
      currentStep: 1,
    };
  },
  methods: {
    stepChanged(index) {
      this.currentStep = index;
    },
    completed() {
      this.$emit('completed');
    },
  },
};
</script>

<style lang="scss" scoped>
@import 'form-wizard-vue3/dist/form-wizard-vue3.css';
@import '@ohrm/oxd/variables.scss';

::v-deep(.form-wizard-vue) {
  .fw-step-active {
    background: $oxd-primary-one-color !important;
    color: white !important;
  }
  .fw-list-progress-active {
    background: $oxd-primary-one-color;
  }

  .fw-body {
    &-content {
      padding: 0;
    }
  }
}

::v-deep(.fw-btn) {
  background: $oxd-primary-one-color !important;
}
</style>
