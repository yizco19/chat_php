<?php
    session_start();
    include_once "config.php";
    $unique_id = $_SESSION['unique_id'];
    $admin = $_SESSION['admin'];
    $super_admin = $_SESSION['is_super_admin'];
    $filterUserNotMessage = $_GET['filterUserNotMessage'];

    if(!isset($searchTerm)){
        $searchTerm = "";
    } else {
        $searchTerm = mysqli_real_escape_string($conn, $_POST['searchTerm']);
    }
   
    if(!isset($sortDirection)){
        $sortDirection = "desc";
    } else {
        $sortDirection = mysqli_real_escape_string($conn, $_POST['sortDirection']);
    }
    
    // Manejar el topic_id si está presente en la solicitud
    $topic_id = isset($_POST['topic_id']) && !empty($_POST['topic_id']) ? mysqli_real_escape_string($conn, $_POST['topic_id']) : null;
    
    // Convertir la variable $filterUserNotMessage a un valor booleano
    $filterUserNotMessage = filter_var($filterUserNotMessage, FILTER_VALIDATE_BOOLEAN);

    $sql_search = "(fname LIKE '%{$searchTerm}%' OR lname LIKE '%{$searchTerm}%')";
    $sql = "SELECT * FROM users WHERE NOT unique_id = {$unique_id} AND {$sql_search} ORDER BY user_id DESC";

    $output = "";
    $query = mysqli_query($conn, $sql);
    $output = "";
    if(mysqli_num_rows($query) == 0){

        $output .= "No users are available to chat";
    } elseif(mysqli_num_rows($query) > 0){
        $user_data = $query->fetch_all(MYSQLI_ASSOC); // Obtener los datos de los usuarios
        $array_data = array();
  
        foreach($user_data as $user_row){
            //echo  $user_row['lname'] . ' '. $user_row['fname'] .'<br>';
            $sql_messages = " SELECT 
            msg_id,
            msg,
            topic_id,
            seen_at,
            is_sender,
            is_seen,
            created_at,
            attachment,
            incoming_msg_id,
            outgoing_msg_id
        FROM
            messages
        WHERE
            msg_id IN ( SELECT MAX(msg_id) AS max_id
             FROM messages WHERE ((incoming_msg_id = $unique_id and outgoing_msg_id = {$user_row['unique_id']}) 
             OR (incoming_msg_id = {$user_row['unique_id']} and outgoing_msg_id = $unique_id)) GROUP BY topic_id)";
            $result_messages = mysqli_query($conn, $sql_messages);
            //echo $sql_messages.'<br>';
            $messages_data = $result_messages->fetch_all(MYSQLI_ASSOC);
            foreach($messages_data as $msg_row){
                //echo $msg_row['msg'].'<br>';
                $array_data[] = array(
                    'admin' => $user_row['admin'],
                    'msg' => $msg_row['msg'],
                    'topic_id' => $msg_row['topic_id'],
                    'attachment' => $msg_row['attachment'],
                    'incoming_msg_id' => $msg_row['incoming_msg_id'],
                    'outgoing_msg_id' => $msg_row['outgoing_msg_id'],
                    'status' => $user_row['status'],
                    'img' => $user_row['img'],
                    'unique_id' => $user_row['unique_id'],
                    'lname' => $user_row['lname'],
                    'fname' => $user_row['fname'],
                    'user_id' => $user_row['user_id'],
                    'msg_id' => $msg_row['msg_id']
                );
            }
        }
        //ordenar el array por sortDirection
        if($sortDirection == "desc"){
            usort($array_data, function($a, $b){
                return $b['msg_id'] <=> $a['msg_id'];
            });
        } else {
            usort($array_data, function($a, $b){
                return $a['msg_id'] <=> $b['msg_id'];
            });
        }
        //filtro topic_id
        if($topic_id!= 0 && $topic_id!= null){
            $array_data = array_filter($array_data, function($a) use ($topic_id){
                return $a['topic_id'] == $topic_id;
            });
        }
        //filtro si es admin o no 
            $array_data = array_filter($array_data, function($a) use ($admin){
                return $a['admin'] !== $admin ;
            });
        //filtro si es super admin o no
            
        include_once "data.php";
    }
    echo $output;
?>
