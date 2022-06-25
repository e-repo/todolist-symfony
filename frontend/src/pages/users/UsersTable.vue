<template>
  <table
      v-if="users.usersData"
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
        v-for="(user, index) in users.usersData"
        :key="index"
    >
      <th scope="row">{{ getRowNumber(index + 1) }}</th>
      <td>{{ user.name }}</td>
      <td>{{ user.email }}</td>
      <td>
            <span
                class="badge"
                :class="useRoleClasses(user.role)"
            >
              {{ useRoleNames(user.role) }}
            </span>
      </td>
      <td>
            <span
                class="badge"
                :class="useStatusClasses(user.status)"
            >
              {{ user.status }}
            </span>
      </td>
      <td>{{ useDateTimeToFormat(user.date) }}</td>
    </tr>
    </tbody>
  </table>
  <app-preloader v-if="! users.usersData"></app-preloader>
</template>

<script setup lang="ts">
  import AppPreloader from "@/components/ui-kit/preloader/AppPreloader.vue"
  import { defineProps, PropType } from "vue"
  import { UsersState } from "@/pages/users/types"
  import { useRoleClasses, useRoleNames, useStatusClasses } from "@/pages/users/composables"
  import { useDateTimeToFormat } from "@/components/composables/formatters"

  const props = defineProps({
    users: { type: Object as PropType<UsersState>, required: true },
    usersColumnsName: { type: Object as PropType<[string]> }
  })

  const getRowNumber = (index: number): number | null => {
    if (! props.users.usersMeta?.currentPage) {
      return null
    }

    return (props.users.usersMeta.currentPage - 1) * props.users.usersMeta.perPage + index
  }

</script>

<style scoped>

</style>