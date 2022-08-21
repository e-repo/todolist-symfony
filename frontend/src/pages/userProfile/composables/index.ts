import { UserNameForm } from "@/pages/userProfile/types";
import { isReactive, watchEffect } from "vue";

export function useUserNameValidator(userNameForm: UserNameForm): void {
    if (! isReactive(userNameForm)) {
        throw Error('\'userName\' must be reactive.')
    }

    function checkName() {
        userNameForm.first.isValid = true
        userNameForm.last.isValid = true

        if (userNameForm.first.fieldValue.length <= 2) {
            userNameForm.first.isValid = false
            userNameForm.first.errorMessage = 'Имя пользователя не может быть меньше 2 символов.'
        }

        if (userNameForm.first.fieldValue.length > 50) {
            userNameForm.first.isValid = false
            userNameForm.first.errorMessage = 'Имя пользователя не может быть меньше 50 символов.'
        }

        if (userNameForm.last.fieldValue.length <= 2) {
            userNameForm.last.isValid = false
            userNameForm.last.errorMessage = 'Фамилия пользователя не может быть меньше 2 символов.'
        }

        if (userNameForm.last.fieldValue.length > 50) {
            userNameForm.last.isValid = false
            userNameForm.last.errorMessage = 'Фамилия пользователя не может быть меньше 50 символов.'
        }
    }

    watchEffect(checkName)
}