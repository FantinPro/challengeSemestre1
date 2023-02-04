import { defineConfig, mergeConfig } from 'vite'
import vue from '@vitejs/plugin-vue'

// https://vitejs.dev/config/
export default defineConfig({
  plugins: [vue()],
  async viteFinal(config, { configType }) {
    return mergeConfig(config, {
      optimizeDeps: {
        include: [
          'vue3-toastify',
        ],
      },
    });
  },
})
