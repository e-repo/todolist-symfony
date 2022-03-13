<template>
  <div
      class="content"
      :class="{'ms-0': !sidebarToggle}"
  >
    <app-navbar></app-navbar>
    <app-main></app-main>
    <app-footer></app-footer>
  </div>
</template>

<script>
import AppNavbar from "@/components/content/header/AppNavbar";
import AppMain from "@/components/content/main/AppMain";
import AppFooter from "@/components/content/footer/AppFooter";

export default {
  name: 'AppContent',
  components: { AppNavbar, AppMain, AppFooter },
  props: {
    sidebarToggle: {
      type: Boolean
    }
  }
}
</script>

<style scoped>

</style>