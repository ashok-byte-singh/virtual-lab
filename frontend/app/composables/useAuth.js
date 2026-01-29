import { navigateTo } from '#app'

export const useAuth = () => {
  const config = useRuntimeConfig()

  const user = useState('user', () => null)
  const authLoaded = useState('authLoaded', () => false)

  // 🔐 helper to read CSRF token from cookie
  const getCsrfToken = () => {
    if (process.server) return null
    const match = document.cookie.match(/XSRF-TOKEN=([^;]+)/)
    return match ? decodeURIComponent(match[1]) : null
  }

  // 🔁 Restore user
  const fetchUser = async () => {
    try {
      user.value = await $fetch(`${config.public.apiBase}/me`, {
        credentials: 'include',
        headers: { Accept: 'application/json' }
      })
    } catch {
      user.value = null
    } finally {
      authLoaded.value = true
    }
  }

  // 🔐 Login
  const login = async (email, password) => {
    authLoaded.value = false
    try {
      // 1️⃣ Get CSRF cookie
      await $fetch(`${config.public.authBase}/sanctum/csrf-cookie`, {
        credentials: 'include'
      })

      // 2️⃣ Read token from cookie
      const csrfToken = getCsrfToken()

      // 3️⃣ Login with CSRF header
      await $fetch(`${config.public.authBase}/login`, {
        method: 'POST',
        credentials: 'include',
        headers: {
          Accept: 'application/json',
          'X-XSRF-TOKEN': csrfToken
        },
        body: { email, password }
      })

      await fetchUser()
      await navigateTo('/dashboard')
    } catch (e) {
      authLoaded.value = true
      throw e
    }
  }

  // 🚪 Logout
  const logout = async () => {
    user.value = null
    authLoaded.value = true

    const csrfToken = getCsrfToken()

    await $fetch(`${config.public.authBase}/logout`, {
      method: 'POST',
      credentials: 'include',
      headers: {
        'X-XSRF-TOKEN': csrfToken
      }
    })

    await navigateTo('/login', { replace: true })
  }

  return { user, authLoaded, fetchUser, login, logout }
}
