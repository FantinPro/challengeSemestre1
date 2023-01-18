<template>
  <div class="flex justify-between">
    <div class="flex flex-col">
      <span class="text-white font-bold">{{ username }}</span>
      <span class="text-gray-400 text-sm">{{ time }}</span>
    </div>
  </div>
</template>
<script setup>
const props = defineProps({
  item: {
    type: Object,
    required: true,
  },
});

const { id, username, avatar, time } =
  props.item;
</script>
<style>
</style>