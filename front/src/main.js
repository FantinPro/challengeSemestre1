import { createApp } from 'vue'
import { VueQueryPlugin } from 'vue-query';
import './style.css'
import App from './App.vue'
import { router } from './router.js'
import { plugin, defaultConfig } from '@formkit/vue'
import { fr } from '@formkit/i18n'
import '@formkit/themes/genesis'

const app = createApp(App)

app.use(router)
app.use(VueQueryPlugin)

app.use(
  plugin,
  defaultConfig({
    locales: { fr },
    locale: 'fr'
  })
)

app.mount('#app')
