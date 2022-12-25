<template>
  <div class="row mb-2">
    <div class="col-md-auto">
      <div class="cropper-wrap">
        <img
          ref="profileImage"
          class="profile-image"
          :src="profileImagePath"
          :alt="profileImageName"
        >
      </div>
    </div>
    <div
      v-if="images.length > 0"
      class="col"
    >
      <div class="col">
        <div class="d-flex">
          <div
            v-for="(image, index) in images"
            :key="index"
            class="card text-center ms-1 me-1 mb-1"
            style="width: 14rem;"
          >
            <img
              :src="getImagePath(image.filepath)"
              class="card-img-top"
              alt="User thumbnail"
            >
            <div class="card-body">
              <h6 class="card-title">
                {{ image.originalFilename }}
              </h6>
              <button
                type="button"
                class="btn btn-primary"
                @click="setImageToActive(image)"
              >
                To main
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="row mb-4">
    <div class="col">
      <div class="d-flex">
        <div class="me-1">
          <label
            class="btn btn-outline-dark"
            for="cropper-input"
          >
            <input
              id="cropper-input"
              ref="cropperInput"
              type="file"
              class="sr-only"
              accept="image/*"
              @change="changeCropperImage()"
            >
            <span class="me-1">Load photo</span>
            <font-awesome-icon icon="download" />
          </label>
        </div>
        <div>
          <button
            type="button"
            class="btn btn-outline-success"
            @click="uploadCropperImage()"
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
  import 'cropperjs/dist/cropper.css'
  import Cropper from 'cropperjs'

  import { onMounted, ref, defineProps, watch, defineEmits } from 'vue'
  import { useCreateAuthHeader, useGetResource, usePatchResource, usePostResource } from "@/components/composables"
  import { API } from "@/conf/api"
  import { useRoute, useRouter } from "vue-router"
  import { useAuthStore } from "@/store/auth"
  import { Image } from "@/pages/userProfile/types"
  import { useToAbsolutePath } from "@/components/composables/formatters"

  const props = defineProps({
    imagePath: { type: String, required: true }
  })

  const emit = defineEmits(['changedImageActive'])

  let cropper: Cropper|null = null

  const authStore = useAuthStore()
  const router = useRouter()
  const route = useRoute()

  const profileImageName = ref('Profile.png')
  const profileImagePath = ref(props.imagePath)
  const profileImage = ref()
  const cropperInput = ref()
  const images = ref<Image[]>([])

  const getImagePath = (relativePath: string): string => {
    return useToAbsolutePath(relativePath)
  }

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

  const setImageToActive = (image: Image) => {
    const url = API.V1.PROFILE_IMAGE_SET_ACTIVE
    const authHeader = useCreateAuthHeader(authStore.token)
    const data = {
      userUuid: route.params.id,
      imageUuid: image.uuid
    }
    const resource: Promise<any> = usePatchResource(url, data, authHeader, authStore.tryRefreshToken, router)

    resource
      .then(() => {
        emit('changedImageActive')
        initImageGallery()
      })
  }

  const uploadCropperImage = () => {
    if (null === cropper) {
      throw new Error('Cropper not initialized.')
    }

    cropper.getCroppedCanvas().toBlob((blob: Blob | null) => {
      if (null === blob) return

      const url = API.V1.PROFILE_IMAGE_UPLOAD
      const authHeader = useCreateAuthHeader(authStore.token)
      const formData = new FormData()

      formData.append('image', blob, profileImageName.value)
      formData.append('uuid', String(route.params.id))

      const resource: Promise<any> = usePostResource(url, formData, authHeader, authStore.tryRefreshToken, router)

      resource
        .then(() => initImageGallery())
    })
  }

  const initCropper = () => {
    cropper?.destroy()

    cropper = new Cropper(profileImage.value, {
      aspectRatio: 1,
    })
  }

  const initImageGallery = (): void => {
    const url = API.V1.USER_IMAGE_LIST
    const authHeader = useCreateAuthHeader(authStore.token)
    const data = {
      params: {
        uuid: route.params.id,
        onlyInactive: true
      }
    }
    const resource: Promise<any> = useGetResource(url, { ...authHeader, ...data }, authStore.tryRefreshToken, router)

    resource
      .then(response => {
        images.value = response.data
      })
  }

  onMounted(() => {
    initCropper()
    initImageGallery()
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
