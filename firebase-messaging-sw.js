// Give the service worker access to Firebase Messaging.
// importScripts('https://www.gstatic.com/firebasejs/11.6.1/firebase-app.js');
// importScripts('https://www.gstatic.com/firebasejs/11.6.1/firebase-messaging.js');
importScripts('https://www.gstatic.com/firebasejs/8.10.0/firebase-app.js');
importScripts('https://www.gstatic.com/firebasejs/8.10.0/firebase-messaging.js');
// importScripts('/firebase-app.js');
// importScripts('/firebase-messaging.js');

// Initialize the Firebase app in the service worker
firebase.initializeApp({
    apiKey: "AIzaSyD1RlN2N0LtoDnwzb_q89FlJSydfBi4Z40",
    authDomain: "takestockinchildren-427bb.firebaseapp.com",
    projectId: "takestockinchildren-427bb",
    storageBucket: "takestockinchildren-427bb.firebasestorage.app",
    messagingSenderId: "393481861829",
    appId: "1:393481861829:web:7a5197e105a5bdc6a18eac",
    measurementId: "G-M2KVNV8MRZ"
});

const messaging = firebase.messaging();

self.addEventListener('notificationclick', (event) => {
    event.notification.close();
    const urlToOpen = event.notification.data.url || '/';
    
    // Check which action was clicked
    if (event.action === 'join') {
        event.waitUntil(
            clients.matchAll({type: 'window'})
                .then((windowClients) => {
                    // Focus on existing tab if already open
                    for (const client of windowClients) {
                        if (client.url === urlToOpen && 'focus' in client) {
                            return client.focus();
                        }
                    }
                    
                    // Open new tab if not already open
                    if (clients.openWindow) {
                        return clients.openWindow(urlToOpen);
                    }
                })
        );
    } else if (event.action === 'dismiss') {
        // Handle Dismiss button click
        console.log('Notification dismissed');
    } else {
        // Default click behavior (notification body clicked)
        event.waitUntil(
            clients.openWindow(urlToOpen)
        );
    }
});

messaging.onBackgroundMessage((payload) => {
    console.log('[firebase-messaging-sw.js] Received background message ', payload);
    
    const notificationTitle = payload.data.title;
    var notificationType = payload.data.type;
    // var senderId = payload.data.sender_id;
    // var receiverUniqueName = payload.data.unique_name;
    var encryptSenderId = payload.data.encrypt_sender_id;
    // var roomCreateData = {};

    // const notificationOptions = {
    //     body: payload.data.body,
    //     icon: '/icon.png',
    //     requireInteraction: true,  // Keeps notification visible until dismissed
    //     data: { // Include click action data
    //         url: 'https://test.tsicmentorapp.org/mentee/videochat/initiate?mentor_id='+encryptSenderId
    //     },
    //     actions: [
    //         {
    //             action: 'join',
    //             title: 'Join Now'
    //         },
    //         {
    //             action: 'dismiss',
    //             title: 'Dismiss'
    //         }
    //     ],
    // };

    const notificationOptions = {
        body: payload.data.body,
        icon: '/icon.png',
        requireInteraction: true,  // Keeps notification visible until dismissed
        data: { // Include click action data
            url: 'https://test.tsicmentorapp.org/mentee/videochat/initiate?mentor_id='+encryptSenderId
        }
    };

    // Only add actions if notificationType is "video_chat"
    if (notificationType === "video_chat") {
        notificationOptions.actions = [
            {
                action: 'join',
                title: 'Join Now'
            },
            {
                action: 'dismiss',
                title: 'Dismiss'
            }
        ];
    }

    // Show notification
    // self.registration.showNotification(notificationTitle, notificationOptions)
    //     .catch(err => console.error('Notification failed:', err));
    return self.registration.showNotification(notificationTitle, notificationOptions);


});