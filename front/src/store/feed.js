import { defineStore } from "pinia";

export const useFeedStore = defineStore('feed', {
  state: () => ({
    feed: [],
  }),
  actions: {
    async fetchFeed(page) {
      const response = await fetch(`${import.meta.env.VITE_API_URL}/api/messages/feed?page=${page}`, {
        headers: {
          Authorization: `Bearer ${$cookies.get('echo_user_token')}`,
        },
      });

      if (page === 1) {
        this.feed = await response.json();
      } else {
        this.feed = this.feed.concat(await response.json());
      }
    },
  },
});