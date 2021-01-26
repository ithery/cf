export function getCsrfToken() {
    const tokenTag = document.head.querySelector('meta[name="csrf-token"]')
    let token

    if (!tokenTag) {
        if (!window.cresenity_token) {
            //throw new Error('Whoops, looks like you haven\'t added a "csrf-token" meta tag')
        }

        token = window.cresenity_token
    } else {
        token = tokenTag.content
    }

    return token
}