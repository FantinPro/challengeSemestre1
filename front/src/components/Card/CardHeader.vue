<template>
  <div class="flex justify-between">
    <div class="flex flex-col">
      <span class="text-white font-bold">{{ pseudo }}</span>
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

const { id, pseudo, time = "Il y a 1 heure" } =
  props.item;
</script>
<style>
</style>