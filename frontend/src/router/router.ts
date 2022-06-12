import { createRouter, createWebHistory, RouteLocationNormalized, Router, RouteRecordRaw } from "vue-router";
import { Middleware, MiddlewarePayload, MiddlewareContext } from "@/router/middleware/types";
import { useAuthStore } from "@/store/auth";

import HomePage from "@/pages/home/HomePage.vue";
import UsersPage from "@/pages/users/UsersPage.vue";
import LoginPage from "@/pages/login/LoginPage.vue";
import ProfilePage from "@/pages/userProfile/ProfilePage.vue";

import guest from "@/router/middleware/guest";
import auth from "@/router/middleware/auth";
import middlewarePipeline from "@/router/middleware/middlewarePipeline";

const routes: RouteRecordRaw[] = [
    {
        path: '/',
        name: 'Home',
        component: HomePage,
        meta: {
            middleware: [
                auth,
            ] as Middleware[]
        }
    },
    {
        path: '/login',
        name: 'Login',
        component: LoginPage,
        meta: {
            middleware: [
                guest,
            ] as Middleware[]
        }
    },
    {
        path: '/users',
        name: 'Users',
        component: UsersPage,
        meta: {
            middleware: [
                auth,
            ] as Middleware[]
        }
    },
    {
        path: '/profile/:id',
        name: 'Profile',
        component: ProfilePage,
        meta: {
            middleware: [
                auth,
            ] as Middleware[]
        }
    }
];

const router: Router = createRouter({
    routes,
    history: createWebHistory(process.env.BASE_URL)
});

router.beforeEach((to: RouteLocationNormalized, from: RouteLocationNormalized) => {
    if (! to.meta.middleware) {
        return false;
    }

    const middleware = to.meta.middleware as Middleware[]

    const context: MiddlewareContext = {
        to,
        from,
        authStore: useAuthStore(),
    }

    const payload: MiddlewarePayload = {
        ...context,
        nextMiddleware: middlewarePipeline(context, middleware, 1)
    }

    middleware[0](payload)
})

export default router