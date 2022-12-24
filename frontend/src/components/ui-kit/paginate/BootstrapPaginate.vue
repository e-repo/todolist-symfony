<template>
  <nav v-if="totalPage > 1">
    <ul class="pagination">
      <li
        v-if="geFirstPageNumber()"
        class="page-item"
      >
        <button
          type="button"
          class="page-link text-dark"
          @click="loadPageEmit(geFirstPageNumber())"
        >
          <font-awesome-icon
            class="me-2"
            icon="angles-left"
          />
        </button>
      </li>
      <li
        v-if="getPrevPageNumber()"
        class="page-item"
      >
        <button
          type="button"
          class="page-link text-dark"
          @click="loadPageEmit(getPrevPageNumber())"
        >
          <font-awesome-icon
            class="me-2"
            icon="angle-left"
          />
        </button>
      </li>


      <li
        v-for="(page, index) in pageItems"
        :key="index"
        class="page-item"
        :class="{'active': checkIsActive(page.number)}"
      >
        <button
          type="button"
          class="page-link text-dark"
          @click="loadPageEmit(page.number)"
        >
          {{ page.number }}
        </button>
      </li>

      <li
        v-if="getNextPageNumber()"
        class="page-item"
      >
        <button
          type="button"
          class="page-link text-dark"
          @click="loadPageEmit(getNextPageNumber())"
        >
          <font-awesome-icon
            class="me-2"
            icon="angle-right"
          />
        </button>
      </li>
      <li
        v-if="getLastPageNumber()"
        class="page-item"
      >
        <button
          type="button"
          class="page-link text-dark"
          @click="loadPageEmit(getLastPageNumber())"
        >
          <font-awesome-icon
            class="me-2"
            icon="angles-right"
          />
        </button>
      </li>
    </ul>
  </nav>
</template>

<script>
const NUMBER_PAGINATION_ITEMS = 5;

export default {
  props: {
    totalPage: {type: Number, default: 1},
    currentPage: {type: Number, default: 1},
    numberPaginationItems: {type: Number, default: NUMBER_PAGINATION_ITEMS},
  },
  data() {
    return {
      pageItems: []
    }
  },
  watch: {
    totalPage() {
      this.createPaginationItems()
    },
    currentPage() {
      this.createPaginationItems()
    }
  },
  mounted() {
    this.createPaginationItems()
  },
  methods: {
    geFirstPageNumber: function () {
      return 1
    },
    getNextPageNumber: function () {
      return this.currentPage < this.totalPage
          ? this.currentPage + 1
          : null
    },
    getPrevPageNumber: function () {
      return this.currentPage > 1
          ? this.currentPage - 1
          : null
    },
    getLastPageNumber: function () {
      return this.totalPage
    },
    createPaginationItems: function () {
      const pageSuit = Math.ceil((this.currentPage + 1) / this.numberPaginationItems)
      let offset = pageSuit * this.numberPaginationItems - this.numberPaginationItems
      const pages = [];

      offset = offset > 0 ? offset - 1 : offset;

      const maxNumberItems = offset + this.numberPaginationItems
      const itemsCount = this.totalPage <= maxNumberItems ? this.totalPage : maxNumberItems;
      for (let i = offset; i < itemsCount; i++) {
        const pageNumber = i + 1;
        const page = {
          number: pageNumber
        }
        pages.push(page)
      }

      this.pageItems = pages;
    },
    loadPageEmit: function (pageNumber = null) {
      this.$emit('loadPage', pageNumber)
    },
    checkIsActive: function (pageNumber) {
      return pageNumber === this.currentPage
    }
  }
}
</script>

<style scoped>
  .page-item.active .page-link {
    background-color: #dee2e6;
    border-color: #dee2e6;
  }
</style>
