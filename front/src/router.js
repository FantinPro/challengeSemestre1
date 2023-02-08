import * as VueRouter from 'vue-router';
import Timeline from './views/Timeline.vue';
import Register from './views/Register.vue';
import Login from './views/Login.vue';
import Messages from './views/Messages.vue';
import Profile from './views/Profile.vue';
import Search from './views/Search.vue';
import Dashboard from './views/Dashboard.vue';
import ManageUsers from './views/ManageUsers.vue';
import ManageReports from './views/ManageReports.vue';
import AdminStats from './views/AdminStats.vue';
import LayoutDefault from './layouts/LayoutDefault.vue';
import { ROLES } from './utils/constants';

const routes = [
  { path: '/register', component: () => Register },
  { path: '/login', component: () => Login },
  {
    path: '/',
    component: LayoutDefault,
    redirect: '/home',
    children: [
      { path: '/home', component: () => Timeline },
      { path: '/messages', component: () => Messages },
      { path: '/search', component: () => Search },
      { path: '/profile/:pseudo', component: () => Profile },
    ],
  },
  {
    path: '/dashboard',
    component: Dashboard,
    redirect: (to) => {
      // default redirection depending on user role
      const user = JSON.parse(localStorage.getItem('echoUser'));
      if (user.roles.includes(ROLES.ROLE_ADMIN)) {
        return { path: 'dashboard/stats' };
      }
      if (user.roles.includes(ROLES.ROLE_MODERATOR)) {
        return { path: 'dashboard/reports' };
      }
      if (user.roles.includes(ROLES.ROLE_PREMIUM)) {
        return { path: 'dashboard/calendar' };
      }
      return { path: 'home' };
    },
    children: [
      {
        path: 'stats',
        beforeEnter: (to, from) => {
          const user = JSON.parse(localStorage.getItem('echoUser'));
          if (user.roles.includes(ROLES.ROLE_ADMIN)) {
            return true;
          }
          return { path: 'dashboard' };
        },
        component: () => AdminStats,
      },
      {
        path: 'users',
        beforeEnter: (to, from) => {
          const user = JSON.parse(localStorage.getItem('echoUser'));
          if (user.roles.includes(ROLES.ROLE_ADMIN)) {
            return true;
          }
          return { path: 'dashboard' };
        },
        component: () => ManageUsers,
      },
      {
        path: 'reports',
        beforeEnter: (to, from) => {
          const user = JSON.parse(localStorage.getItem('echoUser'));
          if (
            user.roles.includes(ROLES.ROLE_ADMIN) ||
            user.roles.includes(ROLES.ROLE_MODERATOR)
          ) {
            return true;
          }
          return { path: 'dashboard' };
        },
        component: () => ManageReports,
      },
      {
        path: 'calendar',
        beforeEnter: (to, from) => {
          const user = JSON.parse(localStorage.getItem('echoUser'));
          if (
            user.roles.includes(ROLES.ROLE_ADMIN) ||
            user.roles.includes(ROLES.ROLE_PREMIUM)
          ) {
            return true;
          }
          return { path: 'dashboard' };
        },
        // component à remplacer
        component: () => ManageUsers,
      },
      {
        path: 'manage_ads',
        beforeEnter: (to, from) => {
          const user = JSON.parse(localStorage.getItem('echoUser'));
          if (user.roles.includes(ROLES.ROLE_ADMIN)) {
            return true;
          }
          return { path: 'dashboard' };
        },
        // component à remplacer
        component: () => ManageUsers,
      },
    ],
  },
  {
    path: '/:pathMatch(.*)*',
    name: 'not-found',
    component: () => import('./views/NotFound.vue'),
  },
];

export const router = VueRouter.createRouter({
  history: VueRouter.createWebHistory(),
  routes,
});

function isAuthenticated() {
  // eslint-disable-next-line no-undef
  const userToken = $cookies.get('echo_user_token');
  return !!userToken;
}

router.beforeEach(async (to, _, next) => {
  if ((to.path === '/login' || to.path === '/register') && isAuthenticated()) {
    next('/home');
  } else if (
    !(to.path === '/login' || to.path === '/register') &&
    !isAuthenticated()
  ) {
    next('/login');
  }
  next();
});
