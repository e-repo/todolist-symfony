import { createRouter, createWebHistory } from "vue-router";
import HomePage from "@/pages/home/HomePage";
import UsersPage from "@/pages/users/UsersPage";
import LoginPage from "@/pages/login/LoginPage";
import ProfilePage from "@/pages/userProfile/ProfilePage";

import { useAuthStore } from "@/store/auth";
import guest from "@/router/middleware/guest";
import auth from "@/router/middleware/auth";
import middlewarePipeline from "@/router/middleware/middlewarePipeline";

const routes = [
    {
        path: '/',
        name: 'Home',
        component: HomePage,
        meta: {
            middleware: [
                auth,
            ]
        }
    },
    {
        path: '/login',
        name: 'Login',
        component: LoginPage,
        meta: {
            middleware: [
                guest,
            ]
        }
    },
    {
        path: '/users',
        name: 'Users',
        component: UsersPage,
        meta: {
            middleware: [
                auth,
            ]
        }
    },
    {
        path: '/profile/:id',
        name: 'Profile',
        component: ProfilePage,
        meta: {
            middleware: [
                auth,
            ]
        }
    }
];

const router = createRouter({
    routes,
    history: createWebHistory(process.env.BASE_URL)
});

router.beforeEach((to, from) => {
    if (! to.meta.middleware) {
        return false;
    }

    const middleware = to.meta.middleware

    const context = {
        to,
        from,
        authStore: useAuthStore(),
    }

    return middleware[0]({
        ...context,
        nextMiddleware: middlewarePipeline(context, middleware, 1)
    })
})

export default router