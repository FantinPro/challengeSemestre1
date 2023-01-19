import * as VueRouter from 'vue-router'
import Timeline from './views/Timeline.vue'
import Register from './views/Register.vue'
import Login from './views/Login.vue'
import Messages from './views/Messages.vue'
import Profile from './views/Profile.vue'

const routes = [
  { path: '/', redirect: '/home' },
  { path: '/home', component: Timeline },
  { path: '/register', component: Register },
  { path: '/login', component: Login },
  { path: '/messages', component: Messages },
  { path: '/:pseudo', component: Profile },
]

export const router = VueRouter.createRouter({
  history: VueRouter.createWebHistory(),
  routes,
})

function isAuthenticated() {
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
