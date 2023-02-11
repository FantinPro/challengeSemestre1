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
              <div class="flex gap-2 pb-3">
                <div class="flex flex-col">
                  <img
                    class="h-12 w-12 min-w-[48px] rounded-full"
                    :src="props.message.creator.profilePicture"
                    alt="avatar" />
                  <div
                    class="mx-auto h-full border border-gray-300 text-black min-h-[20px]"></div>
                </div>

                <div class="overflow-auto text-black">
                  {{ props.message.content }}
                </div>
              </div>

              <div class="flex gap-2">
                <div class="flex flex-col">
                  <img
                    class="h-12 w-12 min-w-[48px] rounded-full"
                    :src="user.profilePicture"
                    alt="avatar" />
                </div>

                <div class="overflow-auto text-black p-1 w-full">
                  <FormKit
                    id="textareaNewMessage"
                    ref="textareaNewMessage"
                    v-model="replyMessage"
                    class="w-full"
                    name="Echo"
                    type="textarea"
                    aria-multiline="true"
                    placeholder="Reply to this message..."
                    validation="length:1,255" />
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
                  :disabled="isLoading"
                  class="inline-flex justify-center gap-2 rounded-md border border-transparent bg-blue-100 px-4 py-2 text-sm font-medium text-blue-900 hover:bg-blue-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2"
                  @click="replyToMessage">
                  <span>Reply</span>
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
  TransitionChild,
  TransitionRoot,
} from '@headlessui/vue';
import { ref } from 'vue';
import { useUserStore } from '../../store/user';
import Spin from '../Loader/Spin.vue';
import { toast } from 'vue3-toastify';
import { useFeedStore } from '../../store/feed';
import { useMutation } from 'vue-query';

const { user } = useUserStore();
const { postMessage } = useFeedStore();

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

const emit = defineEmits(['close', 'upsertMessageFromFeed']);

const replyMessage = ref('');

const { isLoading, mutate: postMessageMutation } = useMutation(
  (data) => postMessage(data),
  {
    onSuccess: (message) => {
      toast.success('Reply sent!');
      emit('upsertMessageFromFeed', {
        ...props.message,
        commentsCount: props.message.commentsCount + 1,
      });
      emit('upsertMessageFromFeed', message)
      replyMessage.value = '';
      closeModal();
    },
    onError: () => {
      toast.error('Something went wrongeeee');
    },
  }
);

function closeModal() {
  emit('close');
}

const replyToMessage = async () => {
  if (replyMessage.value.length === 0 || replyMessage.value.trim().length === 0) {
    return;
  }
  if (replyMessage.value.length > 255) {
    toast.error('Echo is too long');
    return;
  }
  postMessageMutation({
    content: replyMessage.value,
    creator: `/api/users/${user.id}`,
    parent: `/api/messages/${props.message.id}`,
  });
};

</script>
