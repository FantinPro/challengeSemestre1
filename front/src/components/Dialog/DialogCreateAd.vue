<template>
  <TransitionRoot appear :show="props.isOpen" as="template">
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
                Create ad
              </DialogTitle>

              <div class="flex flex-col gap-2 text-black">
                <div class="flex items-center">
                  <div>
                    from :
                    <strong>{{ new Date().toLocaleDateString() }}</strong>
                  </div>
                  <ArrowLongRightIcon class="mx-4 h-4 w-4" />
                  <div>
                    to : <strong>{{ new Date().toLocaleDateString() }}</strong>
                  </div>
                </div>

                <FormKit
                  v-slot="{ state: { valid }}"
                  v-model="newAd"
                  type="form"
                  :actions="false"
                  :submit-attrs="{
                    inputClass: 'im-on-the-button',
                    wrapperClass: 'im-on-the-wrapper',
                    outerClass: 'im-on-the-outer-wrapper',
                  }"
                  @submit="handleSubmit">
                  <FormKit
                    type="textarea"
                    placeholder="Content"
                    name="content"
                    label="Content of the Ad"
                    validation="required|length:1,255"
                    :validation-messages="{
                      length: 'Content must be between 1 and 255 characters',
                    }"
                    help="Maximum 255 characters" />
                  <FormKit
                    type="number"
                    name="price"
                    placeholder="€"
                    :max="1000"
                    :min="1"
                    validation="required|max:1000|min:1"
                    :validation-messages="{
                      max: 'Maximum 1000€',
                      min: 'Minimum 1€',
                    }"
                    label="Price of the Ad"
                    help="The higher your offer, the more likely it is to appear, (max 1000€)" />
                  <div class="mt-auto flex justify-end gap-2">
                    <button
                      type="button"
                      class="inline-flex justify-center rounded-md border border-transparent bg-slate-100 px-4 py-2 text-sm font-medium text-slate-900 hover:bg-slate-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2"
                      @click="closeModal">
                      Cancel
                    </button>
                    <button
                      type="submit"
                      :disabled="!valid"
                      class="inline-flex justify-center gap-2 rounded-md border border-transparent bg-blue-100 px-4 py-2 text-sm font-medium text-blue-900 hover:bg-blue-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2">
                      <span>Create</span>
                    </button>
                  </div>
                </FormKit>
              </div>
            </DialogPanel>
          </TransitionChild>
        </div>
      </div>
    </Dialog>
  </TransitionRoot>
</template>

<script setup>
import {
  Dialog,
  DialogPanel,
  DialogTitle,
  TransitionChild,
  TransitionRoot,
} from '@headlessui/vue';
import { ArrowLongRightIcon } from '@heroicons/vue/20/solid';
import { reactive } from 'vue';

const newAd = reactive({
  content: '',
  price: 5,
});

const props = defineProps({
  isOpen: {
    type: Boolean,
    default: false,
  },
  startDate: {
    type: Date,
    required: true,
  },
  endDate: {
    type: Date,
    required: true,
  },
});

const emit = defineEmits(['close']);

function closeModal() {
  emit('close');
}

const handleSubmit = (values) => {
  console.log(values)
};

</script>
