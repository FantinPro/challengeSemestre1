<template>
  <TabGroup>
    <div class="mt-5 border-b border-[#4c5157]">
      <div class="px-6">
        <h1 class="text-2xl font-bold cursor-pointer">
          {{ title }}
        </h1>
      </div>
      <div class="mt-3">
        <TabList class="flex">
          <Tab
            v-for="tab in tabs"
            :key="tab"
            v-slot="{ selected }"
            class="focus-visible:outline-none w-full">
            <div
              :class="[
                'hover:bg-[#2f3336] text-white text-center font-semibold py-4 mt-0.5 border-b-2',
                selected ? 'border-primary-500' : 'border-transparent',
              ]">
              {{ tab }}
            </div>
          </Tab>
        </TabList>
      </div>
    </div>
    <div class="flex-auto h-[0]">
      <slot name="panels" />
    </div>
  </TabGroup>
</template>
<script setup>
import { TabGroup, TabList, Tab } from '@headlessui/vue';

defineProps({
  title: {
    type: String,
    default: 'Home',
  },
  tabs: {
    type: Array,
    default: () => ['For you', 'Following'],
  },
});
</script>
