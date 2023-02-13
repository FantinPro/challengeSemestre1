<template>
  <div
    ref="containerElement"
    :key="username"
    class="
      flex flex-col
      overflow-auto
      min-w-[300px]
      sm:min-w-[500px]
      md:min-w-[600px]
      h-full
    ">
    <div class="">
      <HeaderMenu :custom-title="true" :title="Echo" :sticky="true">
        <template #title>
          <div class="flex gap-2 cursor-pointer items-center">
            <button
              class="hover:bg-[#2f3336] font-semibold px-2 py-2 rounded-full"
              @click="router.back()">
              <svg
                class="h-6 w-6"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor">
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M10 19l-7-7m0 0l7-7m-7 7h18" />
              </svg>
            </button>
            <h1 class="text-2xl font-semibold">Echo</h1>
          </div>
        </template>
        <template #panels>
          <div class="flex flex-col gap-2 border-b border-[#3b4043]">
            <span v-if="isLoading">
              <EchoLoader />
            </span>
            <span v-else-if="isError">Error: {{ error.message }}</span>
            <div v-else-if="echo.creator" class="flex flex-col p-4 pb-2">
              <div v-if="echo.parent" class="flex mb-4">
                <div class="flex flex-col min-w-max">
                  <img
                    class="h-12 w-12 rounded-md"
                    :src="echo.parent.creator.profilePicture"
                    alt="avatar" />
                  <div
                    class="
                      mt-1
                      mr-auto
                      ml-[22px]
                      h-full
                      min-h-full
                      border border-slate-500
                    " />
                </div>
                <div class="ml-2 flex flex-col w-full">
                  <CardHeader :item="echo.parent" @delete-one-message-from-feed="onClickProfile(echo.parent.creator.pseudo)" />
                  <div v-if="echo.parent.isDeleted" class="text-white">
                    <span class="font-bold text-orange-400">[deleted]</span>
                    <span class="ml-2 text-gray-400">{{ echo.parent.content }}</span>
                  </div>
                  <span v-else class="text-base font-medium">
                    {{ echo.parent.content }}
                  </span>
                </div>
              </div>
              <div class="flex justify-between">
                <div class="flex gap-2 items-center">
                  <img
                    class="h-12 w-12 rounded-full"
                    :src="echo.creator.profilePicture"
                    alt="avatar" />
                  <router-link
                    class="flex flex-col"
                    :to="`/profile/${echo.creator.pseudo}`">
                    <span class="text-base font-semibold">{{
                      echo.creator.pseudo
                    }}</span>
                    <span class="text-sm text-gray-400"
                      >@{{ echo.creator.pseudo }}</span
                    >
                  </router-link>
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
                    "
                    @click.stop>
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
                          v-if="echo.creator.id === user.id"
                          v-slot="{ active }">
                          <button
                            :class="[
                              active
                                ? 'bg-primary-300 text-white'
                                : 'text-gray-900',
                              'group flex w-full items-center rounded-md px-2 py-2 text-sm',
                            ]"
                            :disabled="isLoadingMask"
                            @click.stop="deleteMessageMutation">
                            <TrashIcon
                              :active="active"
                              class="mr-2 h-5 w-5"
                              :class="[
                                active ? 'text-white-400' : 'text-primary-300',
                              ]"
                              aria-hidden="true" />
                            Delete
                          </button>
                        </MenuItem>
                        <MenuItem
                          v-if="echo.creator.id !== user.id"
                          v-slot="{ active }">
                          <button
                            :class="[
                              active
                                ? 'bg-primary-300 text-white'
                                : 'text-gray-900',
                              'group flex w-full items-center rounded-md px-2 py-2 text-sm',
                            ]"
                            @click.stop="openReportDialog">
                            <FlagIcon
                              :active="active"
                              class="mr-2 h-5 w-5"
                              :class="[
                                active ? 'text-white-400' : 'text-primary-300',
                              ]"
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
                  :message="echo"
                  @close="closeReportDialog" />
              </div>
              <div class="flex mt-4 border-b border-[#3b4043]">
                <div class="flex flex-col mb-3">
                  <div
                    v-if="echo.parent"
                    class="flex text-base font-medium text-gray-400 mb-1">
                    Replying to
                    <router-link class="ml-1 text-primary-400 hover:underline cursor-pointer" :to="`/profile/${echo.parent.creator.pseudo}`">
                      @{{ echo.parent.creator.pseudo }}
                    </router-link>
                  </div>
                  <div v-if="echo.isDeleted" class="text-white">
                    <span class="font-bold text-orange-400">[deleted]</span>
                    <span class="ml-2 text-gray-400">{{ echo.content }}</span>
                  </div>
                  <div v-else class="text-base font-medium">
                    {{ echo.content }}
                  </div>
                  <span class="text-base text-gray-400 mt-2">{{
                    createdAt(echo.created)
                  }}</span>
                </div>
              </div>
              <div class="flex mt-4 border-b border-[#3b4043]">
                <div class="flex gap-2 mb-3">
                  <span class="flex text-base font-medium">
                    {{ echo.commentsCount }}
                    <span class="ml-1 text-gray-400">
                      {{ echo.commentsCount > 1 ? 'Comments' : 'Comment' }}
                    </span>
                  </span>
                  <span class="flex text-base font-medium">
                    {{ echo.sharesCount }}
                    <span class="ml-1 text-gray-400">
                      {{ echo.sharesCount > 1 ? 'Shares' : 'Share' }}
                    </span>
                  </span>
                </div>
              </div>
              <div class="flex">
                <CardFooter :item="echo" @upsert-message-from-feed="refresh" />
              </div>
            </div>
          </div>
          <div class="flex flex-col gap-2">
            <div v-for="comment in echo.comments" :key="comment.id">
              <Card
                :item="{
                  ...comment,
                  parent: echo.parent,
                }"
                @delete-one-message-from-feed="refresh" />
            </div>
          </div>
        </template>
      </HeaderMenu>
    </div>
  </div>
