<template>
  <div>
    <bootstrap-modal
      :is-modal-show="isModalShow"
      @modalHide="modalHide"
    >
      <template #title>
        Change email
      </template>
      <template #body>
        <form>
          <div class="mb-3">
            <label
              for="new-email"
              class="form-label"
            >Email address</label>
            <div class="input-group has-validation">
              <input
                v-model="emailForm.email.fieldValue"
                type="email"
                class="form-control"
                :class="{'is-invalid': ! emailForm.email.isValid}"
              >
              <div class="invalid-feedback">
                {{ emailForm.email.errorMessage }}
              </div>
            </div>
          </div>
        </form>
      </template>
      <template #footer>
        <button
          type="button"
          class="btn btn-outline-secondary"
          @click="modalHide()"
        >
          <slot name="close-btn-name">
            Close
          </slot>
        </button>
        <button
          type="button"
          class="btn btn-success"
          @click="changeEmail()"
        >
          Save
        </button>
      </template>
    </bootstrap-modal>
  </div>
</template>

<script setup lang="ts">
  import BootstrapModal from "@/components/ui-kit/modal/BootstrapModal.vue"
  import { useAuthStore } from "@/store/auth"
  import { useRoute, useRouter } from "vue-router"
  import { defineEmits, defineProps, PropType, reactive, watch } from "vue"
  import { ChangingEmailForm, UserProfile } from "@/pages/userProfile/types"
  import { useCreateAuthHeader, usePatchResource } from "@/components/composables"
  import { API } from "@/conf/api"
  import { useEmailFormValidator } from "@/pages/userProfile/composables"

  const authStore = useAuthStore()
  const router = useRouter()
  const route = useRoute()

  const props = defineProps({
    isModalShow: { type: Boolean, default: false },
    userProfile: { type: Object as PropType<UserProfile>, default: null }
  })

  const emit = defineEmits(['modalHide', 'emailChangeMessage'])
  const modalHide = () => emit('modalHide')

  const emailForm: ChangingEmailForm = reactive<ChangingEmailForm>({
    email: {
      fieldValue: '',
      isValid: true
    }
  })

  useEmailFormValidator(emailForm)

  watch(props.userProfile, (userProfile: UserProfile | null) => {
    if (null !== userProfile) {
      emailForm.email.fieldValue = userProfile.email
    }
  })

  const changeEmail = () => {
    for (const field in emailForm) {
      if (! emailForm[field as keyof ChangingEmailForm].isValid) {
        return
      }
    }

    const url = API.V1.PROFILE_CHANGE_EMAIL
    const data = {
      uuid: route.params.id,
      email: emailForm.email.fieldValue
    }
    const authHeader = useCreateAuthHeader(authStore.token)
    const resource: Promise<any> = usePatchResource(url, data, authHeader, authStore.tryRefreshToken, router)

    resource
      .then(response => {
        const responseAttributes = response.data[0]?.attributes

        emit('emailChangeMessage', responseAttributes.message)
        modalHide()
      })
  }
</script>
