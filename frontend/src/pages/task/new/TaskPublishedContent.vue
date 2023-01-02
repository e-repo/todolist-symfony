<template>
  <main class="main pt-3 pb-3">
    <div class="container-fluid">
      <div
        v-if="tasks.length > 0"
        class="row"
      >
        <div
          v-for="(task, index) in tasks"
          :key="index"
          class="col-3 mb-4"
        >
          <div class="card">
            <div class="card-header">
              <h5 class="mb-0 font-weight-bold">
                {{ task.name }}
              </h5>
            </div>
            <div class="card-body">
              <p class="card-text">
                <b>{{ task.description }}</b>
              </p>
              <div class="text-sm-end">
                {{ task.date }}
              </div>
            </div>
            <div class="card-footer d-flex justify-content-end">
              <a
                href="#"
                class="btn btn-outline-primary m-1 js-fulfilled-task"
              >
                <font-awesome-icon icon="pen" />
              </a>
              <a
                href="#"
                class="btn btn-outline-success m-1 js-fulfilled-task"
              >
                <font-awesome-icon icon="check-double" />
              </a>
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
  import { onMounted } from "vue"
  import { useCreateAuthHeader, useGetResource } from "@/components/composables"
  import { API } from "@/conf/api"
  import { useReactiveTaskList } from "@/pages/task/new/composables"

  const authStore = useAuthStore()
  const router = useRouter()
  const tasks = useReactiveTaskList()

  const loadTask = (): void => {
    if (null === authStore.JWTPayload) {
      tasks.value = []
    }

    const authHeader = useCreateAuthHeader(authStore.token)
    const defaultQueryParams = {
      status: 'published',
      userUuid: authStore.JWTPayload.user.id,
    }
    const header = { ...authHeader, ...{ params: defaultQueryParams } }
    const taskListUrl = API.V1.TASK_LIST

    const resource: Promise<any> = useGetResource(taskListUrl, header, authStore.tryRefreshToken, router)

    resource
      .then(response => {
        tasks.value = response.data
      })
  }

  onMounted(() => {
    loadTask()
  })
</script>

<style scoped>

</style>
