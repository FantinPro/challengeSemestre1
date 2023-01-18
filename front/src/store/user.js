// /store/user.js

import { defineStore } from "pinia";

export const useUserStore = defineStore('user', {
  state: () => ({
    user: JSON.parse(localStorage.getItem('echoUser')),
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
      const response = await fetch('http://localhost:8000/api/login', {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify(values),
      });
      const userToken = await response.json();

      $cookies.set(userToken.token, 'echo_user_token');

      const res = await fetch('http://localhost:8000/api/users/me', {
        headers: {
          Authorization: `Bearer ${userToken.token}`,
        },
      });
      const user = await res.json();
      this.user = user;

      localStorage.setItem('echoUser', JSON.stringify(user));
    },
  },
});