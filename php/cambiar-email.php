<?php
session_start();

// Obtener el nuevo correo electrónico del cuerpo de la solicitud POST
require_once 'config.php';

$data = json_decode(file_get_contents("php://input"));

if(isset($data->nuevo_email)){
    $nuevo_email = $data->nuevo_email;

    // Aquí puedes realizar cualquier validación o procesamiento adicional del nuevo correo electrónico

    $sql = "UPDATE users SET email = '$nuevo_email' WHERE unique_id = '{$_SESSION['unique_id']}'";
    if(mysqli_query($conn, $sql)) {
        $_SESSION['email'] = $nuevo_email;
        echo json_encode(['success' => true, 'message' => 'Correo electrónico actualizado correctamente.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al actualizar el correo electrónico: ' . mysqli_error($conn)]);
    }
}
?>
