<template>
  <div>
    <bootstrap-modal
      :is-modal-show="isModalShow"
      @modal-hide="modalHide"
    >
      <template #title>
        Change name
      </template>
      <template #body>
        <form>
          <div class="mb-3">
            <label
              for="new-email"
              class="form-label"
            >First Name</label>
            <div class="input-group has-validation">
              <input
                v-model="userNameForm.first.fieldValue"
                type="text"
                class="form-control"
                :class="{'is-invalid': ! userNameForm.first.isValid}"
              >
              <div class="invalid-feedback">
                {{ userNameForm.first.errorMessage }}
              </div>
            </div>
          </div>
          <div class="mb-3">
            <label
              for="new-email"
              class="form-label"
            >Last Name</label>
            <div class="input-group has-validation">
              <input
                v-model="userNameForm.last.fieldValue"
                type="text"
                class="form-control"
                :class="{'is-invalid': ! userNameForm.last.isValid}"
              >
              <div class="invalid-feedback">
                {{ userNameForm.last.errorMessage }}
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
          @click="changeName()"
        >
          Save
        </button>
      </template>
    </bootstrap-modal>
  </div>
</template>

<script setup lang="ts">
  import BootstrapModal from "@/components/ui-kit/modal/BootstrapModal.vue"
  import { defineEmits, defineProps, PropType, reactive, watch } from "vue"
  import { UserNameForm, UserProfile } from "@/pages/userProfile/types"
  import { useCreateAuthHeader, usePutResource } from "@/components/composables"
  import { API } from "@/conf/api"
  import { useRoute, useRouter } from "vue-router"
  import { useAuthStore } from "@/store/auth"
  import { useNameFormValidator } from "@/pages/userProfile/composables"

  const authStore = useAuthStore()
  const router = useRouter()
  const route = useRoute()

  const props = defineProps({
    isModalShow: { type: Boolean, default: false },
    modelValue: { type: Object as PropType<UserProfile>, required: true }
  })

  const emit = defineEmits(['modalHide', 'update:modelValue'])

  const userNameForm: UserNameForm = reactive<UserNameForm>({
    first: {
      fieldValue: '',
      isValid: true
    },
    last: {
      fieldValue: '',
      isValid: true
    }
  })

  useNameFormValidator(userNameForm)

  watch(props.modelValue, (profileData: UserProfile) => {
    userNameForm.first.fieldValue = profileData.name.split(' ')[0].trim()
    userNameForm.last.fieldValue = profileData.name.split(' ')[1].trim()
  })

  const modalHide = () => emit('modalHide')
  const changeName = (): void => {
    for (const field in userNameForm) {
      if (! userNameForm[field as keyof UserNameForm].isValid) {
        return
      }
    }

    const url = API.V1.PROFILE_CHANGE_NAME
    const data = {
      uuid: route.params.id,
      firstName: userNameForm.first.fieldValue,
      lastName: userNameForm.last.fieldValue
    }
    const authHeader = useCreateAuthHeader(authStore.token)
    const resource: Promise<any> = usePutResource(url, data, authHeader, authStore.tryRefreshToken, router)

    resource
      .then(response => {
        const profile = response.data[0]?.attributes

        emit('update:modelValue', profile)
        modalHide()
      })
  }
</script>

<style scoped>

</style>
