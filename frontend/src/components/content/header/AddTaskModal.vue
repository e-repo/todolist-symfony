<template>
  <div>
    <bootstrap-modal
      :is-modal-show="isModalShow"
    >
    </bootstrap-modal>
  </div>
</template>

<script setup lang="ts">
  import BootstrapModal from "@/components/ui-kit/modal/BootstrapModal.vue";
  import { defineProps } from "vue"

  const props = defineProps({
    isModalShow: { type: Boolean, default: false },
  })
</script>
