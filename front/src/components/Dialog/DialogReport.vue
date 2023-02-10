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
              class="flex h-96 w-full max-w-md transform flex-col overflow-hidden rounded-2xl bg-white p-6 text-left align-middle shadow-xl transition-all">
              <DialogTitle
                as="h3"
                class="text-lg font-medium leading-6 text-gray-900">
                Report Message from <strong>{{}}</strong>
              </DialogTitle>

              <Select :list="reportTypes" :selected-value="selectedReport" @update:model-value="updateReport" />

              <div class="my-4 overflow-auto text-black">
                {{ props.message.content }}
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
                  :disabled="isLoading"
                  class="inline-flex justify-center gap-2 rounded-md border border-transparent bg-blue-100 px-4 py-2 text-sm font-medium text-blue-900 hover:bg-blue-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2"
                  @click="confirm">
                  <span>Confirm</span>
                  <Spin :is-loading="isLoading" />
                </button>
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
  TransitionRoot
} from '@headlessui/vue';
import { ref } from 'vue';
import { useMutation } from 'vue-query';
import { toast } from 'vue3-toastify';
import { createReport } from '../../services/service.reports';
import { useUserStore } from '../../store/user';
import { REPORT_TYPES } from '../../utils/constants';
import Spin from '../Loader/Spin.vue';
import Select from '../Select/Select.vue';

const { user } = useUserStore();

const props = defineProps({
  isOpen: {
    type: Boolean,
    default: false,
  },
  message: {
    type: Object,
    required: true,
  },
});

const emit = defineEmits(['close']);

const reportTypes = Object.values(REPORT_TYPES).map((type) => ({
  value: type,
  display: type,
}));
const selectedReport = ref(reportTypes[0]);

const updateReport = (report) => {
  selectedReport.value = report;
};

function closeModal() {
  emit('close');
}

const { isLoading, mutate: createReportMutation } = useMutation(
  (data) => createReport(data),
  {
    onSuccess: () => {
      toast.success('Message has been reported !');
      closeModal();
    },
    onError: (err) => {
      if (err.message.match(/Access Denied/)) {
        toast.error('You cannot report your own message !');
      } else if (err.message.match(/You already reported it/)) {
        toast.error('You already reported this message !');
      } else {
        toast.error('Something went wrong !');
      }
    },
  }
);

const confirm = () => {
  createReportMutation({
    messageId: props.message.id,
    userId: user.id,
    type: selectedReport.value.value,
  });
};
</script>
