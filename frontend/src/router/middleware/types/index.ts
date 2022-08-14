import { RouteLocationNormalized, RouteLocationRaw } from "vue-router";
import { Store } from "pinia";

export interface MiddlewarePayload {
    to?: RouteLocationNormalized;
    from?: RouteLocationNormalized;
    authStore?: Store;
    nextMiddleware?: NextMiddlewareCallable | null;
}

export type NextMiddlewareCallable = () => RouteLocationRaw | void

export interface MiddlewareContext {
    to?: RouteLocationNormalized;
    from?: RouteLocationNormalized;
    authStore?: Store;
}

export type Middleware = (payload: MiddlewarePayload) => RouteLocationRaw | void

