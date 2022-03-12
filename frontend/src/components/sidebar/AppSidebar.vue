<template>
  <aside class="l-sidebar">
    <div class="container-fluid">
      <div class="logo">
        <a class="logo__link text-decoration-none text-warning" href="#">
          <font-awesome-icon class="me-2" icon="tractor" />
          Evening production
        </a>
      </div>
      <sidebar-nav></sidebar-nav>
    </div>
  </aside>
</template>

<script>
import SidebarNav from "@/components/sidebar/navigation/SidebarNav";

export default {
  name: 'AppSidebar',
  components: { SidebarNav }
}
</script>

<style scoped>

</style>