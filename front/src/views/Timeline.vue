<template>
  <div>HISISHDISHD</div>
</template>
<script>
export default {
  setup() {
    return {
      // timeline message, username, avatar, text, time, likes, comments, shares
      feed: [
        {
          message: "This is a message",
          username: "John Doe",
          avatar: "https://i.pravatar.cc/150?img=1",
          text: "This is a text",
          time: "1 hour ago",
          likes: 0,
          comments: 0,
          shares: 0,
        },
        {
          message: "This is an example message",
          username: "Jane Smith",
          avatar: "https://i.pravatar.cc/150?img=5",
          text: "Lorem ipsum dolor sit amet, consectetur adipiscing elit.",
          time: "3 minutes ago",
          likes: 12,
          comments: 3,
          shares: 2,
        },
        {
          message: "Happy birthday to me!",
          username: "Michael Brown",
          avatar: "https://i.pravatar.cc/150?img=4",
          text: "Another year older, but still feeling young. Thanks for the birthday wishes everyone!",
          time: "5 minutes ago",
          likes: 32,
          comments: 11,
          shares: 6,
        },
        {
          message: "Just finished my run",
          username: "Emily Davis",
          avatar: "https://i.pravatar.cc/150?img=3",
          text: "Feeling great! 5 miles in 40 minutes. #running #fitness",
          time: "2 hours ago",
          likes: 15,
          comments: 2,
          shares: 1,
        },
        {
          message: "Check out this cool video",
          username: "Bob Johnson",
          avatar: "https://i.pravatar.cc/150?img=2",
          text: "https://www.youtube.com/watch?v=dQw4w9WgXcQ",
          time: "1 day ago",
          likes: 24,
          comments: 7,
          shares: 4,
        },
      ],
    };
  },
};
</script>