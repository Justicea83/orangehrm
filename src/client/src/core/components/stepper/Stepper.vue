<template>
  <div class="wizard-wrapper">
    <wizard
      ref="wizard"
      :start-index="startIndex"
      :custom-tabs="tabs"
      :before-mount="beforeMount"
      @change="stepChanged"
      @complete:wizard="completed"
    >
      <div
        v-for="(_, index) in tabs.length"
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
import './form-wizard-vue3.scss';

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
    formRef: {
      type: Object,
      default: null,
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
  emits: ['completed', 'beforeChange', 'afterChange', 'beforeMount'],
  data() {
    return {
      currentStep: 0,
      nextButton: {
        disabled: true,
      },
    };
  },
  methods: {
    stepChanged(index) {
      this.$emit('beforeChange', {index, ref: this.$refs.wizard});
      this.currentStep = index;
      this.$emit('afterChange', {index, ref: this.$refs.wizard});
    },
    completed() {
      this.$emit('completed');
    },
    beforeMount() {
      this.$emit('beforeMount', this.currentStep);
    },
  },
};
</script>

<style lang="scss" scoped>
@import '@ohrm/oxd/variables.scss';

* {
  margin: 0;
  box-sizing: border-box;
  padding: 0;
}

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
