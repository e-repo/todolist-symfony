<template>
  <main class="main pt-3 pb-3">
    <div class="container-fluid">

      <div class="row">
        <div class="col-6">

          <div class="card" v-if="profile">

            <div class="card-header">
              <div class="fw-bold">Profile:</div>
            </div>

            <div class="card-body">
              <table class="table table-hover table-bordered mb-0">

                <tbody>

                  <tr>
                    <td>
                      Name:
                    </td>
                    <td>
                      {{ profile.name }}
                    </td>
                  </tr>

                  <tr>
                    <td>
                      Email:
                    </td>
                    <td>
                      {{ profile.email }}
                    </td>
                  </tr>

                  <tr>
                    <td>
                      Created at:
                    </td>
                    <td>
                      {{ profile.createdAt }}
                    </td>
                  </tr>

                  <tr>
                    <td>
                      Role:
                    </td>
                    <td>
                      {{ profile.role }}
                    </td>
                  </tr>

                  <tr>
                    <td>
                      Status:
                    </td>
                    <td>
                      {{ profile.status }}
                    </td>
                  </tr>

                </tbody>

              </table>
            </div>

          </div>

        </div>
      </div>

    </div>
  </main>
</template>

<script>
import axios from "axios";
import { useAuthStore } from "@/store/auth"
import { API_V1 } from "@/conf/api";

export default {
  name: "ProfileContent",
  setup() {
    const authStore = useAuthStore()

    return {
      authStore,
    }
  },
  data() {
    return {
      profile: null,
    }
  },
  methods: {
    loadProfile: function () {
      axios
          .get(API_V1.USER_PROFILE(this.$route.params.id), {
            headers: {
              Authorization: `Bearer ${this.authStore.user.token}`
            },
          }).then(response => {
            const userData = response.data.data

            this.profile = userData[0].attributes
          }).catch((error) => {
            this.authStore.tryRefreshToken(error, this.$router)
          })
    }
  },
  mounted() {
    if (this.authStore.user.isAuth) {
      this.loadProfile()
    }
  }
}
</script>

<style scoped>

</style>