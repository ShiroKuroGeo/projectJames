export const image = (url) => {
    if (!url) {
        return '/default-avatar.png'
    }

    return `https://projectjames-production.up.railway.app/storage/${url}`
}