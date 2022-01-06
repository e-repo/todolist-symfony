<template>
  <ul class="list-group mb-3">
    <li
      v-for="(file, index) of filesList"
      class="list-group-item"
    >
      <file-item
        :file="file"
        :index="index"
      >
      </file-item>
    </li>
  </ul>
</template>

<script>
import FileItem from "./FileItem";

export default {
  components: {
    FileItem,
  },
  props: {
    filesList: {
      type: Array
    },
  },
}
</script>

<style scoped>

</style>