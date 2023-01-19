import { defineStore } from "pinia";

export const useFeedStore = defineStore('feed', {
  state: () => ({
    feed: [],
  }),
  actions: {
    async fetchFeed(page) {
      const response = await fetch(`${import.meta.env.VITE_API_URL}/api/messages/feed?page=${page}`, {
        headers: {
          Accept: 'application/json',
          Authorization: `Bearer ${$cookies.get('echo_user_token')}`,
        },
      });

      const data = await response.json();
      return data;
    },
  },
});