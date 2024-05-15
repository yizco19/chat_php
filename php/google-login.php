<?php
session_start();

require_once '../auth_config.php';
require_once 'config.php';


if (isset($_GET['code'])) {
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    $client->setAccessToken($token['access_token']);
  
    // Obtener información del perfil
    $google_oauth = new Google_Service_Oauth2($client);
    $google_account_info = $google_oauth->userinfo->get();
    echo '<pre>' . var_export($google_account_info, true) . '</pre>';
    $userinfo = [
      'email' => $google_account_info->email,
      'first_name' => $google_account_info->givenName,
      'last_name' => $google_account_info->familyName,
      'gender' => $google_account_info->gender,
      'full_name' => $google_account_info->name,
      'picture' => $google_account_info->picture,
      'verifiedEmail' => $google_account_info->verifiedEmail,
      'token' => $google_account_info->id,
    ];
  
    // Guardar la información del usuario en la sesión
    $_SESSION['user_token'] = $token;
    createUser($userinfo);
} else {
    // Redirigir a la página de inicio de sesión si no hay un código de autorización
    if (!isset($_SESSION['user_token'])) {
        header("Location: ../login.php");
        die();
    }
}

function createUser($userinfo) {
    global $conn;

    // Comprobar si el usuario ya existe en la base de datos
    $sql = "SELECT * FROM users WHERE email = '{$userinfo['email']}'";
    $result = mysqli_query($conn, $sql);

    if(mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        // Guardar los datos del usuario en la sesión
        $_SESSION['unique_id'] = $row['unique_id'];
        $_SESSION['admin'] = $row['admin'];
        $_SESSION['username'] = $row['fname'] . " " . $row['lname'];
        $_SESSION['email'] = $row['email'];
        $_SESSION['first_login'] = true;
        // El usuario ya existe, redirigir a la página de usuarios
        header("Location: ../users.php");
    } else {
        // El usuario no existe, insertarlo en la base de datos
        $ran_id = rand(time(), 100000000);
        $insert_query = mysqli_query($conn, "INSERT INTO users (unique_id, admin, fname, lname, email, password, img, status) 
                                            VALUES ('$ran_id', 0, '{$userinfo['first_name']}', '{$userinfo['last_name']}', '{$userinfo['email']}', '', '{$userinfo['picture']}', 'Offline now')");
        if($insert_query) {
            // Obtener los datos del usuario recién insertado
            $result = mysqli_query($conn, $sql);
            if(mysqli_num_rows($result) > 0) {
                $row = mysqli_fetch_assoc($result);
                // Guardar los datos del usuario en la sesión
                $_SESSION['unique_id'] = $row['unique_id'];
                $_SESSION['admin'] = $row['admin'];
                $_SESSION['username'] = $row['fname'] . " " . $row['lname'];
                $_SESSION['email'] = $row['email'];
                $_SESSION['first_login'] = true;
                // Redirigir a la página de usuarios
                header("Location: ../users.php");
            }
        } else {
            echo "El usuario no pudo ser creado";
            die();
        }
    }
}

