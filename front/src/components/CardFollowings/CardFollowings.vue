<template>
  <div v-if="!isLoading && data?.length" class="rounded-2xl bg-neutral-800 p-4">
    <h1 class="text-2xl font-semibold">Suggestions</h1>
    <div class="mt-2 flex flex-col gap-2 text-center text-xl">
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
    <button @click="updateStoreFeed">click me</button>
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
