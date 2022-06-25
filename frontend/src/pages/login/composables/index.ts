import { LoginFormState } from "@/pages/login/types/LoginFormState";
import {isReactive, watchEffect} from "vue";

export function useEmailValidator(loginFrom: LoginFormState): void {
    if (! isReactive(loginFrom)) {
        throw Error('\'loginFrom\' must be reactive.')
    }

    function checkEmail() {
        let isValidEmail: boolean = true
        const emailTemplate: RegExp = /^\w+([.-]?\w+)*@\w+([.-]?\w+)*(.\w{2,3})+$/

        if (isValidEmail && loginFrom.email.length < 5) {
            isValidEmail = false
        }

        if (isValidEmail && null === loginFrom.email.match(emailTemplate)) {
            isValidEmail = false
        }

        loginFrom.isValidEmail = isValidEmail
    }

    watchEffect(checkEmail)
}

export function usePasswordValidator(loginFrom: LoginFormState): void {
    if (! isReactive(loginFrom)) {
        throw Error('\'loginFrom\' must be reactive.')
    }

    function checkPassword() {
        let isValidPassword = true

        if (isValidPassword && loginFrom.password.length < 6) {
            isValidPassword = false
        }

        loginFrom.isValidPassword = isValidPassword
    }

    watchEffect(checkPassword)
}