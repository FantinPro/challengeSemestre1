<template>
  <div
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
            <div class="flex flex-col gap-2 p-4 mt-2">
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
import { computed, onMounted } from 'vue-demi';
import { useRouter } from 'vue-router';
import HeaderMenu from '../components/Menu/HeaderMenu.vue';
import ProfilHeader from '../components/Profile/ProfileHeader.vue';
import { useUserStore } from '../store/user';
import { useFeedStore } from '../store/feed';

const { getUserProfileByUsername } = useUserStore();
const { getUserMessagesById } = useFeedStore();
const router = useRouter();

const profile = computed(() => useUserStore().profile);

const userMessages = computed(() => useFeedStore().userMessages);

const tabs = ['Echoes', 'Media', 'Likes'];

onMounted(async () => {
  if (!router.currentRoute.value.params.pseudo) {
    router.push('/');
  }

  if (!profile.value) {
    router.push('/');
  }

  await getUserProfileByUsername(router.currentRoute.value.params.pseudo);
  // Add infinite scroll
  await getUserMessagesById(profile.value.id, 1);
});
</script>
