/**
 * We will load all of this project's JavaScript dependencies which
 * includes Axios and other helpers.
 */

import axios from 'axios';
window.axios = axios;

/**
 * Axios is a HTTP client for making requests to your backend.
 * Laravel automatically sets the CSRF token for security.
 */

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

/**
 * CSRF Token setup (important for POST, PUT, DELETE requests)
 */

let token = document.head.querySelector('meta[name="csrf-token"]');

if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
} else {
    console.error('CSRF token not found: https://laravel.com/docs/csrf');
}

/**
 * Optional: Laravel Echo (for real-time events like chat, notifications)
 * Uncomment this if you are using broadcasting
 */

/*
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: import.meta.env.VITE_PUSHER_APP_KEY,
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
    forceTLS: true
});
*/