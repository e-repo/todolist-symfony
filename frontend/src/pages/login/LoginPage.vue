<template>
  <div class="wrapper">
    <form class="sign-in">
      <div
        v-if="loginForm.errorMessage"
        class="alert alert-danger d-flex justify-content-between"
      >
        <span>{{ loginForm.errorMessage }}</span>
        <button
          type="button"
          class="btn-close"
          @click="resetErrorMessage()"
        />
      </div>

      <div class="form-floating mb-3">
        <input
          id="email-input"
          v-model="loginForm.email"
          type="email"
          class="form-control"
          :class="{'is-invalid': loginForm.errorMessage || loginForm.isValidEmail === false}"
        >
        <label for="email-input">Email address</label>
      </div>
      <div class="form-floating">
        <input
          id="floatingPassword"
          v-model="loginForm.password"
          type="password"
          class="form-control"
          :class="{'is-invalid': loginForm.errorMessage || loginForm.isValidPassword === false}"
        >
        <label for="floatingPassword">Password</label>
      </div>

      <div class="mt-3 text-end">
        <button
          type="button"
          class="btn btn-outline-primary"
          @click="login()"
        >
          Sign in
        </button>
      </div>
    </form>
  </div>
</template>

<script setup lang="ts">

  import { reactive, watch } from "vue"
  import { LoginFormState } from "@/pages/login/types/LoginFormState"
  import { useEmailValidator, usePasswordValidator } from "@/pages/login/composables"
  import { useAuthStore } from "@/store/auth"
  import { useRouter } from "vue-router"
  import { storeToRefs } from "pinia"

  const authStore = useAuthStore()
  const router = useRouter()

  const { user, loginError } = storeToRefs(authStore)

  const loginForm = reactive<LoginFormState>({
    email: '',
    password: '',
    isValidEmail: true,
    isValidPassword: true,
    errorMessage: null
  })

  useEmailValidator(loginForm)
  usePasswordValidator(loginForm)

  const resetErrorMessage = (): void => {
    loginForm.errorMessage = null
  }

  const login = (): void => {
    authStore.login(loginForm.email, loginForm.password)
  }

  watch(loginError.value, (error) => {
    loginForm.errorMessage = null

    if (null !== error.message) {
      loginForm.errorMessage = error.data ? error.data.message : error.message
    }
  })

  watch(user.value, (user) => {
    if (true === user.isAuth) {
      router.push({ name: 'Home' })
    }
  })

</script>

<style scoped>
  .wrapper {
    display: flex;
    align-items: center;
    height: 100vh;
  }

  .sign-in {
    width: 100%;
    max-width: 400px;
    margin: auto;
  }
</style>
