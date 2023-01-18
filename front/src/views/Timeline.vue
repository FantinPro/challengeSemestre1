<template>
  <div class="w-8/12">
    <div v-for="item in feed" :key="item.id">
      <Card :item="item" />
    </div>
  </div>
</template>
<script setup>
import { reactive, computed } from "vue";
import { useQuery } from "vue-query";
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
    username: "Michael Brown",
    avatar: "https://i.pravatar.cc/150?img=4",
    text: "Another year older, but still feeling young. Thanks for the birthday wishes everyone!",
    time: "5 minutes ago",
    comments: 11,
    shares: 6,
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
  {
    id: 1,
    username: "Bob Johnson",
    avatar: "https://i.pravatar.cc/150?img=2",
    text: "https://www.youtube.com/watch?v=dQw4w9WgXcQ",
    time: "1 day ago",
    comments: 7,
    shares: 4,
  },
]);

const filteredFeed = computed(() => {
  return this.$query(this.items, this.query);
});
</script>