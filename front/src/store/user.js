// /store/user.js

import { defineStore } from "pinia";
import jwt_decode from "jwt-decode";
import { router } from "../router";

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
      try {
        const response = await fetch('http://localhost:8000/auth', {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
          },
          body: JSON.stringify(values),
        });
        const userToken = await response.json();
  
        if (userToken.token) {
          $cookies.set('echo_user_token', userToken.token);
    
          const decoded = jwt_decode(userToken.token);
    
          const res = await fetch(`http://localhost:8000/api/users/${decoded.id}`, {
            headers: {
              Authorization: `Bearer ${userToken.token}`,
            },
          });
          const user = await res.json();
  
          if (user) {
            this.user = user;
            localStorage.setItem('echoUser', JSON.stringify(user));
            router.push('/home');
          }
        }
      } catch (e) {
        console.log(e)
      }
    },
    async logout() {
      this.user = null;
      localStorage.removeItem('echoUser');
      $cookies.remove('echo_user_token');
    }
  },
});