<?php
session_start();
// Verificar si hay una solicitud POST y los datos del nuevo nombre y apellido están presentes
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_GET['nuevoNombre']) && isset($_GET['nuevoApellido'])) {
    include_once "config.php";
    // Obtener los datos del nuevo nombre y apellido
    $nuevoNombre = $_GET['nuevoNombre'];
    $nuevoApellido = $_GET['nuevoApellido'];
    $id = $_SESSION['unique_id'];
    $sql = "UPDATE users SET fname = '$nuevoNombre', lname = '$nuevoApellido' WHERE unique_id = $id";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $_SESSION['fname'] = $row['fname'];
        $_SESSION['lname'] = $row['lname'];
    }

    // Cerrar la conexión a la base de datos
    mysqli_close($conn);


    // Si la actualización es exitosa, puedes devolver una respuesta al cliente
    echo "Nombre y apellido actualizados correctamente a: $nuevoNombre $nuevoApellido";
} else {
    // Si no se proporcionaron datos válidos, devolver un mensaje de error
    echo "No se proporcionaron datos válidos para actualizar el nombre y apellido.";
}
