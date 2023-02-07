/* eslint-disable no-undef */
import { defineStore } from "pinia";

export const useFeedStore = defineStore('feed', {
  state: () => ({
    feed: [],
    userMessages: [],
  }),
  actions: {
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
    async postMessage(values) {
      try {
        const response = await fetch(`${import.meta.env.VITE_API_URL}/api/messages`, {
          method: 'POST',
          headers: {
            Accept: 'application/json',
            'Content-Type': 'application/json',
            Authorization: `Bearer ${$cookies.get('echo_user_token')}`
          },
          body: JSON.stringify(values),
        });
        const data = await response.json();
        return data;
      } catch (e) {
        console.log(e);
      }
    },
    async getUserMessagesById(id, page = 1) {
      const response = await fetch(`${import.meta.env.VITE_API_URL}/api/messages?creator=${id}&page=${page}`, {
        headers: {
          Accept: 'application/json',
          Authorization: `Bearer ${$cookies.get('echo_user_token')}`,
        },
      });
      
      const data = await response.json();
      this.userMessages = data;
      return data;
    },
  },
});