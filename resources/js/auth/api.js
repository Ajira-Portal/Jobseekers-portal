function authCsrfToken() {
    const meta = document.querySelector('meta[name="csrf-token"]');
    return meta ? meta.getAttribute('content') : '';
}

window.authApi = {
    login(url, data) {
        return $.ajax({
            url: url,
            type: 'POST',
            data: data,
            headers: {
                'X-CSRF-TOKEN': authCsrfToken()
            }
        });
    },

    register(url, data) {
        return $.ajax({
            url: url,
            type: 'POST',
            data: data,
            headers: {
                'X-CSRF-TOKEN': authCsrfToken()
            }
        });
    },

    verifyZanId(url, data) {
        return $.ajax({
            url: url,
            type: 'POST',
            data: data,
            headers: {
                'X-CSRF-TOKEN': authCsrfToken()
            }
        });
    },

    forgotPassword(url, data) {
        return $.ajax({
            url: url,
            type: 'POST',
            data: data,
            headers: {
                'X-CSRF-TOKEN': authCsrfToken()
            }
        });
    },

    resetPassword(url, data) {
        return $.ajax({
            url: url,
            type: 'POST',
            data: data,
            headers: {
                'X-CSRF-TOKEN': authCsrfToken()
            }
        });
    }
};
