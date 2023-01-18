<template>
  <div class="mt-4">
    <span class="text-white">{{ text }}</span>
  </div>
</template>
<script setup>
const props = defineProps({
  item: {
    type: Object,
    required: true,
  },
});

const { id, text } = props.item;

</script>
<style>
</style>