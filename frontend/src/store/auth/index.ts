import { defineStore, StoreDefinition } from 'pinia'
import axios from "axios"
import { Router } from "vue-router"

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

export interface User
{
    id: string;
    first: string;
    last: string;
    email: string;
    roles: string[];
}

interface JWTPayload {
    user: User;
    username: string;
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
        login(email: string, password: string) {
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
        tryRefreshToken(error, router: Router | null = null) {
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

                if (null !== router) {
                    router.go(0)
                }
            }).catch((error) => {
                this.logout()
                router?.push('login')
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

            this.user.isAuth = false
            this.user.token = null
            this.user.refreshToken = null

            localStorage.removeItem('user')
        }
    },
    getters: {
        findUserFromToken(): User | null {
            if (null === this.JWTPayload) {
                return null
            }

            return this.JWTPayload.user
        },
        token(): string | null {
            return this.user.token
        },
        refreshToken(): string | null {
            return this.user.refreshToken
        },
        isAuth(): boolean {
            return this.user.isAuth
        },
        JWTPayload(): JWTPayload | null {
            if (null === this.user.token) {
                return null
            }

            try {
                return JSON.parse(
                    atob(
                        this.user.token.split('.')[1]
                    )
                )
            } catch (e) {
                return null
            }
        }
    },
})
