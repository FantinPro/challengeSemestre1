<template>
  <div class="flex justify-between">
    <div class="flex flex-col">
      <span class="text-white font-bold">{{ pseudo }}</span>
      <span class="text-gray-400 text-sm">{{ createdAt }}</span>
    </div>
  </div>
</template>
<script setup>
import { formatDistance } from "date-fns";

const props = defineProps({
  item: {
    type: Object,
    required: true,
  },
});

const { id, pseudo, created } = props.item;

const createdAt = formatDistance(new Date(created), new Date(), { addSuffix: true });
</script>
<style>
</style>