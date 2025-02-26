importScripts("https://www.gstatic.com/firebasejs/8.6.8/firebase-app.js");
importScripts("https://www.gstatic.com/firebasejs/8.6.8/firebase-auth.js");
importScripts("https://www.gstatic.com/firebasejs/8.6.8/firebase-messaging.js");

var firebaseConfig = {
    apiKey: "AIzaSyBlp0MEm_5BRHXleR8owSqJmvw_uZmIMzI",
    authDomain: "ewaiter-a25bf.firebaseapp.com",
    databaseURL: "https://ewaiter-a25bf.firebaseio.com",
    projectId: "ewaiter-a25bf",
    storageBucket: "ewaiter-a25bf.appspot.com",
    messagingSenderId: "556392585583",
    appId: "1:556392585583:web:c70660576801ed2b58709a",
    measurementId: "G-PN6H8KW4MP",
};

firebase.initializeApp(firebaseConfig);

const messaging = firebase.messaging();

messaging.setBackgroundMessageHandler(function (payload) {
    console.log(
        "[firebase-messaging-sw.js] Received background message ",
        payload,
    );
    // Customize notification here
    const notificationTitle = payload.notification.title;
    const notificationOptions = {
        body: payload.notification.body,
        data: { url: payload.data.url },
        actions: [{ action: "open_url", title: "Przejdź" }],
    };
    return self.registration.showNotification(
        notificationTitle,
        notificationOptions,
    );
});

self.addEventListener(
    "notificationclick",
    function (event) {
        switch (event.action) {
            case "open_url":
                clients.openWindow(event.notification.data.url); //which we got from above
                break;
            case "any_other_action":
                clients.openWindow("https://www.example.com");
                break;
        }
    },
    false,
);
