<template>
  <div class="flex flex-col overflow-auto h-full">
    <HeaderMenu :tabs="tabs" title="Home" :sticky="true">
      <template #panels>
        <TabPanels>
          <TabPanel>
            <div class="flex gap-2 pt-2 px-2">
              <img
                v-if="user?.profilePicture"
                :src="user?.profilePicture"
                class="w-12 h-12 rounded-full"
                alt="User avatar" />
              <div class="flex flex-col w-full">
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
            <div class="flex justify-end pb-2 pr-2 border-b border-[#4c5157]">
              <button
                type="button"
                class="
                  inline-flex
                  justify-center
                  rounded-full
                  border border-transparent
                  bg-slate-200
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
            <div class="flex flex-col gap-2 mt-2">
              <span v-if="isLoading">Loading...</span>
              <span v-else-if="isError">Error: {{ error.message }}</span>
              <div v-for="feed in data" :key="feed.id">
                <Card :item="feed" />
              </div>
            </div>
          </TabPanel>
          <TabPanel>Content 2</TabPanel>
        </TabPanels>
      </template>
    </HeaderMenu>
  </div>
</template>
<script setup>
import { TabPanel, TabPanels } from '@headlessui/vue';
import { computed, ref, watch } from 'vue';
import { useQuery } from 'vue-query';
import { toast } from 'vue3-toastify';
import Card from '../components/Card/Card.vue';
import HeaderMenu from '../components/Menu/HeaderMenu.vue';

import { useFeedStore } from '../store/feed';
import { useUserStore } from '../store/user';

const { user } = useUserStore();
const newMessage = ref('');

const tabs = ['For you', 'Following'];
const { fetchFeed, postMessage, refetchFeed, setRefetchFeed } = useFeedStore();

let refetch = ref(refetchFeed);

watch(
  () => useFeedStore().refetchFeed,
  (newVal) => {
    if (newVal !== refetch.value) {
      setRefetchFeed(false);
      refetch.value = newVal;
    }
  }
);

const sendMessage = async () => {
  try {
    if (newMessage.value.length === 0 || newMessage.value.trim().length === 0) {
      return;
    }
    if (newMessage.value.length > 255) {
      toast.error('Echo is too long');
      return;
    }
    const sent = await postMessage({
      content: newMessage.value,
      creator: `/api/users/${user.id}`,
    });
    await setRefetchFeed(true);
    if (!sent) {
      toast.error('Error while sending echo');
      return;
    }
    toast.success('Echo created!');
    newMessage.value = '';
  } catch (e) {
    console.log(e);
  }
};

const { isLoading, isError, data, error } = useQuery(
  ['feed', refetch],
  async () => {
    await setRefetchFeed(false);
    return fetchFeed(1);
  },
  {
    keepPreviousData: true,
  }
);

const messageHeight = computed(() => {
  if (newMessage.value.length > 0) {
    return '!h-36';
  }
  return '!h-12';
});
</script>
