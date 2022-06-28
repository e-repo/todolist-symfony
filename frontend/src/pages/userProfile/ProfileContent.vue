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

<script setup lang="ts">
  import { useAuthStore } from "@/store/auth"
  import { API_V1 } from "@/conf/api";
  import { onMounted, reactive } from "vue";
  import { UserProfile } from "@/pages/userProfile/types";
  import { useRouter, useRoute } from "vue-router";
  import {useCreateAuthHeader, useGetResource} from "@/components/composables";

  const authStore = useAuthStore()
  const router = useRouter()
  const route = useRoute()

  const profile = reactive<UserProfile>({
    name: '',
    email: '',
    createdAt: '',
    role: '',
    status: '',
  })

  const loadProfile = (): void => {
    const profileUrl = API_V1.USER_PROFILE(route.params.id as string)

    let resource: Promise<any> = useGetResource(profileUrl, {
      headers: {
        Authorization: useCreateAuthHeader(authStore.token)
      },
      refreshTokenAction: authStore.tryRefreshToken,
      router,
    })

    resource
      .then(response => {
        const profileData: UserProfile = response.data[0]?.attributes

        if (profileData) {
          Object.keys(profileData).forEach(key => {
            profile[key as keyof UserProfile] = profileData[key as keyof UserProfile]
          })
        }
      })
  }

  onMounted(() =>{
    if (authStore.isAuth) {
      loadProfile()
    }
  })

</script>

<style scoped>

</style>