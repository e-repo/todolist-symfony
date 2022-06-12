import { RouteLocationNormalized, RouteLocationRaw } from "vue-router";
import { Store } from "pinia";

export interface MiddlewarePayload {
    to?: RouteLocationNormalized;
    from?: RouteLocationNormalized;
    authStore?: Store;
    nextMiddleware?: NextMiddleware | null;
}

export type NextMiddleware = () => void

export interface MiddlewareContext {
    to?: RouteLocationNormalized;
    from?: RouteLocationNormalized;
    authStore?: Store;
}

export type Middleware = (payload: MiddlewarePayload) => RouteLocationRaw | void

