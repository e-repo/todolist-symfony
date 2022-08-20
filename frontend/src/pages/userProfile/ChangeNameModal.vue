<template>
  <div>
    <bootstrap-modal
        :is-modal-show="isModalShow"
        @modalHide="modalHide"
    >
      <template #title>Change name</template>
      <template #body>
        <form>
          <div class="mb-3">
            <label for="new-email" class="form-label">First Name</label>
            <input type="text" class="form-control" v-model="userName.first">
          </div>
          <div class="mb-3">
            <label for="new-email" class="form-label">Last Name</label>
            <input type="text" class="form-control" v-model="userName.last">
          </div>
        </form>
      </template>
      <template #footer>
        <button
            type="button"
            class="btn btn-outline-secondary"
            @click="modalHide()"
        >
          <slot name="close-btn-name">Close</slot>
        </button>
        <button
            type="button"
            class="btn btn-success"
            @click="changeName()"
        >Save</button>
      </template>
    </bootstrap-modal>
  </div>
</template>

<script setup lang="ts">
  import BootstrapModal from "@/components/ui-kit/modal/BootstrapModal.vue";
  import {defineEmits, defineProps, reactive} from "vue";
  import { UserName } from "@/pages/userProfile/types";
  import {useCreateAuthHeader, usePutResource} from "@/components/composables";
  import { API_V1 } from "@/conf/api";
  import { useRoute, useRouter } from "vue-router";
  import { useAuthStore } from "@/store/auth";

  const authStore = useAuthStore()
  const router = useRouter()
  const route = useRoute()

  defineProps({
    isModalShow: { type: Boolean, default: false },
  })

  const emit = defineEmits(['modalHide', 'profileData'])

  const userName: UserName = reactive<UserName>({
    first: '',
    last: ''
  })

  const modalHide = () => emit('modalHide')
  const changeName = () => {
    let resource: Promise<any> = usePutResource(API_V1.PROFILE_CHANGE_NAME,
      {
        uuid: route.params.id,
        firstName: userName.first,
        lastName: userName.last
      },
      {
        headers: {
          Authorization: useCreateAuthHeader(authStore.token)
        },
        refreshTokenAction: authStore.tryRefreshToken,
        router,
      }
    )

    resource
        .then(response => {
          const profileData = response.data[0]?.attributes

          emit('profileData', profileData)
          modalHide()
        })
  }
</script>

<style scoped>

</style>