<template>
  <div class="wrapper">
    <form class="sign-in">

      <div
          class="alert alert-danger d-flex justify-content-between"
          v-if="errorMessage"
      >
        <span>{{ errorMessage }}</span>
        <button type="button" class="btn-close" @click="resetErrorMessage()"></button>
      </div>

      <div class="form-floating mb-3">
        <input
          type="email"
          class="form-control"
          id="email-input"
          v-model="email"
          :class="{'is-invalid': errorMessage || isValidEmail === false}"
        >
        <label for="email-input">Email address</label>
      </div>
      <div class="form-floating">
        <input
          type="password"
          class="form-control"
          id="floatingPassword"
          v-model="password"
          :class="{'is-invalid': errorMessage || isValidPassword === false}"
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

<script lang="ts">
import { toRefs, watch, defineComponent } from "vue"

import LoginFormValidator from "@/pages/login/LoginFormValidator";
import LoginPage from "@/pages/login/LoginPage";

export default defineComponent({
  name: 'LoginPage',
  setup() {
    const loginPage = new LoginPage()
    const loginFormState = loginPage.getLoginFormState()
    const validator = new LoginFormValidator(loginFormState)

    watch(() => loginFormState.email, (email) => {
      validator.checkEmail(email)
    })

    watch(() => loginFormState.password, (password) => {
      validator.checkPassword(password)
    })

    return {
      ...toRefs(loginFormState),
      login: loginPage.login(),
      resetErrorMessage: loginPage.resetErrorMessage(),
    }
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