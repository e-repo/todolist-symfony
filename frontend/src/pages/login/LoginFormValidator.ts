import { LoginFormState } from "@/pages/login/types/LoginFormState";

export default class LoginFormValidator
{

    private state
    private emailTemplate = /^\w+([.-]?\w+)*@\w+([.-]?\w+)*(.\w{2,3})+$/;

    constructor(loginFormState: LoginFormState)
    {
        this.state = loginFormState
    }

    public checkEmail(email: string): void
    {
        let isValidEmail: boolean = true

        if (email.length < 5) {
            isValidEmail = false
        }

        if (null === email.match(this.emailTemplate)) {
            isValidEmail = false
        }

        this.state.isValidEmail = isValidEmail
    }

    public checkPassword(password: string): void
    {
        let isValidPassword = true

        if (password.length < 6) {
            isValidPassword = false
        }

        this.state.isValidPassword = isValidPassword
    }
}