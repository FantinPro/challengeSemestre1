import { createApp } from 'vue'
import { VueQueryPlugin } from 'vue-query';
import './style.css'
import App from './App.vue'
import { router } from './router.js'
import { plugin, defaultConfig } from '@formkit/vue'
import { fr } from '@formkit/i18n'
import '@formkit/themes/genesis'
import { generateClasses } from '@formkit/themes'

const app = createApp(App)

app.use(router)
app.use(VueQueryPlugin)

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
