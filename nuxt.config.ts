// https://nuxt.com/docs/api/configuration/nuxt-config
export default defineNuxtConfig({
 runtimeConfig: {
    public: {
      apiBase: 'https://api.asd.mauj.ac.in/api',
      authBase: 'https://api.asd.mauj.ac.in'
    },
  },
app: {
    pageTransition: { name: 'page', mode: 'out-in' },
  },

  compatibilityDate: '2025-07-15',
  devtools: { enabled: true }
})
