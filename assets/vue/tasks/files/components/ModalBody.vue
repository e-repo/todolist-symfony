<template>
  <div class="modal-body">
    <div class="modal-body__error"></div>
    <div class="modal-body__content">

      <files-list v-if="filesList" :filesList="filesList"></files-list>

      <div v-else class="mb-3">
        <preloader></preloader>
      </div>

      <vue-dropzone
        ref="dropzoneTaskFiles"
        id="dropzone"
        :options="dropzoneOptions"
        @vdropzone-complete="dropzoneComplete"
      ></vue-dropzone>

    </div>
  </div>
</template>

<script>
import vueDropzone from 'vue2-dropzone'
import 'vue2-dropzone/dist/vue2Dropzone.min.css'
import Preloader from "../../../UI/Preloader";
import FilesList from "./FilesList";
import axios from 'axios';

export default {
  components: {
    FilesList,
    Preloader,
    vueDropzone
  },
  props: {
    taskId: {
      type: String
    }
  },
  data: function () {
    return {
      dropzoneOptions: {
        url: '#',
        thumbnailWidth: 120,
        maxFilesize: 5,
        headers: { 'My-Awesome-Header': 'header value' },
        dictDefaultMessage: "<i class='fas fa-upload mr-1'></i>Загрузка файлов",
        addRemoveLinks: true,
        dictRemoveFile: "<i class='fas fa-trash-alt'></i>"
      },
      filesList: null,
    }
  },
  methods: {
    dropzoneComplete() {
      this.showFilesList();
    },
    removeAllFiles() {
      this.$refs.dropzoneTaskFiles
          .removeAllFiles();
    },
    showFilesList() {
      axios.get(`/tasks/${this.taskId}/files`)
        .then((response) => {
          this.filesList = response.data;
        })
        .catch((error) => {
          window.alert(error.toString());
        });
    },
  },
  watch: {
    taskId() {
      this.$refs.dropzoneTaskFiles
          .setOption('url', `/tasks/${this.taskId}/file`);

      this.removeAllFiles();
      this.showFilesList();
    }
  },
  mounted() {
    this.$root.$on('onClearDropzone', () => {
      this.removeAllFiles();
    });
    this.$root.$on('onDeleteFile', () => {
      this.showFilesList();
    });
  }
}
</script>

<style scoped>

</style>