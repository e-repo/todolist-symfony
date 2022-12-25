import { isReactive, watchEffect } from "vue"
import { TaskForm } from "@/components/content/header/types"

export function useTaskFormValidator(taskForm: TaskForm): void {
    if (! isReactive(taskForm)) {
        throw Error('\'taskForm\' must be reactive.')
    }

    function checkName() {
        taskForm.name.isValid = true
        taskForm.name.isChanged = false
        taskForm.description.isValid = true
        taskForm.description.isChanged = false

        if (taskForm.name.fieldValue.length <= 2) {
            taskForm.name.isValid = false
            taskForm.name.errorMessage = 'Наименование задачи не может быть меньше 2 символов.'
        }

        if (taskForm.name.fieldValue.length > 100) {
            taskForm.name.isValid = false
            taskForm.name.errorMessage = 'Наименование задачи не может быть больше 100 символов.'
        }

        if (taskForm.name.fieldValue.length > 0) {
            taskForm.name.isChanged = true
        }

        if (taskForm.description.fieldValue.length <= 5) {
            taskForm.description.isValid = false
            taskForm.description.errorMessage = 'Описание задачи не может быть меньше 5 символов.'
        }

        if (taskForm.description.fieldValue.length > 250) {
            taskForm.description.isValid = false
            taskForm.description.errorMessage = 'Описание задачи не может быть больше 250 символов.'
        }

        if (taskForm.description.fieldValue.length > 0) {
            taskForm.description.isChanged = true
        }
    }

    watchEffect(checkName)
}
