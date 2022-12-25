<template>
  <div class="nav-sidebar mt-3">
    <ul
      v-if="sidebarMenu"
      class="nav nav-pills flex-column"
    >
      <li
        v-for="(menuItem, index) in menuItems"
        :key="index"
        class="nav-item"
      >
        <span
          v-if="menuItem.subMenu.length > 0"
          class="nav-link text-white cursor-pointer nav-sidebar_sub-menu"
          @click="menuItem.isSubMenuShow = ! menuItem.isSubMenuShow"
        >
          <font-awesome-icon
            class="me-2"
            :icon="menuItem.icon"
          />
          {{ menuItem.name }}
          <font-awesome-icon
            class="ms-auto rotate-close"
            icon="angle-left"
            :class="{'rotate-open': menuItem.isSubMenuShow}"
          />
        </span>

        <router-link
          v-else
          :to="menuItem.uri"
          class="nav-link text-white"
        >
          <font-awesome-icon
            class="me-2"
            :icon="menuItem.icon"
          />
          {{ menuItem.name }}
        </router-link>

        <Transition name="sub-menu">
          <div
            v-if="menuItem.subMenu.length > 0 && menuItem.isSubMenuShow"
            class="container-fluid"
          >
            <ul class="nav flex-column">
              <li
                v-for="(subMenuItem, index) in menuItem.subMenu"
                :key="index"
                class="nav-item"
              >
                <router-link
                  :to="subMenuItem.uri"
                  class="nav-link text-white"
                >
                  {{ subMenuItem.name }}
                </router-link>
              </li>
            </ul>
          </div>
        </Transition>
      </li>
    </ul>
    <app-preloader v-else />
  </div>
</template>

<script>
import AppPreloader from "@/components/ui-kit/preloader/AppPreloader"
const SIDEBAR_ICONS_BY_NAME = {
  'Home': 'house',
  'Users': 'user-group',
  'ToDo': 'list',
}

export default {
  name: 'SidebarNav',
  components: {AppPreloader},
  props: {
    sidebarMenu: {
      type: Object
    }
  },
  data() {
    return {
      menuItems: [],
    }
  },
  watch: {
    sidebarMenu(menu) {
      const [menuData] = menu.data
      this.menuItems = menuData.attributes.tree
          .map(menuItem => {
            menuItem.isSubMenuShow = false

            if (menuItem.name in SIDEBAR_ICONS_BY_NAME) {
              menuItem.icon = SIDEBAR_ICONS_BY_NAME[menuItem.name]
            }

            return menuItem
          })
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
