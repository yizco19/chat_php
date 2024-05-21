function isIOS() {
    return /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;
  }
  
  if (isIOS()) {
    // Llama a la función de Swift para obtener el token
    window.webkit.messageHandlers.getToken.postMessage(null);
  }
  
  function handleToken(token) {
    console.log('FCM Token:', token);
    // Puedes enviar el token a tu servidor o realizar otras acciones necesarias
}
