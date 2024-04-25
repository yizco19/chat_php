<?php
use Google\Auth\Credentials\ServiceAccountCredentials;
use Google\Auth\HttpHandler\HttpHandlerFactory;

/*
https://fcm.googleapis.com//v1/projects/<YOUR-PROJECT-ID>/messages:send
Content-Type: application/json
Authorization: bearer <YOUR-ACCESS-TOKEN>

{
  "message": {
    "token": "eEz-Q2sG8nQ:APA91bHJQRT0JJ...",
    "notification": {
      "title": "Background Message Title",
      "body": "Background message body"
    },
    "webpush": {
      "fcm_options": {
        "link": "https://dummypage.com"
      }
    }
  }
}*/
require 'vendor\autoload.php';

$credentials = new ServiceAccountCredentials(
    "https://www.googleapis.com/auth/firebase.messaging",
    json_decode(file_get_contents("pvKey.json"), true)
);
$token = $credentials->fetchAuthToken(HttpHandlerFactory::build());

$ch = curl_init("https://fcm.googleapis.com//v1/projects/chat-de51e/messages:send");
curl_setopt( $ch, CURLOPT_HTTPHEADER,[
    'Content-Type: application/json',
    'Authorization: Bearer ' . $token['access_token']
]);

curl_setopt( $ch, CURLOPT_POSTFIELDS,'{
    "message": {
      "token": "f2dPlVk6tnvwwp30r3BgWh:APA91bFGyzWSyIC2mfA_tUW_TwRJ191rB3uVjDWl2JCgsfYUaVdRYUJH0pprWRdq_S_OhtmIOgcCzAw3X0n84xcD1JJXBWd_3dk-E_uftQt1tnYaATMYIBEB5Y0ExOP9eDn04kFdgO29",
      "notification": {
        "title": "Background Message Title",
        "body": "Background message body",
        "image": "https://emojiisland.com/cdn/shop/products/Emoji_Icon_-_Clown_emoji_large.png?v=1571606089"
      },
      "webpush": {
        "fcm_options": {
          "link": "https://google.com"
        }
      }
    }
  }');

  curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, "post" );

  $response = curl_exec( $ch );
  curl_close( $ch );
  echo $response;