<template>
  <div class="modal fade" id="js-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content">
        <modal-header></modal-header>
        <modal-body :taskId="taskId"></modal-body>
        <modal-footer></modal-footer>
      </div>
    </div>
  </div>
</template>

<script>
import ModalHeader from "./components/ModalHeader";
import ModalBody from "./components/ModalBody";
import ModalFooter from "./components/ModalFooter";
export default {
  components: {ModalFooter, ModalBody, ModalHeader},
  data() {
    return {
      taskId: ''
    }
  },
  mounted() {
      window.addEventListener('event:showfiles', (e) => {
        this.taskId = e.detail.taskId;
      });
  }
}
</script>

<style scoped>

</style>