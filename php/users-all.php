<?php
session_start();
include_once "config.php";

// Verificar si el usuario está autenticado
if (!isset($_SESSION['unique_id'])) {
    // El usuario no está autenticado, redirigir a la página de inicio de sesión
    header("Location: login.php");
    exit;
}

// Obtener el ID del usuario actual
$unique_id = $_SESSION['unique_id'];

// Consulta para obtener todos los usuarios menores al usuario actual
$sql = "SELECT unique_id, fname, lname, admin, img FROM users WHERE unique_id != $unique_id";
$result = mysqli_query($conn, $sql);

// Crear un arreglo vacío
$users = array();

// Verificar si se obtuvieron resultados de la consulta
if (mysqli_num_rows($result) > 0) {
    // Recorrer los resultados y añadirlos al arreglo de usuarios
    while ($row = mysqli_fetch_assoc($result)) {
        $users[] = $row;
    }
}

// Devolver la lista de usuarios en formato JSON
echo json_encode($users);
?>
