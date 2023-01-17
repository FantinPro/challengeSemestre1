import * as VueRouter from 'vue-router'
import Timeline from './views/Timeline.vue'
import Register from './views/Register.vue'

const routes = [
  { path: '/', component: Timeline },
  { path: '/register', component: Register },
]

export const router = VueRouter.createRouter({
  history: VueRouter.createWebHistory(),
  routes,
})
