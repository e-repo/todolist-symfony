<template>
  <div>
    <bootstrap-modal
        :is-modal-show="isModalShow"
        @modalHide="modalHide"
    >
      <template #title>Change name</template>
      <template #body>
<!--        <form>-->
<!--          <div class="mb-3">-->
<!--            <label for="new-email" class="form-label">Email address</label>-->
<!--            <input type="email" class="form-control" id="new-email" v-model="emailChangingForm.email">-->
<!--          </div>-->
<!--        </form>-->
      </template>
      <template #footer>
        <button
            type="button"
            class="btn btn-outline-secondary"
            @click="modalHide()"
        >
          <slot name="close-btn-name">Close</slot>
        </button>
        <button
            type="button"
            class="btn btn-success"
            @click="changeName()"
        >Save</button>
      </template>
    </bootstrap-modal>
  </div>
</template>

<script setup lang="ts">
  import BootstrapModal from "@/components/ui-kit/modal/BootstrapModal.vue";
  import { defineEmits, defineProps } from "vue";

  defineProps({
    isModalShow: { type: Boolean, default: false },
  })

  const emit = defineEmits(['modalHide'])

  const modalHide = () => emit('modalHide')
  const changeName = () => console.log('asdf')
</script>

<style scoped>

</style>