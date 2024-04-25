<?php
// Verificar si se recibió un token
if (isset($_POST['token'])) {
    session_start();
    // Obtener el token del POST
    $token = $_POST['token'];
    include_once "config.php";
    $outgoing_id = $_SESSION['unique_id'];

    // Realizar cualquier acción necesaria con el token, como almacenarlo en una base de datos
    // Por ejemplo, si quieres almacenarlo en $_SESSION, puedes hacerlo así:

    $_SESSION['token_push'] = $token;
    $sql = mysqli_query($conn, "UPDATE users SET token_push = '{$token}' WHERE unique_id = {$outgoing_id}");

    // Si necesitas enviar alguna respuesta de vuelta, puedes hacerlo
    echo 'Token recibido y almacenado correctamente.';
    echo $_SESSION['token_push'];

} else {
    // Si no se recibió un token, puedes manejar el error aquí
    echo 'No se recibió ningún token.';
}

