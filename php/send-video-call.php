<?php
session_start();
// Verificar si se recibió una solicitud POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    include_once "config.php";
    $outgoing_id = $_SESSION['unique_id'];



    // Verificar si se recibieron datos JSON en el cuerpo de la solicitud
    $data = json_decode(file_get_contents("php://input"), true);

    // Verificar si se recibió el enlace de la videollamada
    if (isset($data["enlaceVideollamada"])) {
        // Obtener el enlace de la videollamada enviado desde el cliente
        $enlaceVideollamada = $data["enlaceVideollamada"];
        $incoming_id = $data["incoming_id"];
        $sql = " INSERT INTO messages (incoming_msg_id, outgoing_msg_id, msg )
        VALUES ({$incoming_id}, {$outgoing_id}, '{$enlaceVideollamada}')";
        echo $sql;
        mysqli_query($conn, $sql);
        // Respuesta al cliente
        http_response_code(200);
        echo "Enlace de videollamada recibido correctamente.";
    } else {
        // Si no se recibió el enlace de la videollamada, responder con un código de estado 400 (Bad Request)
        http_response_code(400);
        echo "Error: No se recibió el enlace de la videollamada.";
    }
} else {
    // Si la solicitud no es POST, responder con un código de estado 405 (Method Not Allowed)
    http_response_code(405);
    echo "Error: Método no permitido.";
}
?>
