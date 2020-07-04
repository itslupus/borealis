export function is_authenticated() {
    for (let cookie of document.cookie.split('; ')) {
        if (cookie.split('=')[0] === 'token') {
            return true;
        }
    }

    return false;
}

export function unauthenticate() {
    document.cookie = 'token=; ;path=/; expires=Thu, 01 Jan 1970 00:00:01 GMT;';
}