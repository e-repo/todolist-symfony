<template>

    <div v-if="user.isAuth">
      <div class="wrapper">
        <app-sidebar @sidebar-toggle="changeSidebarToggle"></app-sidebar>
        <router-view :sidebarToggle="sidebarToggle"></router-view>
      </div>
    </div>
    <div v-else>
      <router-view></router-view>
    </div>

</template>

<script>
import AppSidebar from "@/components/sidebar/AppSidebar"
import { useAuthStore } from "@/store/auth"
import { storeToRefs } from 'pinia'

export default {
  name: 'App',
  components: { AppSidebar },
  setup() {
    const authStore = useAuthStore()
    const { user } = storeToRefs(authStore)

    return {
      user
    }
  },
  data() {
    return {
      sidebarToggle: true
    }
  },
  methods: {
    changeSidebarToggle: function (toggle) {
      this.sidebarToggle = toggle
    }
  },
  created() {
    const localStorage = window.localStorage
    const userFromLocalStorage = JSON.parse(localStorage.getItem('user'))

    if (null === this.user.token && userFromLocalStorage) {
      this.user.isAuth = true
      this.user.token = userFromLocalStorage.token
      this.user.refreshToken = userFromLocalStorage.refreshToken
    }
  }
}
</script>

