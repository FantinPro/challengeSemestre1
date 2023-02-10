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
    <HeaderMenu
      :tabs="tabs"
      :sticky="true"
      :selected-tab="selectedTab"
      :custom-title="true"
      @tab-change="handleTabChange">
      <template #title>
        <div class="flex gap-2 cursor-pointer">
          <button
            class="hover:bg-[#2f3336] font-semibold px-3 py-2 rounded-full"
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
          <div class="flex flex-col">
            <h1 class="text-lg font-semibold">{{ profile?.pseudo }}</h1>
            <p class="text-sm text-gray-500">@{{ profile?.pseudo }}</p>
          </div>
        </div>
      </template>
      <template #panels>
        <TabPanels>
          <TabPanel>
            <div class="flex flex-col gap-2 border-t border-[#3b4043]">
              <span v-if="isLoading">Loading...</span>
              <span v-else-if="isError">Error: {{ error.message }}</span>
              <div v-for="user in data" :key="user.id">
                <UserCard :item="user" />
              </div>
            </div>
          </TabPanel>
          <TabPanel>
            <div class="flex flex-col gap-2 border-t border-[#3b4043]">
              <span v-if="isLoading">Loading...</span>
              <span v-else-if="isError">Error: {{ error.message }}</span>
              <div v-for="user in data" :key="user.id">
                <UserCard :item="user" @update-followers-list="updateFollowersList" />
              </div>
            </div>
          </TabPanel>
        </TabPanels>
      </template>
    </HeaderMenu>
  </div>
</template>
<script setup>
import { computed, onMounted, ref, watch } from 'vue-demi';
import { useQuery, useQueryClient } from 'vue-query';
import { useRouter } from 'vue-router';
import { TabPanel, TabPanels } from '@headlessui/vue';
import { useUserStore } from '../store/user';
import HeaderMenu from '../components/Menu/HeaderMenu.vue';
import UserCard from '../components/UserCard/UserCard.vue';

const {
  getFollowersPaginated,
  getFollowingsPaginated,
  getUserProfileByUsername,
} = useUserStore();
const router = useRouter();
const queryClient = useQueryClient();

const tabs = ['Followers', 'Following'];
const authorizedTabs = ['followers', 'following'];

let username = ref(router.currentRoute.value.params.pseudo);
let currentTab = ref(router.currentRoute.value.params.tab);
let selectedTab = ref(
  currentTab.value === 'followers'
    ? 0
    : currentTab.value === 'following'
    ? 1
    : 0
);

const profile = computed(() => useUserStore().profile);

const updateFollowersList = (data) => {
  console.log('updateFollowersList', data);
  queryClient.invalidateQueries(['followers']);
};

onMounted(async () => {
  if (
    !currentTab.value ||
    (currentTab.value && !authorizedTabs.includes(String(currentTab.value)))
  ) {
    router.push(`/profile/${username.value}`);
  }
});

watch(
  () => router.currentRoute.value.params.pseudo,
  (newVal) => {
    if (newVal !== username.value && newVal !== undefined) {
      username.value = newVal;
    }
  }
);

watch(
  () => router.currentRoute.value.params.tab,
  (newVal) => {
    if (newVal !== currentTab.value && newVal !== undefined) {
      currentTab.value = newVal;
      selectedTab.value =
        currentTab.value === 'followers'
          ? 0
          : currentTab.value === 'following'
          ? 1
          : 0;
    }
  }
);

const handleTabChange = (tab) => {
  if (tab === 0) {
    router.push(`/profile/${username.value}/followers`);
  } else if (tab === 1) {
    router.push(`/profile/${username.value}/following`);
  }
};

const { isLoading, isError, data, error } = useQuery(
  ['followers', username, currentTab],
  async () => {
    try {
      let res = null;
      const profile = await getUserProfileByUsername(username.value);
      if (currentTab.value === 'followers') {
        res = await getFollowersPaginated(1, profile.id);
      } else if (currentTab.value === 'following') {
        res = await getFollowingsPaginated(1, profile.id);
      }
      if (!res || res.error) {
        router.push('/');
      }
      return res;
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
