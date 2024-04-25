
// Configuración de Firebase    
const firebaseConfig = {
    apiKey: "AIzaSyBI_Us0QpCWVaKlR6Ko9KdxKEmVmAN0U6k",
authDomain: "sunny-idiom-413714.firebaseapp.com",
projectId: "sunny-idiom-413714",
storageBucket: "sunny-idiom-413714.appspot.com",
messagingSenderId: "30863650512",
appId: "1:30863650512:web:a9b85477f77e34db5538d5",
measurementId: "G-HPNTQ8P3VH"
};

// Inicializamos Firebase
const app = firebase.initializeApp(firebaseConfig);
const messaging = firebase.messaging();

// Solicitamos el token del dispositivo
messaging.getToken({ vapidKey: 'AAAABy-d7tA:APA91bGjwJBRNv2iwZSeSdM6nhN0KX15eLsozZ_LMywupwfJeDI8rpbmBEJI4P3TQ5GakX_75F4ZT2XjcxkfNVXUJwQolYLAa-BYN8QU-_LGB5UbgW7tkD1FkAOVKt0z-q3Inu3ZYd4O' }).then((currentToken) => {
    if (currentToken) {
        // Mostramos el token en el DOM
        document.querySelector('.token').textContent = currentToken;
    } else {
        console.log('No registration token available.');
    }
}).catch((err) => {
    console.log('An error occurred while retrieving token. ', err);
});

// Manejamos la recepción de mensajes
messaging.onMessage((payload) => {
    console.log('Message received. ', payload);
    const messagingElement = document.querySelector('.message');
    const dataHeaderElement = document.createElement('div');
    const dataElement = document.createElement('div');
    dataElement.style = "overflow-x: hidden";
    dataHeaderElement.textContent = "Message received. ";
    dataElement.textContent = JSON.stringify(payload, null, 2);
    messagingElement.appendChild(dataHeaderElement);
    messagingElement.appendChild(dataElement);
});

