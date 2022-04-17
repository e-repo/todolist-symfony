<template>
  <main class="main pt-3 pb-3">
    <div class="container-fluid">

      <div class="p-3 mt-2 border">
        <form class="row">
          <div class="col-2">
            <input type="text" class="form-control" placeholder="Имя">
          </div>
          <div class="col-2">
            <input type="email" class="form-control" placeholder="Email">
          </div>
          <div class="col-2">
            <select class="form-select form-control">
              <option selected>Все роли</option>
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
            <select class="form-select form-control">
              <option selected>Все статусы</option>
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
            <button type="button" class="btn btn-success me-1">Фильтровать</button>
            <button type="button" class="btn btn-primary me-1">Сбросить</button>
          </div>
        </form>
      </div>

      <div class="row">
        <div class="col">

          <table
              v-if="users"
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
                v-for="(user, index) in users.data"
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
          <app-preloader v-if="! users"></app-preloader>

          <bootstrap-paginate
              v-if="users"
              @load-page="toPage"
              :total-page="users.meta.totalPage"
              :current-page="users.meta.currentPage"
              :page-url="this.$route.path"
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
      users: null,
      userRoles: null,
      userStatuses: null,
      pageItems: [],
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
      return (this.users.meta.currentPage - 1) * this.users.meta.perPage + index
    },
    toPage: function (url = null) {
      if (null === url) {
        return;
      }

      const userPage = new URL(`${window.location.protocol}/${url}`)

      this.$router.push({path: '/users', query: {page: userPage.searchParams.get('page')}})
      this.loadUsers(API_V1.USER_LIST + userPage.search)
    },
    loadUsers: function (url = null, params = null) {
      const usersUrl = null !== url ? url : API_V1.USER_LIST + window.location.search
      const searchParams = null !== params ? params : {status: 'active'}

      axios
        .get(usersUrl, {
          params: searchParams
        })
        .then(response => {
          this.users = response.data
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
    this.loadUsers()
    this.loadRoles()
    this.loadStatuses()
  }
}
</script>

<style scoped>

</style>