<template>
  <header class="header">
    <ul class="nav justify-content-end bg-light border-bottom">
      <li v-click-outside="onClickOutside" class="nav-item position-relative">
        <button
          class="btn btn-link text-decoration-none shadow-none text-dark"
          type="button"
          @click="dropdownProfileToggle = ! dropdownProfileToggle"
        >
          Админ Админов
        </button>
        <ul
          class="dropdown-menu"
          :class="{'dropdown-menu__show-right': dropdownProfileToggle}"
        >
          <li><a class="dropdown-item" href="#">Профиль пользователя</a></li>
          <li>
            <a
              class="dropdown-item"
              href="#"
              @click.prevent="logout()"
            >
              Выйти
            </a>
          </li>
        </ul>
      </li>
    </ul>
  </header>
</template>

<script>
import { useAuthStore } from "@/store/auth"

export default {
  setup() {
    const authStore = useAuthStore()

    return {
      authStore
    }
  },
  data() {
    return {
      dropdownProfileToggle: false
    }
  },
  methods: {
    onClickOutside: function () {
      if (true === this.dropdownProfileToggle) {
        this.dropdownProfileToggle = ! this.dropdownProfileToggle
      }
    },
    logout: function () {
      this.authStore.logout()
      this.$router.push({name: 'Login'})
    }
  }
}
</script>

<style scoped>

</style>