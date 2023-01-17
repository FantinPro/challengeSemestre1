import { createApp } from 'vue'
import './style.css'
import App from './App.vue'
import { router } from './router.js'
import { plugin, defaultConfig } from '@formkit/vue'
import { fr } from '@formkit/i18n'
import '@formkit/themes/genesis'

const app = createApp(App)

app.use(router)

app.use(
  plugin,
  defaultConfig({
    locales: { fr },
    locale: 'fr'
  })
)

app.mount('#app')
