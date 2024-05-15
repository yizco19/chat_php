<?php
session_start();
if (isset($_SESSION['unique_id'])) {
    include_once "config.php";
    include_once "functions.php";
    $outgoing_id = $_SESSION['unique_id'];
    $incoming_id = mysqli_real_escape_string($conn, $_POST['incoming_id']);
    $message = mysqli_real_escape_string($conn, $_POST['message']);
    $adjunto = isset($_FILES['attachment']) ? $_FILES['attachment'] : null;
    // Manejar la carga del archivo
    if ($adjunto) {
        $adjunto_nombre = $adjunto['name'];
        $adjunto_temp = $adjunto['tmp_name'];
    
        $ruta = "images/" . $adjunto_nombre;
        move_uploaded_file($adjunto_temp, $ruta);
    }


    // Si no hay adjunto, inserta solo el mensaje
    $query = mysqli_query($conn, "INSERT INTO messages (incoming_msg_id, outgoing_msg_id, msg, attachment, is_sender)
                                    VALUES ({$incoming_id}, {$outgoing_id}, '{$message}', '{$adjunto_nombre}', 0)") or die(mysqli_error($conn));

    $sql2 = "SELECT * FROM users WHERE unique_id = {$incoming_id}";
    // Comprueba si el usuario está en línea para notificarlo
    $query2 = mysqli_query($conn, $sql2) or die(mysqli_error($conn));

    if (mysqli_num_rows($query2) > 0) {
        $row = mysqli_fetch_array($query2);

        // Conseguir token push
        $token_push = $row["token_push"];
        if (isset($_SESSION["username"])) {
            $username = $_SESSION["username"];
        } else {
            $username = "Desconocido";
        }

        sleep(1);
                //comprueba si actualmente esta con chat de username
                $sql = "SELECT * FROM messages WHERE incoming_msg_id = {$incoming_id} AND outgoing_msg_id = {$outgoing_id} AND is_seen = 0";
                $result = mysqli_query($conn, $sql);
                echo $sql;
        if ($token_push != null && mysqli_num_rows($result) > 0) {
          echo "Hay notificaciones por leer";  
            render_php("send-push", [
                "token_push" => $token_push,
                "message" => $message,
                "username" => $username
            ]);
        }

    }
} else {
    header("location: ../login.php");
}

