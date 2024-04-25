// Import the functions you need from the SDKs you need
import { initializeApp } from "https://www.gstatic.com/firebasejs/10.7.1/firebase-app.js";
import { getMessaging, getToken } from "https://www.gstatic.com/firebasejs/10.7.1/firebase-messaging.js";
// TODO: Add SDKs for Firebase products that you want to use
// https://firebase.google.com/docs/web/setup#available-libraries


        // Your web app's Firebase configuration
        // For Firebase JS SDK v7.20.0 and later, measurementId is optional
        const firebaseConfig = {
            apiKey: "AIzaSyBFjCS-Ex4D8K-e3RdS43P_3ZlVhKFf-C0",
            authDomain: "chat-de51e.firebaseapp.com",
            projectId: "chat-de51e",
            storageBucket: "chat-de51e.appspot.com",
            messagingSenderId: "637347459756",
            appId: "1:637347459756:web:caca4c942b1c3cc037a509",
            measurementId: "G-ZMG1VE0SGB"
        };

        // Initialize Firebase
        const app = initializeApp(firebaseConfig);
        const messaging = getMessaging(app);

        
        navigator.serviceWorker.register("sw.js").then(registration => {
            getToken(messaging, {
                serviceWorkerRegistration: registration,
                vapidKey: 'BJv91e372fmWnJxsvfn2ugTrpwmEpY1BIS5Luoqy86cFcf6weeLT-cY7Ux2CbiIa6tWQOCNcNxxUPcKAVhRNJTs' }).then((currentToken) => {
                if (currentToken) {
                    console.log("Token is: "+currentToken);
                    // Send the token to your server and update the UI if necessary
                    sendTokenToServer(currentToken);
                    // ...
                } else {
                    // Show permission request UI
                    console.log('No registration token available. Request permission to generate one.');
                    // ...
                }
            }).catch((err) => {
                console.log('An error occurred while retrieving token. ', err);
                // ...
            });
        });

        function sendTokenToServer(currentToken) {
            // Enviar el token al servidor mediante una solicitud AJAX
            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'php/save-token.php');
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onload = () => {
                if (xhr.readyState === XMLHttpRequest.DONE) {
                    if (xhr.status === 200) {
                      let data = xhr.response;
                      console.log(data);
                    } else {
                    }
                  }
              // Manejar la respuesta si es necesario
            };
            xhr.send('token=' + encodeURIComponent(currentToken));
            }