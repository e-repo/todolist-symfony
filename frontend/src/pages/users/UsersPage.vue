<template>
  <div
      class="content"
      :class="{'ms-0': !sidebarToggle}"
  >
    <app-navbar></app-navbar>
    <users-content></users-content>
    <app-footer></app-footer>
  </div>
</template>

<script>
import AppNavbar from "@/components/content/header/AppHeader";
import UsersContent from "@/pages/users/UsersContent";
import AppFooter from "@/components/content/footer/AppFooter";

export default {
  name: 'AppContent',
  data() {
    return {
      users: null
    }
  },
  components: { AppNavbar, UsersContent, AppFooter },
  props: {
    sidebarToggle: {
      type: Boolean
    }
  }
}
</script>

<style scoped>

</style>