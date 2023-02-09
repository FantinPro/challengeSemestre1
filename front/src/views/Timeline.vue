<template>
  <div class="flex flex-col overflow-auto h-full">
    <HeaderMenu :tabs="tabs" title="Home">
      <template #panels>
        <TabPanels>
          <TabPanel>
            <div class="flex border-b border-[#4c5157] gap-6 px-6 pt-6 pb-3">
              <img
                v-if="user?.profilePicture"
                :src="user?.profilePicture"
                class="w-8 h-8 rounded-full"
                alt="User avatar"
              />
              <div class="flex flex-col w-full">
                <FormKit
                  ref="textareaNewMessage"
                  v-model="newMessage"
                  :classes="{
                    wrapper: '!max-w-full',
                    input: '!text-white !h-auto !text-xl !min-h-[19px] !p-0 !resize-none',
                    inner: '!shadow-none'
                  }"
                  type="textarea"
                  placeholder="Quoi de neuf ?"
                  @keydown.enter="sendMessage()"
                />
              </div>
            </div>
            <div class="flex flex-col gap-2">
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
import { onMounted, ref } from "vue";
import { useQuery } from "vue-query";
import Card from "../components/Card/Card.vue";
import HeaderMenu from "../components/Menu/HeaderMenu.vue";
import LayoutDefault from "../layouts/LayoutDefault.vue";

import { useFeedStore } from "../store/feed";
import { useUserStore } from "../store/user";


const { user } = useUserStore()
const newMessage = ref('');

const tabs = ['For you', 'Following']
const { fetchFeed, postMessage } = useFeedStore();
const emit = defineEmits(["update:layout"]);

onMounted(() => {
  emit("update:layout", LayoutDefault);
});

const sendMessage = async () => {
  try {
    await postMessage({ content: newMessage.value, creator: `/api/users/${user.id}` });
    newMessage.value = '';
    refetch();
  } catch (e) {
    console.log(e)
  }
}

const { isLoading, isError, refetch, data, error } = useQuery("feed", () =>
  fetchFeed(1),
);
</script>
