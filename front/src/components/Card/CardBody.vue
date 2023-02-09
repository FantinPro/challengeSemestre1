<template>
  <div class="font-medium text-base">
    <div v-if="isDeleted" class="text-white">
      <span class="font-bold">[deleted]</span>
      <span class="ml-2">{{ content }}</span>
    </div>
    <div v-else>
      <span>{{ content }}</span>
    </div>
  </div>
</template>
<script setup>
import { ref } from "vue-demi";

const props = defineProps({
  item: {
    type: Object,
    required: true,
  },
});

const content = ref(props.item.content);
const isDeleted = ref(props.item.isDeleted);

</script>
