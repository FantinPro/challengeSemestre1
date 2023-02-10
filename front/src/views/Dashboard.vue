<!-- eslint-disable vue/multi-word-component-names -->
<template>
  <div class="flex w-full">
    <div class="flex w-60 flex-col border-r border-[#4c5157]">
      <div
        class="flex flex-col items-center justify-center gap-2 border-b border-[#4c5157] p-8">
        <div class="flex">
            <img class="h-6 w-6 text-white" src="/dashboard.svg" />
            <div class="text-center text-xl font-bold">Dashboard</div>
        </div>
        <div class="text-left text-sm font-bold">Hello : {{ user.pseudo }} 👋</div>
      </div>
      <Menu as="div" class="flex flex-1 flex-col pl-2">
        <div
          v-if="user.roles.includes(roles.ROLE_ADMIN)"
          class="ml-4 mt-4 mb-1 text-sm font-bold opacity-60">
          Security
        </div>
        <div
          v-if="user.roles.includes(roles.ROLE_ADMIN)"
          class="flex flex-col gap-4">
          <MenuButton :to="'/dashboard/stats'">
            <div class="group flex items-center gap-2">
              <ChartBarSquareIcon class="h-8 w-8" />
              <div>Echo Stats App</div>
            </div>
          </MenuButton>
          <MenuButton :to="'/dashboard/users'">
            <div class="group flex items-center gap-2">
              <UserGroupIcon class="h-8 w-8" />
              <div>Manage Users</div>
            </div>
          </MenuButton>
        </div>
        <div
          v-if="
            user.roles.includes(roles.ROLE_ADMIN) ||
            user.roles.includes(roles.ROLE_MODERATOR)
          "
          class="ml-4 mt-4 mb-1 text-sm font-bold opacity-60">
          Moderation
        </div>
        <div
          v-if="
            user.roles.includes(roles.ROLE_ADMIN) ||
            user.roles.includes(roles.ROLE_MODERATOR)
          "
          class="flex flex-col gap-4">
          <MenuButton :to="'/dashboard/reports'">
            <div class="group flex items-center gap-2">
              <FlagIcon class="h-8 w-8" />
              <div>Reports</div>
            </div>
          </MenuButton>
        </div>
        <div
          v-if="
            user.roles.includes(roles.ROLE_ADMIN) ||
            user.roles.includes(roles.ROLE_PREMIUM)
          "
          class="ml-4 mt-4 mb-1 text-sm font-bold opacity-60">
          Ads
        </div>
        <div class="flex flex-col gap-4">
          <MenuButton
            v-if="
              user.roles.includes(roles.ROLE_ADMIN) ||
              user.roles.includes(roles.ROLE_PREMIUM)
            "
            :to="'/dashboard/calendar'">
            <div class="group flex items-center gap-2">
              <CalendarDaysIcon class="h-8 w-8" />
              <div>Calendar</div>
            </div>
          </MenuButton>
          <MenuButton
            v-if="user.roles.includes(roles.ROLE_ADMIN)"
            :to="'/dashboard/manage_ads'">
            <div class="group flex items-center gap-2">
              <RectangleStackIcon class="h-8 w-8" />
              <div>Manage Ads</div>
            </div>
          </MenuButton>
        </div>
        <MenuButton class="mt-auto mb-4" :to="'/home'">
          <div class="group flex items-center gap-2">
            <img class="h-6 w-6 rotate-180" src="/back.svg" />
            <div>Back to App</div>
            <img class="ml-2 h-5" :src="user.profilePicture" alt="">
          </div>
        </MenuButton>
      </Menu>
    </div>
    <div class="flex flex-1">
      <router-view></router-view>
    </div>
  </div>
</template>
<script setup>
import { Menu } from '@headlessui/vue';
import MenuButton from '../components/Button/MenuButton.vue';
import { onMounted } from 'vue';
import {
  ChartBarSquareIcon,
  UserGroupIcon,
  FlagIcon,
  CalendarDaysIcon,
  RectangleStackIcon,
} from '@heroicons/vue/20/solid';
import { useUserStore } from '../store/user';
import { ROLES } from '../utils/constants';
const { user } = useUserStore();

const roles = ROLES;

const emit = defineEmits(['update:layout', 'update:classes']);

onMounted(() => {
  emit('update:layout', 'main');
  emit('update:classes', 'flex w-full');
});
</script>
