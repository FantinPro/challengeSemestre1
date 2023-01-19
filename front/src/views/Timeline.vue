<template>
  <div class="flex flex-col overflow-auto h-full">
    <HeaderMenu>
      <template #title>
        <h1 class="text-2xl font-bold cursor-pointer">Home</h1>
      </template>
      <template #body>
        <div class="flex">
          <button
            class="
              hover:bg-[#2f3336]
              text-white
              font-semibold
              w-full
              py-4
            "
          >
            For you
          </button>
          <button
            class="
              hover:bg-[#2f3336]
              text-white
              font-semibold
              w-full
            "
          >
            Following
          </button>
        </div>
      </template>
    </HeaderMenu>
    <div class="flex flex-col gap-2 p-4 mt-2">
      <span v-if="isLoading">Loading...</span>
      <span v-else-if="isError">Error: {{ error.message }}</span>
      <div v-for="feed in data" :key="feed.id">
        <Card :item="feed" />
      </div>
    </div>
  </div>
</template>
<script setup>
import { onMounted } from "vue";
import { useQuery } from "vue-query";
import SideMenu from "../components/Menu/SideMenu.vue";
import Card from "../components/Card/Card.vue";
import HeaderMenu from "../components/Menu/HeaderMenu.vue";
import LayoutDefault from "../layouts/LayoutDefault.vue";

import { useFeedStore } from "../store/feed";

const { fetchFeed } = useFeedStore();
const emit = defineEmits(["update:layout"]);

onMounted(() => {
  emit("update:layout", LayoutDefault);
});

const { isLoading, isError, data, error } = useQuery("feed", () =>
  fetchFeed(1)
);
</script>
