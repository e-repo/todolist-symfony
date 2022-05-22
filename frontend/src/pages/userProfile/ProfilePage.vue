<template>
  <div
      class="content"
      :class="{'ms-0': !sidebarToggle}"
  >
    <app-navbar></app-navbar>
    <profile-content></profile-content>
    <app-footer></app-footer>
  </div>
</template>

<script>
import AppNavbar from "@/components/content/header/AppHeader";
import AppFooter from "@/components/content/footer/AppFooter";
import ProfileContent from "@/pages/userProfile/ProfileContent";

export default {
  name: 'ProfilePage',
  components: { AppNavbar, ProfileContent, AppFooter },
  props: {
    sidebarToggle: {
      type: Boolean
    }
  }
}
</script>

<style scoped>

</style>