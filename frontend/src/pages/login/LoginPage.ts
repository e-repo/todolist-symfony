import { reactive, watch } from "vue";
import { LoginFormState } from "@/pages/login/types/LoginFormState";
import { useAuthStore } from "@/store/auth";
import { Store, storeToRefs } from "pinia";

import router from "@/router/router";

export default class LoginPage
{

    private readonly state
    private readonly authStore

    constructor()
    {
        this.state = reactive<LoginFormState>({
            email: '',
            password: '',
            isValidEmail: true,
            isValidPassword: true,
            errorMessage: null as string | null
        })
        this.authStore = useAuthStore()

        this.initWatchers()
    }

    public getLoginFormState(): LoginFormState
    {
        return this.state
    }

    public getAuthStore(): Store
    {
        return this.authStore
    }

    public login(): () => void
    {
        return () => {
            this.authStore.login(this.state.email, this.state.password)
        }
    }

    public resetErrorMessage(): () => void
    {
        return () => {
            this.state.errorMessage = null
        }
    }

    private initWatchers() {
        const { user, loginError } = storeToRefs(this.authStore)

        watch(loginError.value, (error) => {
          this.state.errorMessage = null

          if (null !== error.message) {
            this.state.errorMessage = error.data ? error.data.message : error.message
          }
        })

        watch(user.value, (user) => {
          if (true === user.isAuth) {
            router.push({name: 'Home'})
          }
        })
    }
}