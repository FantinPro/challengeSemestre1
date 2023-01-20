<template>
  <div class="flex flex-col overflow-auto h-full">
    <HeaderMenu :tabs="tabs" title="Recherche">
      <template #panels>
        <TabPanels>
          <TabPanel>
            <div class="flex flex-col gap-2 p-4 mt-2">
              <span v-if="isLoadingFeed">Loading...</span>
              <span v-else-if="isErrorFeed">Error: {{ error.message }}</span>
              <div v-for="msg in feed" :key="msg.id">
                <Card :item="msg" />
              </div>
            </div>
          </TabPanel>
          <TabPanel>
            <div class="flex flex-col">
              <span v-if="isLoadingUsers">Loading...</span>
              <span v-else-if="isErrorUsers">Error: {{ error.message }}</span>
              <div v-for="user in users" :key="user.id">
                <UserCard :user="user" @click="router.push(`/${user.pseudo}`)" />
              </div>
            </div>
          </TabPanel>
        </TabPanels>
      </template>
    </HeaderMenu>
  </div>
</template>

<script setup>
  import { TabGroup, TabList, Tab, TabPanels, TabPanel } from '@headlessui/vue'
  import LayoutDefault from "../layouts/LayoutDefault.vue";
  import Card from "../components/Card/Card.vue";
  import UserCard from "../components/User/UserCard.vue";
  import HeaderMenu from "../components/Menu/HeaderMenu.vue";
  import { useQuery } from "vue-query";
  import { computed, onMounted } from "vue";
  import { useFeedStore } from "../store/feed";
  import { useUserStore } from '../store/user';
  import { useRouter, useRoute } from "vue-router";

  const tabs = ['Messages', 'Utilisateurs']

  const router = useRouter();
  const route = useRoute();
  const { fetchMessages } = useFeedStore();
  const { fetchUsers } = useUserStore();
  const emit = defineEmits(["update:layout"]);
  const search = computed(() => route.query.q);

  onMounted(() => {
    emit('update:layout', LayoutDefault);
  });
 
  const { isLoading: isLoadingFeed, isError: isErrorFeed, data: feed } = useQuery(["feed", search], () =>
    fetchMessages(1, { content: route.query.q })
  );

  const { isLoading: isLoadingUsers, isError: isErrorUsers, data: users } = useQuery(["users", search], () =>
    fetchUsers(route.query.q)
  );
</script>