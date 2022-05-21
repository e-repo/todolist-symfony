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

<script>
import AppNavbar from "@/components/content/header/AppHeader";
import HomeContent from "@/pages/home/HomeContent";
import AppFooter from "@/components/content/footer/AppFooter";

export default {
  name: 'HomePage',
  components: { AppNavbar, HomeContent, AppFooter },
  props: {
    sidebarToggle: {
      type: Boolean
    }
  }
}
</script>

<style scoped>

</style>