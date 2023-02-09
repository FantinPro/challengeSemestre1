<template>
  <div
    :key="username"
    class="
      flex flex-col
      overflow-auto
      min-w-[300px]
      sm:min-w-[500px]
      md:min-w-[600px]
      h-full
    ">
    <ProfilHeader />
    <HeaderMenu :tabs="tabs">
      <template #panels>
        <TabPanels>
          <TabPanel>
            <div class="flex flex-col gap-2">
              <span v-if="isLoading">Loading...</span>
              <span v-else-if="isError">Error: {{ error.message }}</span>
              <div v-for="feed in data" :key="feed.id">
                <Card :item="feed" />
              </div>
            </div>
          </TabPanel>
          <TabPanel></TabPanel>
        </TabPanels>
      </template>
    </HeaderMenu>
  </div>
</template>
<script setup>
import { ref, watch } from 'vue-demi';
import { useQuery } from 'vue-query';
import { useRouter } from 'vue-router';
import { TabPanel, TabPanels } from '@headlessui/vue';
import { useUserStore } from '../store/user';
import { useFeedStore } from '../store/feed';
import HeaderMenu from '../components/Menu/HeaderMenu.vue';
import ProfilHeader from '../components/Profile/ProfileHeader.vue';
import Card from '../components/Card/Card.vue';

const { getUserProfileByUsername } = useUserStore();
const { fetchMessages } = useFeedStore();
const router = useRouter();
const tabs = ['Echoes', 'Likes'];

let username = ref(router.currentRoute.value.params.pseudo);

watch(
  () => router.currentRoute.value.params.pseudo,
  (newVal) => {
    if (newVal !== username.value && newVal !== undefined) {
      username.value = newVal;
    }
  }
);

const { isLoading, isError, data, error } = useQuery(
  ['profile', username],
  async () => {
    try {
      const res = await getUserProfileByUsername(username.value);
      if (!res || res.error) {
        router.push('/');
      }
      return fetchMessages(1, { creator: res.id });
    } catch (e) {
      console.log(e);
      router.push('/');
    }
  },
  {
    keepPreviousData: true,
  }
);
</script>
