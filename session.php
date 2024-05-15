<?php
session_start();
require_once 'vendor/autoload.php';
require_once 'php/functions.php';
require_once 'auth_config.php';
include_once "php/config.php";
//comprueba si actualmenta ya esta en login.php

if (!isset($_SESSION['unique_id']) &&	 !isset($_SESSION['user_token'])) {
    $currentPage = basename($_SERVER['PHP_SELF']);
    if ($currentPage !== "login.php" && $currentPage !== "register.php") {
        header("location: login.php");
    }
}else{
    $currentPage = basename($_SERVER['PHP_SELF']);
    if ($currentPage === "login.php" || $currentPage === "register.php") {
    header("location: users.php");
    }
    if($currentPage === "chat.php" || $currentPage === "users.php") {
    $_SESSION["first_login"] = true;
    }
    echo '<script src="javascript/message.js" type="module"></script>'; 
}
