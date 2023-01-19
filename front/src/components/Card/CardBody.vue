<template>
  <div class="mt-4 font-medium text-base">
    <div v-if="!isDeleted" class="text-white">
      <span class="font-bold">[deleted]</span>
      <span class="ml-2">This echo has been deleted because it violated the community guidelines.</span>
    </div>
    <div v-else class="">
      <span class="">{{ content }}</span>
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

const { id, content, isDeleted } = props.item;

</script>
