<template>
  <div
      class="content"
      :class="{'ms-0': !sidebarToggle}"
  >
    <app-navbar></app-navbar>
    <home-content></home-content>
    <app-footer></app-footer>
  </div>
</template>

<script setup lang="ts">
  import AppNavbar from "@/components/content/header/AppHeader.vue";
  import HomeContent from "@/pages/home/HomeContent.vue";
  import AppFooter from "@/components/content/footer/AppFooter.vue";
  import { defineProps } from "vue";

  defineProps({
    sidebarToggle: { type: Boolean }
  })

</script>

<style scoped>

</style>