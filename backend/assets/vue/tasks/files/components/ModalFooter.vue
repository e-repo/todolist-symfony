<template>
  <div class="modal-footer d-flex justify-content-between">
    <div>
      <button type="button" class="btn btn-outline-danger" @click="clearDropzone">Очистить поле загрузки</button>
    </div>
    <div>
      <button type="button" class="btn btn-outline-dark" data-dismiss="modal">Закрыть</button>
    </div>
  </div>
</template>

<script>

export default {
  methods: {
    clearDropzone() {
      this.$root.$emit('onClearDropzone')
    }
  }
}
</script>

<style scoped>

</style>