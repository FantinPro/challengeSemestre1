<template>
  <div class="font-medium text-base">
    <div v-if="props.item.isDeleted" class="text-white">
      <span class="font-bold text-orange-400">[deleted]</span>
      <span class="ml-2 text-gray-400">{{ props.item.content }}</span>
    </div>
    <div v-else>
      <span>{{ props.item.content }}</span>
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
</script>
