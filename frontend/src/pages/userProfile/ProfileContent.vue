<template>
  <main class="main pt-3 pb-3">
    <div class="container-fluid">

      <div class="row">
        <div class="col">
          <bootstrap-alert
            :message="alertMessage"
            :is-alert-show="isAlertShow"
            @alertHide="alertHide"
          ></bootstrap-alert>
        </div>
      </div>

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
                    <td class="text-center">
                      <button
                        type="button"
                        class="btn btn-outline-primary"
                        @click="changeNameModalShow = ! changeNameModalShow"
                      >
                        <font-awesome-icon icon="pen" />
                      </button>
                    </td>
                  </tr>

                  <tr>
                    <td>
                      Email:
                    </td>
                    <td>
                      {{ profile.email }}
                    </td>
                    <td class="text-center">
                      <button
                        type="button"
                        class="btn btn-outline-primary"
                        @click="changeEmailModalShow = ! changeEmailModalShow"
                      >
                        <font-awesome-icon icon="pen" />
                      </button>
                    </td>
                  </tr>

                  <tr>
                    <td>
                      Created at:
                    </td>
                    <td>
                      {{ createdAt }}
                    </td>
                    <td>
                    </td>
                  </tr>

                  <tr>
                    <td>
                      Role:
                    </td>
                    <td>
                      {{ profile.role }}
                    </td>
                    <td>
                    </td>
                  </tr>

                  <tr>
                    <td>
                      Status:
                    </td>
                    <td>
                      {{ profile.status }}
                    </td>
                    <td>
                    </td>
                  </tr>

                </tbody>

              </table>
            </div>

          </div>

        </div>
      </div>

      <change-email-modal
          :is-modal-show="changeEmailModalShow"
          @modalHide="modalsHide"
          @emailChangeMessage="emailChangeMessage"
      ></change-email-modal>

      <change-name-modal
        :is-modal-show="changeNameModalShow"
        v-model="profile"
        @modalHide="modalsHide"
      ></change-name-modal>

    </div>
  </main>
</template>

<script setup lang="ts">
  import BootstrapAlert from '@/components/ui-kit/alert/BootstrapAlert.vue'
  import ChangeNameModal from '@/pages/userProfile/ChangeNameModal.vue'
  import ChangeEmailModal from '@/pages/userProfile/ChangeEmailModal.vue'
  import moment from "moment";
  import { useAuthStore } from "@/store/auth"
  import { API_V1 } from "@/conf/api";
  import { onMounted, reactive, ref, computed } from "vue";
  import { useRouter, useRoute } from "vue-router";
  import { useCreateAuthHeader, useGetResource } from "@/components/composables";
  import { UserProfile } from "@/pages/userProfile/types";

  const authStore = useAuthStore()
  const router = useRouter()
  const route = useRoute()

  const profile: UserProfile = reactive<UserProfile>({
    name: '',
    email: '',
    createdAt: null,
    role: '',
    status: '',
  })

  const alertMessage = ref<string>('')
  const isAlertShow = ref<boolean>(false)
  const alertHide = () => isAlertShow.value = false

  const changeEmailModalShow = ref<boolean>(false)
  const changeNameModalShow = ref<boolean>(false)
  const modalsHide = () => {
    changeEmailModalShow.value = false
    changeNameModalShow.value = false
  }

  const createdAt = computed(
      () => profile.createdAt ? moment.unix(profile.createdAt).format('DD.MM.YYYY') : ''
  )

  function emailChangeMessage(message: string): void
  {
    if ('' !== message) {
      alertMessage.value = message
      isAlertShow.value = true
    }
  }

  function setProfileData(profileData: UserProfile): void {
    profile.name = profileData.name
    profile.email = profileData.email
    profile.createdAt = profileData.createdAt
    profile.role = profileData.role
    profile.status = profileData.status
  }

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
        const profileData = response.data[0]?.attributes

        setProfileData(profileData)
      })
  }

  onMounted(() => {
    if (authStore.isAuth) {
      loadProfile()
    }
  })

</script>

<style scoped>

</style>