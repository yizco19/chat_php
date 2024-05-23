<?php

declare(strict_types= 1);

function render_php(string $template, array $data = []){

    extract($data);
    require "$template.php";
}
function connect() {
    $host = "localhost";
    $username = "root";
    $password = "";
    $dbname = "chat-master";

    try {
        $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $conn;
    } catch (PDOException $e) {
        die(json_encode(['error' => 'Error de conexión a la base de datos']));
    }
}

function sendEmailInBackground(string $incoming_id,string $outgoing_id, string $username) {

            // hace una contar tiempo si el usuario no seen el mensaje se requier para notificarlo a por email
            sleep(30);
    include_once "config.php";
// Comprueba si el usuario destinatario es admin
$sql3 = "SELECT * FROM users WHERE unique_id = {$incoming_id} AND admin = 1";
$query3 = mysqli_query($conn, $sql3) or die(mysqli_error($conn));

if (mysqli_num_rows($query3) > 0) {
    // El destinatario es admin, procede a verificar los mensajes no vistos
    $sql2 = "SELECT * FROM messages
             WHERE incoming_msg_id = {$outgoing_id} AND outgoing_msg_id = {$incoming_id} AND is_seen = 0";
    $query2 = mysqli_query($conn, $sql2) or die(mysqli_error($conn));

    if (mysqli_num_rows($query2) > 0) {
        // Hay mensajes no vistos, obtén la información del usuario
        $row = mysqli_fetch_assoc($query3);
        $email = $row["email"];

        // Envía un correo electrónico al admin
        render_php("send-email", [
            "username" => $username,
            "email" => $email,
            "message" => 'El usuario ' . $username . ' ha enviado un nuevo mensaje.',
        ]);
    }
}

}