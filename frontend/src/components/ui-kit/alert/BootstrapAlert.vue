<template>
  <div
    v-if="isAlertShow"
    class="alert alert-dismissible"
    role="alert"
    :class="alertClass"
  >
    {{ message }}
    <button
      type="button"
      class="btn-close"
      @click="hide()"
    />
  </div>
</template>

<script setup lang="ts">
  import { defineProps, defineEmits } from "vue"

  defineProps({
    isAlertShow: { type: Boolean, default: false },
    message: { type: String, required: true, default: '' },
    alertClass: { type: String, default: 'alert-success' }
  })

  const emit = defineEmits(['alertHide'])
  const hide = () => emit("alertHide")
</script>
