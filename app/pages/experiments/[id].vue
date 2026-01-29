 <script setup>
definePageMeta({ middleware: 'auth' })

import { ref, watch } from 'vue'
const { public: { apiBase } } = useRuntimeConfig()
const route = useRoute()
const videoSrc = ref(null)

const { data: experiment, pending, error } = await useAsyncData(
  `experiment-${route.params.id}`,
  () =>
    $fetch(`${apiBase}/experiments/${route.params.id}`, {
      credentials: 'include',
      headers: { Accept: 'application/json' }
    }),
  { server: false }
)

watch(
  () => experiment.value?.video_url,
  async (val) => {
    if (!val) return

    const res = await $fetch(`${apiBase}/video/${route.params.id}`, {
      credentials: 'include'
    })

    videoSrc.value = res.playlist
  },
  { immediate: true }
)


// breadcrumb
watch(() => experiment.value, (val) => {
  if (val) route.meta.breadcrumb = val.title
})
</script>


    
    <template>
      <div class="experiment-detail">
        <!-- ⛔ Guard rendering -->
        <div v-if="pending">
          Loading experiment…
        </div>
    
        <div v-else-if="error">
          Failed to load experiment.
        </div>
    
        <!-- ✅ ONLY render when data exists -->
        <div v-else-if="experiment">
          <h1>{{ experiment.title }}</h1>
    
          <details open>
            <summary>Aim</summary>
            <p>{{ experiment.aim }}</p>
          </details>
    
          <details>
            <summary>Objective</summary>
            <p>{{ experiment.objective }}</p>
          </details>
    
          <details>
            <summary>Procedure</summary>
            <p>{{ experiment.procedure }}</p>
          </details>
    
          <section v-if="videoSrc">
  <h3>Video</h3>
  <HlsPlayer :src="videoSrc" />
</section>

        </div>
      </div>
    </template>
    
      
      <style scoped>
      .experiment-detail {
  padding: 16px;
  max-width: 900px;
  margin: auto;
}

section {
  margin-top: 20px;
}

h1 {
  font-size: 22px;
}

h3 {
  font-size: 16px;
}
details {
  margin-top: 16px;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  padding: 10px;
}

summary {
  font-weight: 600;
  cursor: pointer;
}
/* 📱 Mobile */
@media (max-width: 640px) {
  h1 {
    font-size: 20px;
  }

  p {
    font-size: 14px;
    line-height: 1.6;
  }
}

      </style>
