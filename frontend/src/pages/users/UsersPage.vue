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

<script lang="ts">
import AppNavbar from "@/components/content/header/AppHeader.vue";
import UsersContent from "@/pages/users/UsersContent.vue";
import AppFooter from "@/components/content/footer/AppFooter.vue";
import { defineComponent } from "vue";

export default defineComponent({
  name: 'UserPage',
  components: { AppNavbar, UsersContent, AppFooter },
  props: {
    sidebarToggle: {
      type: Boolean
    }
  }
})
</script>

<style scoped>

</style>