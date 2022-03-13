<template>
  <div class="wrapper">
    <app-sidebar @sidebar-toggle="changeSidebarToggle"></app-sidebar>
    <app-content :sidebarToggle="sidebarToggle"></app-content>
  </div>
</template>

<script>
import AppSidebar from "@/components/sidebar/AppSidebar";
import AppContent from "@/components/content/AppContent";

export default {
  name: 'App',
  components: { AppSidebar, AppContent },
  data() {
    return {
      sidebarToggle: true
    }
  },
  methods: {
    changeSidebarToggle: function (toggle) {
      this.sidebarToggle = toggle;
    }
  }
}
</script>

