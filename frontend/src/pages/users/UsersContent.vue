<template>
  <main class="main pt-3 pb-3">
    <div class="container-fluid">
      <div class="p-3 mt-2 border">
        <users-table-filters
          :users="users"
        />
      </div>

      <div class="row">
        <div class="col">
          <users-table
            :users="users"
            :users-columns-name="usersColumnsName"
          />

          <bootstrap-paginate
            v-if="users.usersMeta"
            :total-page="users.usersMeta.totalPage"
            :current-page="users.usersMeta.currentPage"
            @load-page="toPage"
          />
        </div>
      </div>
    </div>
  </main>
</template>

<script setup lang="ts">
  import BootstrapPaginate from "@/components/ui-kit/paginate/BootstrapPaginate.vue"
  import UsersTable from "@/pages/users/UsersTable.vue"
  import UsersTableFilters from "@/pages/users/UsersTableFilters.vue"
  import axios from "axios"
  import { API } from "@/conf/api"
  import { useAuthStore } from "@/store/auth"
  import { reactive, ref, watch, onMounted } from "vue"
  import { TableFilters, UsersState } from "@/pages/users/types"
  import { UsersTableColumn } from "@/pages/users/enums/UsersTableColumn"
  import { useRouter, useRoute } from "vue-router"
  import { useCreateAuthHeader, useGetResource } from "@/components/composables"

  const authStore = useAuthStore()
  const router = useRouter()
  const route = useRoute()

  const pageNumber = ref<number | null>(null)

  const filters: TableFilters = {
    name: '',
    email: '',
    role: '',
    status: '',
  }

  const users = reactive<UsersState>({
    usersData: null,
    usersMeta: null,
    userRoles: null,
    userStatuses: null,
  })

  const usersColumnsName = [
    UsersTableColumn.rawNumber,
    UsersTableColumn.fullName,
    UsersTableColumn.email,
    UsersTableColumn.role,
    UsersTableColumn.status,
    UsersTableColumn.createdAt
  ]

  const toPage = (newPageNumber: number | null = null): void => {
    if (null === newPageNumber) {
      return
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

  const resetFilter = () => {
    pageNumber.value = null

    Object.keys(filters).forEach(key => {
      filters[key as keyof TableFilters] = ''
    })
  }

  const loadFilters = (params: TableFilters = {}): void => {
    if (0 === Object.keys(params).length) {
      resetFilter()
      return
    }

    Object.keys(params).forEach(key => {
      if (1 !== pageNumber.value && params[key as keyof TableFilters]?.trim()) {
        pageNumber.value = 1
      }

      filters[key as keyof TableFilters] = params[key as keyof TableFilters]
    })
  }

  const paramFilter = (queryParams: object = {}): TableFilters => {
    let paramsList = Object.entries(queryParams)

    paramsList = paramsList.filter(([keyParam]) => {
      return keyParam in filters
    })

    return Object.fromEntries(paramsList)
  }

  const loadUsers = (queryParams: object = {}, url: string = '') => {
    loadFilters(paramFilter(queryParams))

    const defaultParams = {
      status: '' !== filters.status ? filters.status : 'active',
      page: pageNumber.value
    }

    const searchParams = 0 !== Object.keys(queryParams).length
        ? { ...filters, ...defaultParams }
        : {}

    const usersUrl = '' !== url ? url : API.V1.USER_LIST

    const authHeader = useCreateAuthHeader(authStore.token)
    const header = { ...authHeader, ...{ params: searchParams } }
    const resource: Promise<any> = useGetResource(usersUrl, header, authStore.tryRefreshToken, router)

    resource
      .then(response => {
        users.usersData = response.data
        users.usersMeta = response.meta
      })
  }

  const loadRoles = () => {
    axios
      .get(API.V1.USER_ROLE_LIST, {
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
      .get(API.V1.USER_STATUS_LIST, {
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
      return
    }

    loadUsers(route.query)
    loadRoles()
    loadStatuses()
  })

  watch(() => route.query, (toParams) => {
    loadUsers(toParams)
  })

</script>

<style scoped>

</style>
