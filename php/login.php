<?php
session_start();
include_once "config.php";
$email = mysqli_real_escape_string($conn, $_POST['email']);
$password = mysqli_real_escape_string($conn, $_POST['password']);

if (!empty($email) && !empty($password)) {
    $sql = mysqli_query($conn, "SELECT * FROM users WHERE email = '{$email}'");
    if (mysqli_num_rows($sql) > 0) {
        $row = mysqli_fetch_assoc($sql);
        $user_pass = md5($password);
        $enc_pass = $row['password'];
        if ($user_pass === $enc_pass) {
            $status = "Disponible";
            $sql2 = mysqli_query($conn, "UPDATE users SET status = '{$status}' WHERE unique_id = {$row['unique_id']}");
            if ($sql2) {
                if(isset($_POST['rememberMe'])){
                    setcookie('email', $email, time() + (86400 * 30), "/");
                    setcookie('password', $password, time() + (86400 * 30), "/");
                }
                $_SESSION['unique_id'] = $row['unique_id'];
                $_SESSION['admin'] = $row['admin'];
                $_SESSION['username'] = $row['fname'] . " " . $row['lname'];
                $_SESSION['email'] = $row['email'];
                $_SESSION['is_super_admin'] = $row['is_super_admin'];
                $_SESSION['first_login'] = true;

                $userIp = getUserIP();
                $geolocation = getGeolocation($userIp);
                if($geolocation != "error"){
                    $sql3 = mysqli_query($conn, "UPDATE users SET localizacion = '{$geolocation}' WHERE unique_id = {$row['unique_id']}");
                }
                echo "success";
            } else {
                echo "Algo salió mal. ¡Inténtalo de nuevo!";
            }
        } else {
            echo "¡Correo electrónico o la contraseña son incorrectos!";
        }
    } else {
        echo "$email - ¡Este correo electrónico no existe!";
    }
} else {
    echo "¡Todos los campos de entrada son obligatorios!";
}

function getUserIP() {
    // Obtén la dirección IP del usuario
    $ip = '';
    if (isset($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } elseif (isset($_SERVER['REMOTE_ADDR'])) {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}

function getGeolocation($ip) {
    // Usa ip-api.com para obtener la geolocalización
    $url = "http://ip-api.com/json/{$ip}";
    $response = file_get_contents($url);
    $data = json_decode($response, true);
    if (isset($data['status']) && $data['status'] == 'success') {
        $geolocation = $data['country']. ", ". $data['regionName']. ", ". $data['city'];
    } else {
        $geolocation = "error";
    }
    return $geolocation;
}
?>
