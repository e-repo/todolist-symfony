import { MiddlewarePayload } from "@/router/middleware/types";
import { RouteLocationRaw } from "vue-router";

export default function auth(payload: MiddlewarePayload): RouteLocationRaw | void {
    const {authStore, nextMiddleware} = payload

    // @ts-ignore
    if (authStore && ! authStore.user.isAuth) {
        return {
            name: 'Login'
        }
    }

    if (nextMiddleware) {
        return nextMiddleware()
    }
}