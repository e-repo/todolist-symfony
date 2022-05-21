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

<script>
import { useAuthStore } from "@/store/auth"
import { storeToRefs } from "pinia"
import { watch } from "vue";

export default {
  name: 'LoginPage',
  setup() {
    const authStore = useAuthStore()
    const { user, loginError } = storeToRefs(authStore)

    return {
      user,
      loginError,
      authStore
    }
  },
  data() {
    return {
      email: null,
      isValidEmail: null,
      password: null,
      isValidPassword: null,
      errorMessage: null
    }
  },
  methods: {
    login: function () {
      this.authStore.login(this.email, this.password)
    },
    resetErrorMessage: function () {
      this.errorMessage = null
    },
    emailValidator: function (email) {
      const mailFormat = /^\w+([.-]?\w+)*@\w+([.-]?\w+)*(.\w{2,3})+$/;

      if (email.length < 5) {
        this.isValidEmail = false
        return;
      }

      if (null === email.match(mailFormat)) {
        this.isValidEmail = false
        return;
      }

      this.isValidEmail = true
    },
    passwordValidator: function (password) {
      if (password.length < 6) {
        this.isValidPassword = false
        return;
      }

      this.isValidPassword = true
    }
  },
  watch: {
    email(email) {
      this.emailValidator(email)
    },
    password(password) {
      this.passwordValidator(password)
    }
  },
  mounted() {
    watch(this.loginError, (error) => {
      this.errorMessage = null

      if (null !== error.message) {
        this.errorMessage = error.data ? error.data.message : error.message
      }
    })
    watch(this.user, (user) => {
      if (true === user.isAuth) {
        this.$router.push({name: 'Home'})
      }
    })
  }
}
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