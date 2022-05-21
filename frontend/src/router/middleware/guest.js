export default function guest({authStore, nextMiddleware}) {
    if (authStore.user.isAuth) {
        return {
            name: 'Home'
        }
    }

    if (null !== nextMiddleware) {
        return nextMiddleware()
    }
}