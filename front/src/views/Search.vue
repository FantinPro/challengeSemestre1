<template>
  <div class="flex flex-col overflow-auto h-full">
    <HeaderMenu :tabs="tabs" title="Recherche">
      <template #panels>
        <TabPanels>
          <TabPanel>
            <div class="flex flex-col text-center text-xl gap-2 p-4 mt-2">
              <span v-if="isLoadingFeed">Loading...</span>
              <span v-else-if="isErrorFeed">Error: {{ error.message }}</span>
              <div v-for="msg in feed" :key="msg.id" class="text-left text-base">
                <Card :item="msg" />
              </div>
              <div v-if="!feed?.length && !isLoadingFeed">
                No results
              </div>
            </div>
          </TabPanel>
          <TabPanel>
            <div class="flex flex-col text-center text-xl mt-6">
              <span v-if="isLoadingUsers">Loading...</span>
              <span v-else-if="isErrorUsers">Error: {{ error.message }}</span>
              <div v-for="user in users" :key="user.id" class="text-left text-base">
                <UserCard :user="user" @click="router.push(`/profile/${user.pseudo}`)" />
              </div>
              <div v-if="!users?.length && !isLoadingFeed">
                No results
              </div>
            </div>
          </TabPanel>
        </TabPanels>
      </template>
    </HeaderMenu>
  </div>
</template>

<script setup>
import { TabPanel, TabPanels } from '@headlessui/vue';
import { computed } from "vue";
import { useQuery } from "vue-query";
import { useRoute, useRouter } from "vue-router";
import Card from "../components/Card/Card.vue";
import HeaderMenu from "../components/Menu/HeaderMenu.vue";
import UserCard from "../components/User/UserCard.vue";
import { useFeedStore } from "../store/feed";
import { useUserStore } from '../store/user';

const tabs = ['Messages', 'Utilisateurs']

const router = useRouter();
const route = useRoute();
const { fetchMessages } = useFeedStore();
const { fetchUsers } = useUserStore();
const search = computed(() => route.query.q);

const { isLoading: isLoadingFeed, isError: isErrorFeed, data: feed } = useQuery(["feed", search], () =>
  fetchMessages(1, { content: route.query.q })
);

const { isLoading: isLoadingUsers, isError: isErrorUsers, data: users } = useQuery(["users", search], () =>
  fetchUsers(route.query.q)
);
</script>