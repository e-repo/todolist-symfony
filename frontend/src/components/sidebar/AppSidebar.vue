<template>
  <aside
    class="l-sidebar sidebar-open"
    :class="{'sidebar-close': ! toggle}"
  >
    <div
      class="l-sidebar_control cursor-pointer"
      @click="emitSidebarToggle"
    >
      <font-awesome-icon v-if="toggle" icon="xmark" />
      <font-awesome-icon v-else icon="bars" />
    </div>
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
  components: { SidebarNav },
  data() {
    return {
      toggle: true
    }
  },
  methods: {
    emitSidebarToggle: function () {
      this.toggle = ! this.toggle;
      this.$emit('sidebarToggle', this.toggle)
    }
  }
}
</script>

<style scoped>
  .sidebar-open {
    transform: translateX(0px);
    transition: all .3s ease;
  }

  .sidebar-close {
    transform: translateX(-250px);
    transition: all .3s ease;
  }
</style>