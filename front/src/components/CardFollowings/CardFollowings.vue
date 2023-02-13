<template>
  <div v-if="!isLoading && data?.length" class="rounded-2xl bg-neutral-800 p-4">
    <h1 class="text-xl font-semibold">Suggestions</h1>
    <div class="flex flex-col text-center text-xl gap-1 mt-4">
      <Spin v-if="isLoading" :is-loading="isLoading" />
      <span v-else-if="isError">Error: {{ error.message }}</span>
      <div v-for="user in data" :key="user.id" class="text-left text-base">
        <!-- bad name for @update-followers-list but anyway -->
        <UserCard
          :user="user"
          :suggestions="true"
          @update-followers-list="onFolllow" />
      </div>
    </div>
  </div>
</template>

<script setup>
import { useQuery, useQueryClient } from 'vue-query';
import { useUserStore } from '../../store/user';
import UserCard from '../../components/UserCard/UserCard.vue';
import Spin from '../../components/Loader/Spin.vue';

const queryClient = useQueryClient();

const userStore = useUserStore();

const onFolllow = () => {
  updateStoreFeed();
  queryClient.invalidateQueries('users-suggestions');
  queryClient.invalidateQueries('feedv2')
};  

const updateStoreFeed = () => {
  userStore.refetchFeed++;
};

const { isLoading, isError, data } = useQuery(['users-suggestions'], () =>
  userStore.fetchUsersSuggestions()
);
</script>
