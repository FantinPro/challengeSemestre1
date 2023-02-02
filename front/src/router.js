import * as VueRouter from 'vue-router'
import Timeline from './views/Timeline.vue'
import Register from './views/Register.vue'
import Login from './views/Login.vue'
import Messages from './views/Messages.vue'
import Profile from './views/Profile.vue'
import Search from './views/Search.vue'
import Admin from './views/Admin.vue'
import { useUserStore } from './store/user';
import { ROLES } from './utils/constants'

const routes = [
  { path: '/', redirect: '/home' },
  { path: '/home', component: Timeline },
  { path: '/register', component: Register },
  { path: '/login', component: Login },
  { path: '/messages', component: Messages },
  { path: '/search', component: Search },
  { path: '/:pseudo', component: Profile },
  { 
    path: '/admin', 
    component: Admin,
    beforeEnter: (to, from, next) => {
      const { user } = useUserStore();
      const isAdmin = user.roles.includes(ROLES.ROLE_ADMIN)
      if (isAdmin) {
        next()
      }
      else {
        next('/home')
      }
    }
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
