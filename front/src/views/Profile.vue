<template>
  <div
    class="
      flex flex-col
      overflow-auto
      min-w-[300px]
      sm:min-w-[500px]
      md:min-w-[600px]
    ">
    <HeaderMenu :customTitle="true">
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
            <p class="text-sm text-gray-500">
              {{ profile?.messagesCount }} Echo{{
                profile?.messagesCount > 1 ? 's' : ''
              }}
            </p>
          </div>
        </div>
      </template>
    </HeaderMenu>
    <ProfilHeader />
  </div>
</template>
<script setup>
import { computed, onMounted } from 'vue-demi';
import { useRouter } from 'vue-router';
import HeaderMenu from '../components/Menu/HeaderMenu.vue';
import ProfilHeader from '../components/Profile/ProfileHeader.vue';
import { useUserStore } from '../store/user';

const { getUserProfileByUsername } = useUserStore();
const router = useRouter();

const profile = computed(() => useUserStore().profile);

const userMessages = computed(() => useFeedStore().userMessages);

const tabs = ['Following', 'Followers'];

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
