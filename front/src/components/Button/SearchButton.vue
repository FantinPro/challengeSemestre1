<template>
  <Combobox v-model="searchUser" v-slot="{ open }">
    <ComboboxButton
      :as="ComboboxInput"
      @click="open = true"
      autocomplete="off"
      class="w-full border-none py-2 pl-3 pr-10 text-sm leading-5 text-white focus:ring-0 rounded-full"
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
          <div class="pl-3 py-4 bg-neutral-800">
            Tapez un pseudo
          </div>
        </template>
        <template v-else-if="data?.length == 0">
          <div class="pl-3 py-4 bg-neutral-800">
            Aucun résultat
          </div>
        </template>
        <template v-else>
          <ComboboxOptions>
            <ComboboxOption :value="debouncedSearchUser">
              <div class="pl-3 py-4 bg-neutral-800 hover:bg-neutral-700/60 hover:cursor-pointer" @click="handleSelectUser(searchUser, 'enter')">
                Rechercher "{{ debouncedSearchUser }}"
              </div>
            </ComboboxOption>
            <ComboboxOption
              v-for="user in data"
              :key="user.pseudo"
              :value="user.pseudo"
              @click="handleSelectUser(user.pseudo, 'select')"
            >
              <div class="flex items-center pl-3 py-4 bg-neutral-800 hover:bg-neutral-700/60 hover:cursor-pointer">
                <img
                  v-if="user?.profilePicture"
                  :src="user?.profilePicture"
                  class="w-8 h-8 rounded-full"
                  alt="User avatar"
                />
                <div v-else class="w-7 h-7 bg-neutral-500 rounded-full" />
                <span class="font-bold text-lg ml-3 hidden md:block">{{ user?.pseudo }}</span>
              </div>
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

  const router = useRouter();
  const route = useRoute();

  const searchUser = ref(route.query.q || '');
  const debouncedSearchUser = refDebounced(searchUser, 200);
  const isSearchUserNotEmpty = computed(() => debouncedSearchUser.value !== '');

  function fetchUsers(search) {
    return fetch(`${import.meta.env.VITE_API_URL}/api/users?page=1&pseudo=${search.value}`, {
      headers: {
        "Authorization": `Bearer ${$cookies.get('echo_user_token')}`,
        "Accept": "application/json",
      }
    })
    .then((res) => res.json());
  }

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

  const { isLoading, isError, data, error } = useUsersQuery(debouncedSearchUser, { enabled: isSearchUserNotEmpty });

  // const selectedPerson = ref(people[0])
</script>