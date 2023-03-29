<script setup lang="ts">
import { RouterLink, RouterView } from "vue-router";
</script>

<template>
  <nav class="relative flex flex-wrap items-center justify-between px-2 py-3 bg-green-400 mb-3">
    <div class="container px-4 mx-auto flex flex-wrap items-center justify-between">
      <div class="w-full relative flex justify-between lg:w-auto  px-4 lg:static lg:block lg:justify-start">
        <a class="text-sm font-bold leading-relaxed inline-block mr-4 py-2 whitespace-nowrap uppercase text-white">
          <RouterLink to="/">Home</RouterLink>
        </a>
      </div>
    </div>
  </nav>

  <div class="container mx-auto">
    <RouterView />
  </div>
</template>

<style scoped>

</style>
