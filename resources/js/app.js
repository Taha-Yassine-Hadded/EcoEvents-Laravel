import './bootstrap';
import { makeProtectedRequest } from './protected';
window.makeProtectedRequest = makeProtectedRequest;

// Intercepter toutes les requêtes AJAX pour ajouter l'en-tête Authorization
(function(open) {
    XMLHttpRequest.prototype.open = function(method, url, async, user, pass) {
        open.call(this, method, url, async, user, pass);
        const token = localStorage.getItem('jwt_token');
        console.log('Intercepteur AJAX:', { method, url, token: token ? token.substring(0, 20) + '...' : null });
        if (token) {
            this.setRequestHeader('Authorization', `Bearer ${token}`);
        }
    };
})(XMLHttpRequest.prototype.open);
