import { createApp } from 'vue'
import { VueQueryPlugin } from 'vue-query';
import './style.css'
import App from './App.vue'
import { router } from './router.js'
import { plugin, defaultConfig } from '@formkit/vue'
import { en } from '@formkit/i18n'
import '@formkit/themes/genesis'
import { generateClasses } from '@formkit/themes'
import { createPinia } from 'pinia'
import VueCookies from 'vue-cookies'
import 'vue3-toastify/dist/index.css';

const app = createApp(App)

app.config.globalProperties.$filters = {
  displayRole(role) {
    if (!role) return ''
    const mapped = role.replace('ROLE_', '')
    const roleCapitalized = mapped.charAt(0).toUpperCase() + mapped.slice(1).toLowerCase()
    return roleCapitalized
  },
  displayStat(statTitle) {
    if (statTitle === 'nbUsers') return 'Number of users'
    if (statTitle === 'nbAds') return 'Number of Ads'
    if (statTitle === 'nbMessages') return 'Number of Messages'
    if (statTitle === 'amountEarned') return 'Amount Earned'
  }
}

const pinia = createPinia()

app.use(router)
app.use(VueQueryPlugin)
app.use(pinia)
app.use(VueCookies, { expires: '90d' })
app.use(
  plugin,
  defaultConfig({
    locales: { en },
    locale: 'en',
    config: {
      classes: generateClasses({
        global: {
          message: '!text-error',
        },
        form: {
          submitAttrs: { outerClass: 'pt-4', inputClass: '!w-full !bg-primary-500' }
        }
      })
    }
  })
)

app.mount('#app')
