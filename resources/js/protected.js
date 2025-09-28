// resources/js/protected.js
export async function makeProtectedRequest(url, method = 'GET', data = null) {
    const token = localStorage.getItem('jwt_token');
    if (!token) {
        window.location.href = '/login';
        return;
    }

    try {
        const response = await fetch(url, {
            method,
            headers: {
                'Authorization': `Bearer ${token}`,
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: data ? JSON.stringify(data) : null,
        });

        if (!response.ok) {
            throw new Error('Erreur lors de la requête');
        }

        return await response.json();
    } catch (error) {
        console.error('Erreur:', error);
        if (error.response && error.response.status === 401) {
            localStorage.removeItem('jwt_token');
            window.location.href = '/login';
        }
        throw error;
    }
}
