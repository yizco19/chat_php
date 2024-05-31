<?php
    session_start();
    include_once "config.php";
    $outgoing_id = $_SESSION['unique_id'];
    $admin = $_SESSION['admin'];
    $super_admin=$_SESSION['is_super_admin'];
    $filterUserNotMessage = $_GET['filterUserNotMessage'];

    if(!isset($searchTerm)){
        $searchTerm ="";
    }else{
        $searchTerm = mysqli_real_escape_string($conn, $_POST['searchTerm']);
    }
   
    if(!isset($sortDirection)){
        $sortDirection = "desc";
    }else{
        $sortDirection = mysqli_real_escape_string($conn, $_POST['sortDirection']);
    }
    // Manejar el topic_id si está presente en la solicitud
$topic_id = isset($_POST['topic_id']) && !empty($_POST['topic_id']) ? mysqli_real_escape_string($conn, $_POST['topic_id']) : null;
// Convertir la variable $filterUserNotMessage a un valor booleano
$filterUserNotMessage = filter_var($filterUserNotMessage, FILTER_VALIDATE_BOOLEAN);

$sql_search = " AND (fname LIKE '%{$searchTerm}%' OR lname LIKE '%{$searchTerm}%')";

$sql="SELECT u.*, m.*
FROM users u
LEFT JOIN (
    SELECT m.*
    FROM
        messages m
    INNER JOIN(
        SELECT incoming_msg_id,
            MAX(msg_id) AS max_id
        FROM
            
            messages
            GROUP BY topic_id ,incoming_msg_id,outgoing_msg_id
    ) max_dates
ON
    m.incoming_msg_id = max_dates.incoming_msg_id AND m.msg_id = max_dates.max_id
) m ON u.unique_id = m.incoming_msg_id

WHERE         u.unique_id != $outgoing_id and ";

if($super_admin ==1){
    $sql_search = " (fname LIKE '%{$searchTerm}%' OR lname LIKE '%{$searchTerm}%')";
}else{
if($admin == 1){
    $sql .= " admin = 0";
}
else{
    $sql .= "admin = 1";
}
}
    $sql.= $sql_search;
    $sql.= " ORDER BY created_at {$sortDirection}";
    $output = "";
    echo $sql;
    $query = mysqli_query($conn, $sql);
    $output = "";
    if(mysqli_num_rows($query) == 0){
        $output .= "No users are available to chat";
    }elseif(mysqli_num_rows($query) > 0){
        include_once "data.php";
    }
    echo $output;
?>