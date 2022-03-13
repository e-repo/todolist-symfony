<template>
  <div class="nav-sidebar mt-3">
    <ul class="nav nav-pills flex-column">
      <li
        class="nav-item"
        v-for="(menuItem, index) in menuItems"
        :key="index"
      >

        <span
          class="nav-link text-white cursor-pointer nav-sidebar_sub-menu"
          v-if="menuItem.subMenu"
          @click="menuItem.isSubMenuShow = ! menuItem.isSubMenuShow"
        >
          <font-awesome-icon class="me-2" :icon=menuItem.icon />
          {{ menuItem.name }}
          <font-awesome-icon
            class="ms-auto rotate-close"
            icon="angle-left"
            :class="{'rotate-open': menuItem.isSubMenuShow}"
          />
        </span>

        <a
          v-else
          class="nav-link text-white"
          :href=menuItem.link
        >
          <font-awesome-icon class="me-2" :icon=menuItem.icon />
          {{ menuItem.name }}
        </a>

        <Transition name="sub-menu">
        <div
          v-if="menuItem.subMenu && menuItem.isSubMenuShow"
          class="container-fluid"
        >
          <ul class="nav flex-column">
            <li class="nav-item" v-for="(subMenuItems, index) in menuItem.subMenu" :key=index>
              <a class="nav-link text-white" href="#">{{ subMenuItems.name }}</a>
            </li>
          </ul>
        </div>
        </Transition>

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
          isSubMenuShow: false,
          subMenu: [
            { name: 'Главная' },
            { name: 'Выполненные' },
          ],
        },
        { name: 'Тест', icon: 'house', link: '#' },
      ],
    }
  }
}
</script>

<style scoped>
  .rotate-close {
    transform: rotate(0);
    transition: all .3s ease;
  }

  .rotate-open {
    transform: rotate(-90deg);
    transition: all .3s ease;
  }

  .sub-menu-enter-active {
    transition: all .3s ease;
  }

  .sub-menu-leave-active {
    transition: all .3s ease;
  }

  .sub-menu-enter-from,
  .sub-menu-leave-to {
    transform: translateX(20px);
    opacity: 0;
  }
</style>