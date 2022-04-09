<template>
  <main class="main pt-3 pb-3">
    <div class="container-fluid">

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
  </main>
</template>

<script>
import { ROLE_BADGE, STATUS_BADGE } from "@/pages/users/conf"
import {URL_V1} from "@/api/conf"
import AppPreloader from "@/components/UI/AppPreloader"
import BootstrapPaginate from "@/components/UI/BootstrapPaginate";
import moment from 'moment'
import axios from "axios";

export default {
  components: { AppPreloader, BootstrapPaginate },
  data() {
    return {
      users: null,
      pageItems: [],
      usersColumnsName: ['№', 'ФИО', 'Email', 'Роль', 'Статус', 'Дата создания']
    }
  },
  methods: {
    getRoleClass: (role) => ROLE_BADGE[role] ? ROLE_BADGE[role].class : '',
    getRoleName: (role) => ROLE_BADGE[role] ? ROLE_BADGE[role].name : '',
    getStatusClass: (status) => STATUS_BADGE[status.toUpperCase()] ? STATUS_BADGE[status.toUpperCase()].class : '',
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
      this.loadUsers(URL_V1.USERS_LIST + userPage.search)
    },
    loadUsers: function (url = null, params = null) {
      const usersUrl = null !== url ? url : URL_V1.USERS_LIST + window.location.search
      const searchParams = null !== params ? params : {status: 'active'}

      axios
        .get(usersUrl, {
          params: searchParams
        })
        .then(response => {
          this.users = response.data
        })
    },
  },
  mounted() {
    this.loadUsers()
  }
}
</script>

<style scoped>

</style>