<template>
  <div class="nav-sidebar mt-3">
    <ul class="nav nav-pills flex-column">
      <li class="nav-item" v-for="(menuItem, index) in menuItems" :key="index">
        <a class="nav-link text-white" :href=menuItem.link>
          <font-awesome-icon class="me-2" :icon=menuItem.icon />
          {{ menuItem.name }}
        </a>
        <div v-if=menuItem.subMenu class="container-fluid">
          <ul class="nav flex-column">
            <li class="nav-item" v-for="(subMenuItems, index) in menuItem.subMenu" :key=index>
              <a class="nav-link text-white" href="#">{{ subMenuItems.name }}</a>
            </li>
          </ul>
        </div>
      </li>
    </ul>
  </div>
</template>

<script>
export default {
  data() {
    return {
      menuItems: [
        { name: 'Главная', icon: 'house', link: '#' },
        { name: 'Пользователи', icon: 'user-group', link: '#' },
        {
          name: 'Задачи',
          icon: 'list',
          link: '#',
          subMenu: [
            { name: 'Главная' },
            { name: 'Выполненные' },
          ],
        },
      ],
    }
  }
}
</script>

<style scoped>

</style>