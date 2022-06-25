<template>
  <main class="main pt-3 pb-3">
    <div class="container-fluid">

      <div class="p-3 mt-2 border">
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
                  v-for="(roleName, index) in userRoles"
                  :key="index"
                  :value="roleName"
              >
                {{ this.getRoleName(roleName) }}
              </option>
            </select>
          </div>
          <div class="col-2">
            <select class="form-select form-control" v-model="filters.status">
              <option selected value="">Все статусы</option>
              <option
                v-for="(statusName, index) in userStatuses"
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
      </div>

      <div class="row">
        <div class="col">

          <table
              v-if="usersData"
              class="table table-hover table-striped"
          >
            <thead>
            <tr>
              <th
                  v-for="(columnName, index) in usersColumnsName"
                  :key="index"
                  scope="col"
              >
                {{ columnName }}
              </th>
            </tr>
            </thead>
            <tbody>
            <tr
                v-for="(user, index) in usersData"
                :key="index"
            >
              <th scope="row">{{ getRowNumber(index + 1) }}</th>
              <td>{{ user.name }}</td>
              <td>{{ user.email }}</td>
              <td>
            <span
                class="badge"
                :class="getRoleClass(user.role)"
            >
              {{ getRoleName(user.role) }}
            </span>
              </td>
              <td>
            <span
                class="badge"
                :class="getStatusClass(user.status)"
            >
              {{ user.status }}
            </span>
              </td>
              <td>{{ formatUserDate(user.date) }}</td>
            </tr>
            </tbody>
          </table>
          <app-preloader v-if="! usersData"></app-preloader>

          <bootstrap-paginate
              v-if="usersMeta"
              @load-page="toPage"
              :total-page="usersMeta.totalPage"
              :current-page="usersMeta.currentPage"
          ></bootstrap-paginate>

        </div>
      </div>

    </div>
  </main>
</template>

<script lang="ts">
import AppPreloader from "@/components/ui-kit/preloader/AppPreloader.vue"
import BootstrapPaginate from "@/components/ui-kit/paginate/BootstrapPaginate.vue"
import axios from "axios"
import { API_V1 } from "@/conf/api"
import { useAuthStore } from "@/store/auth"
import { storeToRefs } from "pinia"
import { defineComponent, reactive, ref, toRefs, watch, onMounted } from "vue"
import { TableFilters, UsersState } from "@/pages/users/types";
import {UsersTableColumn } from "@/pages/users/enums/UsersTableColumn";
import { useRoleClasses, useRoleNames, useStatusClasses } from "@/pages/users/composables";
import { useDateTimeToFormat } from "@/components/composables/formatters";
import { useRouter, useRoute } from "vue-router";

export default defineComponent({
  components: { AppPreloader, BootstrapPaginate },
  setup() {
    const authStore = useAuthStore()
    const router = useRouter()
    const route = useRoute()
    const { user } = storeToRefs(authStore)

    const pageNumber = ref<number | null>(null)
    const users = reactive<UsersState>({
      usersData: null,
      usersMeta: null,
      userRoles: null,
      userStatuses: null,
    })
    let filters = reactive<TableFilters>({
      name: '',
      email: '',
      role: '',
      status: '',
    })
    const usersColumnsName = [
      UsersTableColumn.rawNumber,
      UsersTableColumn.fullName,
      UsersTableColumn.email,
      UsersTableColumn.role,
      UsersTableColumn.status,
      UsersTableColumn.createdAt
    ]

    const getRowNumber = (index: number): number | null => {
      if (! users.usersMeta?.currentPage) {
        return null
      }

      return (users.usersMeta.currentPage - 1) * users.usersMeta.perPage + index
    }

    const toPage = (newPageNumber: number | null = null): void => {
      if (null === newPageNumber) {
        return;
      }

      pageNumber.value = newPageNumber

      router.push({
        path: '/users',
        query: {
          page: pageNumber.value,
          ...filters
        }
      })
    }

    const applyFilters = (): void => {
      router.push({
        path: '/users',
        query: {
          ...filters
        }
      })
    }

    const resetPageNumber = (): number => pageNumber.value = 1

    const resetFilters = (): void => {
      filters = {
        name: '',
        email: '',
        role: '',
        status: '',
      }

      resetPageNumber()
      applyFilters()
    }

    const loadFilters = (params: TableFilters | null = null): void => {
      if (null === params) {
        return;
      }

      Object.keys(params).forEach(key => {
          filters[key as keyof TableFilters] = params[key as keyof TableFilters]
      })
    }

    const paramFilter = (queryParams: object | null = null): TableFilters | null => {
      if (null === queryParams) {
        return null
      }

      let paramsList = Object.entries(queryParams)

      paramsList = paramsList.filter(([keyParam]) => {
        return keyParam in filters
      })

      return Object.fromEntries(paramsList)
    }

    const loadUsers = (queryParams: object | null = null, url: string = '') => {
      const defaultParams = {status: 'active'}
      const searchParams = null !== queryParams
          ? {...queryParams, ...defaultParams}
          : null

      loadFilters(paramFilter(queryParams))

      const usersUrl = '' !== url ? url : API_V1.USER_LIST

      axios
          .get(usersUrl, {
            headers: {
              Authorization: `Bearer ${authStore.token}`
            },
            params: searchParams
          }).then(response => {
        users.usersData = response.data.data
        users.usersMeta = response.data.meta
      }).catch((error) => {
        authStore.tryRefreshToken(error, router)
      })
    }

    const loadRoles = () => {
      axios
          .get(API_V1.USER_ROLE_LIST, {
            headers: {
              Authorization: `Bearer ${authStore.token}`
            },
          }).then(response => {
        const roles = response.data
        const [rolesData] = roles.data

        users.userRoles = rolesData.attributes.roles
      }).catch((error) => {
        authStore.tryRefreshToken(error, router)
      })
    }

    const loadStatuses = () => {
      axios
          .get(API_V1.USER_STATUS_LIST, {
            headers: {
              Authorization: `Bearer ${authStore.token}`
            },
          }).then(response => {
        const statuses = response.data
        const [statusesData] = statuses.data

        users.userStatuses = statusesData.attributes.statuses
      }).catch((error) => {
        authStore.tryRefreshToken(error, router)
      })
    }

    onMounted(() => {
      if (! authStore.isAuth) {
        return;
      }

      loadUsers(route.query)
      loadRoles()
      loadStatuses()
    })

    watch(() => route.query, (toParams) => {
      loadUsers(toParams)
    })

    return {
      ...toRefs(users),
      user,
      filters,
      pageNumber,
      usersColumnsName,
      getRowNumber,
      toPage,
      applyFilters,
      resetFilters,
      loadFilters,
      loadUsers,
      loadRoles,
      loadStatuses,
      getRoleClass: useRoleClasses,
      getRoleName: useRoleNames,
      getStatusClass: useStatusClasses,
      formatUserDate: useDateTimeToFormat,
    }
  }
})
</script>

<style scoped>

</style>