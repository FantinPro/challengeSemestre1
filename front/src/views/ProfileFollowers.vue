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
          <router-link
            class="flex flex-col"
            :to="`/profile/${profile?.pseudo}`">
            <h1 class="text-lg font-semibold">{{ profile?.pseudo }}</h1>
            <p class="text-sm text-gray-500">@{{ profile?.pseudo }}</p>
          </router-link>
        </div>
      </template>
      <template #panels>
        <TabPanels>
          <TabPanel>
            <div class="flex flex-col gap-2 border-t border-[#3b4043]">
              <span v-if="isLoading">Loading...</span>
              <span v-else-if="isError">Error: {{ error.message }}</span>
              <div v-for="user in followers" :key="user.id">
                <UserCard
                  :user="user"
                  @update-followers-list="updateFollowersList" />
              </div>
            </div>
          </TabPanel>
          <TabPanel>
            <div class="flex flex-col gap-2 border-t border-[#3b4043]">
              <span v-if="isLoading">Loading...</span>
              <span v-else-if="isError">Error: {{ error.message }}</span>
              <div v-for="user in following" :key="user.id">
                <UserCard
                  :user="user"
                  @update-followers-list="updateFollowersList" />
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

const updateFollowersList = () => {
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

const followers = ref([]);
const following = ref([]);

const { isLoading, isError } = useQuery({
  queryKey: ['followers', page, username],
  queryFn: async () => {
    const profile = await getUserProfileByUsername(username.value);
    const dataFollowers = await getFollowersPaginated(page.value, profile.id);
    const dataFollowing = await getFollowingsPaginated(page.value, profile.id);

    // update followers list with unique values only
    const updatedFollowers = [
      ...new Map(
        [...followers.value, ...dataFollowers].map((item) => [item.id, item])
      ).values(),
    ];
    followers.value = updatedFollowers;

    // update following list with unique values only
    const updatedFollowing = [
      ...new Map(
        [...following.value, ...dataFollowing].map((item) => [item.id, item])
      ).values(),
    ];
    following.value = updatedFollowing;

    hasHit80.value = false;
  },
  keepPreviousData: true,
  refetchOnWindowFocus: false,
});
</script>
