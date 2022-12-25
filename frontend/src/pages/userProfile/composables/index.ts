import { ChangingEmailForm, UserNameForm } from "@/pages/userProfile/types"
import { isReactive, watchEffect } from "vue"

export function useEmailFormValidator(emailForm: ChangingEmailForm): void {
    if (! isReactive(emailForm)) {
        throw Error('\'userName\' must be reactive.')
    }

    function checkEmail(): void {
        emailForm.email.isValid = true
        const emailTemplate: RegExp = /^\w+([.-]?\w+)*@\w+([.-]?\w+)*(.\w{2,3})+$/

        if (emailForm.email.fieldValue.length < 5) {
            emailForm.email.isValid = false
            emailForm.email.errorMessage = 'E-mail не может быть меньше 5-ти символов.'

            return
        }

        if (! emailForm.email.fieldValue.match(emailTemplate)) {
            emailForm.email.isValid = false
            emailForm.email.errorMessage = 'Неверный формат электронной почты.'

            return
        }
    }

    watchEffect(checkEmail)
}

export function useNameFormValidator(userNameForm: UserNameForm): void {
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
