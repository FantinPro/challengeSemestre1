import * as VueRouter from 'vue-router'
import Timeline from './views/Timeline.vue'

const routes = [
  { path: '/', component: Timeline },
]

export const router = VueRouter.createRouter({
  history: VueRouter.createWebHistory(),
  routes,
})
