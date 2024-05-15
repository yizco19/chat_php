<?php
// Conexión a la base de datos
include_once "config.php";

// Verificar si hay una sesión de usuario iniciada
session_start();
if (!isset($_SESSION['unique_id'])) {
    header("location: login.php");
}

// Obtener el detalle del usuario
$userId = $_SESSION['unique_id'];
$sql = "SELECT * FROM users WHERE unique_id = {$userId}";
$query = mysqli_query($conn, $sql);
if (mysqli_num_rows($query) > 0) {
    $row = mysqli_fetch_assoc($query);
    echo json_encode($row); // Devolver los detalles del usuario en formato JSON
} else {
    echo "No se encontró ningún usuario con el ID proporcionado.";
}

