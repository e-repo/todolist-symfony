<template>
  <div>
    <bootstrap-modal
        :is-modal-show="isModalShow"
        @modalHide="modalHide"
    >
      <template #title>Change email</template>
      <template #body>
        <form>
          <div class="mb-3">
            <label for="new-email" class="form-label">Email address</label>
            <input type="email" class="form-control" id="new-email" v-model="emailChangingForm.email">
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
            @click="changeEmail()"
        >Save</button>
      </template>
    </bootstrap-modal>
  </div>
</template>

<script setup lang="ts">
  import BootstrapModal from "@/components/ui-kit/modal/BootstrapModal.vue";
  import { useAuthStore } from "@/store/auth";
  import { useRoute, useRouter } from "vue-router";
  import { defineEmits, defineProps, reactive } from "vue";
  import { ChangingEmailForm } from "@/pages/userProfile/types";
  import {useCreateAuthHeader, usePatchResource} from "@/components/composables";
  import { API_V1 } from "@/conf/api";

  const authStore = useAuthStore()
  const router = useRouter()
  const route = useRoute()

  defineProps({
    isModalShow: { type: Boolean, default: false },
  })

  const emit = defineEmits(['modalHide', 'emailChangeMessage'])
  const modalHide = () => emit('modalHide')

  const emailChangingForm: ChangingEmailForm = reactive<ChangingEmailForm>({
    email: ''
  })

  const changeEmail = () => {
    let resource: Promise<any> = usePatchResource(API_V1.PROFILE_CHANGE_EMAIL,
        {
          uuid: route.params.id,
          email: emailChangingForm.email
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
          const responseAttributes = response.data[0]?.attributes

          emit('emailChangeMessage', responseAttributes.message)
          modalHide()
        })
  }
</script>