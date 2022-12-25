<template>
  <Transition name="modal">
    <div
      v-if="isModalShow"
      class="modal d-block"
      tabindex="-1"
    >
      <div
        class="modal-dialog"
        :class="size"
      >
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">
              <slot name="title">
                Modal title
              </slot>
            </h5>
            <button
              type="button"
              class="btn-close"
              @click="hide()"
            />
          </div>
          <div class="modal-body">
            <slot name="body">
              Modal body
            </slot>
          </div>
          <div class="modal-footer">
            <slot name="footer" />
          </div>
        </div>
      </div>
    </div>
  </Transition>
  <Transition name="fade">
    <div
      v-if="isModalShow"
      class="modal-backdrop"
    />
  </Transition>
</template>

<script setup lang="ts">
  import { defineProps, defineEmits } from "vue"

  const emit = defineEmits(['modalHide'])
  const hide = () => emit("modalHide")

  defineProps({
    isModalShow: { type: Boolean, required: true, default: false },
    size: { type: String, default: null }
  })

</script>

<style scoped>
  .fade-enter-active,
  .fade-leave-active {
    transition: opacity .15s linear;
  }

  .fade-enter-from,
  .fade-leave-to {
    opacity: 0;
  }

  .modal-enter-active,
  .modal-leave-active {
    transition: transform .2s linear, opacity .2s linear;
  }

  .modal-enter-from,
  .modal-leave-to {
    transform: scale(.8);
    opacity: 0;
  }

  .modal-backdrop {
    background-color: rgba(0, 0, 0, .5);
  }
</style>
