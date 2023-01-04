<template>
  <main class="main pt-3 pb-3">
    <div class="container-fluid">
      <div
        v-if="tasks === null"
        class="row"
      >
        <h4 class="text-center">
          Task not found.
        </h4>
      </div>
      <div
        v-else-if="tasks.length > 0"
        class="row"
      >
        <div
          v-for="(task, index) in tasks"
          :key="index"
          class="col-3 mb-4"
        >
          <div class="card">
            <div class="card-header text-success">
              <h5 class="mb-0 font-weight-bold">
                {{ task.name }}
              </h5>
            </div>
            <div class="card-body text-white">
              <p class="card-text">
                <b>{{ task.description }}</b>
              </p>
              <div class="text-sm-end">
                {{ task.date }}
              </div>
            </div>
            <div class="card-footer d-flex justify-content-end">
              <button
                class="btn btn-outline-danger m-1 js-fulfilled-task"
                @click="deleteTask(task.id)"
              >
                <font-awesome-icon icon="trash" />
              </button>
            </div>
          </div>
        </div>
      </div>
      <app-preloader v-else />
    </div>
  </main>
</template>

<script setup lang="ts">
import AppPreloader from "@/components/ui-kit/preloader/AppPreloader.vue"
import { useAuthStore } from "@/store/auth"
import { useRouter } from "vue-router"
import { onMounted, ref } from "vue"
import { useCreateAuthHeader, useDeleteResource, useGetResource } from "@/components/composables"
import { API } from "@/conf/api"
import { Task } from "@/pages/task/types"

const authStore = useAuthStore()
const router = useRouter()

const tasks = ref<Task[] | null>([])

const deleteTask = (taskId: string): void => {
  const taskDeleteUrl = API.V1.TASK_DELETE(taskId)
  const header = useCreateAuthHeader(authStore.token)

  const resource: Promise<any> = useDeleteResource(taskDeleteUrl, header, authStore.tryRefreshToken, router)

  resource
      .then(() => {
        loadTask()
      })
}

const loadTask = (): void => {
  if (null === authStore.JWTPayload) {
    tasks.value = []

    return
  }

  const authHeader = useCreateAuthHeader(authStore.token)
  const defaultQueryParams = {
    status: 'fulfilled',
    userUuid: authStore.JWTPayload.user.id,
  }
  const header = { ...authHeader, ...{ params: defaultQueryParams } }
  const taskListUrl = API.V1.TASK_LIST

  const resource: Promise<any> = useGetResource(taskListUrl, header, authStore.tryRefreshToken, router)

  resource
      .then(response => {
        if (! response.data) {
          tasks.value = null

          return
        }

        tasks.value = response.data
      })
}

onMounted(() => {
  loadTask()
})
</script>

<style lang="scss" scoped>
  @import "bootstrap/scss/functions";
  @import "bootstrap/scss/variables";

  .card {
    border-color: $green-300;

    &-body {
      background-color: $green-300;
    }
  }
</style>
