/* eslint-disable no-undef */
import { defineStore } from "pinia";

export const useFeedStore = defineStore('feed', {
  state: () => ({
    feed: [],
    userMessages: [],
    refetchFeed: false,
  }),
  actions: {
    async setRefetchFeed(value) {
      this.refetchFeed = value;
    },
    async fetchFeed(page) {
      try {
        const response = await fetch(`${import.meta.env.VITE_API_URL}/api/messages/feed/v2?page=${page}`, {
          headers: {
            Accept: 'application/json',
            Authorization: `Bearer ${$cookies.get('echo_user_token')}`,
          },
        });
  
        const data = await response.json();
        return data;
      } catch (e) {
        console.log(e);
      }
    },
    async fetchMessages(page, options = {}) {
      try {
        const { content, creator } = options;
        const response = await fetch(`${import.meta.env.VITE_API_URL}/api/messages?page=${page}${content ? '&content=' + content : ''}${creator ? '&creator=' + creator : ''}`, {
          headers: {
            Accept: 'application/json',
            Authorization: `Bearer ${$cookies.get('echo_user_token')}`,
          },
        });

        const data = await response.json();
        return data;
      } catch (e) {
        console.log(e);
      }
    },
    async postMessage(values) {
      try {
        if (!values.content && !values.content.length < 255 && !values.content.length > 0) {
          throw new Error('Invalid message');
        }
        if (values.content.trim().length === 0) {
          throw new Error('Invalid message');
        }
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
      try {
        const response = await fetch(`${import.meta.env.VITE_API_URL}/api/messages?creator=${id}&page=${page}`, {
          headers: {
            Accept: 'application/json',
            Authorization: `Bearer ${$cookies.get('echo_user_token')}`,
          },
        });
        
        const data = await response.json();
        this.userMessages = data;
        return data;
      } catch (e) {
        console.log(e);
      }
    },
    async deleteMessage(id) {
      try {
        const response = await fetch(`${import.meta.env.VITE_API_URL}/api/messages/${id}`, {
          method: 'DELETE',
          headers: {
            Accept: 'application/json',
            Authorization: `Bearer ${$cookies.get('echo_user_token')}`,
          },
        });
        if (response.ok) {
          return true;
        }
        return false;
      } catch (e) {
        console.log(e);
      }
    }
  },
});
