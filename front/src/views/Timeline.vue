<template>
  <div class="flex flex-col overflow-auto overflow-x-hidden h-full">
    <span v-if="isLoading">Loading...</span>
    <span v-else-if="isError">Error: {{ error.message }}</span>
    <div v-for="feed in timeline" :key="feed.id">
      <Card :item="feed" />
    </div>
  </div>
</template>
<script setup>
import { reactive, computed, onMounted } from "vue";
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

function useFeedQuery() {
  return useQuery("feed", fetchFeed(1));
}

const { isLoading, isError, data, error } = useFeedQuery();

const timeline = reactive([
  {
    id: 1,
    username: "John Doe",
    avatar: "https://i.pravatar.cc/150?img=9",
    text: "This is a text",
    time: "1 hour ago",
    comments: 0,
    shares: 0,
  },
  {
    id: 1,
    username: "John Doe",
    avatar: "https://i.pravatar.cc/150?img=1",
    text: "This is a text",
    time: "1 hour ago",
    comments: 0,
    shares: 0,
  },
  {
    id: 1,
    username: "Jane Smith",
    avatar: "https://i.pravatar.cc/150?img=5",
    text: "Lorem ipsum dolor sit amet, consectetur adipiscing elit.",
    time: "3 minutes ago",
    comments: 3,
    shares: 2,
  },
  {
    id: 1,
    username: "Emily Davis",
    avatar: "https://i.pravatar.cc/150?img=3",
    text: "Feeling great! 5 miles in 40 minutes. #running #fitness",
    time: "2 hours ago",
    comments: 2,
    shares: 1,
  },
]);

const filteredFeed = computed(() => {
  return this.$query(this.items, this.query);
});
</script>