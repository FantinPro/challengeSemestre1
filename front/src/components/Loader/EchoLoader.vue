<template>
  <div class="flex flex-col">
    <div v-for="i in props.quantity" :key="i" class="flex">
      <div class="flex border-b border-[#4c5157] w-full">
        <div class="flex p-2 pt-0 mb-2">
          <SkeletonLoader class="w-12 h-12 mt-6" type="circle" />
          <div class="flex flex-col ml-2">
            <SkeletonLoader class="w-52 h-4 mt-6" />
            <SkeletonLoader class="w-72 h-3 mt-3" />
            <SkeletonLoader class="w-72 h-3 mt-2" />
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script setup>
import SkeletonLoader from './SkeletonLoader.vue';
const props = defineProps({
  quantity: {
    type: Number,
    default: 10,
  },
})
</script>