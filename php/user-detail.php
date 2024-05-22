<?php 
session_start();

$unique_id = $_SESSION('unique_id');

$sql = "SELECT * FROM users WHERE unique_id = '$unique_id'";

$query = mysqli_query($conn, $sql);

if (mysqli_num_rows($query) > 0) {
    $row = mysqli_fetch_assoc($query);
    //devolver la lista de usuarios en formato JSON
    echo json_encode($row);
}