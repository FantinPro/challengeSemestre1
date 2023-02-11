/* eslint-disable no-undef */
import { defineStore } from 'pinia';
import jwt_decode from 'jwt-decode';
import { router } from '../router';

export const useUserStore = defineStore('user', {
  state: () => ({
    user: JSON.parse(localStorage.getItem('echoUser')),
    profile: null,
  }),
  actions: {
    setLocalUser(user) {
      this.user = user;
      localStorage.setItem('echoUser', JSON.stringify(user));
    },
    async signUp(values) {
      return await fetch(`${import.meta.env.VITE_API_URL}/api/users`, {
        method: 'POST',
        headers: {
          Accept: 'application/json',
          'Content-Type': 'application/json',
        },
        body: JSON.stringify(values),
      });
    },
    async signIn(values) {
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
          this.setLocalUser(user);
          router.push('/home');
        }
      }

      return response;
    },
    async forgotPassword(values) {
      return await fetch(`${import.meta.env.VITE_API_URL}/forgot_password`, {
        method: 'POST',
        headers: {
          Accept: 'application/json',
          'Content-Type': 'application/json',
        },
        body: JSON.stringify(values),
      });
    },
    async resetPassword(values, token) {
      return await fetch(`${import.meta.env.VITE_API_URL}/reset_password?token=${token}`, {
        method: 'POST',
        headers: {
          Accept: 'application/json',
          'Content-Type': 'application/json',
        },
        body: JSON.stringify(values),
      });
    },
    async logout() {
      this.user = null;
      localStorage.removeItem('echoUser');
      $cookies.remove('echo_user_token');
    },
    async getUserProfileByUsername(username) {
      try {
        const response = await fetch(
          `${import.meta.env.VITE_API_URL
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
        if (!response.ok) {
          throw new Error(profile.detail)
        }
        this.profile = profile;
        return profile;
      } catch (e) {
        console.log(e);
      }
    },
    async fetchUsers(pseudo) {
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
      if (!response.ok) {
        throw new Error(users.detail)
      }
      return users;
    },
    async fetchUsersPaginated(page = 1) {
      const response = await fetch(
        `${import.meta.env.VITE_API_URL}/api/users?page=${page}`,
        {
          headers: {
            // explicitly no set accept
            // Accept: 'application/json',
            'Content-Type': 'application/json',
            Authorization: `Bearer ${$cookies.get('echo_user_token')}`,
          },
        }
      );
      const { 'hydra:member': users, 'hydra:totalItems': total } =
        await response.json();
      if (!response.ok) {
        throw new Error(users.detail)
      }
      return { users, total };
    },
    async updateProfile({
      userId,
      pseudo,
      bio,
      avatar,
    }) {
      const response = await fetch(`${import.meta.env.VITE_API_URL}/api/users/${userId}`, {
        method: 'PUT',
        headers: {
          Accept: 'application/json',
          'Content-Type': 'application/json',
          Authorization: `Bearer ${$cookies.get('echo_user_token')}`,
        },
        body: JSON.stringify({
          pseudo,
          bio,
          profilePicture: avatar,
        }),
      })
      const json = await response.json()
      if (!response.ok) {
        throw new Error(json.detail)
      }
      await this.getUserProfileByUsername(json.pseudo)
      this.setLocalUser({
        ...this.user,
        pseudo: json.pseudo,
        bio: json.bio,
        profilePicture: json.profilePicture,
      })
      return json
    },
    async getFollowersPaginated(page = 1, userId) {
      const response = await fetch(`${import.meta.env.VITE_API_URL}/api/users/followers?userId=${userId}&page=${page}`, {
        method: 'GET',
        headers: {
          Accept: 'application/json',
          'Content-Type': 'application/json',
          Authorization: `Bearer ${$cookies.get('echo_user_token')}`,
        },
      })

      const json = await response.json()
      if (!response.ok) {
        throw new Error(json.detail)
      }
      return json
    },
    async getFollowingsPaginated(page = 1, userId) {
      try {
        const response = await fetch(`${import.meta.env.VITE_API_URL}/api/users/follows?userId=${userId}&page=${page}`, {
          method: 'GET',
          headers: {
            Accept: 'application/json',
            'Content-Type': 'application/json',
            Authorization: `Bearer ${$cookies.get('echo_user_token')}`,
          },
        })

        const json = await response.json()
        if (!response.ok) {
          throw new Error(json.detail)
        }
        return json
      } catch (e) {
        console.log(e);
      }
    },
    async followUserById(userId) {
      try {
        const response = await fetch(`${import.meta.env.VITE_API_URL}/api/user_to_users`, {
          method: 'POST',
          headers: {
            Accept: 'application/json',
            'Content-Type': 'application/json',
            Authorization: `Bearer ${$cookies.get('echo_user_token')}`,
          },
          body: JSON.stringify({
            me: `/api/users/${this.user.id}`,
            other: `/api/users/${userId}`,
            status: 'following',
          }),
        })
        const json = await response.json()
        if (!response.ok) {
          throw new Error(json.detail)
        }
        return json
      } catch (e) {
        console.log(e);
      }
    },
    async unfollowUserById(userId) {
      const response = await fetch(`${import.meta.env.VITE_API_URL}/api/user_to_users/delete?userId=${userId}`, {
        method: 'GET',
        headers: {
          Accept: 'application/json',
          'Content-Type': 'application/json',
          Authorization: `Bearer ${$cookies.get('echo_user_token')}`,
        },
      })
      if (!response.ok) {
        throw new Error('Error while unfollowing user')
      }
      return true
    }
  },
});
