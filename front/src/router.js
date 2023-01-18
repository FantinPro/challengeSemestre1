import * as VueRouter from 'vue-router'
import Timeline from './views/Timeline.vue'
import Register from './views/Register.vue'
import Login from './views/Login.vue'

const routes = [
  { path: '/', component: Timeline },
  { path: '/register', component: Register },
  { path: '/login', component: Login },
]

export const router = VueRouter.createRouter({
  history: VueRouter.createWebHistory(),
  routes,
})

function isAuthenticated() {
  const userToken = $cookies.get('echo_user_token')
  console.log(!!userToken)
  return !!userToken
}

router.beforeEach(async (to, from, next) => {
 if ((to.path === '/login' || to.path === '/register') && isAuthenticated()) {
    next('/home')
 } else if (!(to.path === '/login' || to.path === '/register') && !isAuthenticated()) {
    next('/login')
 } else {
    next()
 }
})
