<script setup>
const api = useApi()

const { data, error } = await useAsyncData('test-api', () =>
  api('/api/test')
)
</script>

<template>
  <div>
    <h1>Nuxt → Laravel API</h1>

    <pre v-if="data">{{ data }}</pre>
    <pre v-if="error">{{ error }}</pre>
  </div>
</template>
