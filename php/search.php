<?php
    session_start();
    include_once "config.php";

    $outgoing_id = $_SESSION['unique_id'];
    $searchTerm = mysqli_real_escape_string($conn, $_POST['searchTerm']);
    $admin =$_SESSION['admin'];
    $super_admin = $_SESSION['is_super_admin'];
    $filterUserNotMessage = mysqli_real_escape_string($conn, $_POST['filterUserNotMessage']);
    $sortDirection = mysqli_real_escape_string($conn, $_POST['sortDirection']);
    // Convertir la variable $filterUserNotMessage a un valor booleano
$filterUserNotMessage = filter_var($filterUserNotMessage, FILTER_VALIDATE_BOOLEAN);

$sql="SELECT u.*, m.*
FROM users u
LEFT JOIN (
    SELECT m.*
    FROM messages m
    INNER JOIN (
        SELECT incoming_msg_id, MAX(created_at) AS max_created_at
        FROM messages
        WHERE outgoing_msg_id = $outgoing_id
        GROUP BY incoming_msg_id
    ) max_dates ON m.incoming_msg_id = max_dates.incoming_msg_id AND m.created_at = max_dates.max_created_at
) m ON u.unique_id = m.incoming_msg_id

WHERE ";

$sql_search = " AND (CONCAT(u.fname, ' ', u.lname) LIKE '%{$searchTerm}%')";
if($super_admin ==1){
    $sql_search = " (CONCAT(u.fname, ' ', u.lname) LIKE '%{$searchTerm}%')";
}else{
if($admin == 1){
$sql .= "admin = 0";
}
else{
$sql .= " admin = 1";
}
}
$sql.= $sql_search;
    $sql.= " ORDER BY created_at {$sortDirection}";
    $output = "";
    $query = mysqli_query($conn, $sql);
    if(mysqli_num_rows($query) > 0){
        include_once "data.php";
    }else{
        $output .= 'No existe un usuario con ese nombre';
    }
    echo $output;
?>