<template>
  <div class="flex gap-2">
    <div class="w-3/12">
      <SideMenu />
    </div>
    <div class="flex flex-col w-6/12">
      <div v-for="item in feed" :key="item.id">
        <Card :item="item" />
      </div>
    </div>
    <div class="w-3/12">
    </div>
  </div>
</template>
<script setup>
import { reactive, computed } from "vue";
import { useQuery } from "vue-query";
import SideMenu from "../components/Menu/SideMenu.vue";
import Card from "../components/Card/Card.vue";

// const query = useQuery("feed", getFeed);

const feed = reactive([
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