export default defineNuxtRouteMiddleware(async () => {
  const { authLoaded, fetchUser } = useAuth()

  if (!authLoaded.value) {
    await fetchUser()
  }
})
