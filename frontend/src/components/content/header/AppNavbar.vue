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
          <li><a class="dropdown-item" href="#">Выйти</a></li>
        </ul>
      </li>
    </ul>
  </header>
</template>

<script>
export default {
  data() {
    return {
      dropdownProfileToggle: false
    }
  },
  methods: {
    onClickOutside: function () {
      this.dropdownProfileToggle = ! this.dropdownProfileToggle
    }
  }
}
</script>

<style scoped>

</style>