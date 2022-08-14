import { Middleware, NextMiddlewareCallable, MiddlewareContext } from "@/router/middleware/types";

export default function middlewarePipeline(
    context: MiddlewareContext,
    middleware: Middleware[],
    index: number
): NextMiddlewareCallable | null {
    const nextMiddleware = middleware[index]

    if (! nextMiddleware) {
        return null
    }

    return () => {
        const nextPipeline = middlewarePipeline(context, middleware, index + 1)

        nextMiddleware({...context, nextMiddleware: nextPipeline})
    }
}