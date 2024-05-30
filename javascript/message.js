import { initializeApp } from "https://www.gstatic.com/firebasejs/10.7.1/firebase-app.js";
import { getMessaging, getToken } from "https://www.gstatic.com/firebasejs/10.7.1/firebase-messaging.js";

const firebaseConfig = {
    apiKey: "AIzaSyBFjCS-Ex4D8K-e3RdS43P_3ZlVhKFf-C0",
    authDomain: "chat-de51e.firebaseapp.com",
    projectId: "chat-de51e",
    storageBucket: "chat-de51e.appspot.com",
    messagingSenderId: "637347459756",
    appId: "1:637347459756:web:caca4c942b1c3cc037a509",
    measurementId: "G-ZMG1VE0SGB"
};

const app = initializeApp(firebaseConfig);
const messaging = getMessaging(app);

navigator.serviceWorker.register("sw.js").then(registration => {
    getToken(messaging, {
        serviceWorkerRegistration: registration,
        vapidKey: 'BJv91e372fmWnJxsvfn2ugTrpwmEpY1BIS5Luoqy86cFcf6weeLT-cY7Ux2CbiIa6tWQOCNcNxxUPcKAVhRNJTs'
    }).then((currentToken) => {
        if (currentToken) {
            sendTokenToServer(currentToken);
        } else {
            console.log('No registration token available. Request permission to generate one.');
        }
    }).catch((err) => {
        console.error('An error occurred while retrieving token. ', err);
        requestNotificationPermission();
    });
}).catch(error => {
    console.error('An error occurred during service worker registration:', error);
    requestNotificationPermission();
});

function requestNotificationPermission() {
    if (typeof Android !== 'undefined') {
        Android.requestNotificationPermission();
        setTimeout(function () {
            const token = localStorage.getItem('token_push');
            if (token) {
                sendTokenToServer(token);
            }
        }, 3000);
    } else if (window.webkit && window.webkit.messageHandlers && window.webkit.messageHandlers.getToken) {
        window.webkit.messageHandlers.getToken.postMessage(null);
    } else {
        console.error('No interface available for requesting notification permission.');
    }
}

function sendTokenToServer(currentToken) {
    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'php/save-token.php');
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onload = () => {
        if (xhr.readyState === XMLHttpRequest.DONE) {
            if (xhr.status === 200) {
                let data = xhr.response;
            } else {
                console.error('Failed to send token to server.');
            }
        }
    };
    xhr.send('token=' + encodeURIComponent(currentToken));
}
