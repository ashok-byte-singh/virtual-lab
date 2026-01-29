// https://nuxt.com/docs/api/configuration/nuxt-config
export default defineNuxtConfig({
 runtimeConfig: {
    public: {
      apiBase: 'https://api.asd.XXX.XX.in/api',
      authBase: 'https://api.asd.XXX.XX.in'
    },
  },
app: {
    pageTransition: { name: 'page', mode: 'out-in' },
  },

  compatibilityDate: '2025-07-15',
  devtools: { enabled: true }
})
