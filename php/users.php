<?php
    session_start();
    include_once "config.php";
    $outgoing_id = $_SESSION['unique_id'];
    $admin = $_SESSION['admin'];
    $sql = "SELECT * FROM users WHERE NOT unique_id = {$outgoing_id} ";
    # si es admin no mostrar admin ,muestrar todos
    if($admin == 1){
        $sql .= "AND admin = 0";
    }
    else{
        $sql .= "AND admin = 1";
    }
    $sql .= " ORDER BY user_id DESC ";
    #echo $sql;
    $query = mysqli_query($conn, $sql);
    $output = "";
    if(mysqli_num_rows($query) == 0){
        $output .= "No users are available to chat";
    }elseif(mysqli_num_rows($query) > 0){
        include_once "data.php";
    }
    echo $output;
?>