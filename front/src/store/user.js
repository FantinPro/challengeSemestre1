import { defineStore } from 'pinia';
import jwt_decode from 'jwt-decode';
import { router } from '../router';

export const useUserStore = defineStore('user', {
  state: () => ({
    user: JSON.parse(localStorage.getItem('echoUser')),
    profile: null,
  }),
  actions: {
    async signUp(values) {
      try {
        await fetch(`${import.meta.env.VITE_API_URL}/api/users`, {
          method: 'POST',
          headers: {
            Accept: 'application/json',
            'Content-Type': 'application/json',
          },
          body: JSON.stringify(values),
        });
      } catch (e) {
        console.log(e);
      }
    },
    async signIn(values) {
      try {
        const response = await fetch(`${import.meta.env.VITE_API_URL}/auth`, {
          method: 'POST',
          headers: {
            Accept: 'application/json',
            'Content-Type': 'application/json',
          },
          body: JSON.stringify(values),
        });
        const userToken = await response.json();

        if (userToken && userToken.token) {
          $cookies.set('echo_user_token', userToken.token);

          const decoded = jwt_decode(userToken.token);

          const res = await fetch(
            `${import.meta.env.VITE_API_URL}/api/users/${decoded.id}`,
            {
              headers: {
                Accept: 'application/json',
                'Content-Type': 'application/json',
                Authorization: `Bearer ${userToken.token}`,
              },
            }
          );

          const user = await res.json();

          if (user) {
            this.user = user;
            localStorage.setItem('echoUser', JSON.stringify(user));
            router.push('/home');
          }
        }
      } catch (e) {
        console.log(e);
      }
    },
    async logout() {
      this.user = null;
      localStorage.removeItem('echoUser');
      $cookies.remove('echo_user_token');
    },
    async getUserProfileByUsername(username) {
      try {
        const response = await fetch(
          `${
            import.meta.env.VITE_API_URL
          }/api/users/profile?pseudo=${username}`,
          {
            headers: {
              Accept: 'application/json',
              'Content-Type': 'application/json',
              Authorization: `Bearer ${$cookies.get('echo_user_token')}`,
            },
          }
        );
        const profile = await response.json();
        this.profile = profile;
      } catch (e) {
        console.log(e);
      }
    },
    // http://localhost:8000/api/user_to_users?me=67  == les gens que je follow
    // http://localhost:8000/api/user_to_users?other=67  == les gens qui me follow
    async getFollowers() {
      try {
        const response = await fetch(
          `${import.meta.env.VITE_API_URL}/api/user_to_users?other=${
            this.user.id
          }`,
          {
            headers: {
              Accept: 'application/json',
              'Content-Type': 'application/json',
              Authorization: `Bearer ${$cookies.get('echo_user_token')}`,
            },
          }
        );
        const followers = await response.json();
        return followers;
      } catch (e) {
        console.log(e);
      }
    },
    async getFollowings() {
      try {
        const response = await fetch(
          `${import.meta.env.VITE_API_URL}/api/user_to_users?me=${
            this.user.id
          }`,
          {
            headers: {
              Accept: 'application/json',
              'Content-Type': 'application/json',
              Authorization: `Bearer ${$cookies.get('echo_user_token')}`,
            },
          }
        );
        const followings = await response.json();
        return followings;
      } catch (e) {
        console.log(e);
      }
    },
    async fetchUsers(pseudo) {
      try {
        const response = await fetch(
          `${import.meta.env.VITE_API_URL}/api/users?page=1&pseudo=${pseudo}`,
          {
            headers: {
              Accept: 'application/json',
              'Content-Type': 'application/json',
              Authorization: `Bearer ${$cookies.get('echo_user_token')}`,
            },
          }
        );
        const users = await response.json();
        return users;
      } catch (e) {
        console.log(e);
      }
    },
    async fetchUsersPaginated(page = 1) {
      try {
        const response = await fetch(
          `${import.meta.env.VITE_API_URL}/api/users?page=${page}`,
          {
            headers: {
              // explicitly no set accept
            //   Accept: 'application/json',
              'Content-Type': 'application/json',
              Authorization: `Bearer ${$cookies.get('echo_user_token')}`,
            },
          }
        );
        const { 'hydra:member': users, 'hydra:totalItems': total } = await response.json();
        return { users, total };
      } catch (e) {
        console.log(e);
      }
    }
  },
});
