<?php
require_once 'vendor/autoload.php';

// init configuration
$clientID = 'NopHO0cHth9ER8wLLBB9CLNt0R1lwRCP';
$clientSecret = 'PBVLqbVGFIvU-PStSSc9nwcqqZkrNyatTZBdElIrsAGNVR8uRroNpe_Bwe9V5orW';
$redirectUri = 'http://localhost/chat-master/php/google-login.php';
// create Client Request to access Google API
$client = new Google_Client();
$client->setClientId($clientID);
$client->setClientSecret($clientSecret);
$client->setRedirectUri($redirectUri);
$client->addScope("email");
$client->addScope("profile");

