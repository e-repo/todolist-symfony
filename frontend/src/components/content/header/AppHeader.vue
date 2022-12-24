<template>
  <header class="header">
    <ul class="nav justify-content-end bg-light border-bottom">
      <li v-if="route.name === 'TaskPublishedPage'">
        <button
          @click="addingTaskModalToggle = !addingTaskModalToggle"
          class="btn btn-link text-decoration-none shadow-none text-dark"
        >
          <font-awesome-icon icon="plus" class="me-1" /> Add task
        </button>
      </li>
      <li v-click-outside="onClickOutside" class="nav-item position-relative">
        <button
          class="btn btn-link text-decoration-none shadow-none text-dark"
          type="button"
          @click="dropdownProfileToggle = ! dropdownProfileToggle"
        >
          {{ this.username() }}
        </button>
        <ul
          class="dropdown-menu"
          :class="{'dropdown-menu__show-right': dropdownProfileToggle}"
        >
          <li>
            <a
              class="dropdown-item"
              href="#"
              @click.prevent="toProfilePage()"
            >
                User profile
            </a>
          </li>
          <li>
            <a
              class="dropdown-item"
              href="#"
              @click.prevent="logout()"
            >
              Logout
            </a>
          </li>
        </ul>
      </li>
    </ul>
  </header>
</template>

<script setup lang="ts">
  import { useAuthStore } from "@/store/auth"
  import { ref } from "vue"
  import { useRouter, useRoute } from "vue-router"

  const authStore = useAuthStore()
  const router = useRouter()
  const route = useRoute()

  const addingTaskModalToggle = ref<boolean>(false)
  const dropdownProfileToggle = ref<boolean>(false)

  console.log(addingTaskModalToggle.value)

  const onClickOutside = (): void => {
    if (true === dropdownProfileToggle.value) {
      dropdownProfileToggle.value = ! dropdownProfileToggle.value
    }
  }

  const logout = (): void => {
    authStore.logout()
    router.push({name: 'Login'})
  }

  const username = (): string => {
    return null !== authStore.findUserFromToken
        ? `${authStore.findUserFromToken.first} ${authStore.findUserFromToken.last}`
        : '';
  }

  const toProfilePage = (): Promise<any> => {
    return router.push({path: `/profile/${authStore.findUserFromToken.id}`})
  }
</script>

<style scoped>

</style>
