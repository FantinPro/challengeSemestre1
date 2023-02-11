<template>
  <div>
    <div class="justify-center">
      <button
        type="button"
        class="rounded-md bg-black bg-opacity-20 px-4 py-2 text-sm font-medium text-white hover:bg-opacity-30 focus:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-opacity-75"
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
                  Manage ad of
                  <strong>{{ props.ad.owner.pseudo }}</strong>
                </DialogTitle>

                <div class="my-4 flex flex-col gap-3  text-black">
                  <div class="flex justify-center">
                    <img
                      class="h-12 rounded-full"
                      :src="props.ad.owner.profilePicture"
                      alt="" />
                  </div>
                  <div class="flex">
                    <ChatBubbleBottomCenterIcon
                      class="mt-1 mr-2 h-5 w-5 min-w-[20px] text-primary-300" />
                    Content :&nbsp;{{ props.ad.message }}
                  </div>
                  <div class="flex items-center">
                    <CurrencyEuroIcon
                      class="mr-2 h-5 w-5 min-w-[20px] text-primary-300" />
                    Price :&nbsp; <strong>{{ props.ad.price }}€</strong>
                  </div>
                  <div class="flex items-center">
                    <CalendarDaysIcon
                      class="mr-2 h-5 w-5 min-w-[20px] text-primary-300" />
                    Dates :&nbsp; from &nbsp;
                    <strong
                      >{{
                        new Date(props.ad.startDate).toLocaleDateString()
                      }}</strong
                    >&nbsp; to&nbsp;
                    <strong
                      >{{
                        new Date(props.ad.endDate).toLocaleDateString()
                      }}</strong
                    >
                  </div>
                  <div class="w-full mt-3 h-52">
                      <Select title="Manage Ad" :list="actionsStatus" :selected-value="selectedStatus" @update:model-value="updateStatus" />
                  </div>
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
                    :disabled="isLoadingReject || isLoadingMask"
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
import { useMutation } from 'vue-query';
import { toast } from 'vue3-toastify';
import { patchAd } from '../../services/service.ads';
import {
  CalendarDaysIcon,
  ChatBubbleBottomCenterIcon,
  CurrencyEuroIcon,
} from '@heroicons/vue/20/solid';
import { AD_STATUS } from '../../utils/constants';
import Select from '../Select/Select.vue';

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

const emit = defineEmits(['updateAdList']);

const actionsStatus = AD_STATUS;

const selectedStatus = ref(actionsStatus[0]);

const updateStatus = (action) => {
  selectedStatus.value = action;
};

const isOpen = ref(false);

function closeModal() {
  isOpen.value = false;
}
function openModal() {
  isOpen.value = true;
}

const { isLoading: isLoadingReject, mutate: patchAdMutation } =
  useMutation((data) => patchAd(data), {
    onSuccess: (ad) => {
      toast.success(`Ad has been set to "${ad.status}"`);
      emit('updateAdList');
      closeModal();
    },
    onError: () => {
      toast.error('Something went wrongeeee');
    },
  });

const confirm = () => {
  patchAdMutation({
    id: props.ad.id,
    status: selectedStatus.value.value,
  });
};
</script>
