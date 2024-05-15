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
require_once  '../vendor/autoload.php';


$token_push = $data['token_push'];
$message = $data['message'];
$username = $data['username'];
echo $token_push .''. $message .'';
if(isset($token_push)!= null && isset($message) != null && isset($username) != null){  

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
      "token": "'.$token_push.'",
      "notification": {
        "title": "Mensaje de '.$username.'",
        "body": "'.$message.'",
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
  
}