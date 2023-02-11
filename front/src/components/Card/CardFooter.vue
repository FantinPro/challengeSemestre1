<template>
  <div class="mt-2 ml-[-10px] flex">
    <button
      v-if="!props.item.parent"
      class="flex cursor-pointer items-center rounded-full px-2 py-1 hover:bg-[#4c5157]"
      @click="openReplyMessageDialog">
      <svg
        class="h-6 w-6"
        fill="rgb(156, 163, 175)"
        stroke="currentColor"
        viewBox="0 0 700 600">
        <g>
          <path
            d="m137.2 67.199c-21.426 0-39.199 17.773-39.199 39.199v291.2c0 21.426 17.773 39.199 39.199 39.199h134.23l68.25 52.5c2.9492 2.2969 6.582 3.5469 10.324 3.5469s7.375-1.25 10.324-3.5469l72.801-56c3.5273-2.7383 5.8242-6.7656 6.3828-11.199 0.55859-4.4297-0.66797-8.8984-3.4062-12.43-2.7383-3.5273-6.7656-5.8203-11.195-6.3789-4.4336-0.55859-8.9023 0.66797-12.43 3.4062l-62.477 48.125-62.477-48.125c-2.957-2.2812-6.5898-3.5117-10.324-3.5h-140c-3.3906 0-5.5977-2.207-5.5977-5.5977v-291.2c0-3.3906 2.207-5.5977 5.5977-5.5977h425.6c3.3906 0 5.5977 2.207 5.5977 5.5977v291.2c0 3.3906-2.207 5.5977-5.5977 5.5977h-39.199c-4.5-0.0625-8.832 1.6797-12.035 4.8359s-5.0039 7.4688-5.0039 11.965 1.8008 8.8086 5.0039 11.965 7.5352 4.8984 12.035 4.8359h39.199c21.426 0 39.199-17.773 39.199-39.199v-291.2c0-21.426-17.773-39.199-39.199-39.199zm336 336c-9.2773 0-16.801 7.5234-16.801 16.801s7.5234 16.801 16.801 16.801c9.2773 0 16.801-7.5234 16.801-16.801s-7.5234-16.801-16.801-16.801z" />
        </g>
      </svg>
      <span class="ml-1 text-gray-400">{{ props.item.commentsCount }}</span>
    </button>
    <Menu as="div" class="relative">
      <MenuButton
        class="flex items-center justify-center rounded-full bg-opacity-20 p-2 hover:bg-[#4c5157] focus:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-opacity-95">
        <ArrowPathRoundedSquareIcon
          class="mr-2 h-5 w-5"
          :class="[props.item.shared ? 'text-green-400' : 'text-[#9ca3af]']" />
        <span class="ml-1 text-gray-400">{{ props.item.sharesCount }}</span>
      </MenuButton>

      <transition
        enter-active-class="transition duration-100 ease-out"
        enter-from-class="transform scale-95 opacity-0"
        enter-to-class="transform scale-100 opacity-100"
        leave-active-class="transition duration-75 ease-in"
        leave-from-class="transform scale-100 opacity-100"
        leave-to-class="transform scale-95 opacity-0">
        <MenuItems
          class="absolute mt-2 w-56 origin-top-right divide-y divide-gray-100 rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none">
          <div class="px-1 py-1">
            <MenuItem v-slot="{ active }">
              <button
                :class="[
                  active ? 'bg-primary-300 text-white' : 'text-gray-900',
                  'group flex w-full items-center rounded-md px-2 py-2 text-sm',
                ]"
                @click="onClickShare">
                <ArrowPathRoundedSquareIcon
                  :active="active"
                  class="mr-2 h-5 w-5"
                  :class="[active ? 'text-white-400' : 'text-primary-300']"
                  aria-hidden="true" />
                {{ props.item.shared ? 'Cancel Share' : 'Share it' }}
              </button>
            </MenuItem>
          </div>
        </MenuItems>
      </transition>
    </Menu>

    <DialogReplyMessage
      :message="props.item"
      :is-open="isOpenReplyMessageDialog"
      @close="closeReplyMessageDialog"
      @upsert-message-from-feed="upsertMessageFromFeed" />
  </div>
</template>
<script setup>
import { Menu, MenuButton, MenuItem, MenuItems } from '@headlessui/vue';
import { ArrowPathRoundedSquareIcon } from '@heroicons/vue/20/solid';
import { createShare, deleteShare } from '../../services/service.shares';
import { useMutation } from 'vue-query';
import { toast } from 'vue3-toastify';
import { useUserStore } from '../../store/user';
import DialogReplyMessage from '../Dialog/DialogReplyMessage.vue';
import { ref } from 'vue';

const { user } = useUserStore();

const props = defineProps({
  item: {
    type: Object,
    required: true,
  },
});

const emit = defineEmits(['upsertMessageFromFeed']);

const upsertMessageFromFeed = (message) => {
  emit('upsertMessageFromFeed', message);
};

const { mutate: createShareMutation } = useMutation(
  (data) => createShare(data),
  {
    onSuccess: () => {
      toast.success('Echo has been shared');
      upsertMessageFromFeed({
        ...props.item,
        sharesCount: props.item.sharesCount + 1,
        shared: true,
      });
    },
    onError: () => {
      toast.error('Something went wrong');
    },
  }
);

const { mutate: deleteShareMutation } = useMutation(
  (message) => deleteShare(message),
  {
    onSuccess: () => {
      toast.success('Shared canceled!');
      emit('upsertMessageFromFeed', {
        ...props.item,
        sharesCount: props.item.sharesCount - 1,
        shared: false,
      });
    },
    onError: () => {
      toast.error('Something went wrong');
    },
  }
);

const onClickShare = () => {
  if (!props.item.shared) {
    createShareMutation({
      message: props.item,
      user,
    });
  } else {
    deleteShareMutation(props.item);
  }
};

const isOpenReplyMessageDialog = ref(false);

const closeReplyMessageDialog = () => {
  isOpenReplyMessageDialog.value = false;
};

const openReplyMessageDialog = () => {
  isOpenReplyMessageDialog.value = true;
};
</script>
