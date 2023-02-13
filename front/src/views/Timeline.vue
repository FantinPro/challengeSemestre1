<template>
  <div ref="containerElement" class="flex h-full flex-col overflow-auto">
    <HeaderMenu :tabs="tabs" title="Home" :sticky="true">
      <template #panels>
        <TabPanels>
          <TabPanel>
            <div class="flex gap-2 px-2 pt-2">
              <img
                v-if="userStore.user?.profilePicture"
                :src="userStore.user?.profilePicture"
                class="h-12 w-12 rounded-full"
                alt="User avatar" />
              <div class="flex w-full flex-col">
                <FormKit
                  id="textareaNewMessage"
                  ref="textareaNewMessage"
                  v-model="newMessage"
                  name="Echo"
                  :classes="{
                    wrapper: '!max-w-full',
                    input: `!text-white !h-auto !text-lg !min-h-[40px] !p-0 !resize-none ${messageHeight}`,
                    inner: '!shadow-none',
                  }"
                  type="textarea"
                  aria-multiline="true"
                  placeholder="What's happening?"
                  validation="length:1,255" />
              </div>
            </div>
            <div class="flex justify-end border-b border-[#4c5157] pb-2 pr-2">
              <button
                type="button"
                class="inline-flex justify-center rounded-full border border-transparent bg-slate-200 px-4 py-2 text-sm font-medium text-slate-900 hover:bg-slate-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2 disabled:opacity-50"
                :disabled="
                  isLoading ||
                  newMessage.length === 0 ||
                  newMessage.trim().length === 0
                "
                @click="sendMessage">
                Echo
              </button>
            </div>
            <div class="flex flex-col">
              <span v-if="isLoading">Loading...</span>
              <span v-else-if="isError">Error occured</span>
              <div v-for="message in feed" :key="message.id">
                <div v-if="message.isAd" class="border-b border-[#4c5157]">
                  <div class="flex flex-col gap-2 p-2">
                    <div class="flex">
                      <img
                        class="h-12 w-12 rounded-full"
                        :src="message.owner.profilePicture"
                        alt="avatar" />
                      <div class="ml-2 flex-1 flex flex-col">
                        <div class="flex gap-1 items-center">
                          <router-link
                            :to="`/profile/${message.owner.pseudo}`"
                            class="font-bold text-gray-200">
                            {{ message.owner.pseudo }}
                          </router-link>
                          <CheckBadgeIcon class="h-4 w-4 text-green-500" />
                          <router-link
                            :to="`/profile/${message.owner.pseudo}`"
                            class="text-sm text-gray-400">
                            @{{message.owner.pseudo }}
                          </router-link>
                        </div>
                        <div>
                            {{message.message }}
                        </div>
                      </div>
                    </div>
                    <span
                      class="ease flex w-max cursor-pointer items-center rounded-lg bg-gray-200 p-1 text-[10px] font-semibold text-gray-500 opacity-60 transition duration-300 active:bg-gray-300">
                      Sponsored
                      <ArrowUpRightIcon class="ml-1 h-4 w-4" />
                    </span>
                  </div>
                </div>
                <Card
                  v-else
                  :item="message"
                  @delete-one-message-from-feed="deleteOneMessageFromFeed"
                  @upsert-message-from-feed="upsertMessageFromFeed" />
              </div>
            </div>
          </TabPanel>
          <TabPanel>Available soon</TabPanel>
        </TabPanels>
      </template>
    </HeaderMenu>
  </div>
</template>
<script setup>
import { TabPanel, TabPanels } from '@headlessui/vue';
import { ArrowUpRightIcon, CheckBadgeIcon } from '@heroicons/vue/20/solid';
import { computed, onMounted, ref } from 'vue';
import { useMutation, useQuery } from 'vue-query';
import { toast } from 'vue3-toastify';
import Card from '../components/Card/Card.vue';
import HeaderMenu from '../components/Menu/HeaderMenu.vue';
import { getRandomAd } from '../services/service.ads';
import { fetchFeed } from '../services/service.messages';

import { useFeedStore } from '../store/feed';
import { useUserStore } from '../store/user';

const userStore = useUserStore();

const containerElement = ref();

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

userStore.$subscribe((mutation) => {
  if(mutation.events.key === "refetchFeed") {
    feed.value = [];
    page.value = 1;
    hasHit80.value = false;
  }
});

const feed = ref([]);

const { isLoading, isError } = useQuery({
  queryKey: ['feedv2', page],
  queryFn: () => Promise.all([fetchFeed(page.value), getRandomAd()]),
  keepPreviousData: true,
  refetchOnWindowFocus: false,
  onSuccess: ([dataFeed, dataRandomAd]) => {
    if (dataFeed.length === 0) {
      return;
    }
    hasHit80.value = false;
    if (dataRandomAd) {
      dataFeed.splice(Math.floor(Math.random() * dataFeed.length), 0, {
        ...dataRandomAd,
        isAd: true,
      });
    }
    const tab = [...feed.value, ...dataFeed];
    feed.value = tab;
  },
});


const newMessage = ref('');

const tabs = ['For you', 'Following'];
const { postMessage } = useFeedStore();

const { mutate: postMessageMutation } = useMutation(
  (data) => postMessage(data),
  {
    onSuccess: (message) => {
      toast.success('Echo created!');
      feed.value = [message, ...feed.value];
      newMessage.value = '';
    },
    onError: () => {
      toast.error('Something went wrongeeee');
    },
  }
);

const sendMessage = async () => {
  if (newMessage.value.length === 0 || newMessage.value.trim().length === 0) {
    return;
  }
  if (newMessage.value.length > 255) {
    toast.error('Echo is too long');
    return;
  }
  postMessageMutation({
    content: newMessage.value,
    creator: `/api/users/${userStore.user.id}`,
  });
};

const deleteOneMessageFromFeed = (message) => {
  feed.value = feed.value.filter((m) => m.id !== message.id);
};

const upsertMessageFromFeed = (message) => {
  const idx = feed.value.findIndex((m) => m.id === message.id);
  if (idx > -1) {
    feed.value[idx] = message;
  } else {
    feed.value = [message, ...feed.value];
  }
};

const messageHeight = computed(() => {
  if (newMessage.value.length > 0) {
    return '!h-36';
  }
  return '!h-12';
});
</script>
