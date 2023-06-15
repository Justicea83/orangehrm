<template>
  <h3 class="italic">
    {{ name['name'] ?? '' }}
  </h3>
</template>

<script>
export default {
  name: 'NameCell',
  props: {
    name: {
      type: String,
      required: true,
    },
  },
};
</script>

<style scoped></style>
