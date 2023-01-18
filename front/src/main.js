import { createApp } from 'vue'
import { VueQueryPlugin } from 'vue-query';
import './style.css'
import App from './App.vue'
import { router } from './router.js'
import { plugin, defaultConfig } from '@formkit/vue'
import { fr } from '@formkit/i18n'
import '@formkit/themes/genesis'
import { generateClasses } from '@formkit/themes'
import { createPinia } from 'pinia'
import VueCookies from 'vue-cookies'

const app = createApp(App)
const pinia = createPinia()

app.use(router)
app.use(VueQueryPlugin)

app.use(pinia)

app.use(VueCookies, { expires: '90d' })

app.use(
  plugin,
  defaultConfig({
    locales: { fr },
    locale: 'fr',
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
