<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>iOS Token Test</title>
</head>
<body>
    <h1>iOS Token Test</h1>
    <p id="status">Initializing...</p>
    <script>

        


        // setupWKWebViewJavascriptBridge
        function setupWKWebViewJavascriptBridge(callback) {
            if (window.WKWebViewJavascriptBridge) { return callback(WKWebViewJavascriptBridge); }
            if (window.WKWVJBCallbacks) { return window.WKWVJBCallbacks.push(callback); }
            window.WKWVJBCallbacks = [callback];
            var WVJBIframe = document.createElement('iframe');
            WVJBIframe.style.display = 'none';
            WVJBIframe.src = 'https://__bridge_loaded__';
            document.documentElement.appendChild(WVJBIframe);
            setTimeout(function() { document.documentElement.removeChild(WVJBIframe) }, 0);
        }

        setupWKWebViewJavascriptBridge(function(bridge) {
            bridge.init();

            bridge.callHandler('getToken', null, function(fcmToken) {
                if (fcmToken) {
                    document.getElementById('status').innerText = 'Token obtained: ' + fcmToken;
                    console.log('FCM Token:', fcmToken);
                } else {
                    document.getElementById('status').innerText = 'Failed to get token from iOS.';
                    console.error('Failed to get token from iOS.');
                }
            });
        });
    </script>
</body>
</html>
