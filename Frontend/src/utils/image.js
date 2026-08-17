export const image = (url) => {
    if (!url) {
        return '/default-avatar.png'
    }

    return `http://127.0.0.1:8000/storage/${url}`
}