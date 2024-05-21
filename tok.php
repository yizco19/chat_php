<input type="text" name="token" id="token" value="" />
<input type="text" name="error_token" id="error_token" value="0" />


    <script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js?v=<?php echo(rand()); ?>"></script>
    <script>
        // if (matchMedia && window.matchMedia('(min-device-width: 320px) and (max-device-width: 480px)').matches) {
        // FIREBASE
        ios_token = setupWKWebViewJavascriptBridge(function (bridge) {
            bridge.init();
        });

        if (ios_token == undefined) {
            document.getElementById("error_token").value = "1";

            function getVariables(fcmToken) {
                document.getElementById('token').value = fcmToken;
            }

            $(document).ready(function () {
                window.location.href = "mobincube://javascript/getVariables('{fcmToken}')";
            });
        }
        // }
        function isIOS() {
  return /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;
}

if (isIOS()) {
    document.getElementById("token").value = "IOS";
  // Llama a la función de Swift para obtener el token
  window.webkit.messageHandlers.getToken.postMessage(null);
}

    </script>
