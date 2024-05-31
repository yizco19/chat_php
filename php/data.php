<?php


include_once "functions.php";
//while ($row = mysqli_fetch_assoc($query)) {
foreach ($array_data as $row) {
    $super_admin =$_SESSION['is_super_admin'];
    $result = $row['msg'];
    $topic_id= $row['topic_id'];

    if($result == ""){
        $result = "No hay mensajes";

    }
    if (strpos($result, "https://meet.jit.si/") === 0) {
        $result = "Enlace de videoconferencia";
    }
    
    (strlen($result) > 28) ? $msg =  substr($result, 0, 28) . '...' : $msg = $result;

    $filter = false; // Inicializamos $filter como falso

    if (isset($row['attachment'])) {
        $attachment = $row['attachment'];
    } else {
        $attachment = "";
    }

    if (isset($row['outgoing_msg_id'])) {
        ($unique_id == $row['outgoing_msg_id']) ? $you = "Tu: " : $you = "";
    } else {
        $you = "";
    }

    // Comprobamos si created_at es diferente de null y si el día es hoy mostramos el tiempo, sino mostramos la fecha
    if (isset($row['created_at'])) {
        $created_at = $row['created_at'];
        $created_at_timestamp = strtotime($created_at);
        $today_date = date('Y-m-d');

        // Convertimos created_at a formato 'd-m-Y H:i'
        $created_at_formatted = date('d-m-Y H:i', $created_at_timestamp);

        // Obtenemos la fecha de hoy en formato 'Y-m-d'
        $today_date_formatted = date('Y-m-d');

        if ($today_date_formatted === date('Y-m-d', $created_at_timestamp)) {
            // Si created_at es hoy, mostramos solo la hora
            $created_at = date('H:i', $created_at_timestamp);
        } else {
            // Si created_at no es hoy, mostramos mes y día
            $created_at = date('d-m', $created_at_timestamp);
        }
        if($result == "No hay mensajes"){
            $result="";
        }
    } else {
        // Si created_at es null, establecemos un valor predeterminado
        $created_at = "";
        if($msg == "No hay mensajes"){
        $filter = true; // Establecemos $filter como verdadero si no hay fecha de creación
        }
    }

    ($row['status'] == "Offline now") ? $offline = "offline" : $offline = "";
    ($unique_id == $row['unique_id']) ? $hid_me = "hide" : $hid_me = "";

    // Aplicamos el filtro solo si $filterUserNotMessage es verdadero
    //echo "filter'. $filter";
    if ($filterUserNotMessage && $filter ) {
       // $output .= '$filternot'.$filterUserNotMessage;
        //$output .= '%filter'. $filter;
    
        continue;
    }

    // Agregamos el usuario al $output solo si no se aplica el filtro
    $output .= '<a href="chat.php?user_id=' . $row['unique_id'];

    if ($topic_id != 0 && $topic_id != null) {
        $output .= '&topic_id=' . $row['topic_id'];
    }

    $output .= '" class="user_link" data-id="' . $row['unique_id'] . '">

                        <div class="content">';
                        if ($topic_id != 0 && $topic_id != null) {
                            $output .= getImgById($topic_id);
                        }
                        $output.='    <img src="' . $row['img'] . '" alt="">
                            <div class="details">
                                <span>' . $row['fname'] . " " . $row['lname'] . '</span>
                                <p>' . $you . $msg . ' <span style="color: #1ce5e8;">' . $attachment . '</span> </p>
                            </div>
                        </div>
                        <div class="last-message-time">' . $created_at . '</div>
                        <div class="status-dot ' . $offline . '" style="margin-right:10px;"><i class="fas fa-circle"></i></div>';
    
    if ($super_admin == 1) {
        $output .= '<label class="switch">';
        $output .= '<input type="checkbox" class="toggle-switch" data-id="' . $row['user_id'] . '" ' . ($row['admin'] == 1 ? 'checked' : '') . '>';
        $output .= '<span class="slider round"></span>';
        $output .= '</label>';
        $output .= '<img src="resource/delete.png" alt="delete" class="delete-user" data-id="' . $row['unique_id'] . '" />';
    }
    
    $output .= '</a>';
    

}

