import { defineStore } from "pinia";

export const useFeedStore = defineStore('feed', {
  state: () => ({
    feed: [],
  }),
  actions: {
    // 
    async fetchFeed(page) {
      const response = await fetch(`${import.meta.env.VITE_API_URL}/api/messages/feed/v2?page=${page}`, {
        headers: {
          Accept: 'application/json',
          Authorization: `Bearer ${$cookies.get('echo_user_token')}`,
        },
      });

      const data = await response.json();
      return data;
    },
    async fetchMessages(page, options = {}) {
      const { content, creator } = options;
      const response = await fetch(`${import.meta.env.VITE_API_URL}/api/messages?page=${page}${content ? '&content=' + content : ''}${creator ? '&creator=' + creator : ''}`, {
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