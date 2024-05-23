<?php
session_start();
include_once "config.php";
if(isset($_SESSION['is_super_admin']) && $_SESSION['is_super_admin'] == 1){
    
    if(isset($_POST['userId']) && !empty($_POST['userId'])){
        $userId = mysqli_real_escape_string($conn, $_POST['userId']);
        $sql = mysqli_query($conn, "UPDATE users SET admin = 1 WHERE user_id = {$userId}");
        if($sql){
            echo "success";
        }else{
            echo "Algo salió mal. ¡Inténtalo de nuevo!";
        }
    }else{
        echo "Algo salió mal. ¡Inténtalo de nuevo!";
    }
}else{
    header("location: ../login.php");
}