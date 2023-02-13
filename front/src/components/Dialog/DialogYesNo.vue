<template>
    <div>
      <div class="justify-center">
        <button
          type="button"
          class="rounded-md bg-red-400 px-4 py-2 text-sm font-medium text-white hover:bg-red-600 focus:outline-none focus-visible:ring-2"
          @click="openModal">
          {{ props.actionName }}
        </button>
      </div>
      <TransitionRoot appear :show="isOpen" as="template">
        <Dialog as="div" class="relative z-10" @close="closeModal">
          <TransitionChild
            as="template"
            enter="duration-300 ease-out"
            enter-from="opacity-0"
            enter-to="opacity-100"
            leave="duration-200 ease-in"
            leave-from="opacity-100"
            leave-to="opacity-0">
            <div class="fixed inset-0 bg-black bg-opacity-25" />
          </TransitionChild>
  
          <div class="fixed inset-0 overflow-y-auto">
            <div
              class="flex min-h-full items-center justify-center p-4 text-center">
              <TransitionChild
                as="template"
                enter="duration-300 ease-out"
                enter-from="opacity-0 scale-95"
                enter-to="opacity-100 scale-100"
                leave="duration-200 ease-in"
                leave-from="opacity-100 scale-100"
                leave-to="opacity-0 scale-95">
                <DialogPanel
                  class="flex w-full max-w-md transform flex-col overflow-hidden rounded-2xl bg-white p-6 text-left align-middle shadow-xl transition-all">
                  <DialogTitle
                    as="h3"
                    class="text-lg font-medium leading-6 text-gray-900">
                    Delete ad
                  </DialogTitle>
  
                  <div class="my-4 flex flex-col gap-3  text-black">
                    You are about to delete this ad. Are you sure you want to ?
                  </div>
  
                  <div class="mt-auto flex justify-end gap-2">
                    <button
                      type="button"
                      class="inline-flex justify-center rounded-md border border-transparent bg-slate-100 px-4 py-2 text-sm font-medium text-slate-900 hover:bg-slate-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2"
                      @click="closeModal">
                      Cancel
                    </button>
                    <button
                      type="button"
                      class="inline-flex justify-center gap-2 rounded-md border border-transparent bg-blue-100 px-4 py-2 text-sm font-medium text-blue-900 hover:bg-blue-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2"
                      @click="confirm">
                      <span>Confirm</span>
                    </button>
                  </div>
                </DialogPanel>
              </TransitionChild>
            </div>
          </div>
        </Dialog>
      </TransitionRoot>
    </div>
  </template>
  
<script setup>
import {
  Dialog,
  DialogPanel,
  DialogTitle,
  TransitionChild,
  TransitionRoot,
} from '@headlessui/vue';
import { ref } from 'vue';
import { AD_STATUS } from '../../utils/constants';
  
const props = defineProps({
  actionName: {
    type: String,
    default: 'Action',
  },
  ad: {
    type: Object,
    required: true,
  },
});
  
const emit = defineEmits(['action']);
  
const isOpen = ref(false);
  
function closeModal() {
  isOpen.value = false;
}
function openModal() {
  isOpen.value = true;
}
  
const confirm = () => {
  emit('action', props.ad);
};
</script>
  