<template>
  <div class="modal-body">
    <div class="modal-body__error"></div>
    <div class="modal-body__content">
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

export default {
  components: {
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
      }
    }
  },
  methods: {
    dropzoneComplete() {
      console.log('dropzone-complete.')
    },
  },
  watch: {
    taskId() {
      this.dropzoneComplete();
      this.$refs.dropzoneTaskFiles
          .setOption('url', `/tasks/${this.taskId}/file`);
    }
  },
  mounted() {
    this.$root.$on('onClearDropzone', () => {
      this.$refs.dropzoneTaskFiles
          .removeAllFiles();
    })
  }
}
</script>

<style scoped>

</style>