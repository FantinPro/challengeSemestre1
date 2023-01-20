<template>
  <Combobox v-model="searchUser" v-slot="{ open }">
    <ComboboxInput
      class="w-full border-none py-2 pl-3 pr-10 text-sm leading-5 text-white focus:ring-0 rounded-full"
      @input="searchUser = $event.target.value"
      @keydown.enter="handleSelectUser(searchUser, 'enter')"
    />
    <div v-show="open" class="relative">

      <div class="absolute top-2 w-full max-h-60 overflow-auto rounded-md p-3 text-base shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm">
        <template v-if="isLoading">
          <div class="absolute inset-y-0 flex items-center pr-2 pointer-events-none">
            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
            </svg>
          </div>
        </template>
        <template v-else>
          <ComboboxOptions>
            <ComboboxOption :value="debouncedSearchUser">
              Rechercher "{{ debouncedSearchUser }}"
            </ComboboxOption>
            <ComboboxOption
              v-for="user in data"
              :key="user"
              :value="user"
              @click="handleSelectUser(user, 'select')"
            >
              {{ user }}
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
  import { useRouter } from 'vue-router';

  const router = useRouter();

  const searchUser = ref('');
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
    return useQuery(["users", search], async () => {
      const data = await fetchUsers(search);
      return data.map(user => user.pseudo);
    }, { enabled });
  }

  function handleSelectUser(user, type) {
    if (type === 'select') {
      router.push(`/${user}`);
      searchUser.value = '';
    } else if (type === 'enter') {
      router.push(`/search?q=${user}`);
    }
  }

  const { isLoading, isError, data, error } = useUsersQuery(debouncedSearchUser, { enabled: isSearchUserNotEmpty });

  // const selectedPerson = ref(people[0])
</script>