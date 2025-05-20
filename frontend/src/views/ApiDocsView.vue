<template>
  <div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div id="swagger-ui"></div>
    </div>
  </div>
</template>

<script setup>
import { onMounted } from 'vue'
import SwaggerUI from 'swagger-ui'
import 'swagger-ui/dist/swagger-ui.css'
import swaggerSpec from '@/docs/swagger.json'

onMounted(() => {
  SwaggerUI({
    spec: swaggerSpec,
    dom_id: '#swagger-ui',
    deepLinking: true,
    presets: [
      SwaggerUI.presets.apis,
      SwaggerUI.SwaggerUIStandalonePreset
    ],
    layout: 'BaseLayout',
    supportedSubmitMethods: ['get', 'post', 'put', 'delete', 'patch'],
    docExpansion: 'list',
    defaultModelsExpandDepth: 3,
    defaultModelExpandDepth: 3,
    defaultModelRendering: 'model',
    displayRequestDuration: true,
    filter: true,
    showExtensions: true,
    showCommonExtensions: true,
    syntaxHighlight: {
      activated: true,
      theme: 'monokai'
    }
  })
})
</script>

<style>
.swagger-ui .topbar {
  display: none;
}
</style> 