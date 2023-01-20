<template>
  <div class="flex flex-col overflow-auto h-full">
    <HeaderMenu :tabs="tabs" title="Recherche">
      <template #panels>
        <TabPanels>
          <TabPanel>
            <div class="flex flex-col gap-2 p-4 mt-2">
              <span v-if="isLoading">Loading...</span>
              <span v-else-if="isError">Error: {{ error.message }}</span>
              <div v-for="feed in data" :key="feed.id">
                <Card :item="feed" />
              </div>
            </div>
          </TabPanel>
          <TabPanel>Content 2</TabPanel>
        </TabPanels>
      </template>
    </HeaderMenu>
  </div>
</template>

<script setup>
  import { TabGroup, TabList, Tab, TabPanels, TabPanel } from '@headlessui/vue'
  import LayoutDefault from "../layouts/LayoutDefault.vue";
  import Card from "../components/Card/Card.vue";
  import HeaderMenu from "../components/Menu/HeaderMenu.vue";
  import { useQuery } from "vue-query";
  import { computed, onMounted } from "vue";
  import { useFeedStore } from "../store/feed";
  import { useRoute } from "vue-router";

  const tabs = ['Messages', 'Utilisateurs']

  const route = useRoute();
  const { fetchMessages } = useFeedStore();
  const emit = defineEmits(["update:layout"]);
  const search = computed(() => route.query.q);
  onMounted(() => {
    emit('update:layout', LayoutDefault);
  });

  const { isLoading, isError, data, error } = useQuery(["feed", search], () =>
    fetchMessages(1, { content: route.query.q })
  );
</script>