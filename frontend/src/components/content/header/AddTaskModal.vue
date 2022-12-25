<template>
  <div>
    <bootstrap-modal
      :is-modal-show="isModalShow"
      @modal-hide="modalHide()"
    >
      <template #title>
        Adding task
      </template>
      <template #body>
        <form>
          <div class="mb-3">
            <label
              for="task-name"
              class="form-label"
            >Name</label>
            <div class="input-group has-validation">
              <input
                v-model="taskForm.name.fieldValue"
                type="text"
                class="form-control"
                :class="{'is-invalid': ! taskForm.name.isValid && taskForm.name.isChanged}"
              >
              <div class="invalid-feedback">
                {{ taskForm.name.errorMessage }}
              </div>
            </div>
          </div>
          <div class="mb-3">
            <label
              for="task-name"
              class="form-label"
            >Description</label>
            <div class="input-group has-validation">
              <input
                v-model="taskForm.description.fieldValue"
                type="text"
                class="form-control"
                :class="{'is-invalid': ! taskForm.description.isValid && taskForm.description.isChanged}"
              >
              <div class="invalid-feedback">
                {{ taskForm.description.errorMessage }}
              </div>
            </div>
          </div>
        </form>
      </template>
      <template #footer>
        <button
          type="button"
          class="btn btn-outline-secondary"
          @click="modalHide()"
        >
          <slot name="close-btn-name">
            Close
          </slot>
        </button>
        <button
          type="button"
          class="btn btn-success"
          @click="addTask()"
        >
          Add
        </button>
      </template>
    </bootstrap-modal>
  </div>
</template>

<script setup lang="ts">
  import BootstrapModal from "@/components/ui-kit/modal/BootstrapModal.vue"
  import { defineProps, defineEmits, reactive } from "vue"
  import { TaskForm } from "@/components/content/header/types"
  import { useTaskFormValidator } from "@/components/content/header/composebles"

  const props = defineProps({
    isModalShow: { type: Boolean, default: false },
  })
  const emit = defineEmits(['modalHide'])

  const modalHide = (): void => emit('modalHide')

  const taskForm: TaskForm = reactive<TaskForm>({
    name: {
      fieldValue: '',
      isValid: true,
      isChanged: false,
    },
    description: {
      fieldValue: '',
      isValid: true,
      isChanged: false,
    }
  })
  
  useTaskFormValidator(taskForm)

  const addTask = (): void => {
    for (const field in taskForm) {
      if (! taskForm[field as keyof TaskForm].isValid) {
        return
      }
    }
    console.log('adding task!')
  }
</script>
