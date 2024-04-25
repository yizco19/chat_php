<?php
while ($row = mysqli_fetch_assoc($query)) {
    $sql2 = "SELECT * FROM messages WHERE (incoming_msg_id = {$row['unique_id']}
                OR outgoing_msg_id = {$row['unique_id']}) AND (outgoing_msg_id = {$outgoing_id} 
                OR incoming_msg_id = {$outgoing_id}) ORDER BY msg_id DESC LIMIT 1";
    $query2 = mysqli_query($conn, $sql2);
    $row2 = mysqli_fetch_assoc($query2);
    (mysqli_num_rows($query2) > 0) ? $result = $row2['msg'] : $result = "No hay mensajes disponibles";
    (strlen($result) > 28) ? $msg =  substr($result, 0, 28) . '...' : $msg = $result;
    
    if(isset($row2['attachment'])){
        $attachment = $row2['attachment'];
    }else{
        $attachment = "";
    }
    if (isset($row2['outgoing_msg_id'])) {
        ($outgoing_id == $row2['outgoing_msg_id']) ? $you = "Tu: " : $you = "";
    } else {
        $you = "";
    }
    //comprueba si created_at es diferente de null y si la dia es hoy muestra el tiempo si no muestra la fecha
    if (isset($row2['created_at'])) {
        $created_at = $row2['created_at'];
        $created_at_timestamp = strtotime($created_at);
        $today_date = date('Y-m-d');
    
        // Convertir created_at a formato 'd-m-Y H:i'
        $created_at_formatted = date('d-m-Y H:i', $created_at_timestamp);
    
        // Obtener la fecha de hoy en formato 'Y-m-d'
        $today_date_formatted = date('Y-m-d');
    
        if ($today_date_formatted === date('Y-m-d', $created_at_timestamp)) {
            // Si created_at es hoy, mostrar solo la hora
            $created_at = date('H:i', $created_at_timestamp);
        } else {
            // Si created_at no es hoy, mostrar la fecha completa
            $created_at = $created_at_formatted;
        }
    } else {
        // Si created_at es null, establecer un valor predeterminado
        $created_at = "Fecha no disponible";
    }
    
    ($row['status'] == "Offline now") ? $offline = "offline" : $offline = "";
    ($outgoing_id == $row['unique_id']) ? $hid_me = "hide" : $hid_me = "";

    $output .= '<a href="chat.php?user_id=' . $row['unique_id'] . '">
                    <div class="content">
                    <img src="php/images/' . $row['img'] . '" alt="">
                    <div class="details">
                        <span>' . $row['fname'] . " " . $row['lname'] . '</span>
                        <p>' . $you . $msg . ' <span style="color: #1ce5e8;">' .$attachment. '</span> </p>
                    </div>
                    </div>
                    <div class="last-message-time">' . $created_at . '</div>
                    <div class="status-dot ' . $offline . '"><i class="fas fa-circle"></i></div>
                </a>';
}
