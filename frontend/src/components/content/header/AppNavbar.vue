<template>
  <header class="header">
    <ul class="nav justify-content-end bg-light border-bottom">
      <li class="nav-item">
        <a class="nav-link active" aria-current="page" href="#">Active</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="#">Link</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="#">Link</a>
      </li>
      <li class="nav-item position-relative">
        <button
          class="btn btn-link text-decoration-none shadow-none"
          type="button"
          @click="dropdownProfileToggle = ! dropdownProfileToggle"
        >
          Dropdown button
        </button>
        <ul
          class="dropdown-menu"
          :class="{'dropdown-profile__show': dropdownProfileToggle}"
        >
          <li><a class="dropdown-item" href="#">Action</a></li>
          <li><a class="dropdown-item" href="#">Another action</a></li>
          <li><a class="dropdown-item" href="#">Something else here</a></li>
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
  .dropdown-profile__show {
    display: block;
    position: absolute;
    inset: 0 auto auto 0;
    margin: 0;
    transform: translate(-26px, 40px);
  }
</style>