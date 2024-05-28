<?php 
session_start();

include_once "config.php";

if (isset($_POST['unique_id'])) {
    $unique_id = mysqli_real_escape_string($conn, $_POST['unique_id']);
} else {
    $unique_id = $_SESSION['unique_id'];
}

$sql = "SELECT * FROM users WHERE unique_id = '$unique_id'";
$query = mysqli_query($conn, $sql);

if (mysqli_num_rows($query) > 0) {
    $row = mysqli_fetch_assoc($query);
    // Devolver la lista de usuarios en formato JSON
    echo json_encode($row);
} else {
    // No se encontró ningún usuario con ese unique_id
    echo json_encode(null);
}

