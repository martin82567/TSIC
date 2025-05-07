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

messaging.onBackgroundMessage((payload) => {
    console.log('[firebase-messaging-sw.js] Received background message ', payload);
    
    const notificationTitle = payload.data.title;
    var notificationType = payload.data.type;
    var senderId = payload.data.sender_id;
    var receiverUniqueName = payload.data.unique_name;
    var encryptSenderId = payload.data.encrypt_sender_id;
    var roomCreateData = {};

    if (notificationType == 'video_chat') {
        $("#videoCallPop").modal({backdrop: "static"});
        callingAudio.load();
        callingAudio.play();

    } else if  (notificationType == 'denied_call') {
        $("#videoCallPop").modal("hide");
        callingAudio.pause(); 

        $.post(
            mainUrl + "/api/webvideochat/disconnect_room", {
                unique_name: receiverUniqueName,
            },
            function(data, status) {
                roomCreateData = {};
                alert(data.message);
                window.location.href = mainUrl + "/mentor/chat/userlist?type=mm";
            }
        );
    }

    const notificationOptions = {
        body: payload.data.body,
        icon: '/icon.png'
    };

    return self.registration.showNotification(notificationTitle, notificationOptions);
});