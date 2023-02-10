<template>
  <TabGroup :selected-index="selectedTab" @change="changeTab">
    <div :class="sticky ? 'sticky top-0 z-10 bg-[#212529] opacity-95 pt-5' : '' " class="border-b border-[#3b4043]">
      <div v-if="customTitle" class="px-2 pb-3">
        <slot name="title" />
      </div>
      <div v-else-if="title" class="px-4 pb-3">
        <h1 class="text-2xl font-bold cursor-pointer">
          {{ title }}
        </h1>
      </div>
      <div v-if="type === 'tabs'">
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
                  v-if="selected"
                  class="
                    rounded-md
                    bg-primary-400
                    h-[3px]
                    w-full
                  " />
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
import { ref } from 'vue-demi';

const props = defineProps({
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
  sticky: {
    type: Boolean,
    default: false,
  },
  selectedTab: {
    type: Number,
    default: 0,
  },
});

const emit = defineEmits(['tabChange']);

const selectedTab = ref(props.selectedTab);

function changeTab(index) {
  selectedTab.value = index
  emit('tabChange', index)
}
</script>
