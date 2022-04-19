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
              <th scope="row">{{ getUserNumber(index + 1) }}</th>
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

<script>
import { ROLE_NAMES_RU, BADGE } from "@/conf";
import { API_V1 } from "@/conf/api"
import AppPreloader from "@/components/UI/AppPreloader"
import BootstrapPaginate from "@/components/UI/BootstrapPaginate";
import moment from 'moment'
import axios from "axios";

export default {
  components: { AppPreloader, BootstrapPaginate },
  data() {
    return {
      usersData: null,
      usersMeta: null,
      userRoles: null,
      userStatuses: null,
      pageNumber: null,
      filters: {
        name: '',
        email: '',
        role: '',
        status: '',
      },
      usersColumnsName: ['№', 'ФИО', 'Email', 'Роль', 'Статус', 'Дата создания']
    }
  },
  methods: {
    getRoleClass: (role) => BADGE.USER_ROLE_BADGE[role] ? BADGE.USER_ROLE_BADGE[role].class : '',
    getRoleName: (role) => ROLE_NAMES_RU[role] ? ROLE_NAMES_RU[role] : '',
    getStatusClass: (status) => BADGE.USER_STATUS_BADGE[status.toUpperCase()]
        ? BADGE.USER_STATUS_BADGE[status.toUpperCase()].class
        : '',
    formatUserDate: (value) => moment(value).format('DD.MM.YYYY hh:mm:ss'),
    getUserNumber: function (index) {
      return (this.usersMeta.currentPage - 1) * this.usersMeta.perPage + index
    },
    toPage: function (pageNumber = null) {
      if (null === pageNumber) {
        return;
      }

      this.pageNumber = pageNumber

      this.$router.push({
        path: '/users',
        query: {
          page: pageNumber,
          name: this.filters.name,
          email: this.filters.email,
          role: this.filters.role,
          status: this.filters.status
        }
      })
    },
    applyFilters: function () {
      this.$router.push({
        path: '/users',
        query: {
          name: this.filters.name,
          email: this.filters.email,
          role: this.filters.role,
          status: this.filters.status
        }
      })
    },
    resetFilters: function () {
      this.filters = {
        page: 1,
        name: '',
        email: '',
        role: '',
        status: '',
      }
      this.applyFilters()
    },
    loadFilters(params = null) {
      if (null === params) {
        return;
      }

      Object.keys(params).forEach(key => {
        const filtersList = Object.keys(this.filters)

        if (
            filtersList.includes(key) &&
            '' !== params[key].trim()
        ) {
          this.filters[key] = params[key]
        }
      })
    },
    loadUsers: function (params = null, url = null) {
      const defaultParams = {status: 'active'}
      const searchParams = null !== params
          ? {...params, ...defaultParams}
          : null

      this.loadFilters(params)

      console.log(this.filters)

      const usersUrl = null !== url ? url : API_V1.USER_LIST

      axios
        .get(usersUrl, {
          params: searchParams
        })
        .then(response => {
          this.usersData = response.data.data
          this.usersMeta = response.data.meta
        })
    },
    loadRoles: function () {
      axios
        .get(API_V1.USER_ROLE_LIST)
        .then(response => {
          const roles = response.data
          const [rolesData] = roles.data

          this.userRoles = rolesData.attributes.roles
        })
    },
    loadStatuses: function () {
      axios
          .get(API_V1.USER_STATUS_LIST)
          .then(response => {
            const statuses = response.data
            const [statusesData] = statuses.data

            this.userStatuses = statusesData.attributes.statuses
          })
    }
  },
  mounted() {
    this.loadUsers(this.$route.query)
    this.loadRoles()
    this.loadStatuses()

    this.$watch(
      () => this.$route.query,
      (toParams) => {
            this.loadUsers(toParams)
          }
    )
  }
}
</script>

<style scoped>

</style>