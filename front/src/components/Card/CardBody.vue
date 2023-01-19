<template>
  <div class="mt-4">
    <span class="text-white">{{ content }}</span>
  </div>
</template>
<script setup>
const props = defineProps({
  item: {
    type: Object,
    required: true,
  },
});

const { id, content } = props.item;

</script>
<style>
</style>