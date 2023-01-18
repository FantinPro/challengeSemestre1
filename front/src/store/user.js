// /store/user.js

import { defineStore } from "pinia";



export const useUserStore = defineStore('user', {
  state: () => ({
    user: null,
    userToken: $cookies.get('userToken'),
  }),
  actions: {
    async signUp(values) {
      await fetch('http://localhost:8000/api/users', {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify(values),
      });
    },
    async signIn(values) {
      const res = await fetch('http://localhost:8000/api/login', {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify(values),
      });
      const userToken = await res.json();
      return userToken.token;
    },
  },
});