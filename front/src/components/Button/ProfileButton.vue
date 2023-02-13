<template>
  <div>
    <Menu as="div" class="relative inline-block text-left">
      <MenuButton class="flex items-center gap-3 rounded-3xl px-2 py-2 w-fit hover:bg-opacity-10 hover:bg-white transition duration-200 ease-in-out">
        <img
          v-if="user?.profilePicture"
          :src="user?.profilePicture"
          class="w-8 h-8 rounded-full object-cover"
          alt="User avatar"
        />
        <div v-else class="w-7 h-7 bg-neutral-500 rounded-full" />
        <span class="font-bold text-lg hidden md:block">{{ pseudo }}</span>
      </MenuButton>
      <MenuItems class="absolute left-0 bottom-14 mt-2 w-40 origin-top-left divide-y divide-gray-100 rounded-md bg-neutral-800 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none ">
        <div class="p-2">
          <MenuItem v-slot="{ active }">
            <button :class="['flex w-full items-center rounded-md px-2 py-2 text-sm transition duration-200 ease-in-out', active && 'bg-neutral-700']" @click="openDialogSettings">
              Settings
            </button>
          </MenuItem>
          <MenuItem v-slot="{ active }">
            <button :class="['flex w-full items-center rounded-md px-2 py-2 text-sm transition duration-200 ease-in-out', active && 'bg-neutral-700']" @click="logout">
              Logout
            </button>
          </MenuItem>
        </div>
      </MenuItems>
    </Menu>
    <DialogSettings
      v-if="isOpenDialogSettings"
      :is-open="isOpenDialogSettings"
      :profile="user"
      @close="closeDialogSettings"
    />
  </div>
</template>

<script setup>
import { Menu, MenuButton, MenuItems, MenuItem } from '@headlessui/vue'
import { useUserStore } from '../../store/user'
import { useRouter } from 'vue-router'
import { ref, watch } from 'vue-demi';
import DialogSettings from '../Profile/DialogSettings.vue'

const userStore = useUserStore()
const { user } = userStore;
const router = useRouter()
let pseudo = ref(user.pseudo);

watch(
  () => useUserStore().user.pseudo,
  (newPseudo) => {
    pseudo.value = newPseudo;
  }
);

const isOpenDialogSettings = ref(false);

const openDialogSettings = () => {
  isOpenDialogSettings.value = true
}

const closeDialogSettings = () => {
  isOpenDialogSettings.value = false;
};


const logout = () => {
  userStore.logout()
  router.push('/login')
}
</script>