</template>
<script setup>
import { onMounted, ref, watch } from 'vue-demi';
import { useMutation, useQuery, useQueryClient } from 'vue-query';
import { useRouter } from 'vue-router';
import { fetchMessage } from '../services/service.messages';
import HeaderMenu from '../components/Menu/HeaderMenu.vue';
import Card from '../components/Card/Card.vue';
import CardHeader from '../components/Card/CardHeader.vue';
import CardFooter from '../components/Card/CardFooter.vue';
import { Menu, MenuButton, MenuItem, MenuItems } from '@headlessui/vue';
import { FlagIcon } from '@heroicons/vue/20/solid';
import {
  EllipsisHorizontalIcon,
  TrashIcon,
} from '@heroicons/vue/24/solid';
import DialogReport from '../components/Dialog/DialogReport.vue';
import EchoLoader from '../components/Loader/EchoLoader.vue';

import { toast } from 'vue3-toastify';
import { useFeedStore } from '../store/feed';
import { useUserStore } from '../store/user';

const router = useRouter();
const { deleteMessage } = useFeedStore();
const containerElement = ref();
const { user } = useUserStore();
const queryClient = useQueryClient();

const hasHit80 = ref(false);
const page = ref(1);

const onScroll = () => {
  const { scrollTop, scrollHeight, clientHeight } = containerElement.value;
  const scrollableHeight = scrollHeight - clientHeight;
  const scrollPercentage = scrollTop / scrollableHeight;
  if (scrollPercentage >= 0.8 && !hasHit80.value) {
    hasHit80.value = true;
    page.value += 1;
  }
};

onMounted(() => {
  containerElement.value.addEventListener('scroll', onScroll);
});

const echoId = ref(router.currentRoute.value.params.id);
const echo = ref({});
const username = ref(router.currentRoute.value.params.pseudo);
const refresh = () => {
  queryClient.invalidateQueries(['echo']);
};

watch(
  () => router.currentRoute.value.params.pseudo,
  (newVal) => {
    if (newVal !== username.value && newVal !== undefined) {
      username.value = newVal;
    }
  }
);

watch(
  () => router.currentRoute.value.params.id,
  (newVal) => {
    if (newVal !== echoId.value && newVal !== undefined) {
      echoId.value = newVal;
    }
  }
);

const { isLoading, isError } = useQuery({
  queryKey: ['echo', username, echoId],
  queryFn: async () => {
    const echo = await fetchMessage(echoId.value);
    // if username is not the same as the creator of the echo redirect to 404
    if (username.value !== echo.creator.pseudo) {
      router.push('/404');
    }
    return echo;
  },
  onSuccess: (data) => {
    echo.value = data;
  },
  onError: (error) => {
    if (String(error).match(/Not Found/)) {
      router.push('/404');
    }
  },
  keepPreviousData: true,
  refetchOnWindowFocus: false,
  retry: 0,
});

const createdAt = (date) => {
  const dateObj = new Date(date);
  const month = dateObj.toLocaleString('default', { month: 'short' });
  const day = dateObj.getDate();
  const year = dateObj.getFullYear();
  const time = dateObj.toLocaleString('fr-FR', {
    hour: 'numeric',
    minute: 'numeric',
    hour12: true,
  });
  return `${time} · ${month} ${day}, ${year}`;
};

const isOpenReportDialog = ref(false);

const openReportDialog = () => {
  isOpenReportDialog.value = true;
};

const closeReportDialog = () => {
  isOpenReportDialog.value = false;
};

const { isLoading: isLoadingMask, mutate: deleteMessageMutation } = useMutation(
  () => deleteMessage(echo.value.id),
  {
    onSuccess: () => {
      toast.success('Your echo has been deleted');
      router.push('/profile/' + username.value);
    },
    onError: () => {
      toast.error('Something went wrong');
    },
  }
);

const onClickProfile = (pseudo) => {
  router.push('/profile/' + pseudo ? pseudo : echo.value.creator.pseudo);
};
</script>