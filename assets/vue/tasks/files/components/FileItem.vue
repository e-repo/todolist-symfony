<template>
  <div class="d-flex justify-content-between">
    <div>
      <span class="mr-1">
        {{ `${index + 1}.` }}
      </span>
      {{ file.originalFilename }}
    </div>
    <div>
      <button class="btn" @click="deleteFile(file.id)">
        <i class="fas fa-trash-alt"></i>
      </button>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  props: {
    file: {
      type: Object,
      required: true,
    },
    index: {
      type: Number,
      required: true,
    }
  },
  methods: {
    deleteFile(fileId) {
      axios.delete(`/tasks/file-delete/${fileId}`)
        .then((response) => {
          this.$root.$emit('onDeleteFile')
        })
        .catch((error) => {
          window.alert(error.toString());
        });
    }
  }
}
</script>

<style scoped>

</style>