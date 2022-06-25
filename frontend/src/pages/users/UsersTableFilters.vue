<template>
  <form class="row" @submit.prevent="applyFilters()">
    <div class="col-2">
      <input
          type="text"
          class="form-control"
          placeholder="Имя"
          v-model="filters.name"
      >
    </div>
    <div class="col-2">
      <input
          type="email"
          class="form-control"
          placeholder="Email"
          v-model="filters.email"
      >
    </div>
    <div class="col-2">
      <select class="form-select form-control" v-model="filters.role">
        <option disabled value="">Все роли</option>
        <option
            v-for="(roleName, index) in users.userRoles"
            :key="index"
            :value="roleName"
        >
          {{ useRoleNames(roleName) }}
        </option>
      </select>
    </div>
    <div class="col-2">
      <select class="form-select form-control" v-model="filters.status">
        <option selected value="">Все статусы</option>
        <option
            v-for="(statusName, index) in users.userStatuses"
            :key="index"
            :value="statusName"
        >
          {{ statusName.toUpperCase() }}
        </option>
      </select>
    </div>
    <div class="col-4">
      <button type="submit" class="btn btn-success me-1">Фильтровать</button>
      <button type="button" class="btn btn-primary me-1" @click="resetFilters()">Сбросить</button>
    </div>
  </form>
</template>

<script setup lang="ts">
  import { defineProps, PropType, reactive } from "vue";
  import { useRoleNames } from "@/pages/users/composables";
  import { useRouter } from "vue-router";
  import { TableFilters, UsersState } from "@/pages/users/types";

  let filters = reactive<TableFilters>({
    name: '',
    email: '',
    role: '',
    status: '',
  })

  const router = useRouter()

  defineProps({
    users: { type: Object as PropType<UsersState>, required: true },
  })

  const applyFilters = (clear: boolean = false): void => {
    router.push({
      path: '/users',
      query: ! clear ? { ...filters } : {}
    })
  }

  const resetFilters = (): void => {
    filters.name = ''
    filters.email = ''
    filters.role = ''
    filters.status = ''

    applyFilters(true)
  }

</script>

<style scoped>

</style>