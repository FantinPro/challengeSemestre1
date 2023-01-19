<template>
  <div class="flex flex-col overflow-auto h-full">
    <span v-if="isLoading">Loading...</span>
    <span v-else-if="isError">Error: {{ error.message }}</span>
    <div v-if="data">
      <div
        v-for="feed in data"
        :key="feed.id"
      >
        <Card :item="feed" />
      </div>
    </div>
  </div>
</template>
<script setup>
import { reactive, onMounted } from "vue";
import { useQuery } from "vue-query";
import SideMenu from "../components/Menu/SideMenu.vue";
import Card from "../components/Card/Card.vue";
import LayoutDefault from "../layouts/LayoutDefault.vue";

import { useUserStore } from "../store/user";
import { useFeedStore } from "../store/feed";

const { user } = useUserStore();
const { fetchFeed } = useFeedStore();
const emit = defineEmits(["update:layout"]);

onMounted(() => {
  emit("update:layout", LayoutDefault);
});

const { isLoading, isError, data, error } = useQuery("feed", () =>
  fetchFeed(1)
);

</script>
