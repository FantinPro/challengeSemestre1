<template>
  <div v-if="!isLoading && data?.length" class="rounded-2xl bg-neutral-800 p-4">
    <h1 class="text-2xl font-semibold">Suggestions</h1>
    <div class="flex flex-col text-center text-xl gap-2 mt-2">
      <Spin v-if="isLoading" :is-loading="isLoading" />
      <span v-else-if="isError">Error: {{ error.message }}</span>
      <div v-for="user in data" :key="user.id" class="text-left text-base">
        <UserCard
          :user="user"
          :suggestions="true"
          @on-follow-user="invalidateQueries"
        />
      </div>
    </div>
  </div>
</template>


<script setup>

import { useQuery, useQueryClient } from 'vue-query';
import { useUserStore } from '../../store/user';
import UserCard from '../../components/UserCard/UserCard.vue'
import Spin from '../../components/Loader/Spin.vue'

const queryClient = useQueryClient();

const { fetchUsersSuggestions } = useUserStore();

const invalidateQueries = () => {
  queryClient.invalidateQueries('users-suggestions')
  queryClient.invalidateQueries('feedv2')
}

const { isLoading, isError, data } = useQuery(['users-suggestions', ], () =>
  fetchUsersSuggestions()
);

</script>
