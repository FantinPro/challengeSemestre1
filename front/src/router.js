import * as VueRouter from 'vue-router'
import Timeline from './views/Timeline.vue'
import Register from './views/Register.vue'
import Login from './views/Login.vue'
import Messages from './views/Messages.vue'
import Profile from './views/Profile.vue'
import Search from './views/Search.vue'
import Dashboard from './views/Dashboard.vue'
import ManageUsers from './views/ManageUsers.vue'
import { useUserStore } from './store/user';
import { ROLES } from './utils/constants'

const routes = [
  { path: '/', redirect: '/home' },
  { path: '/home', component: Timeline },
  { path: '/register', component: Register },
  { path: '/login', component: Login },
  { path: '/messages', component: Messages },
  { path: '/search', component: Search },
  { path: '/profile/:pseudo', component: Profile },
  { 
    path: '/dashboard', 
    component: Dashboard,
    redirect: (to) => {
      console.log('🟩🟩🟩🟩🟩🟩🟩🟩🟩🟩🟩🟩🟩🟩')
      console.log(to)
      console.log('🟦🟦🟦🟦🟦🟦🟦🟦🟦🟦🟦🟦🟦🟦')
      return { path: 'dashboard/users' }
    },
    children: [
      { path: 'stats', component: ManageUsers },
      { path: 'users', component: ManageUsers },
      { path: 'reports', component: ManageUsers },
      { path: 'calendar', component: ManageUsers },
      { path: 'manage_ads', component: ManageUsers },
    ]
  },
]

export const router = VueRouter.createRouter({
  history: VueRouter.createWebHistory(),
  routes,
})

function isAuthenticated() {
  // eslint-disable-next-line no-undef
  const userToken = $cookies.get('echo_user_token')
  return !!userToken
}

router.beforeEach(async (to, _, next) => {
  if ((to.path === '/login' || to.path === '/register') && isAuthenticated()) {
    next('/home')
  } else if (!(to.path === '/login' || to.path === '/register') && !isAuthenticated()) {
    next('/login')
  } else {
    next()
  }
})
