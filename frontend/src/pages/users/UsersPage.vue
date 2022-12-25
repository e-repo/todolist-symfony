<template>
  <div
    class="content"
    :class="{'ms-0': !sidebarToggle}"
  >
    <app-navbar />
    <users-content />
    <app-footer />
  </div>
</template>

<script setup lang="ts">
  import AppNavbar from "@/components/content/header/AppHeader.vue"
  import UsersContent from "@/pages/users/UsersContent.vue"
  import AppFooter from "@/components/content/footer/AppFooter.vue"
  import { defineProps } from "vue"

  defineProps({
    sidebarToggle: { type: Boolean }
  })
</script>

<style scoped>

</style>
