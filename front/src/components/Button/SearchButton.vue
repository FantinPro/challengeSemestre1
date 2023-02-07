<template>
  <Combobox v-slot="{ open }" v-model="searchUser">
    <ComboboxButton
      :as="ComboboxInput"
      placeholder="Search for a user"
      autocomplete="off"
      class="w-full border-none py-2 pl-3 pr-10 text-sm leading-5 text-white focus:ring-0 rounded-full"
      @click="open = true"
      @input="searchUser = $event.target.value"
      @keydown.enter="searchUser !== '' && handleSelectUser(searchUser, 'enter')"
    />
    <div v-show="open" class="relative">
      <div class="absolute top-3 w-full max-h-60 overflow-auto rounded-md text-base shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm">
        <template v-if="isLoading">
          <div class="py-4 bg-neutral-800">
            <svg class="animate-spin inset-0 h-5 w-5 mx-auto text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
          </div>
        </template>
        <template v-else-if="!isSearchUserNotEmpty">
          <div class="pl-3 py-4 bg-neutral-800 italic">
            search by @username
          </div>
        </template>
        <template v-else-if="data?.length == 0">
          <div class="pl-3 py-4 bg-neutral-800">
            No result
          </div>
        </template>
        <template v-else>
          <ComboboxOptions>
            <ComboboxOption :value="debouncedSearchUser">
              <div class="pl-3 py-4 bg-neutral-800 hover:bg-neutral-700/60 hover:cursor-pointer" @click="handleSelectUser(searchUser, 'enter')">
                Searching "{{ debouncedSearchUser }}"
              </div>
            </ComboboxOption>
            <ComboboxOption
              v-for="user in data"
              :key="user.pseudo"
              :value="user.pseudo"
              @click="handleSelectUser(user.pseudo, 'select')"
            >
              <UserCard :user="user" type="searchBar" />
            </ComboboxOption>
          </ComboboxOptions>
        </template>
      </div>

    </div>
  </Combobox>
</template>

<script setup>
import { ref, computed } from 'vue';
import { refDebounced } from '@vueuse/core'
import { Combobox, ComboboxInput, ComboboxButton, ComboboxOptions, ComboboxOption } from '@headlessui/vue';
import { useQuery } from "vue-query";
import { useRouter, useRoute } from 'vue-router';
import { useUserStore } from '../../store/user';
import UserCard from '../User/UserCard.vue'

const router = useRouter();
const route = useRoute();
const { fetchUsers } = useUserStore();

const searchUser = ref(route.query.q || '');
const debouncedSearchUser = refDebounced(searchUser, 200);
const isSearchUserNotEmpty = computed(() => debouncedSearchUser.value !== '');

function useUsersQuery(search , { enabled }) {
  return useQuery(["users", search], async () => await fetchUsers(search), { enabled });
}

function handleSelectUser(userPseudo, type) {
  if (type === 'select') {
    router.push(`/${userPseudo}`);
    searchUser.value = '';
  } else if (type === 'enter') {
    router.push(`/search?q=${userPseudo}`);
  }
}

const { isLoading, isError, data, error } = useUsersQuery(debouncedSearchUser.value, { enabled: isSearchUserNotEmpty });
</script>