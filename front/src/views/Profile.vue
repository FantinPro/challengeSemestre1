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
    <ProfilHeader />
    <div class="mt-4">
      <HeaderMenu :tabs="tabs">
        <template #panels>
          <TabPanels>
            <TabPanel>
              <div class="flex flex-col border-t border-[#3b4043]">
                <span v-if="isLoading">Loading...</span>
                <span v-else-if="isError">Error: {{ error.message }}</span>
                <div v-for="echo in echoes" :key="echo.id">
                  <Card :item="echo" @delete-one-message-from-feed="deleteOneMessageFromFeed" />
                </div>
              </div>
            </TabPanel>
            <TabPanel></TabPanel>
          </TabPanels>
        </template>
      </HeaderMenu>
    </div>
  </div>
</template>
<script setup>
import { TabPanel, TabPanels } from '@headlessui/vue';
import { onMounted, ref, watch } from 'vue-demi';
import { useQuery } from 'vue-query';
import { useRouter } from 'vue-router';
import Card from '../components/Card/Card.vue';
import HeaderMenu from '../components/Menu/HeaderMenu.vue';
import ProfilHeader from '../components/Profile/ProfileHeader.vue';
import { useFeedStore } from '../store/feed';
import { useUserStore } from '../store/user';

const { getUserProfileByUsername } = useUserStore();
const { fetchMessages } = useFeedStore();
const router = useRouter();
const tabs = ['Echoes', 'Likes'];

const containerElement = ref();

let username = ref(router.currentRoute.value.params.pseudo);

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

const echoes = ref([]);

const deleteOneMessageFromFeed = (echo) => {
  echoes.value = echoes.value.filter((m) => m.id !== echo.id);
};

const { isLoading, isError } = useQuery({
  queryKey: ['profile', page],
  queryFn: async () => {
    const [profile] = await Promise.all([getUserProfileByUsername(username.value)]);
    const dataEchoes = await fetchMessages(page.value, {
      creator: profile.id,
    });
    return dataEchoes;
  },
  onSuccess: (dataEchoes) => {
    if (dataEchoes.length === 0) {
      return;
    }
    hasHit80.value = false;
    echoes.value = [...echoes.value, ...dataEchoes]
  },
  keepPreviousData: true,
  refetchOnWindowFocus: false,
});

watch(
  () => router.currentRoute.value.params.pseudo,
  (newVal) => {
    if (newVal !== username.value && newVal !== undefined) {
      username.value = newVal;
    }
  }
);
</script>
