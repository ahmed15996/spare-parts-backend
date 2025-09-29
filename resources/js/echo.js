import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

// Only initialize Echo if Pusher credentials are available
const pusherKey = import.meta.env.VITE_PUSHER_APP_KEY;
const pusherCluster = import.meta.env.VITE_PUSHER_APP_CLUSTER;

if (pusherKey && pusherCluster) {
    window.Echo = new Echo({
        broadcaster: 'pusher',
        key: pusherKey,
        cluster: pusherCluster,
        forceTLS: true,
        encrypted: true,
        enabledTransports: ['ws', 'wss'],
        disableStats: true,
        authEndpoint: '/broadcasting/auth',
        auth: {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
        },
    });
} else {
    console.warn('Pusher credentials not found. Real-time features will be disabled. Please set VITE_PUSHER_APP_KEY and VITE_PUSHER_APP_CLUSTER in your .env file.');
    
    // Create a mock Echo object to prevent errors
    window.Echo = {
        private: function(channel) {
            return {
                listen: function(event, callback) {
                    console.warn(`Echo mock: Would listen to event '${event}' on private channel '${channel}'`);
                    return this;
                }
            };
        },
        channel: function(channel) {
            return {
                listen: function(event, callback) {
                    console.warn(`Echo mock: Would listen to event '${event}' on channel '${channel}'`);
                    return this;
                }
            };
        }
    };
}




