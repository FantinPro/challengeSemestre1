<!-- eslint-disable vue/multi-word-component-names -->
<template>
  <div class="flex w-full">
    <div class="flex flex-col w-60 border-r border-[#4c5157]">
      <div class="flex items-center justify-center gap-2 p-8 border-b border-[#4c5157]">
        <img class="h-6 w-6 text-white" :src="DashboardLogo" />
        <div class="text-center text-xl font-bold">Dashboard</div>
      </div>
      <Menu as="div" class="flex flex-col flex-1 pl-2">
        <div class="text-sm font-bold ml-4 mt-4 opacity-60 mb-1">
            Security
        </div>
        <div class="flex flex-col gap-4">
          <MenuButton v-slot="{ isActive }" :to="'/dashboard/stats'">
            <div class="group flex items-center gap-2">
              <img class="h-6 w-6" :src="DashboardLogo" />
              <div>Echo Stats App</div>
            </div>
          </MenuButton>
          <MenuButton v-slot="{ isActive }" :to="'/dashboard/users'">
            <div class="group flex items-center gap-2">
              <img class="h-6 w-6" :src="DashboardLogo" />
              <div>Manage Users</div>
            </div>
          </MenuButton>
        </div>
        <div class="text-sm font-bold ml-4 mt-4 opacity-60 mb-1">
            Moderation
        </div>
        <div class="flex flex-col gap-4">
          <MenuButton v-slot="{ isActive }" :to="'/dashboard/reports'">
            <div class="group flex items-center gap-2">
              <img class="h-6 w-6" :src="DashboardLogo" />
              <div>Reports</div>
            </div>
          </MenuButton>
        </div>
        <div class="text-sm font-bold ml-4 mt-4 opacity-60 mb-1">
            Ads
        </div>
        <div class="flex flex-col gap-4">
          <MenuButton v-slot="{ isActive }" :to="'/dashboard/calendar'">
            <div class="group flex items-center gap-2">
              <img class="h-6 w-6" :src="DashboardLogo" />
              <div>Calendar</div>
            </div>
          </MenuButton>
          <MenuButton v-slot="{ isActive }" :to="'/dashboard/manage_ads'">
            <div class="group flex items-center gap-2">
              <img class="h-6 w-6" :src="DashboardLogo" />
              <div>Manage Ads</div>
            </div>
          </MenuButton>
        </div>
        <MenuButton class="mt-auto mb-4" :to="'/home'">
            <div class="group flex items-center gap-2">
              <img class="h-6 w-6 rotate-180" :src="BackLogo" />
              <div>Back to App</div>
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
import DashboardLogo from '../assets/dashboard.svg';
import BackLogo from '../assets/back.svg';
import { Menu } from '@headlessui/vue';
import MenuButton from '../components/Button/MenuButton.vue';
import { onMounted } from 'vue';

const emit = defineEmits(['update:layout', 'update:classes']);

onMounted(() => {
  emit('update:layout', 'main');
  emit('update:classes', 'flex w-full');
});
</script>
