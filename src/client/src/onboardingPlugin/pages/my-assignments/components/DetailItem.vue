<template>
  <div
    class="flex justify-start items-center grid grid-flow-col mt-3 auto-cols-max"
  >
    <div class="w-32">
      <h5 class="text-sm text-gray-400">{{ title }}</h5>
    </div>

    <h4
      class="text-gray-500"
      :class="{
        'text-red-400': expiring,
      }"
    >
      {{ subTitle }}
    </h4>
  </div>
</template>

<script>
export default {
  name: 'DetailItem',
  props: {
    title: {
      type: String,
      required: true,
    },
    subTitle: {
      type: String,
      required: true,
    },
    expiring: {
      type: Boolean,
      default: false,
      required: false,
    },
  },
};
</script>

<style scoped></style>
