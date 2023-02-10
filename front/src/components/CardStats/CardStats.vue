<template>
  <div v-if="!isLoading" class="h-28">
    <div class="rounded-2xl bg-white p-5 shadow-sm">
      <div class="text-base text-gray-400">
        {{ $filters.displayStat(props.title) }}
      </div>
      <div class="flex items-center pt-1">
        <div class="text-6xl font-bold text-gray-900">
          {{ props.unit }}{{ props.value }}
        </div>
        <span
          v-if="props.diff !== 0"
          :class="
            diff > 0 ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600'
          "
          class="mx-2 flex items-center rounded-full px-2 py-0.5 text-sm">
          <svg
            :class="diff > 0 ? 'rotate-180' : ''"
            class="h-4 w-4"
            viewBox="0 0 24 24"
            fill="none"
            xmlns="http://www.w3.org/2000/svg">
            <path
              d="M6 9L12 15L18 9"
              stroke="currentColor"
              stroke-width="2"
              stroke-linecap="round"
              stroke-linejoin="round"></path>
          </svg>
          <span>{{ props.diff }}</span>
        </span>
      </div>
    </div>
  </div>
  <div v-else class="w-[160px]">
    <div
      class="relative h-28 space-y-5 overflow-hidden rounded-2xl bg-white/5 p-5 shadow-xl shadow-black/5 before:absolute before:inset-0 before:-translate-x-full before:-skew-x-12 before:animate-[shimmer_2s_infinite] before:border-t before:border-white/10 before:bg-gradient-to-r before:from-transparent before:via-white/10 before:to-transparent">
      <div class="space-y-2">
        <div class="h-6 w-4/5 rounded-lg bg-white/10"></div>
        <div class="flex items-end gap-3">
          <div class="h-8 w-2/5 rounded-lg bg-white/5"></div>
          <div class="h-5 w-12 rounded-lg bg-white/5"></div>
        </div>
      </div>
    </div>
  </div>
</template>
<script setup>
const props = defineProps({
  isLoading: {
    type: Boolean,
    default: false,
  },
  title: {
    type: String,
    required: true,
  },
  unit: {
    type: String,
    default: '',
  },
  value: {
    type: Number,
    required: true,
  },
  diff: {
    type: Number,
    required: true,
  },
});
</script>
