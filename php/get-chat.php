<?php
session_start();
if (isset($_SESSION['unique_id'])) {
    include_once "config.php";
    
    // Obtener el ID de usuario saliente de la sesión
    $outgoing_id = $_SESSION['unique_id'];

    
    // Obtener el ID de usuario entrante del formulario
    $incoming_id = mysqli_real_escape_string($conn, $_POST['incoming_id']);
    $topic_id = isset($_POST['topic_id'])? mysqli_real_escape_string ($conn, $_POST['topic_id']) : null;
    $first = $_SESSION["first_login"];
    if ($first) {
        displayMessage($outgoing_id, $incoming_id, $conn,$topic_id);
        
    }
    // Obtener los mensajes no leídos para el usuario saliente
    $sql_seen = "SELECT COUNT(*) AS unread_count 
                FROM messages 
                WHERE incoming_msg_id = {$outgoing_id} 
                AND outgoing_msg_id = {$incoming_id} 
                AND is_seen = 0";
                if($topic_id!== null && $topic_id!== "") {
                    $sql_seen.= " AND topic_id = {$topic_id}";
                }

    $msg_seen = mysqli_query($conn, $sql_seen);

    // Obtener los mensajes no enviados para el usuario entrante
    $sql_sender = "SELECT COUNT(*) AS unread_count 
                FROM messages 
                WHERE incoming_msg_id = {$incoming_id} 
                AND outgoing_msg_id = {$outgoing_id} 
                AND is_sender = 0";
                                if($topic_id!== null) {
                                    $sql_seen.= " AND topic_id = {$topic_id}";
                                }
    $msg_sender = mysqli_query($conn, $sql_sender);

    // Manejo de errores y actualización de mensajes
    if ($msg_seen && $msg_sender) { 
		
        $row_seen = mysqli_fetch_assoc($msg_seen);
        $row_sender = mysqli_fetch_assoc($msg_sender);
        $unread_count_seen = $row_seen['unread_count'];
        $unread_count_sender = $row_sender['unread_count'];
       
        // Actualizar mensajes no leídos solo si hay alguno
        if ($unread_count_seen > 0) {
            $sql_update_seen = "UPDATE messages 
                                SET is_seen = 1, seen_at = NOW() 
                                WHERE incoming_msg_id = {$outgoing_id} 
                                AND outgoing_msg_id = {$incoming_id} 
                                AND is_seen = 0";
            mysqli_query($conn, $sql_update_seen);
        }

        // Actualizar mensajes no enviados solo si hay alguno
        if ($unread_count_sender > 0) {
            $sql_update_sender = "UPDATE messages
                                SET is_sender = 1
                                WHERE incoming_msg_id = {$incoming_id}
                                AND outgoing_msg_id = {$outgoing_id}
                                AND is_sender = 0";
            mysqli_query($conn, $sql_update_sender);
			
        }
        $_SESSION["first_login"] = false;
        // Mostrar mensajes después de actualizar los mensajes no leídos
        if($unread_count_seen > 0 || $unread_count_sender > 0) {
            if( !$first) {

                displayMessage($outgoing_id, $incoming_id, $conn,$topic_id);
            }
            
        }

    } else {
        // Manejo de errores
        echo "Error al obtener mensajes: " . mysqli_error($conn);
    }
} else {
    // Redirigir al usuario si no ha iniciado sesión
    header("location: ../login.php");
}

function displayMessage($outgoing_id, $incoming_id,$conn, $topic_id) {
    $output = "";
    $prev_date = ""; 

    $current_date = "";

      // Utilizando la marca de tiempo en la consulta SQL para filtrar mensajes nuevos
      $sql = "SELECT * FROM messages 
      LEFT JOIN users ON users.unique_id = messages.outgoing_msg_id
      WHERE ((outgoing_msg_id = {$outgoing_id} AND incoming_msg_id = {$incoming_id})
             OR (outgoing_msg_id = {$incoming_id} AND incoming_msg_id = {$outgoing_id}))";
if ($topic_id !== null && $topic_id!== "") {
  $sql .= " AND topic_id = {$topic_id}";
} else {
  $sql .= ' AND (topic_id IS NULL OR topic_id = "")';
}
$sql .= " ORDER BY created_at ASC";

$query = mysqli_query($conn, $sql);


// Cadena para buscar al inicio del enlace
$inicio = "https://meet.jit.si/";

if ($query) {

  if (mysqli_num_rows($query) > 0) {

      while ($row = mysqli_fetch_assoc($query)) {

          $adjunto = $row['attachment'];
          $es_imagen = false;
          $extensiones_permitidas = array('jpg', 'jpeg', 'png', 'gif');
          $extension = pathinfo($adjunto, PATHINFO_EXTENSION);
          if (in_array(strtolower($extension), $extensiones_permitidas)) {
              $es_imagen = true;
          }
          if($row['msg']!=null && $row['msg']!=" "){
              $message = '<p>' . $row['msg'] . '</p>';

          }else{
              $message = "";
          }
         
          $fecha = $row['created_at'];
          $created_at = date('d-m-Y H:i', strtotime($fecha));
          $current_date = date('Y-m-d', strtotime($created_at));
          // Verifica si la fecha actual es diferente a la fecha anterior
          if ($current_date != $prev_date) {
              // Si es diferente, muestra la fecha en el chat
              $output .= '<div class="msg-date">' . $current_date . '</div>';
              // Actualiza la fecha previa
              $prev_date = $current_date;
          }
      

          if(strpos($row['msg'], $inicio) === 0) {
              $message = '<a href="' . htmlspecialchars($row['msg']) . '" style="width: 100px;" > <i class="fas fa-video" style="color: #990033;"></i> click</a>';

          }        

          // verifica si el mensaje es un enlace de videollamada
          if ($row['outgoing_msg_id'] === $outgoing_id) {
              $output .= '<div class="chat outgoing">';
              if( $row['is_seen'] == 1) {
                  $seen_at = $row['seen_at'];
                  $seen_date = date('Y-m-d', strtotime($seen_at));
                  if($seen_date == $current_date) {
                      $seen_time = date('H:i', strtotime($seen_at));
                  }else{
                      $seen_time = date('d-m-Y H:i', strtotime($seen_at));
                  }

                  $output .= '<div class="is-seen">'.$seen_time.'<i class="fas fa-check"></i></div>';
              }
                              $output.='<div class="details">
                                 ' . $message ;
                                 
          } else {

              $output .= '<div class="chat incoming">

                              <img src="' . $row['img'] . '" alt="" class="profile-image">
                              <div class="details">
                              ' . $message.'                  ' ;
          }

          if ($row['attachment'] != null && $row['attachment'] != "images/") {
              if ($es_imagen) {
                  $output .= '<a href="php/images/' . $adjunto . '" download="' . $adjunto . '"><img class="imagenFile" src="php/images/' . $adjunto . '" alt="'.$adjunto.'"></a>';

              } else {
                  $output .= '<a href="php/images/' . $adjunto . '" download="' . $adjunto . '">' . $adjunto . '</a>
                                            ';

              }

          }


          $output .= '</div>';
          if($row['outgoing_msg_id'] !== $outgoing_id) {
            $sender_at = $row['created_at'];
            $sender_time = date('H:i', strtotime($sender_at));
            $output .= '<div class="is-sender" style="margin-right:10px; display: flex;">' . $sender_time . '</div>';  
            }

          $output .= '</div>';    
      }
  } else {
      $output .= '';
  }
  echo $output;
} else {
  echo "Error al ejecutar la consulta SQL: " . mysqli_error($conn);
}
}
