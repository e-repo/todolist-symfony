import { createRouter, createWebHistory } from "vue-router";
import HomePage from "@/pages/home/HomePage";
import UsersPage from "@/pages/users/UsersPage";

const routes = [
    {
        path: '/',
        component: HomePage
    },
    {
        path: '/users',
        component: UsersPage
    }
];

const router = createRouter({
    routes,
    history: createWebHistory(process.env.BASE_URL)
});

export default router