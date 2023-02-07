<template>
  <div class="flex flex-col overflow-auto h-full">
    <HeaderMenu>
      <template #title>
        <div class="flex gap-2 cursor-pointer">
          <button
            class="
              hover:bg-[#2f3336]
              text-white
              font-semibold
              px-3
              py-2
              rounded-full
            "
            @click="router.back()"
          >
            <svg
              class="h-6 w-6"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M10 19l-7-7m0 0l7-7m-7 7h18"
              />
            </svg>
          </button>
          <div class="flex flex-col">
            <h1 class="text-lg font-semibold">{{ profile?.pseudo }}</h1>
            <p class="text-sm text-gray-500">
              {{ profile?.messagesCount }} Echo{{
                profile?.messagesCount > 1 ? "s" : ""
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
import { computed, onMounted } from "vue-demi";
import { useRouter } from "vue-router";
import HeaderMenu from "../components/Menu/HeaderMenu.vue";
import ProfilHeader from "../components/Profile/ProfileHeader.vue";
import { useUserStore } from "../store/user";

const { getUserProfileByUsername } = useUserStore();
const router = useRouter();

const profile = computed(() => useUserStore().profile);

onMounted(async () => {
  if (!router.currentRoute.value.params.pseudo) {
    router.push("/");
  }

  await getUserProfileByUsername(router.currentRoute.value.params.pseudo);

  if (!profile.value) {
    router.push("/");
  }

});
</script>
