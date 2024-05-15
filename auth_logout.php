<?php

session_start();
unset($_SESSION['user_token']);
session_destroy();
header("Location: auth_login.php");
