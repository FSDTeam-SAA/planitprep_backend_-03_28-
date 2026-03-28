import 'bootstrap';

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

// import Echo from 'laravel-echo';

// import Pusher from 'pusher-js';
// window.Pusher = Pusher;

// window.Echo = new Echo({
//     broadcaster: 'pusher',
//     key: import.meta.env.VITE_PUSHER_APP_KEY,
//     cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER ?? 'mt1',
//     wsHost: import.meta.env.VITE_PUSHER_HOST ?? `ws-${import.meta.env.VITE_PUSHER_APP_CLUSTER}.pusher.com`,
//     wsPort: import.meta.env.VITE_PUSHER_PORT ?? 80,
//     wssPort: import.meta.env.VITE_PUSHER_PORT ?? 443,
//     forceTLS: (import.meta.env.VITE_PUSHER_SCHEME ?? 'https') === 'https',
//     enabledTransports: ['ws', 'wss'],
// });

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allow your team to quickly build robust real-time web applications.
 */

// import './echo';
import { initializeApp } from "firebase/app";
import { getMessaging, getToken, onMessage } from "firebase/messaging";

const firebaseConfig = {
    apiKey: 'AIzaSyDorYa5_AO3sULcDGyADJlWcyUtxYO0HeE',
    authDomain: 'dietitian-61a26.firebaseapp.com',
    databaseURL: 'https://dietitian-61a26.firebaseio.com',
    projectId: 'dietitian-61a26',
    storageBucket: 'dietitian-61a26.appspot.com',
    messagingSenderId: '968524122508',
    appId: '1:968524122508:web:ad643d5e686e52f8e86ea9',
    measurementId: 'G-CBMWMGP4CG',
};

// Initialize Firebase
const app = initializeApp(firebaseConfig);
const messaging = getMessaging(app);


getToken(messaging, { vapidKey: "BH021Co_j0UtF2UX7e7Ut7zTJv1PAFCwP_BpcTxemPudt2kHXlbjAJ35Nhtq-tkFyqjtxCMflSwSbyvrC1WVoGE" })
  .then((currentToken) => {
    if (currentToken) {
      console.log("FCM Token:", currentToken);
      // Send the token to your server and save it
    } else {
      console.log("No registration token available.");
    }
  })
  .catch((err) => {
    console.error("Error getting FCM token:", err);
  });
  
// Request permission to receive notifications
async function requestPermission() {
  try {
    const permission = await Notification.requestPermission();
    if (permission === 'granted') {
      console.log('Notification permission granted.');
      getFcmToken(); // Fetch the FCM token if permission is granted
    } else {
      console.log('Notification permission denied.');
    }
  } catch (error) {
    console.error('Error requesting notification permission:', error);
  }
}

// Get the FCM token
async function getFcmToken() {
  try {
    const token = await getToken(messaging, { vapidKey: "your-public-vapid-key" });
    if (token) {
      console.log("FCM Token:", token);
      // Send this token to your server to subscribe the user to notifications
    } else {
      console.log("No registration token available.");
    }
  } catch (error) {
    console.error("Error getting FCM token:", error);
  }
}

// Handle foreground messages
onMessage(messaging, (payload) => {
  console.log("Foreground message received:", payload);
  // Display notification in the UI or handle as needed
  const notificationOptions = {
    body: payload.notification.body,
    icon: payload.notification.icon,
  };
  new Notification(payload.notification.title, notificationOptions);
});

// Call requestPermission to initiate the process
requestPermission();


