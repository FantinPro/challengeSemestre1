import * as VueRouter from 'vue-router';
import Timeline from './views/Timeline.vue';
import Register from './views/Register.vue';
import Login from './views/Login.vue';
import ForgotPassword from './views/ForgotPassword.vue';
import ResetPassword from './views/ResetPassword.vue';
import Messages from './views/Messages.vue';
import Profile from './views/Profile.vue';
import ProfileFollowers from './views/ProfileFollowers.vue';
import Search from './views/Search.vue';
import Dashboard from './views/Dashboard.vue';
import ManageUsers from './views/ManageUsers.vue';
import ManageReports from './views/ManageReports.vue';
import AdminStats from './views/AdminStats.vue';
import ManageAds from './views/ManageAds.vue';
import LayoutDefault from './layouts/LayoutDefault.vue';
import Echo from './views/Echo.vue';
import { ROLES } from './utils/constants';
import Calendar from './views/Calendar.vue';

const routes = [
  { path: '/register', component: Register },
  { path: '/login', component: Login },
  { path: '/forgot-password', component: ForgotPassword },
  { path: '/reset_password', component: ResetPassword },
  {
    path: '/',
    component: LayoutDefault,
    redirect: '/home',
    children: [
      { path: '/home', component: Timeline },
      { path: '/messages', component: Messages },
      { path: '/search', component: Search },
      { path: '/profile/:pseudo', component: Profile },
      { path: '/profile/:pseudo/:tab', component: ProfileFollowers },
      { path: '/profile/:pseudo/status/:id', component: Echo },
    ],
  },
  {
    path: '/dashboard',
    component: Dashboard,
    redirect: (to) => {
      // default redirection depending on user role
      const user = JSON.parse(localStorage.getItem('echoUser'));
      if (user.roles.includes(ROLES.ROLE_ADMIN)) {
        return { path: '/dashboard/stats' };
      }
      if (user.roles.includes(ROLES.ROLE_MODERATOR)) {
        return { path: '/dashboard/reports' };
      }
      if (user.roles.includes(ROLES.ROLE_PREMIUM)) {
        return { path: '/dashboard/calendar' };
      }
      return { path: '/home' };
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
        component: AdminStats,
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
        component: ManageUsers,
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
        component: ManageReports,
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
        component: Calendar,
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
        component: ManageAds,
      },
    ],
  },
  {
    path: '/:pathMatch(.*)*',
    name: 'not-found',
    component: import('./views/NotFound.vue'),
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
  if ((to.path === '/login' || to.path === '/register' || to.path === '/forgot-password' || to.path === '/reset_password') && isAuthenticated()) {
    next('/home');
  } else if (
    !(to.path === '/login' || to.path === '/register' || to.path === '/forgot-password' || to.path === '/reset_password') &&
    !isAuthenticated()
  ) {
    next('/login');
  }
  next();
});
