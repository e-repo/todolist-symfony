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
      <sidebar-nav :sidebarMenu="sidebarMenu"></sidebar-nav>
    </div>
  </aside>
</template>

<script>
import SidebarNav from "@/components/sidebar/navigation/SidebarNav"
import axios from 'axios'
import { useAuthStore } from "@/store/auth"
import { storeToRefs } from "pinia"

export default {
  name: 'AppSidebar',
  components: { SidebarNav },
  setup() {
    const authStore = useAuthStore()
    const { user } = storeToRefs(authStore)

    return {
      user,
      authStore,
    }
  },
  data() {
    return {
      toggle: true,
      sidebarMenu: null,
    }
  },
  methods: {
    emitSidebarToggle: function () {
      this.toggle = ! this.toggle;
      this.$emit('sidebarToggle', this.toggle);
    },
    loadSidebarMenu: function () {
      if (true === this.user.isAuth) {
        axios
            .get('/api/v1/sidebar-menu', {
              headers: {
                Authorization: `Bearer ${this.user.token}`
              }
            }).then(response => {
              this.sidebarMenu = response.data
            }).catch((error) => {
              this.authStore.tryRefreshToken(error, this.$router)
            })
      }
    }
  },
  mounted() {
    this.loadSidebarMenu()
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