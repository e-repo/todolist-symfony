<template>
  <div class="wrapper">
    <app-sidebar></app-sidebar>
    <app-content></app-content>
  </div>
</template>

<script>
import AppSidebar from "@/components/sidebar/AppSidebar";
import AppContent from "@/components/content/AppContent";

export default {
  name: 'App',
  components: { AppSidebar, AppContent }
}
</script>

