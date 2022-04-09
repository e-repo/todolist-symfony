<template>
  <div class="wrapper">
    <app-sidebar @sidebar-toggle="changeSidebarToggle"></app-sidebar>
    <router-view :sidebarToggle="sidebarToggle"></router-view>
  </div>
</template>

<script>
import AppSidebar from "@/components/sidebar/AppSidebar";

export default {
  name: 'App',
  components: { AppSidebar },
  data() {
    return {
      sidebarToggle: true
    }
  },
  methods: {
    changeSidebarToggle: function (toggle) {
      this.sidebarToggle = toggle
    }
  }
}
</script>

