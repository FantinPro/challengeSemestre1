<template>
  <TabGroup>
    <div class="mt-5 border-b border-[#4c5157]">
      <div class="px-6">
        <slot name="title" />
      </div>   
      <div class="mt-3">
        <TabList class="flex">
          <Tab v-for="tab in tabs" :key="tab" class="hover:bg-[#2f3336] text-white text-center font-semibold w-full py-4">
            {{ tab }}
          </Tab>
        </TabList>
      </div>
    </div>
    <div class="mt-3">
      <slot name="panels" />
    </div>
  </TabGroup>
</template>
<script setup>

import { TabGroup, TabList, Tab } from '@headlessui/vue';

defineProps({
  tabs: {
    type: Array,
    default: () => ['For you', 'Following']
  }
});
</script>
