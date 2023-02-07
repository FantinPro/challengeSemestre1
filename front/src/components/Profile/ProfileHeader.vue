<template>
  <div class="flex flex-col border-b border-[#4c5157]">
    <div
      class="h-40 w-full relative"
      :style="{
        backgroundColor: `#${Math.floor(Math.random() * 16777215).toString(
          16
        )}`,
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
          :src="profile?.avatar || 'https://i.pravatar.cc/160?img=40'"
          alt="" />
      </div>
    </div>
    <div class="flex gap-4 p-4">
      <div class="flex flex-col items-end w-full">
        <div v-if="user?.pseudo !== profile?.pseudo" class="flex gap-2">
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
            @click="editprofile()">
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
        <div class="flex gap-6 mt-2">
          <span class="flex gap-1 font-medium">
            <p class="font-bold">
              {{ profile?.followsCount }}
            </p>
            <p class="text-gray-500">Following</p>
          </span>
          <span class="flex gap-1 font-medium">
            <p class="font-bold">
              {{ profile?.followersCount }}
            </p>
            <p class="text-gray-500">
              {{ profile?.followersCount > 1 ? 'Followers' : 'Follower' }}
            </p>
          </span>
        </div>
      </div>
    </div>
    <HeaderMenu :tabs="tabs" />
  </div>
</template>
<script setup>
import { computed } from 'vue';
import HeaderMenu from '../Menu/HeaderMenu.vue';
import { useUserStore } from '../../store/user';

const tabs = ['Echoes', 'Media', 'Likes'];

const { user } = useUserStore();

const profile = computed(() => useUserStore().profile);

const createdAt = computed(() => {
  const date = new Date();
  return date.toLocaleDateString('en-US', {
    month: 'long',
    year: 'numeric',
  });
});
</script>
