import { defineStore, StoreDefinition } from 'pinia'
import axios from "axios"

interface AuthStoreUser {
    isAuth: boolean,
    token: string | null,
    refreshToken: string | null,
}

interface AuthStoreLoginError {
    message: string | null,
    data: object | null
}

export interface AuthStore {
    loginError: AuthStoreLoginError,
    user: AuthStoreUser,
}

export const useAuthStore: StoreDefinition = defineStore('auth', {
    state: (): AuthStore => ({
        user: {
            isAuth: false as boolean,
            token: null as string | null,
            refreshToken: null as string | null,
        } as AuthStoreUser,
        loginError: {
            message: null as string | null,
            data: null as object | null
        } as AuthStoreLoginError
    } as AuthStore),
    actions: {
        login(email, password) {
            axios.post('/api/login_check', {
                'username': email,
                'password': password,
            }).then((response) => {
                const data = response.data

                if (! data.token) {
                    throw new Error('Auth token is empty.')
                }

                this.setUserData(data)
            }).catch((error) => {
                this.loginError.message = error.message

                if (error.response.data) {
                    this.loginError.data = error.response.data
                }
            })
        },
        tryRefreshToken(error, $router = null) {
            const errorData = error.response.data

            if (401 !== errorData.code) {
                return
            }

            axios.post('/api/token/refresh', {
                refresh_token: this.user.refreshToken
            }).then((response) => {
                const data = response.data

                if (! data.token) {
                    throw new Error('Invalid refresh token.')
                }

                this.setUserData(data)

                if (null !== $router) {
                    $router.go()
                }
            }).catch((error) => {
                throw new Error(error.message)
            })
        },
        setUserData(data) {
            const localStorage = window.localStorage

            this.user.isAuth = true
            this.user.token = data.token
            this.user.refreshToken = data.refresh_token

            localStorage.setItem('user', JSON.stringify({
                token: data.token,
                refreshToken: data.refresh_token
            }))
        },
        logout() {
            const localStorage = window.localStorage

            this.user.isAuth = false;
            this.user.token = null;
            this.user.refreshToken = null;

            localStorage.removeItem('user')
        }
    },
    getters: {
        findUserFromToken() {
            if (null === this.user.token) {
                return;
            }

            const tokenParts = this.user.token.split('.')
            const tokenPayload = JSON.parse(atob(tokenParts[1]))

            if (! tokenPayload) {
                return null
            }

            return tokenPayload.user
        }
    },
})