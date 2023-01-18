<template>
  <div>
    <Menu as="div" class="relative inline-block text-left">
      <MenuButton class="flex items-center gap-2 rounded-3xl px-4 py-2 w-fit hover:bg-opacity-10 hover:bg-white transition duration-200 ease-in-out">
        <img
          v-if="user.avatar"
          :src="user.avatar"
          class="w-8 h-8 rounded-full"
          alt="User avatar"
        />
        <img
          v-else
          src="https://www.gravatar.com/avatar/00000000000000000000000000000000?d=mp&f=y"
          class="w-8 h-8 rounded-full"
          alt="User avatar"
        />
        <span>{{ user.pseudo }}</span>
      </MenuButton>
      <MenuItems class="absolute right-0 mt-2 w-56 origin-top-right divide-y divide-gray-100 rounded-md bg-neutral-800 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none ">
        <div class="p-2">
          <MenuItem v-slot="{ active }">
            <button @click="logout" :class="['flex w-full items-center rounded-md px-2 py-2 text-sm transition duration-200 ease-in-out', active && 'bg-neutral-700']">
              Se déconnecter
            </button>
          </MenuItem>
        </div>
      </MenuItems>
    </Menu>
  </div>
</template>

<script setup>
  import { Menu, MenuButton, MenuItems, MenuItem } from '@headlessui/vue'
  import { useUserStore } from '../../store/user'
  import { useRouter } from 'vue-router'

  const userStore = useUserStore()
  const { user } = userStore;
  const router = useRouter()

  const logout = () => {
    userStore.logout()
    router.push('/login')
  }
</script>