import { defineStore } from "pinia";
import jwt_decode from "jwt-decode";
import { router } from "../router";

export const useUserStore = defineStore('user', {
  state: () => ({
    user: JSON.parse(localStorage.getItem('echoUser')),
  }),
  actions: {
    async signUp(values) {
      try {
        await fetch(`${import.meta.env.VITE_API_URL}/api/users`, {
          method: "POST",
          headers: {
            Accept: 'application/json',
            "Content-Type": "application/json",
          },
          body: JSON.stringify(values),
        });
      } catch (e) {
        console.log(e)
      }
    },
    async signIn(values) {
      try {
        const response = await fetch(`${import.meta.env.VITE_API_URL}/auth`, {
          method: "POST",
          headers: {
            Accept: 'application/json',
            "Content-Type": "application/json",
          },
          body: JSON.stringify(values),
        });
        const userToken = await response.json();

        if (userToken && userToken.token) {
          $cookies.set('echo_user_token', userToken.token);

          const decoded = jwt_decode(userToken.token);

          const res = await fetch(`${import.meta.env.VITE_API_URL}/api/users/${decoded.id}`, {
            headers: {
              "Content-Type": "application/json",
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