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
                class="
                  inline-flex
                  justify-center
                  rounded-full
                  border border-transparent
                  bg-slate-200
                  px-4
                  py-1
                  text-sm
                  font-bold
                  text-slate-900
                  hover:bg-slate-200
                  focus:outline-none
                  focus-visible:ring-2
                  focus-visible:ring-blue-500
                  focus-visible:ring-offset-2
                  disabled:opacity-50
                "
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
              <span v-if="isLoading || isFetching">
                <EchoLoader />
              </span>
              <span v-else-if="isError">
                <div class="flex p-4 justify-center">
                  <span class="text-base font-medium">Please try again later.</span>
                </div>
              </span>
              <div v-for="message in feed" :key="message.id">
                <div v-if="message.isAd" class="border-b border-[#4c5157]">
                  <Ad :item="message" />
                </div>
                <Card
                  v-else
                  :item="message"
                  @delete-one-message-from-feed="deleteOneMessageFromFeed"
                  @upsert-message-from-feed="upsertMessageFromFeed" />
              </div>
              <div v-if="isFetching" class="mt-4">
                <EchoLoader :quantity="1" />
              </div>
            </div>
          </TabPanel>
          <TabPanel>
            <div class="flex p-4 justify-center bg-slate-600">
              <span class="text-lg font-bold">Available soon ⏳</span>
            </div>
          </TabPanel>
        </TabPanels>
      </template>
    </HeaderMenu>
  </div>
</template>
<script setup>
import { TabPanel, TabPanels } from '@headlessui/vue';
import { computed, onMounted, ref } from 'vue';
import { useMutation, useQuery } from 'vue-query';
import { toast } from 'vue3-toastify';
import Card from '../components/Card/Card.vue';
import EchoLoader from '../components/Loader/EchoLoader.vue';
import Ad from '../components/Card/Ad.vue';
import HeaderMenu from '../components/Menu/HeaderMenu.vue';
import { getRandomAd, createImpressionForAd } from '../services/service.ads';
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

const { isLoading, isFetching, isError } = useQuery({
  queryKey: ['feedv2', page],
  queryFn: () => Promise.all([fetchFeed(page.value), getRandomAd()]),
  keepPreviousData: false,
  refetchOnWindowFocus: false,
  onSuccess: async ([dataFeed, dataRandomAd]) => {
    if (dataFeed.length === 0) {
      return;
    }
    hasHit80.value = false;
    if (dataRandomAd) {
      dataFeed.splice(Math.floor(Math.random() * dataFeed.length), 0, {
        ...dataRandomAd,
        isAd: true,
      });
      await createImpression(dataRandomAd);
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
      toast.success('Echo published !');
      feed.value = [message, ...feed.value];
      newMessage.value = '';
    },
    onError: () => {
      toast.error('Something went wrong');
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

const createImpression = async (ad) => {
  const impression = {
    ad: `/api/pubs/${ad.id}`,
    fromUser: `/api/users/${userStore.user.id}`,
  };
  try { 
    await createImpressionForAd(impression);
    console.info('[AD_IMPRESSION] add impression');
  } catch (e) {
    console.info('[AD_IMPRESSION] already seen');
  }
}
</script>
