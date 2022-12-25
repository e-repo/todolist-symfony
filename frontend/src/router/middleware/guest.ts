import { MiddlewarePayload } from "@/router/middleware/types"
import { RouteLocationRaw } from "vue-router"

export default function guest(payload: MiddlewarePayload): RouteLocationRaw | void {
    const { authStore, nextMiddleware } = payload

    // @ts-ignore
    if (authStore && authStore.user.isAuth) {
        return {
            name: 'Home'
        }
    }

    if (nextMiddleware) {
        return nextMiddleware()
    }
}
