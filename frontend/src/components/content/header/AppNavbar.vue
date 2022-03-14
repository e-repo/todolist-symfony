<template>
  <header class="header">
    <ul class="nav justify-content-end bg-light border-bottom">
      <li class="nav-item">
        <a class="nav-link link-secondary" href="#">Active</a>
      </li>
      <li class="nav-item">
        <a class="nav-link link-dark" href="#">Link</a>
      </li>
      <li class="nav-item">
        <a class="nav-link link-dark" href="#">Link</a>
      </li>
      <li class="nav-item position-relative">
        <button
          class="btn btn-link text-decoration-none shadow-none text-dark"
          type="button"
          @click="dropdownProfileToggle = ! dropdownProfileToggle"
        >
          Dropdown button
        </button>
        <ul
          class="dropdown-menu"
          :class="{'dropdown-menu__show-right': dropdownProfileToggle}"
        >
          <li><a class="dropdown-item" href="#">Action</a></li>
          <li><a class="dropdown-item" href="#">Another action</a></li>
          <li><a class="dropdown-item" href="#">Something else</a></li>
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
  }
}
</script>

<style scoped>

</style>