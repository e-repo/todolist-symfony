export default function auth({authStore, nextMiddleware}) {
    if (! authStore.user.isAuth) {
        return {
            name: 'Login'
        }
    }

    if (null !== nextMiddleware) {
        return nextMiddleware()
    }
}