<template>
  <div class="flex justify-between">
    <div class="flex flex-col">
      <div class="flex gap-1 items-center">
        <router-link
          :to="`/profile/${props.item.creator.pseudo}`"
          class="font-bold text-gray-200">
          {{ props.item.creator.pseudo }}
        </router-link>
        <CheckBadgeIcon v-if="props.item.creator.isVerified" class="h-4 w-4 text-green-500" />
        <router-link
          :to="`/profile/${props.item.creator.pseudo}`"
          class="text-sm text-gray-400">
          @{{ props.item.creator.pseudo }}
        </router-link>
        <span class="text-sm text-gray-400"> · </span>
        <span class="text-sm text-gray-400">{{ createdAt }}</span>
      </div>
    </div>
    <Menu as="div" class="relative text-left">
      <MenuButton
        class="
          absolute
          -top-2
          right-0
          justify-center
          rounded-full
          bg-opacity-20
          p-2
          hover:bg-[#4c5157]
          focus:outline-none
          focus-visible:ring-2
          focus-visible:ring-white
          focus-visible:ring-opacity-95
        ">
        <EllipsisHorizontalIcon
          class="h-5 w-5 text-violet-200 hover:text-violet-100"
          aria-hidden="true" />
      </MenuButton>

      <transition
        enter-active-class="transition duration-100 ease-out"
        enter-from-class="transform scale-95 opacity-0"
        enter-to-class="transform scale-100 opacity-100"
        leave-active-class="transition duration-75 ease-in"
        leave-from-class="transform scale-100 opacity-100"
        leave-to-class="transform scale-95 opacity-0">
        <MenuItems
          class="
            absolute
            right-0
            mt-2
            w-56
            origin-top-right
            divide-y divide-gray-100
            rounded-md
            bg-white
            shadow-lg
            ring-1 ring-black ring-opacity-5
            focus:outline-none
          ">
          <div class="px-1 py-1">
            <MenuItem
              v-if="props.item.creator.id === user.id"
              v-slot="{ active }">
              <button
                :class="[
                  active ? 'bg-primary-300 text-white' : 'text-gray-900',
                  'group flex w-full items-center rounded-md px-2 py-2 text-sm',
                ]"
                :disabled="isLoadingMask"
                @click="deleteMessageMutation">
                <TrashIcon
                  :active="active"
                  class="mr-2 h-5 w-5"
                  :class="[active ? 'text-white-400' : 'text-primary-300']"
                  aria-hidden="true" />
                Delete
              </button>
            </MenuItem>
            <MenuItem
              v-if="props.item.creator.id !== user.id"
              v-slot="{ active }">
              <button
                :class="[
                  active ? 'bg-primary-300 text-white' : 'text-gray-900',
                  'group flex w-full items-center rounded-md px-2 py-2 text-sm',
                ]"
                @click="openReportDialog">
                <FlagIcon
                  :active="active"
                  class="mr-2 h-5 w-5"
                  :class="[active ? 'text-white-400' : 'text-primary-300']"
                  aria-hidden="true" />
                Report
              </button>
            </MenuItem>
          </div>
        </MenuItems>
      </transition>
    </Menu>
    <DialogReport
      :is-open="isOpenReportDialog"
      :message="props.item"
      @close="closeReportDialog" />
  </div>
</template>
<script setup>
import { Menu, MenuButton, MenuItem, MenuItems } from '@headlessui/vue';
import { FlagIcon } from '@heroicons/vue/20/solid';
import { EllipsisHorizontalIcon, TrashIcon, CheckBadgeIcon } from '@heroicons/vue/24/solid';
import { formatDistance } from 'date-fns';
import { ref } from 'vue';
import { useMutation } from 'vue-query';
import { toast } from 'vue3-toastify';
import DialogReport from '../../components/Dialog/DialogReport.vue';
import { useFeedStore } from '../../store/feed';
import { useUserStore } from '../../store/user';

const { user } = useUserStore();
const { deleteMessage } = useFeedStore();

const props = defineProps({
  item: {
    type: Object,
    required: true,
  },
});

const emit = defineEmits(['deleteOneMessageFromFeed']);

const createdAt = formatDistance(new Date(props.item.created), new Date(), {
  addSuffix: true,
});

const isOpenReportDialog = ref(false);

const openReportDialog = () => {
  isOpenReportDialog.value = true;
};

const closeReportDialog = () => {
  isOpenReportDialog.value = false;
};

const { isLoading: isLoadingMask, mutate: deleteMessageMutation } = useMutation(
  () => deleteMessage(props.item.id),
  {
    onSuccess: () => {
      toast.success('Your echo has been deleted');
      emit('deleteOneMessageFromFeed', props.item);
    },
    onError: () => {
      toast.error('Something went wrong');
    },
  }
);
</script>
