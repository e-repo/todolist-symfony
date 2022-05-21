import { defineStore } from 'pinia'
import axios from "axios";

export const useAuthStore = defineStore('auth', {
    state: () => ({
        user: {
            isAuth: false,
            token: null,
            refreshToken: null,
        },
        loginError: {
            message: null,
            data: null
        }
    }),
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
        refreshToken($router = null) {
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
                console.log(error.message)
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
    }
})