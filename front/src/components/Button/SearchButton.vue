<template>
  <Combobox v-slot="{ open }" v-model="search">
    <ComboboxButton
      :as="ComboboxInput"
      placeholder="Search Echo"
      autocomplete="off"
      class="
        w-full
        border-none
        py-2
        pl-3
        pr-10
        text-sm
        leading-5
        text-white
        focus:ring-0
        rounded-full
      "
      @click="open = true"
      @input="search = $event.target.value"
      @keydown.enter="
        search !== ''
          ? handleSelectUser(search, 'enter')
          : router.push('/home')
      " />
    <div v-show="open" class="relative">
      <div
        class="
          absolute
          top-3
          w-full
          max-h-60
          overflow-auto
          rounded-md
          text-base
          shadow-lg
          ring-1 ring-black ring-opacity-5
          focus:outline-none
          sm:text-sm
        ">
        <template v-if="isLoading">
          <div class="py-4 bg-neutral-800">
            <svg
              class="animate-spin inset-0 h-5 w-5 mx-auto text-white"
              xmlns="http://www.w3.org/2000/svg"
              fill="none"
              viewBox="0 0 24 24">
              <circle
                class="opacity-25"
                cx="12"
                cy="12"
                r="10"
                stroke="currentColor"
                stroke-width="4"></circle>
              <path
                class="opacity-75"
                fill="currentColor"
                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
          </div>
        </template>
        <template v-else-if="!isSearchNotEmpty">
          <div class="pl-3 py-4 bg-neutral-800 italic">
            Search echo or @username
          </div>
        </template>
        <template v-else-if="data?.length == 0">
          <div class="pl-3 py-4 bg-neutral-800">
            No result for "{{ debouncedSearch }}"
          </div>
        </template>
        <template v-else>
          <ComboboxOptions>
            <ComboboxOption :value="debouncedSearch">
              <div
                class="
                  pl-3
                  py-4
                  bg-neutral-800
                  hover:bg-neutral-600 hover:cursor-pointer
                "
                @click="handleSelectUser(search, 'enter')">
                Searching "{{ debouncedSearch }}"
              </div>
            </ComboboxOption>
            <ComboboxOption
              v-for="echo in messages"
              :key="echo.id"
              :value="echo.id"
              @click="handleSelectEcho(echo.id, echo.creator.pseudo, 'select')">
              <EchoCard :echo="echo" type="searchBar" />
            </ComboboxOption>
            <ComboboxOption
              v-for="user in users"
              :key="user.pseudo"
              :value="user.pseudo"
              @click="handleSelectUser(user.pseudo, 'select')">
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
import { refDebounced } from '@vueuse/core';
import {
  Combobox,
  ComboboxInput,
  ComboboxButton,
  ComboboxOptions,
  ComboboxOption,
} from '@headlessui/vue';
import { useQuery } from 'vue-query';
import { useRouter, useRoute } from 'vue-router';
import { useUserStore } from '../../store/user';
import UserCard from '../User/UserCard.vue';
import EchoCard from '../User/EchoCard.vue';
import { useFeedStore } from '../../store/feed';

const router = useRouter();
const route = useRoute();
const { fetchUsers } = useUserStore();
const { fetchMessages } = useFeedStore();

const search = ref(route.query.q || '');
const debouncedSearch = refDebounced(search, 200);
const isSearchNotEmpty = computed(() => debouncedSearch.value !== '');

function handleSelectUser(userPseudo, type) {
  if (type === 'select') {
    router.push(`/profile/${userPseudo}`);
    search.value = '';
  } else if (type === 'enter') {
    router.push(`/search?q=${userPseudo}`);
  }
}

function handleSelectEcho(echoId, username, type) {
  if (type === 'select') {
    router.push(`/profile/${username}/status/${echoId}`);
    search.value = '';
  } else if (type === 'enter') {
    router.push(`/search?q=${search.value}`);
  }
}

const { isLoading: isLoadingMessages, data: messages } = useQuery(
  ['messages_search_button', debouncedSearch],
  () => fetchMessages(1, { content: debouncedSearch.value })
    .then((res) => {
      console.log(res);
      if (!res) return [];
      if (res.length > 5) return res.slice(0, 5);
      return res;
    }),
  { enabled: isSearchNotEmpty }
);

const { isLoading: isLoadingUsers, data: users } = useQuery(
  ['users_search_button', debouncedSearch],
  () => fetchUsers(debouncedSearch.value),
  { enabled: isSearchNotEmpty }
);
</script>