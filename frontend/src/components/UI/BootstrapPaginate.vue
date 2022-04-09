<template>
  <nav v-if="totalPage > 1">
    <ul class="pagination">
      <li v-if="this.getUrlToFirstPage()" class="page-item">
        <button type="button" class="page-link text-dark" @click="loadPageEmit(this.getUrlToFirstPage())">
          <font-awesome-icon class="me-2" icon="angles-left" />
        </button>
      </li>
      <li v-if="this.getUrlToPrevPage()" class="page-item">
        <button type="button" class="page-link text-dark" @click="loadPageEmit(this.getUrlToPrevPage())">
          <font-awesome-icon class="me-2" icon="angle-left" />
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
            @click="loadPageEmit(page.url)"
        >
          {{ page.number }}
        </button>
      </li>

      <li v-if="this.getUrlToNextPage()" class="page-item">
        <button type="button" class="page-link text-dark" @click="loadPageEmit(this.getUrlToNextPage())">
          <font-awesome-icon class="me-2" icon="angle-right" />
        </button>
      </li>
      <li v-if="this.getUrlToLastPage()" class="page-item" >
        <button type="button" class="page-link text-dark" @click="loadPageEmit(this.getUrlToLastPage())">
          <font-awesome-icon class="me-2" icon="angles-right" />
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
    pageUrl: {type: String, default: window.location.pathname},
    numberPaginationItems: {type: Number, default: NUMBER_PAGINATION_ITEMS},
  },
  data() {
    return {
      pageItems: []
    }
  },
  methods: {
    getUrlToFirstPage: function () {
      return this.pageUrl + '?page=1'
    },
    getUrlToNextPage: function () {
      return this.currentPage < this.totalPage
          ? this.pageUrl + `?page=${this.currentPage + 1}`
          : null
    },
    getUrlToPrevPage: function () {
      return this.currentPage > 1
          ? this.pageUrl + `?page=${this.currentPage - 1}`
          : null
    },
    getUrlToLastPage: function () {
      return this.pageUrl + `?page=${this.totalPage}`
    },
    createPaginationItems: function () {
      const pageSuit = Math.ceil((this.currentPage + 1) / this.numberPaginationItems)
      let offset = pageSuit * this.numberPaginationItems - this.numberPaginationItems
      let pages = [];

      offset = offset > 0 ? offset - 1 : offset;

      const maxNumberItems = offset + this.numberPaginationItems
      const itemsCount = this.totalPage <= maxNumberItems ? this.totalPage : maxNumberItems;
      for (let i = offset; i < itemsCount; i++) {
        let pageNumber = i + 1;
        let page = {
          number: pageNumber,
          url: this.pageUrl + '?page=' + pageNumber
        }
        pages.push(page)
      }

      this.pageItems = pages;
    },
    loadPageEmit: function (url = null) {
      this.$emit('loadPage', url)
    },
    checkIsActive: function (pageNumber) {
      return pageNumber === this.currentPage
    }
  },
  watch: {
    currentPage() {
      this.createPaginationItems()
    }
  },
  mounted() {
    this.createPaginationItems()
  }
}
</script>

<style scoped>
  .page-item.active .page-link {
    background-color: #dee2e6;
    border-color: #dee2e6;
  }
</style>