<template>
  <div class="flex justify-between">
    <div class="flex flex-col">
      <div class="flex items-baseline gap-2">
        <router-link :to="`/${pseudo}`" class="text-gray-200 font-bold">{{ pseudo }}</router-link>
        <router-link :to="`/${pseudo}`" class="text-gray-400 text-sm">@{{ pseudo }}</router-link>
      </div>
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

const { pseudo, created } = props.item;

const createdAt = formatDistance(new Date(created), new Date(), { addSuffix: true });
</script>
