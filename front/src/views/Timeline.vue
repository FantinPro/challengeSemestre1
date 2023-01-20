<template>
  <div class="flex flex-col overflow-auto h-full">
    <HeaderMenu :tabs="tabs" title="Home">
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
  import { TabList, Tab, TabPanels, TabPanel } from '@headlessui/vue'
  import { onMounted } from "vue";
  import { useQuery } from "vue-query";
  import SideMenu from "../components/Menu/SideMenu.vue";
  import Card from "../components/Card/Card.vue";
  import HeaderMenu from "../components/Menu/HeaderMenu.vue";
  import LayoutDefault from "../layouts/LayoutDefault.vue";

  import { useFeedStore } from "../store/feed";

  const tabs = ['For you', 'Following']
  const { fetchFeed } = useFeedStore();
  const emit = defineEmits(["update:layout"]);

  onMounted(() => {
    emit("update:layout", LayoutDefault);
  });

  const { isLoading, isError, data, error } = useQuery("feed", () =>
    fetchFeed(1)
  );
</script>
