<?php
session_start();
include_once "php/config.php";
//comprueba si actualmenta ya esta en login.php

if (!isset($_SESSION['unique_id'])) {
    $currentPage = basename($_SERVER['PHP_SELF']);
    if ($currentPage !== "login.php" && $currentPage !== "register.php") {
        header("location: login.php");
    }
}else{
    $currentPage = basename($_SERVER['PHP_SELF']);
    if ($currentPage === "login.php" || $currentPage === "register.php") {
    header("location: users.php");
    }
    if($currentPage === "chat.php"){
    $_SESSION["first_login"] = true;
    }
    echo '<script src="javascript/message.js" type="module"></script>'; 
}

