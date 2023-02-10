<template>
  <div>
    <div class="justify-center">
      <button
        type="button"
        class="
          rounded-md
          bg-black bg-opacity-20
          px-4
          py-2
          text-sm
          font-medium
          text-white
          hover:bg-opacity-30
          focus:outline-none
          focus-visible:ring-2
          focus-visible:ring-white
          focus-visible:ring-opacity-75
        "
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
                class="
                  flex
                  h-[450px]
                  w-full
                  max-w-md
                  transform
                  flex-col
                  overflow-hidden
                  rounded-2xl
                  bg-white
                  p-6
                  text-left
                  align-middle
                  shadow-xl
                  transition-all
                ">
                <DialogTitle
                  as="h3"
                  class="text-lg font-medium leading-6 text-gray-900">
                  Manage message from
                  <strong>{{ props.message.creator.pseudo }}</strong>
                </DialogTitle>
                <div class="mt-2">
                  <p class="text-sm text-gray-500">
                    Your are about to
                    <strong>{{ selectedAction.display }}</strong>
                  </p>
                  <Select
                    :list="actions"
                    :selected-value="selectedAction"
                    @update:model-value="updateAction" />
                </div>
                <div class="my-4 flex flex-col gap-3 overflow-auto text-black">
                  <div class="flex items-center">
                    <FlagIcon class="mr-2 h-5 w-5 text-primary-300" />
                    Total reports :&nbsp;<strong>{{
                      props.message.reportsCount
                    }}</strong>
                  </div>
                  <div>
                    <div class="text-sm text-gray-500">Details</div>
                    <div class="flex gap-2 rounded border p-2 text-sm">
                      <div v-for="[a, b] in detailsGroupBy" :key="a">
                        {{ a }} : <strong>{{ b }}</strong>
                      </div>
                    </div>
                  </div>
                  <div class="flex">
                    <ChatBubbleBottomCenterIcon
                      class="mt-2 mr-2 h-5 w-5 min-w-[20px] text-primary-300" />
                    <div>
                      Content :&nbsp;
                      <div
                        :class="
                          selectedAction.value === 'mask' ? 'line-through' : ''
                        ">
                        {{ props.message.content }}
                      </div>
                    </div>
                  </div>
                </div>

                <div class="mt-auto flex justify-end gap-2">
                  <button
                    type="button"
                    class="
                      inline-flex
                      justify-center
                      rounded-md
                      border border-transparent
                      bg-slate-100
                      px-4
                      py-2
                      text-sm
                      font-medium
                      text-slate-900
                      hover:bg-slate-200
                      focus:outline-none
                      focus-visible:ring-2
                      focus-visible:ring-blue-500
                      focus-visible:ring-offset-2
                    "
                    @click="closeModal">
                    Cancel
                  </button>
                  <button
                    type="button"
                    :disabled="isLoadingReject || isLoadingMask"
                    class="
                      inline-flex
                      justify-center
                      gap-2
                      rounded-md
                      border border-transparent
                      bg-blue-100
                      px-4
                      py-2
                      text-sm
                      font-medium
                      text-blue-900
                      hover:bg-blue-200
                      focus:outline-none
                      focus-visible:ring-2
                      focus-visible:ring-blue-500
                      focus-visible:ring-offset-2
                    "
                    @click="confirm">
                    <span>Confirm</span>
                    <Spin :is-loading="isLoadingReject || isLoadingMask" />
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
import { ChatBubbleBottomCenterIcon, FlagIcon } from '@heroicons/vue/20/solid';
import { ref } from 'vue';
import { useMutation } from 'vue-query';
import { toast } from 'vue3-toastify';
import { maskMessage } from '../../services/service.messages';
import { rejectReports } from '../../services/service.reports';
import Spin from '../Loader/Spin.vue';
import Select from '../Select/Select.vue';

const props = defineProps({
  actionName: {
    type: String,
    default: 'Action',
  },
  message: {
    type: Object,
    required: true,
  },
});

const emit = defineEmits(['updateMessagesWithReportsList']);

const detailsGroupBy = Object.entries(
  props.message.reports.reduce((acc, report) => {
    if (acc[report.type]) {
      acc[report.type] += 1;
    } else {
      acc[report.type] = 1;
    }
    return acc;
  }, {})
);

const actions = [
  {
    display: 'Mask the message',
    value: 'mask',
  },
  {
    display: 'Reject reports',
    value: 'reject',
  },
];

const selectedAction = ref(actions[0]);

const updateAction = (action) => {
  selectedAction.value = action;
};

const isOpen = ref(false);

function closeModal() {
  isOpen.value = false;
}
function openModal() {
  isOpen.value = true;
}

const { isLoading: isLoadingReject, mutate: rejectReportsMutation } =
  useMutation((data) => rejectReports(data), {
    onSuccess: () => {
      toast.success('Reports have been rejected');
      emit('updateMessagesWithReportsList', props.message);
      closeModal();
    },
    onError: () => {
      toast.error('Something went wrongeeee');
    },
  });

const { isLoading: isLoadingMask, mutate: maskMessageMutation } = useMutation(
  (data) => maskMessage(data),
  {
    onSuccess: () => {
      toast.success('Message has been masked');
      emit('updateMessagesWithReportsList', props.message);
      closeModal();
    },
    onError: () => {
      toast.error('Something went wrong');
    },
  }
);

const confirm = () => {
  if (selectedAction.value.value === 'reject') {
    rejectReportsMutation({
      messageId: props.message.id,
    });
  } else {
    maskMessageMutation({
      messageId: props.message.id,
    });
  }
};
</script>
