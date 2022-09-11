<template>
  <div class="row mb-2">
    <div class="col-md-auto">

      <div class="cropper-wrap">
        <img
          class="profile-image"
          ref="profileImage"
          :src="profileImagePath"
          :alt="profileImageName"
        >
      </div>

    </div>
    <div class="col text-center">
      Список изображений профиля пользователя.
    </div>
  </div>
  <div class="row mb-4">
    <div class="col">

      <div class="d-flex">
        <div class="me-1">
          <label class="btn btn-outline-dark" for="cropper-input">
            <input
              @change="changeCropperImage()"
              ref="cropperInput"
              type="file"
              id="cropper-input"
              class="sr-only"
              accept="image/*"
            >
            <span class="me-1">Load photo</span>
            <font-awesome-icon icon="download" />
          </label>
        </div>
        <div>
          <button
            @click="uploadCropperImage()"
            type="button"
            class="btn btn-outline-success"
          >
            <span class="me-1">Save</span>
            <font-awesome-icon icon="upload" />
          </button>
        </div>
      </div>

    </div>
  </div>
</template>

<script setup lang="ts">
  import 'cropperjs/dist/cropper.css';
  import Cropper from 'cropperjs';

  import { onMounted, ref, defineProps, watch } from 'vue'
  import { useCreateAuthHeader, usePostResource } from "@/components/composables";
  import { API_V1 } from "@/conf/api";
  import { useRoute, useRouter } from "vue-router";
  import { useAuthStore } from "@/store/auth";

  const props = defineProps({
    imagePath: { type: String, required: true }
  })

  let cropper: Cropper|null = null

  const authStore = useAuthStore()
  const router = useRouter()
  const route = useRoute()

  const profileImageName = ref('Profile.png')
  const profileImagePath = ref(props.imagePath)
  const profileImage = ref()
  const cropperInput = ref()

  watch(() => props.imagePath, (path) => {
    if (path) {
      const url = new URL(path)

      profileImagePath.value = url.pathname
      profileImage.value.src = url.pathname

      initCropper()
    }
  })

  const changeCropperImage = () => {
    const inputImage = cropperInput.value.files[0]

    if (! (inputImage instanceof File)) return

    profileImage.value.src = URL.createObjectURL(inputImage)
    profileImageName.value = inputImage.name

    initCropper()
  }

  const uploadCropperImage = () => {
    if (null === cropper) {
      throw new Error('Cropper not initialized.')
    }

    cropper.getCroppedCanvas().toBlob((blob: Blob | null) => {
      if (null === blob) return

      const formData = new FormData()
      formData.append('image', blob, profileImageName.value)
      formData.append('uuid', String(route.params.id))

      usePostResource(API_V1.PROFILE_IMAGE_UPLOAD,
        formData,
        {
          headers: {
            Authorization: useCreateAuthHeader(authStore.token),
            'Content-Type': 'multipart/form-data'
          }
        },
        authStore.tryRefreshToken,
        router
      )
    })
  }

  const initCropper = () => {
    cropper?.destroy()

    cropper = new Cropper(profileImage.value, {
      aspectRatio: 1,
    })
  }

  onMounted(() => {
    initCropper()
  })
</script>

<style scoped>
  .cropper-wrap {
    max-width: 600px;
  }

  .profile-image {
    height: 400px;
  }
</style>