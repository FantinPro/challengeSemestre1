<template>
  <TabGroup>
    <div class="mt-5 border-b border-[#4c5157]">
      <div v-if="customTitle" class="px-6">
        <slot name="title" />
      </div>
      <div v-else-if="title" class="px-6">
        <h1 class="text-2xl font-bold cursor-pointer">
          {{ title }}
        </h1>
      </div>
      <div v-if="type === 'tabs'" class="mt-3">
        <TabList class="flex">
          <Tab
            v-for="tab in tabs"
            :key="tab"
            v-slot="{ selected }"
            class="focus-visible:outline-none w-full">
            <div
              class="
                hover:bg-[#2f3336]
                hover:transition
                hover:duration-600
                hover:ease-in-out
                text-white text-center
                font-semibold
                w-full
                justify-center
                flex
              ">
              <div class="flex flex-col items-center">
                <span class="p-4">{{ tab }}</span>
                <div
                  class="
                    rounded-md
                    bg-primary-400
                    h-[3px]
                    w-full
                  "
                  v-if="selected" />
              </div>
            </div>
          </Tab>
        </TabList>
      </div>
      <div v-else class="mt-3">
        <slot name="buttons" />
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
    default: '',
  },
  customTitle: {
    type: Boolean,
    default: false,
  },
  type: {
    type: String,
    default: 'tabs',
  },
  tabs: {
    type: Array,
    default: () => [],
  },
});
</script>
