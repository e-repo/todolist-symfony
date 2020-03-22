import Vue from 'vue'
import VueRouter from 'vue-router'
import Home from '@/components/Main/Home'
import Registration from '@/components/Auth/Registration'
import Login from '@/components/Auth/Login'

Vue.use(VueRouter)

export default new VueRouter({
    mode: 'history',
    base: process.env.BASE_URL,
    routes: [
        {
            path: '/',
            name: 'home',
            component: Home
        },
        {
            path: '/registration',
            name: 'registration',
            component: Registration
        },
        {
            path: '/login',
            name: 'login',
            component: Login
        }
    ],
})