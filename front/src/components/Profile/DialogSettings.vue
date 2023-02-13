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

      <div class="fixed inset-0">
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
              class="
                absolute
                flex
                w-full
                max-w-2xl
                transform
                flex-col
                overflow-auto
                rounded-2xl
                bg-[#212529]
                text-left
                align-middle
                shadow-xl
                transition-all
              ">
              <FormKit type="form" autocomplete="off" :actions="false" @submit="saveMutation">
                <DialogTitle
                  as="div"
                  class="
                    sticky
                    top-0
                    z-10
                    bg-[#212529]
                    opacity-95
                    flex
                    p-2
                    justify-between
                  ">
                  <div class="flex justify-start items-center gap-2">
                    <button
                      type="button"
                      class="
                        inline-flex
                        justify-center
                        rounded-full
                        border border-transparent
                        p-2
                        text-sm
                        font-medium
                        text-white
                        hover:bg-gray-700
                        focus:outline-none
                        focus-visible:ring-1
                        focus-visible:ring-gray-500
                        focus-visible:ring-offset-1
                      "
                      @click="closeModal">
                      <svg
                        class="h-6 w-6"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        aria-hidden="true">
                        <path
                          stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="2"
                          d="M6 18L18 6M6 6l12 12" />
                      </svg>
                    </button>
                    <h3 class="text-xl font-bold text-white">Settings</h3>
                  </div>
                  <div class="flex justify-end items-center">
                    <button
                      type="submit"
                      :disabled="isLoading"
                      class="
                        inline-flex
                        justify-center
                        gap-2
                        rounded-full
                        border border-transparent
                        font-bold
                        bg-white
                        px-4
                        py-1
                        text-md text-black
                        hover:bg-gray-200
                        focus:outline-none
                        focus-visible:ring-1
                        focus-visible:ring-gray-500
                        focus-visible:ring-offset-1
                      ">
                      <span>Save</span>
                      <Spin :is-loading="isLoading" />
                    </button>
                  </div>
                </DialogTitle>
                <div class="px-6 pt-4 pb-2">
                  <FormKit
                    v-model="password"
                    type="password"
                    name="password"
                    label="Password"
                    validation="required"
                    class="w-full"
                    :wrapper-class="{ 'formkit-wrapper': false }"
                    :classes="{ input: '!text-white' }" />
                  <FormKit
                    type="password"
                    label="Confirm password"
                    name="password_confirm"
                    validation="required|confirm"
                    class="w-full"
                    :wrapper-class="{ 'formkit-wrapper': false }"
                    :classes="{ input: '!text-white' }" />
                  <FormKitMessages />
                </div>
              </FormKit>
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
import { ref } from 'vue';
import { useMutation } from 'vue-query';
import { toast } from 'vue3-toastify';
import { useUserStore } from '../../store/user';
import Spin from '../Loader/Spin.vue';
import { FormKitMessages } from '@formkit/vue'

const { user, updateProfile } = useUserStore();
const emit = defineEmits(['close']);

const props = defineProps({
  isOpen: {
    type: Boolean,
    default: false,
  },
  profile: {
    type: Object,
    required: true,
  },
});

const password = ref('');

function closeModal() {
  emit('close');
}

const { isLoading, mutate: saveMutation } = useMutation(
  () => updateProfile({
    userId: user.id,
    password: password.value,
  }),
  {
    onSuccess: () => {
      toast.success('Password updated !');
      closeModal();
    },
    onError: (err) => {
      if (err.message.match(/Access Denied/)) {
        toast.error('You are not allowed to do this !');
      } else {
        toast.error('Something went wrong !');
      }
    },
  }
);

</script>
