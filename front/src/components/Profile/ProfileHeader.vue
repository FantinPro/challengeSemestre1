<template>
  <div class="flex flex-col">
    <HeaderMenu :custom-title="true" :sticky="true">
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
    <div v-if="props.disableInfos === false">
      <div
        class="h-40 w-full relative"
        :style="{
          backgroundColor: `#${randomColor}`,
        }">
        <div
          class="
            absolute
            top-24
            left-6
            h-32
            w-32
            rounded-full
            bg-gray-300
            border-4 border-[#2f3336]
            shadow-md
          ">
          <img
            class="h-full w-full rounded-full object-cover"
            :src="profile?.profilePicture"
            alt="" />
        </div>
      </div>
      <div class="flex gap-4 p-4">
        <div class="flex flex-col items-end w-full">
          <div v-if="user?.pseudo === profile?.pseudo" class="flex gap-2">
            <PremiumSubscribeButton/>
            <button
              class="
                hover:bg-[#2f3336]
                text-white
                font-semibold
                px-3
                py-2
                rounded-full
                border border-[#3b4043]
                transition-all
                duration-300
              "
              @click="editProfile()">
              <span class="">Edit profile</span>
            </button>
          </div>
          <div v-else class="flex gap-2">
            <button
              class="
                hover:bg-[#2f3336]
                text-white
                font-semibold
                px-3
                py-2
                rounded-full
                border border-[#3b4043]
                transition-all
                duration-300
              ">
              <svg
                class="h-6 w-6"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor">
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="1"
                  d="M4 8h16M4 16h16" />
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="1"
                  d="M4 8h16M4 16h16" />
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="1"
                  d="M4 8h16M4 16h16" />
              </svg>
            </button>
            <button
              class="
                font-semibold
                px-4
                py-1
                rounded-full
                border border-[#3b4043]
                text-white
                hover:bg-[#2f3336]
                transition-all
                duration-300
              "
              @click="sendMessage()">
              <svg
                class="h-6 w-6"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor">
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
              </svg>
            </button>
            <button
              class="
                font-semibold
                px-4
                py-1
                rounded-full
                border border-white
                bg-white
                text-[#1a1c1d]
                hover:opacity-80
                transition-all
                duration-300
              "
              @click="follow()">
              <span>Follow</span>
            </button>
          </div>
        </div>
      </div>
      <div class="flex flex-col p-4 pb-0">
        <div class="flex flex-col">
          <h1 class="text-xl font-semibold">{{ profile?.pseudo }}</h1>
          <p class="text-gray-500 font-medium">@{{ profile?.pseudo }}</p>
        </div>
        <div class="flex flex-col mt-4">
          <h1 class="text-md">{{ profile?.bio }}</h1>
          <div class="flex gap-6 mt-2">
            <span class="flex gap-1 font-medium text-gray-500">
              <p class="">
                Joined
                {{ createdAt }}
              </p>
            </span>
          </div>
          <div class="flex gap-6 mt-2 text-gray-500">
            <router-link :to="'/profile/' + profile?.pseudo + '/following'">
              <span class="flex gap-1 font-medium hover:underline">
                <p class="font-bold text-white">
                  {{ profile?.followsCount }}
                </p>
                <p class="">Following</p>
              </span>
            </router-link>
            <router-link :to="'/profile/' + profile?.pseudo + '/followers'">
              <span class="flex gap-1 font-medium hover:underline">
                <p class="font-bold text-white">
                  {{ profile?.followersCount }}
                </p>
                <p class="">
                  {{ profile?.followersCount > 1 ? 'Followers' : 'Follower' }}
                </p>
              </span>
            </router-link>
          </div>
        </div>
      </div>
    </div>
    <DialogEditProfile
      :is-open="isOpenReportDialog"
      :profile="profile"
      @close="closeReportDialog" />
  </div>
</template>
<script setup>
import { computed, ref } from 'vue';
import { useRouter } from 'vue-router';
import { useUserStore } from '../../store/user';
import HeaderMenu from '../Menu/HeaderMenu.vue';
import DialogEditProfile from './DialogEditProfile.vue';
import PremiumSubscribeButton from "../Button/PremiumSubscribeButton.vue";

const props = defineProps({
  disableInfos: {
    type: Boolean,
    default: false,
  },
});

const router = useRouter();
const { user } = useUserStore();
const profile = computed(() => useUserStore().profile);

const createdAt = computed(() => {
  const date = new Date();
  return date.toLocaleDateString('en-US', {
    month: 'long',
    year: 'numeric',
  });
});

const isOpenReportDialog = ref(false);

const editProfile = () => {
  isOpenReportDialog.value = true;
};

const closeReportDialog = () => {
  isOpenReportDialog.value = false;
};

const randomColor = Math.floor(Math.random() * 16777215).toString(16);
</script>
