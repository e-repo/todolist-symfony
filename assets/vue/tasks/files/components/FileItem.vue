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
      <button class="btn" @click="downloadFile(file.id, file.originalFilename)">
        <i class="fas fa-cloud-download-alt"></i>
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
    },
    downloadFile(fileId, originalFileName) {
      axios({
        url: `/tasks/file-download/${fileId}`,
        method: 'GET',
        responseType: 'blob'
      }).then((response) => {
        console.log(response);
        const url = window.URL.createObjectURL(new Blob([response.data]));
        const link = document.createElement('a');
        link.href = url;
        link.setAttribute('download', originalFileName)
        document.body.appendChild(link);
        link.click();
      });
    }
  }
}
</script>

<style scoped>

</style>