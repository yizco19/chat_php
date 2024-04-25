<?php
    session_start();
    require 'config/config.php';

    $action = $_GET['action'];

    if (empty($action)) {
        header("location: index.php");
        exit;
    } else if ((!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] != true) && $_GET['action'] != "login" && $_GET['action'] != "register" && $_GET['action'] != "forgot_password" && $_GET['action'] != "reset_email_password") {
        header("location: login.php");
        exit;
    }

    function generateRandomString($length) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    require 'PHPMailer-master/src/Exception.php';
    require 'PHPMailer-master/src/PHPMailer.php';
    require 'PHPMailer-master/src/SMTP.php';
    require "gmail_account/gmail_account.php";
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    if ($action == "login") {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $serverip = $_SERVER['HTTP_CLIENT_IP'];
        } else if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $serverip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $serverip = $_SERVER['REMOTE_ADDR'];
        }
        $redirect_link = "home.php";
        if (!empty($_GET['redirect_link'])) {
            $redirect_link = $_GET['redirect_link'];
        }
        $acces = 1;
        $username = htmlspecialchars($_POST['username']);
        $password = htmlspecialchars($_POST['password']);
        $input_data = "username=".$username."&password=".$password."";
        if (empty($username)) {
            $username_err = "Por favor, introduce un nombre de usuario/correo electrónico.";
            header("location: login.php?$input_data&username_err=".$username_err."");
            $acces = 0;
        } else if (empty($password)) {
            $password_err = "Por favor, introduce tu contraseña.";
            header("location: login.php?$input_data&password_err=".$password_err."");
            $acces = 0;
        }  else {
            $sql = "SELECT * FROM users WHERE username='$username' OR email='$username'";
            $result = mysqli_query($link, $sql);
            if (mysqli_num_rows($result) == 0) {
                $username_err = "No se encontró ninguna cuenta con ese nombre de usuario/correo electrónico.";
                header("location: login.php?$input_data&username_err=".$username_err."");
                $acces = 0;
            } else {
                $row = mysqli_fetch_assoc($result);
                $hashed_password = $row['password'];
        
                if (!password_verify($password, $hashed_password)) {
                    $password_err = "La contraseña que ingresaste no es válida.";
                    header("location: login.php?$input_data&password_err=".$password_err."");
                    $acces = 0;
                }
            }
        }
        

        if ($acces == 1) {
            $sql = "SELECT * FROM users WHERE username='$username' OR email='$username'";
            $result = mysqli_query($link, $sql);
            $row = mysqli_fetch_assoc($result);

            $username = $row['username'];
            $email = $row['email'];
            $id = $row['id'];
            $admin = $row['admin'];
            $send_message = $row['send_message'];
            $created_at = $row['created_at'];
            $bio = $row['bio'];
            $file = $row['file'];
            $email = $row['email'];
            $founder = $row['founder'];
            $banned = $row['banned'];
            $logged = $row['logged'];
            $ip = $row['ip'];
            $last_ip = $row['last_ip'];
            $verified = $row['verified'];

            $_SESSION["loggedin"] = true;
            $_SESSION["id"] = $id;
            $_SESSION["username"] = $username;  
            $_SESSION["admin"] = $admin;  
            $_SESSION["send_message"] = $send_message;  
            $_SESSION["created_at"] = $created_at;
            $_SESSION["bio"] = $bio;  
            $_SESSION["file"] = $file;  
            $_SESSION["email"] = $email;  
            $_SESSION["founder"] = $founder;  
            $_SESSION["banned"] = $banned;  
            $_SESSION["logged"] = $logged;
            $_SESSION["ip"] = $ip;
            $_SESSION["last_ip"] = $last_ip;
            $_SESSION["verified"] = $verified;

            $sql = "UPDATE users SET last_ip='".$serverip."', logged=1 WHERE id='".$_SESSION["id"]."'";
            mysqli_query($link, $sql);
            $sql = "INSERT INTO chat (action, actiontext) VALUES ('1', '$username just connected!')";
            mysqli_query($link, $sql);
            header("location: $redirect_link");
        }
    } else if ($action == "set_send_message") {
        $send_message = $_GET['send_message'];
        $userid = $_GET['userid'];

        $new_value = 0;
        if ($send_message == 1) {
            $new_value = 2;
        } else if ($send_message == 2) {
            $new_value = 1;
        }
        $sql = "UPDATE users SET send_message=$new_value WHERE id=$userid";
        $result = mysqli_query($link, $sql);
        
        $_SESSION['send_message'] = $new_value;

        $err_message = "";
        if ($send_message == 1) {
            $err_message = "Para enviar mensajes ahora, debes presionar el botón de enviar.";
        } else if ($send_message == 2) {
            $err_message = "Ahora puedes enviar mensajes presionando la tecla Enter.";
        }

        if ($result) {
            header("location: profile.php?id=$userid&err_message=$err_message");
        }
    } else if ($action == "ban") {
        if (!isset($_GET['id'])) {
            header('Location: index.php');
            exit();
        } else if ($_SESSION['admin'] == 0) {
            $err_message = "No tienes acceso. ¡Necesitas el rol de administrador!";
            header("location: home.php?err_message=".$err_message."");
        } else {
            $id = $_GET['id'];
    
            $queryString = "SELECT * FROM users WHERE id='$id' ORDER BY id DESC LIMIT 1"; 
            $result = mysqli_query($link, $queryString);
            $row = mysqli_fetch_assoc($result);
    
            $user_id = $row['id'];
            $username = $row['username'];
            $lastname = $_SESSION['username'];
    
            $sql = "UPDATE users SET banned=1 WHERE id='$user_id'";
            mysqli_query($link, $sql);
            $sql = "INSERT INTO notifications (text, userid) VALUES ('<b>$lastname</b> te ha prohibido.', '".$user_id."')";
            mysqli_query($link, $sql);
            $sql = "INSERT INTO notifications (text, userid) VALUES ('Has prohibido a <b>".$username."</b>.', '".$_SESSION['id']."')";
            mysqli_query($link, $sql);
            $sql = "INSERT INTO chat (action, actiontext) VALUES ('1', '$username ha sido prohibido por $lastname.')";
            mysqli_query($link, $sql);
            
            $err_message = "$username ha sido prohibido!";
            header('location: profile.php?id='.$user_id.'&err_message='.$err_message.'');
            
        }
    } else if ($action == "send_mail") {
        $message = $_POST['message'];
        $myName = $_SESSION['username'];
        if ($_SESSION['admin'] == 0) {
            $err_message = "No tienes acceso. ¡Necesitas el rol de administrador!";
            header("location: home.php?err_message=".$err_message."&subject=$subject&message=$message");
        } else if (empty($message)) {
            $err_message = "¡Por favor completa el mensaje!";
            header("location: send-mail.php?err_message=".$err_message."&subject=$subject&message=$message");
        } else if (!isset($_POST['send_mail'])) {
            $err_message = "Por favor confirma presionando la casilla de verificación.";
            header("location: send-mail.php?err_message=".$err_message."&subject=$subject&message=$message");
        } else {
            $sql = "INSERT INTO emails (name, message, sended) VALUES ('$myName', '$message', 0)";
            mysqli_query($link, $sql);
            $err_message = "¡Ahora necesitas ponerte en contacto con uno de los fundadores para aceptar tu correo electrónico!";
            header("location: home.php?err_message=$err_message");
        }
    } else if ($action == "accept_mail") {
        $email_id = $_GET['id'];
        $sql3 = "SELECT * FROM emails WHERE id='$email_id'";
        $result3 = mysqli_query($link, $sql3);
        $row3 = mysqli_fetch_assoc($result3);
        $message = $row3['message'];
        $sended = $row3['sended'];

        $users = array();
        $sql1 = "SELECT * FROM users";
        $result1 = mysqli_query($link, $sql1);
        if (mysqli_num_rows($result1) > 0) {
            while ($row = mysqli_fetch_assoc($result1)) {
                $username = $row['email'];
                array_push($users, $username);
            }
        }
        if (count($users) > 0 && !empty($email_id) && $_SESSION['founder'] != 0 && $sended == 0) {
            $myName = $_SESSION['username'];
            for ($i = 0; $i < count($users); $i++) {
                $newUserEmail = $users[$i];
                $mail = new PHPMailer();
                $mail->IsSMTP();
                $mail->SMTPDebug = 0;
                $mail->SMTPAuth = true;
                $mail->SMTPSecure = 'ssl';
                $mail->Host = "mail.lazarnarcis.ro";
                $mail->Port = 465;
                $mail->IsHTML(true);
                $mail->Username = "$email_gmail";
                $mail->Password = "$password_gmail";
                $mail->SetFrom("$email_gmail");
                $mail->Subject = "Email From $myName - The team of Administrators - https://$_SERVER[SERVER_NAME]";
                $mail->Body = "$message";
                $mail->AddAddress("$newUserEmail");

                if (!$mail->send()) {
                    $err_message = "¡El correo electrónico no se envió debido a problemas técnicos!";
                    header("location: emails.php?err_message=$err_message");
                } else {
                    $sql = "UPDATE emails SET sended=1 WHERE id=$email_id";
                    mysqli_query($link, $sql);
                    $err_message = "¡El correo electrónico se ha enviado a todos!";
                    header("location: emails.php?err_message=$err_message");
                }
            }
        } else {
            $err_message = "El correo electrónico no se puede enviar porque ya se ha enviado o no tienes el rol de administrador.";

            header("location: emails.php?err_message=$err_message");
        }
    } else if ($action == "unban") {
        if (!isset($_GET['id'])) {
            header('Location: index.php');
            exit();
        } else if ($_SESSION['admin'] == 0) {
            $err_message = "No tienes acceso. ¡Necesitas el rol de administrador!";
            header("location: home.php?err_message=".$err_message."");
        } else {
            $id = $_GET['id'];
        
            $queryString = "SELECT * FROM users WHERE id='$id' ORDER BY id DESC LIMIT 1"; 
            $result = mysqli_query($link, $queryString);
            $row = mysqli_fetch_assoc($result);
        
            $user_id = $row['id'];
            $username = $row['username'];
            $lastname = $_SESSION['username'];
        
            $sql = "UPDATE users SET banned=0 WHERE id='$user_id'";
            mysqli_query($link, $sql);
            $sql = "INSERT INTO notifications (text, userid) VALUES ('<b>$lastname</b> te desbaneó.', '".$user_id."')";
            mysqli_query($link, $sql);
            $sql = "INSERT INTO notifications (text, userid) VALUES ('Has desbaneado a <b>".$username."</b>.', '".$_SESSION['id']."')";
            mysqli_query($link, $sql);
            $sql = "INSERT INTO chat (action, actiontext) VALUES ('1', '$username ha sido desbaneado por $lastname.')";
            mysqli_query($link, $sql);
            
            $err_message = "$username fue desbaneado!";
            header('location: profile.php?id='.$user_id.'&err_message='.$err_message.'');
        }
    } else if ($action == "change_bio") {
        $set_bio = htmlspecialchars($_POST["new_bio"]);
        $acces = 1;

        if (empty($set_bio)) {
            $new_bio_err = "Por favor, ingresa la nueva biografía.";
            header("location: change-bio.php?new_bio_err=".$new_bio_err."");
            $acces = 0;
        } else if (strlen($set_bio) > 100) {
            $new_bio_err = "La biografía es demasiado larga. (máximo 100 caracteres)";
            header("location: change-bio.php?new_bio_err=".$new_bio_err."");
            $acces = 0;
        }

        if ($acces == 1) {
            $user_id = $_SESSION["id"];
            $sql = "UPDATE users SET bio='$set_bio' WHERE id='$user_id'";
            mysqli_query($link, $sql);
            $sql = "INSERT INTO notifications (text, userid) VALUES ('Tu biografía ha sido cambiada de <b>".$_SESSION['bio']."</b> a <b>".$set_bio."</b>.', '".$_SESSION['id']."')";
            mysqli_query($link, $sql);
            
            $_SESSION['bio'] = $set_bio;

            $err_message = "¡Tu biografía ha sido modificada!";
            header('location: profile.php?err_message='.$err_message.'&id='.$user_id.'');
        }
    } else if ($action == "change_email") {
        $set_email = htmlspecialchars($_POST["new_email"]);
        $acces = 1;

        if (empty($set_email)) {
            $new_email_err = "Por favor, introduce un correo electrónico.";
            header("location: change-email.php?new_email_err=".$new_email_err."");
            $acces = 0;     
        } else if (strlen($set_email) < 5) {
            $new_email_err = "¡Correo electrónico demasiado corto!";
            header("location: change-email.php?new_email_err=".$new_email_err."");
            $acces = 0;     
        } else if (strlen($set_email) > 50) {
            $new_email_err = "¡Correo electrónico demasiado largo!";
            header("location: change-email.php?new_email_err=".$new_email_err."");
            $acces = 0;     
        } else if (!filter_var($_POST["new_email"], FILTER_VALIDATE_EMAIL)) {
            $new_email_err = "¡Por favor, ingresa un correo electrónico válido!";
            header("location: change-email.php?new_email_err=".$new_email_err."");
            $acces = 0;     
        } else {
            $sql = "SELECT id FROM users WHERE email='$set_email'";
            $result = mysqli_query($link, $sql);
            if (mysqli_num_rows($result) > 0) {
                $err_message = "Este correo electrónico ya está en uso.";
                header("location: change-email.php?new_email_err=".$err_message."");
                $acces = 0;
            } 
        }

        if ($acces == 1) {
            $user_id = $_SESSION["id"];
            $sql = "UPDATE users SET email='$set_email', verified=0 WHERE id='$user_id'";
            mysqli_query($link, $sql);
            $sql = "INSERT INTO notifications (text, userid) VALUES ('Tu correo electrónico ha sido cambiado de <b>".$_SESSION['email']."</b> a <b>".$set_email."</b>.', '".$_SESSION['id']."')";
            mysqli_query($link, $sql);
            
            $_SESSION['email'] = $set_email;

            $err_message = "¡Tu correo electrónico ha sido cambiado!";
            header('location: profile.php?err_message='.$err_message.'&id='.$user_id.'');
        }
    } else if ($action == "change_name") {
        $set_name = htmlspecialchars($_POST["new_name"]);
        $acces = 1;
        $new_name = "";

        if (empty($set_name)) {
            $new_name_err = "Por favor, introduce el nuevo nombre.";
            header("location: change-name.php?new_name_err=".$new_name_err."");
            $acces = 0;        
        } else if (strlen($set_name) < 6) {
            $new_name_err = "El nombre de usuario debe tener al menos 6 caracteres.";
            header("location: change-name.php?new_name_err=".$new_name_err."");
            $acces = 0;        
        } else if (strlen($set_name) > 25) {
            $new_name_err = "Nombre de usuario demasiado largo.";
            header("location: change-name.php?new_name_err=".$new_name_err."");
            $acces = 0;        
        } else if ( preg_match('/\s/',$set_name)) {
            $new_name_err = "Tu nombre de usuario no debe contener espacios en blanco.";
            header("location: change-name.php?new_name_err=".$new_name_err."");
            $acces = 0;        
        } else if (preg_match('/[A-Z]/', $set_name)) {
            $new_name_err = "El nombre no puede contener letras mayúsculas.";
            header("location: change-name.php?new_name_err=".$new_name_err."");
            $acces = 0;        
        } else {
            $sql = "SELECT id FROM users WHERE username='$set_name'";
            $result = mysqli_query($link, $sql);
            if (mysqli_num_rows($result) > 0) {
                $err_message = "Este nombre de usuario ya está en uso.";
                header("location: change-name.php?new_name_err=".$err_message."");
                $acces = 0;
            } else {
                $new_name = $set_name;
            }
        }
        if ($acces == 1) {
            $param_id = $_SESSION["id"];
            $lastname = $_SESSION['username'];
            $sql = "UPDATE users SET username='$new_name' WHERE id='$param_id'";
            mysqli_query($link, $sql);
            $sql = "INSERT INTO notifications (text, userid) VALUES ('Your name has been changed from <b>".$lastname."</b> to <b>".$new_name."</b>.', '".$param_id."')";
            mysqli_query($link, $sql);
            $sql = "UPDATE chat SET name='$new_name' WHERE userid='$param_id'";
            mysqli_query($link, $sql);
            $sql = "INSERT INTO chat (action, actiontext) VALUES ('1', '$lastname changed his name from $lastname to $new_name.')";
            mysqli_query($link, $sql);
            $_SESSION['username'] = $new_name;

            $err_message = "¡Tu nombre ha sido cambiado!";
            header('location: profile.php?err_message='.$err_message.'&id='.$param_id.'');
        }
    } else if ($action == "change_photo") {
        $id = $_SESSION['id'];
        $acces = 1;
        if (!empty($_FILES["image"]["name"])) { 
            $fileName = basename($_FILES["image"]["name"]); 
            $fileType = pathinfo($fileName, PATHINFO_EXTENSION);
            $allowTypes = array('jpg','png','jpeg','gif'); 
            if (!in_array($fileType, $allowTypes)) {
                $msg = 'Lo siento, solo se permiten archivos JPG, JPEG, PNG y GIF para subir.';
                header("location: change-photo.php?new_photo_err=".$msg."");
                $acces = 0;    
            } 
        } else { 
            $msg = 'Por favor, selecciona un archivo de imagen para subir.';
            header("location: change-photo.php?new_photo_err=".$msg."");
            $acces = 0; 
        }

        if ($acces == 1) {
            $image = $_FILES['image']['tmp_name'];
            $image_base64 = base64_encode(file_get_contents($image));
            $imgContent = 'data:image/jpg;base64,'.$image_base64; 
            $sql = "UPDATE users SET file='$imgContent' WHERE id='$id'";
            mysqli_query($link, $sql);
            $sql = "UPDATE chat SET file='$imgContent' WHERE userid='$id'";
            mysqli_query($link, $sql);
            $lastname = $_SESSION['username'];
            $sql = "INSERT INTO chat (action, actiontext) VALUES ('1', '$lastname changed his profile picture.')";
            mysqli_query($link, $sql);
            $_SESSION['file'] = $imgContent;

            $err_message = "¡Tu foto de perfil ha sido cambiada!";
            header('location: profile.php?err_message='.$err_message.'&id='.$id.''); 
        }
    } else if ($action == "send_message") {
        $message = htmlspecialchars(isset($_POST['message']) ? $_POST['message'] : null);
        $adjunto = isset($_FILES['archivo']) ? $_FILES['archivo'] : null;
        $id = $_SESSION['id'];
        $to_id = $_SESSION['to_id'];

        if (!empty($message)) {
            if ($_SESSION['banned'] == 1) {
                return;
            }
            if (strlen($message) > 100000) {
                return;
            } else if (preg_match('/\S{500,}/', $message)) { 
                return; 
            } 
            // Manejar la carga del archivo
        $adjunto_nombre = $adjunto['name'];
        $adjunto_temp = $adjunto['tmp_name'];


        // Guardar el archivo en el servidor
        $ruta_destino = 'files/' . $adjunto_nombre; // Reemplaza 'files/' con la ruta de la carpeta donde deseas almacenar los archivos.
        move_uploaded_file($adjunto_temp, $ruta_destino);

        $message = str_replace('<br>', PHP_EOL, $message);
        $message = str_replace("'", "\'", $message);
        $message = strip_tags($message);
        $message = preg_replace('@(https?://([-\w\.]+[-\w])+(:\d+)?(/([\w/_\.#-]*(\?\S+)?[^\.\s])?)?)@', '<a href="$1" target="_blank" id="link-by-user">$1</a>', $message);
        $sql = "INSERT INTO chat (`message`, `userid`, `adjunto`, `to_id`) VALUES ('".$message."', '".$id."', '".$ruta_destino."' , '".$to_id."')";
        mysqli_query($link, $sql);
        }
    } else if ($action == "show_loaded_chat") {
        $message_id = $_GET['message_id'];
        // Verificar si existe to_id en la sesión
        if(isset($_SESSION['to_id'])) {
            $to_id = $_SESSION['to_id'];
            $from_id= $_SESSION["id"];
    
            // Construir la consulta SQL solo si existe to_id en la sesión
            $sql = "SELECT DISTINCT * FROM chat WHERE id=$message_id and (userid=$to_id or userid=$from_id) and (to_id=$to_id or to_id=$from_id)";
            
            // Ejecutar la consulta solo si existe to_id en la sesión
            $result = mysqli_query($link, $sql);
            $row = mysqli_fetch_assoc($result);
    
            if($row) {
                // Si hay resultados, mostrar el chat
                $userid = $row['userid'];
                $message = $row['message'];
                $adjunto = $row['adjunto'];
                $action = $row['action'];
                $actiontext = $row['actiontext'];
                $created_at = $row['created_at'];
    
                if ($action == 0) {
                    // Consulta para obtener información del usuario
                    $sql1 = "SELECT * FROM users WHERE id=$userid";
                    $result1 = mysqli_query($link, $sql1);
                    $row1 = mysqli_fetch_assoc($result1);
    
                    $username = $row1['username'];
                    $file = $row1['file'];
                    $admin = $row1['admin'];
                    $founder = $row1['founder'];
                    
                    // Lógica para mostrar si es admin o founder
                    $adm = "";
                    if ($founder == 1) {
                        $adm = " (Founder)";
                    } elseif ($admin == 1) {
                        $adm = " (Admin)";
                    } else {
                        $adm = "";
                    }
    
                    // Establecer el color del mensaje basado en el usuario
                    $color = ($userid == $_SESSION['id']) ? "#536160" : "#5d7191";
    
                    // Mostrar el mensaje del usuario
                    echo '
                        <div id="all-message">
                          <div class="date">
                            <div id="nameUser">
                              <a id="user-profile-link" href="profile.php?id='.$userid.'">'.$username.'</a>
                              <span id="admin-text">'.$adm.'</span>
                            </div>
                          </div>
                          <div class="user-message">
                            <div>
                              <img id="profile-message-picture" src="'.$file.'" /> 
                            </div>
                            <span class="active-user" style="background-color: #0fbf15;"></span>
                            <div 
                              class="msj" 
                              onmouseover="showOptionsForMessage('.$message_id.')" 
                              onmouseout="unshowOptionsForMessage('.$message_id.')"
                              style="background-color: '.$color.'"
                            >';
                            // Mostrar adjunto si existe
                            if (!empty($adjunto) && $adjunto!= 'files/') {
                                $image_info = @getimagesize($adjunto);
                                if ($image_info !== false) {
                                    echo '<img src="' . $adjunto . '" alt="Adjunto" id="message-picture" />';
                                } else {
                                    echo '<a href="' . $adjunto . '" download><img src="images/download.png" alt="Descargar ' . basename($adjunto) . '" id="download-image" /></a>';
                                }
                            }
                            echo '<span>'.$message.'</span>
                            </div>
                            <div id="timeS">
                              <span class="time" id="showTimes'.$message_id.'" style="display: none;"><small>'.$created_at.'</small></span>
                            </div>
                          </div>
                        </div>
                    ';
                } else {
                    // Mostrar texto de acción
                    echo '
                        <div id="all-message">
                          <div class="actiontext">
                            <span>'.$actiontext.' '.$created_at.'</span>
                          </div>
                        </div>
                    ';
                }
            } else {
                echo "";
            }
        } else {
            echo "";
        }
    }
     else if ($action == "load_chat") {
        $result = array();
        $start = isset($_GET['start']) ? intval($_GET['start']) : 0;
        $items = mysqli_query($link, "SELECT * FROM chat WHERE id > " . $start);
        while ($row = mysqli_fetch_assoc($items)) {
            $result['items'][] = $row;
        }
        header('Access-Control-Allow-Origin: *');
        header('Content-Type: application/json');
        echo json_encode($result);
    } else if ($action == "close_ticket") {
        if (!isset($_GET['id'])) {
            header('Location: index.php');
            exit();
        } else {
            $id = $_GET['id'];
            $queryString = "SELECT * FROM tickets WHERE id='$id' ORDER BY id DESC LIMIT 1"; 
            $result = mysqli_query($link, $queryString);
            $row = mysqli_fetch_assoc($result);
        
            $ticketid = $row['id'];
            $created_at = $row['created_at'];
            $userid = $row['userid'];
            $closed = $row['closed'];
        
            $user_name = $_SESSION['username'];
            $user_id = $_SESSION['id'];
        
            if ($_SESSION['admin'] == 0 && $_SESSION['id'] != $userid) {
                $err_message = "No tienes acceso. ¡Necesitas el rol de administrador!";
                header("location: home.php?err_message=".$err_message."");
                return;
            }
        
            $sql = "SELECT * FROM users WHERE id=$userid";
            $newResult = mysqli_query($link, $sql);
            $newRow = mysqli_fetch_assoc($newResult);
            $username = $newRow['username'];
        
            $sql = "UPDATE tickets SET closed=1 WHERE id='$ticketid'";
            mysqli_query($link, $sql);
            $sql = "INSERT INTO comments (text, userid, forTicket) VALUES ('$user_name cerró el ticket! (ID de ticket: $id)', '2', '$id')";
            mysqli_query($link, $sql);
            $sql = "INSERT INTO notifications (text, userid) VALUES ('<b>$user_name</b> cerró tu ticket! (ID de ticket: $id)', '$userid')";
            mysqli_query($link, $sql);
            $sql = "INSERT INTO notifications (text, userid) VALUES ('Cerraste el ticket de <b>$username</b>. (ID de ticket: $id)', '$user_id')";
            mysqli_query($link, $sql);
            

            $err_message = "Ticket on ID ".$ticketid." cerrado!";
            header("location: show-ticket.php?id=$id&err_message=".$err_message."");
        }
    } else if ($action == "open_ticket") {
        if (!isset($_GET['id'])) {
            header('Location: index.php');
            exit();
        } else {
            $id = $_GET['id'];
        
            $queryString = "SELECT * FROM tickets WHERE id='$id' ORDER BY id DESC LIMIT 1"; 
            $result = mysqli_query($link, $queryString);
            $row = mysqli_fetch_assoc($result);
            
            $ticketid = $row['id'];
            $created_at = $row['created_at'];
            $userid = $row['userid'];
            $closed = $row['closed'];
            
            $user_name = $_SESSION['username'];
            $user_id = $_SESSION['id'];
        
            if ($_SESSION['admin'] == 0 && $_SESSION['id'] != $userid) {
                $err_message = "No tienes acceso. ¡Necesitas el rol de administrador!";
                header("location: home.php?err_message=".$err_message."");
                return;
            }
        
            $sql = "SELECT * FROM users WHERE id=$userid";
            $newResult = mysqli_query($link, $sql);
            $newRow = mysqli_fetch_assoc($newResult);
            $username = $newRow['username'];
        
            $sql = "UPDATE tickets SET closed=0 WHERE id='$ticketid'";
            mysqli_query($link, $sql);
            $sql = "INSERT INTO comments (text, userid, forTicket) VALUES ('$user_name  abrió el ticket! (ticketid: $id)', '2', '$id')";
            mysqli_query($link, $sql);
            $sql = "INSERT INTO notifications (text, userid) VALUES ('<b>$user_name</b>  abrió el ticket! (ticketid: $id)', '$userid')";
            mysqli_query($link, $sql);
            $sql = "INSERT INTO notifications (text, userid) VALUES ('Usted abrió el ticket de  <b>$username</b>\'s ticket. (ticketid: $id)', '$user_id')";
            mysqli_query($link, $sql);
            
            $err_message = "Ticket con ID ".$ticketid." abierto!";
            header("location: show-ticket.php?id=$id&err_message=".$err_message."");
        }
    } else if ($action == "send_ticket_message") {
        $message = htmlspecialchars($_POST['message']);
        $text = $_POST['text'];
        $user_name = $_SESSION['username'];
        $user_id = $_SESSION['id'];
        $message = str_replace('<br>', PHP_EOL, $message);
        $message = str_replace("'", "\'", $message);
        $message = strip_tags($message);
        $message = preg_replace('@(https?://([-\w\.]+[-\w])+(:\d+)?(/([\w/_\.#-]*(\?\S+)?[^\.\s])?)?)@', '<a href="$1" target="_blank" id="link-by-user">$1</a>', $message);
        $admin = $_SESSION['admin'];
        $file = $_SESSION['file'];

        if (empty($message)) {
            header("location: show-ticket.php?id=$text");
            return;
        } else if (!empty($message)) {
            $sql = "INSERT INTO comments (text, userid, forTicket) VALUES ('$message', '$user_id', '$text')";
            $result = mysqli_query($link, $sql);
            header("location: show-ticket.php?id=$text");
        }
    } else if ($action == "create_ticket") {
        $set_message = htmlspecialchars($_POST['message']);
        $acces = 1;

        if (empty($set_message)) {
            $err_message = "Por favor, ingresa el mensaje.";
            header("location: contact.php?err_message=".$err_message."");     
            $acces = 0;
        }

        if (strlen($set_message) > 1000) {
            $err_message = "¡No puedes tener más de 1000 letras!";
            header("location: contact.php?err_message=".$err_message."");     
            $acces = 0;
        }

        $ticket_user_id = $_SESSION['id'];
        $count_the_tickets = mysqli_query($link, "SELECT COUNT(*) FROM `tickets` WHERE userid=$ticket_user_id AND closed=0");
        $number_of_tickets = mysqli_fetch_row($count_the_tickets)[0];

        if ($number_of_tickets >= 10) {
            $err_message = "¡No puedes tener más de 10 tickets abiertos!";
            header("location: contact.php?err_message=".$err_message."");     
            $acces = 0;
        }

        if ($acces == 1) {
            $sql = "INSERT INTO tickets (text, userid) VALUES ('".$set_message."', '".$_SESSION['id']."')";
            mysqli_query($link, $sql);
            $selectquery = "SELECT * FROM tickets ORDER BY id DESC LIMIT 1";
            $result = mysqli_query($link, $selectquery);
            $row = mysqli_fetch_assoc($result);
            $ticketid = $row['id'];
            $ticketuserid = $row['userid'];
            $sql = "SELECT * FROM users WHERE id=$ticketuserid";
            $newResult = mysqli_query($link, $sql);
            $newRow = mysqli_fetch_assoc($newResult);
            $ticketusername = $newRow['username'];
            $ticketemail = $newRow['email'];
            $sql = "INSERT INTO comments (text, userid, forTicket) VALUES ('¡Hola, $ticketusername!\n¡Soy un bot administrador! Por favor, cuéntanos detalladamente cuál es tu problema. ¡Un administrador te ayudará lo antes posible!\nSi no respondes en 24 horas, este ticket se cerrará automáticamente.\n\n¡Que tengas un buen día!', '2', '$ticketid')";
            mysqli_query($link, $sql);
            $sql = "INSERT INTO notifications (text, userid) VALUES ('¡El ticket (#$ticketid) ha sido creado! ¡Recibirás una respuesta pronto!', '".$_SESSION['id']."')";
            mysqli_query($link, $sql);
            
            $admins = array();
            $sql1 = "SELECT * FROM users WHERE admin=1 OR founder=1";
            $result1 = mysqli_query($link, $sql1);

            if (mysqli_num_rows($result1) > 0) {
                while ($row = mysqli_fetch_assoc($result1)) {
                    $adminname = $row['email'];
                    array_push($admins, $adminname);
                }
            }

            if (count($admins) > 0) {
                for ($i = 0; $i < count($admins); $i++) {
                    $newAdminEmail = $admins[$i];
                    $mail = new PHPMailer();
                    $mail->IsSMTP();
                    $mail->SMTPDebug = 0;
                    $mail->SMTPAuth = true;
                    $mail->SMTPSecure = 'ssl';
                    $mail->Host = "mail.lazarnarcis.ro";
                    $mail->Port = 465;
                    $mail->IsHTML(true);
                    $mail->Username = "$email_gmail";
                    $mail->Password = "$password_gmail";
                    $mail->SetFrom("$email_gmail");
                    $mail->Subject = "New ticket ($ticketusername) [#$ticketid] || $ticketemail";
                    $linkTicket = "https://$_SERVER[SERVER_NAME]/login.php?redirect_link=show-ticket.php?id=$ticketid";
                    $linkUsername = "https://$_SERVER[SERVER_NAME]/login.php?redirect_link=profile.php?id=$ticketuserid";
                    $mail->Body = "Una persona acaba de crear un ticket en el sitio. Ayúdala tan pronto como puedas.<br><a href='$linkTicket' target='_blank'>Ver ticket (#$ticketid)</a> o <a href='$linkUsername' target='_blank'>Ver Usuario ($ticketusername #$ticketuserid)</a><br><br><b>Mensaje del ticket:</b><br>$set_message";
                    $mail->AddAddress("$newAdminEmail");
                    $mail->send();
                }
            }

            $err_message = "¡El ticket ha sido creado!";
            header("location: show-ticket.php?id=$ticketid&err_message=".$err_message."");
        }
    } else if ($action == "forgot_password") {
        $email = $_POST['email'];
        $string = "SELECT * FROM users WHERE email='$email'";
        $result = mysqli_query($link, $string);

        if (mysqli_num_rows($result) > 0) {
            $code = generateRandomString(500);
            $sql = "INSERT INTO forgot_password (email, code) VALUES ('$email', '$code')";
            $query = mysqli_query($link, $sql); 

            $mail = new PHPMailer();
            $mail->IsSMTP();
            $mail->SMTPDebug = 0;
            $mail->SMTPAuth = true;
            $mail->SMTPSecure = 'ssl';
            $mail->Host = "mail.lazarnarcis.ro";
            $mail->Port = 465;
            $mail->IsHTML(true);
            $mail->Username = "$email_gmail";
            $mail->Password = "$password_gmail";
            $mail->SetFrom("$email_gmail");
            $mail->Subject = "Restablece tu contraseña";
            $link = "https://$_SERVER[SERVER_NAME]/reset-email-password.php?email=$email&code=$code";
            $mail->Body = "To reset your password please click on the following link: $link.";
            $mail->AddAddress("$email");

            if (!$mail->send() || !$query) {
                $message = "¡El correo electrónico no se envió debido a problemas técnicos!";
                header("location: forgot-password.php?email=$email&email_err=$message");
            } else {
                $email_err = "¡Se te ha enviado un enlace de restablecimiento de contraseña a $email!";
                header('location: login.php?password_err='.$email_err.'');
            }
        } else {
            $message = "¡No hay cuentas asociadas a este correo electrónico!";
            header("location: forgot-password.php?email=$email&email_err=$message");
        }
    } else if ($action == "reset_email_password") {
        $code = htmlspecialchars($_POST['code']);
        $email = htmlspecialchars($_POST['email']);
        $password = htmlspecialchars($_POST['password']);
        
        $sql = "DELETE FROM forgot_password WHERE email='$email' AND code='$code'";
        $result = mysqli_query($link, $sql);
        
        $password = password_hash($password, PASSWORD_DEFAULT);
        $sql1 = "UPDATE users SET password='$password' WHERE email='$email'";
        $result1 = mysqli_query($link, $sql1);

        if ($result && $result1) {
            $message = "¡La contraseña ha sido cambiada!";
            header("location: login.php?password_err=$message");
        }
    } else if ($action == "delete_bio") {
        $name = $_SESSION['username'];
        $acces = 1;
        if (!isset($_POST['delete'])) {
            $confirm_err = 'Por favor, confirma presionando la casilla de verificación.';
            header("location: delete-bio.php?err_message=".$confirm_err."");
            $acces = 0;
        }
        if ($acces == 1) {
            $sql = "UPDATE users SET bio='' WHERE username='$name'";
            mysqli_query($link, $sql);
            $sql = "INSERT INTO notifications (text, userid) VALUES ('Tu biografía <b>".$_SESSION['bio']."</b> ha sido eliminada.', '".$_SESSION['id']."')";
            mysqli_query($link, $sql);
            $_SESSION['bio'] = "";
            $id = $_SESSION['id'];

            $err_message = "¡Tu biografía ha sido eliminada!";
            header('location: profile.php?id='.$id.'&err_message='.$err_message.'');
        }
    } else if ($action == "delete_chat") {
        $name = $_SESSION['username'];
        $action = 1;

        if (!isset($_POST['delete'])) {
            $confirm_err = 'Por favor confirma presionando la casilla de verificación.';
            header("location: delete-chat.php?err_message=".$confirm_err."");
            $action = 0;
        }
        if ($action == 1) {
            $sql = "DELETE FROM chat";
            mysqli_query($link, $sql);
            $sql = "INSERT INTO notifications (text, userid) VALUES ('Has borrado el chat.', '".$_SESSION['id']."')";
            mysqli_query($link, $sql);
            $sql = "INSERT INTO chat (action, actiontext) VALUES ('1', '$name borró el chat.')";
            mysqli_query($link, $sql);

            $admins = array();
            $sql1 = "SELECT * FROM users";
            $result1 = mysqli_query($link, $sql1);

            if (mysqli_num_rows($result1) > 0) {
                while ($row = mysqli_fetch_assoc($result1)) {
                    $adminname = $row['email'];
                    array_push($admins, $adminname);
                }
            }

            $sql123 = "SELECT * FROM users WHERE username='$name'";
            $result123 = mysqli_query($link, $sql123);
            $row123 = mysqli_fetch_assoc($result123);
            $nameID = $row123['id'];
            $emailID = $row123['email'];

            if (count($admins) > 0) {
                for ($i = 0; $i < count($admins); $i++) {
                    $newAdminEmail = $admins[$i];
                    $mail = new PHPMailer();
                    $mail->IsSMTP();
                    $mail->SMTPDebug = 0;
                    $mail->SMTPAuth = true;
                    $mail->SMTPSecure = 'ssl';
                    $mail->Host = "mail.lazarnarcis.ro";
                    $mail->Port = 465;
                    $mail->IsHTML(true);
                    $mail->Username = "$email_gmail";
                    $mail->Password = "$password_gmail";
                    $mail->SetFrom("$email_gmail");
                    $mail->Subject = "$name deleted the chat ~ $emailID";
                    $contactLink = "https://$_SERVER[SERVER_NAME]/login.php?redirect_link=contact.php";
                    $linkName = "https://$_SERVER[SERVER_NAME]/login.php?redirect_link=profile.php?id=$nameID";
                    $mail->Body = "<a href='$linkName' target='_blank'>$name</a> just deleted all main chat. If you think he made the <b>wrong decision</b> you can make a ticket <a href='$contactLink' target='_blank'>here</a>.";
                    $mail->AddAddress("$newAdminEmail");
                    $mail->send();
                }
            }

            $err_message = "The chat has been deleted!";
            header("location: home.php?err_message=".$err_message."");
        }
    } else if ($action == "delete_nofitications") {
        if (!isset($_GET['id'])) {
            header('Location: index.php');
            exit();
        } else if ($_SESSION['admin'] == 0) {
            $err_message = "No tienes acceso. ¡Necesitas el rol de administrador!";
            header("location: home.php?err_message=".$err_message."");
        } else {
            $id = $_GET['id'];
        
            $queryString = "SELECT * FROM users WHERE id='$id' ORDER BY id DESC LIMIT 1"; 
            $result = mysqli_query($link, $queryString);
            $row = mysqli_fetch_assoc($result);
        
            $user_id = $row['id'];
            $username = $row['username'];
            $lastname = $_SESSION['username'];
            $userid = $_SESSION['id'];
        
            $sql = "DELETE FROM notifications WHERE userid='$user_id'";
            mysqli_query($link, $sql);
            $sql = "INSERT INTO notifications (text, userid) VALUES ('<b>$lastname</b> deleted your notifications.', '$user_id')";
            mysqli_query($link, $sql);
            $sql = "INSERT INTO notifications (text, userid) VALUES ('You deleted <b>$username</b>\'s notifications.', '$userid')";
            mysqli_query($link, $sql);
            $sql = "INSERT INTO chat (action, actiontext) VALUES ('1', '$lastname deleted $username\'s notifications.')";
            mysqli_query($link, $sql);

            $err_message = "$username's notifications have been deleted.";
            header('location: profile.php?id='.$user_id.'&err_message='.$err_message.'');
        }
    } else if ($action == "delete_tickets") {
        $name = $_SESSION['username'];
        $action = 1;
        if (!isset($_POST['delete'])) {
            $confirm_err = 'Por favor confirma presionando la casilla de verificación.';
            header("location: delete-tickets.php?err_message=".$confirm_err."");
            $action = 0;
        }
        if ($action == 1) {
            $sql = "DELETE FROM tickets";
            mysqli_query($link, $sql);
            $sql = "INSERT INTO notifications (text, userid) VALUES ('You deleted the tickets.', '".$_SESSION['id']."')";
            mysqli_query($link, $sql);
            $sql = "INSERT INTO chat (action, actiontext) VALUES ('1', '$name deleted the tickets.')";
            mysqli_query($link, $sql);

            $err_message = "Tickets have been deleted!";
            header("location: tickets.php?err_message=".$err_message."");
        }
    } else if ($action == "make_admin") {
        if (!isset($_GET['id'])) {
            header('Location: index.php');
            exit();
        } else if ($_SESSION['founder'] == 0) {
            $err_message = "You have no access! You need the role of founder!";
            header("location: home.php?err_message=".$err_message."");
        } else {
            $id = $_GET['id'];
            
            $queryString = "SELECT * FROM users WHERE id='$id' ORDER BY id DESC LIMIT 1"; 
            $result = mysqli_query($link, $queryString);
            $row = mysqli_fetch_assoc($result);
        
            $user_id = $row['id'];
            $username = $row['username'];
        
            $lastname = $_SESSION['username'];
            
            $sql = "UPDATE users SET admin=1 WHERE id='$user_id'";
            mysqli_query($link, $sql);
            $sql = "INSERT INTO notifications (text, userid) VALUES ('<b>$lastname</b> made you admin.', '".$user_id."')";
            mysqli_query($link, $sql);
            $sql = "INSERT INTO notifications (text, userid) VALUES ('You made <b>$username</b> admin.', '".$_SESSION['id']."')";
            mysqli_query($link, $sql);
            $sql = "INSERT INTO chat (action, actiontext) VALUES ('1', '$lastname set $username as administrator.')";
            mysqli_query($link, $sql);

            $err_message = "$username is now admin!";
            header('location: profile.php?id='.$user_id.'&err_message='.$err_message.'');
        }
    } else if ($action == "remove_admin") {
        if (!isset($_GET['id'])) {
            header('Location: index.php');
            exit();
        } else if ($_SESSION['founder'] == 0) {
            $err_message = "You have no access! You need the role of founder!";
            header("location: home.php?err_message=".$err_message."");
        } else {
            $id = $_GET['id'];
        
            $queryString = "SELECT * FROM users WHERE id='$id' ORDER BY id DESC LIMIT 1"; 
            $result = mysqli_query($link, $queryString);
            $row = mysqli_fetch_assoc($result);
        
            $user_id = $row['id'];
            $username = $row['username'];
        
            $lastname = $_SESSION['username'];
        
            $sql = "UPDATE users SET admin=0 WHERE id='$user_id'";
            mysqli_query($link, $sql);
            $sql = "INSERT INTO notifications (text, userid) VALUES ('<b>$lastname</b> deleted your admin role.', '".$user_id."')";
            mysqli_query($link, $sql);
            $sql = "INSERT INTO notifications (text, userid) VALUES ('You deleted <b>".$username."</b> admin role.', '".$_SESSION['id']."')";
            mysqli_query($link, $sql);
            $sql = "INSERT INTO chat (action, actiontext) VALUES ('1', '$lastname removed $username from the role of administrator.')";
            mysqli_query($link, $sql);  

            $err_message = "$username is no longer admin!";
            header('location: profile.php?id='.$user_id.'&err_message='.$err_message.'');
        }
    } else if ($action == "reset_password") {
        $user_id = $_SESSION["id"];
        $password = htmlspecialchars($_POST["new_password"]);
        $confirm_password = htmlspecialchars($_POST["confirm_password"]);
        $acces = 1;

        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        if (empty($password)) {
            $new_password_err = "Por favor ingresa una contraseña.";     
            header("location: reset-password.php?err_message=".$new_password_err."");
            $acces = 0;
        } else if (strlen($password) < 6) {
            $new_password_err = "La contraseña debe tener al menos 6 caracteres.";
            header("location: reset-password.php?err_message=".$new_password_err."");
            $acces = 0;
        } else if (strlen($password) > 18) {
            $new_password_err = "La contraseña es demasiado larga (máximo 18 caracteres).";
            header("location: reset-password.php?err_message=".$new_password_err."");
            $acces = 0;
        } else if (!preg_match("#[0-9]+#", $password)) {
            $new_password_err = "La contraseña debe incluir al menos un número.";
            header("location: reset-password.php?err_message=".$new_password_err."");
            $acces = 0;
        } else if (!preg_match("#[a-zA-Z]+#", $password)) {
            $new_password_err = "La contraseña debe incluir al menos una letra.";
            header("location: reset-password.php?err_message=".$new_password_err."");
            $acces = 0;
        } else if (empty($confirm_password)) {
            $confirm_password_err = "Por favor confirma la contraseña.";
            header("location: reset-password.php?err_message=".$confirm_password_err."");
            $acces = 0;
        } else {
            if ($password != $confirm_password) {
                $confirm_password_err = "Las contraseñas no coinciden.";
                header("location: reset-password.php?err_message=".$confirm_password_err."");
                $acces = 0;
            }
        }
        

        if ($acces == 1) {
            $sql = "UPDATE users SET password='$hashed_password' WHERE id='$user_id'";
            mysqli_query($link, $sql);
            $sql = "INSERT INTO notifications (text, userid) VALUES ('¡Tu contraseña ha sido cambiada!', '".$_SESSION['id']."')";
            mysqli_query($link, $sql);

            $err_message = "¡Contraseña cambiada!";

            header('location: profile.php?id='.$user_id.'&err_message='.$err_message.'');
        }
    } else if ($action == "search_admins") {
        $sql = "SELECT * FROM users WHERE logged=1 AND admin=1";
        $result = mysqli_query($link, $sql);

        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $admin_username = $row['username'];
                $admin_file = $row['file'];
                $admin_id = $row['id'];
                ?>
                    <div class='admin-card' onclick='window.location="profile.php?id=<?php echo $admin_id ?>";'>
                        <img src='<?php echo $admin_file; ?>' id='admin-photo' alt='profile picture'>
                        <p id='admin-name'><?php echo $admin_username; ?></p>
                    </div>
                <?php
            }
        } else {
            echo "<span style='color: white'>¡Sin administradores!</span>";

        }
    } else if ($action == "search_user") {
        $username = $_REQUEST["username"];
        
        if (isset($username)) {
            $sql = "SELECT * FROM users WHERE username LIKE '%$username%'";
            $result = mysqli_query($link, $sql);
            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    ?>  
                        <div id="noFound" onclick="window.location='profile.php?id=<?php echo $row['id'] ?>' + '&username=' + '<?php echo $row['username'] ?>';" style="cursor: pointer;">
                            <img src='<?php echo $row['file']; ?>' id="imgUser" height="30" width="30">
                            <span id="linkToProfile"><?php echo $row["username"] ?></span></a> 
                        </div>
                    <?php
                }
            } else {
                echo "<div id='noFound'>¡No se encontraron usuarios!</div>";

            }
        }
    }else if ($action == "search_contact") {
        $username = $_REQUEST["username"];
        $admin = $_SESSION['admin'];
    
        $sql = "SELECT * FROM users WHERE username LIKE ? AND id != ?";
        if ($admin == 0) {
            $sql .= " AND admin = 1";
        }
        $stmt = mysqli_prepare($link, $sql);
        // Verificar si la preparación de la consulta fue exitosa
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "si", $username_like, $_SESSION['id']);
            $username_like = '%' . $username . '%';
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            // Verificar si se encontraron resultados
            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    ?>  
                    <div id="noFound" onclick="window.location='home.php?id=<?php echo $row['id'] ?>';">
                        <img src='<?php echo $row['file']; ?>' id="imgUser" height="30" width="30">
                        <span id="linkToProfile"><?php echo $row["username"];
                         if($row['admin'] == 1) 
                         { echo " (Admin)"; }?></span>
                    </div>
                    <?php
                }
            } else {
                echo "<div id='noFound'>¡No se encontraron usuarios!</div>";
            }
        } else {
            echo "Error al preparar la consulta.";
        }
    } else if($action == "getAllUsers"){
        $login_id = $_SESSION['id'];
        $sql = "SELECT * FROM users WHERE admin=0 AND id!=$login_id";
            $result = mysqli_query($link, $sql);
            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    ?>  
                        <div id="noFound" onclick="window.location='profile.php?id=<?php echo $row['id'] ?>';">
                            <img src='<?php echo $row['file']; ?>' id="imgUser" height="30" width="30">
                            <span id="linkToProfile"><?php echo $row["username"] ?></span></a> 
                        </div>
                    <?php
                }
            } else {
                echo "<div id='noFound'>¡No se encontraron usuarios!</div>";

            }
        
    }else if($action == "getAllContacts"){
        $login_id = $_SESSION['id'];
        //comproba si es admin o no
        $admin = $_SESSION['admin'];
        if($admin == 1){
            $sql = "SELECT * FROM users WHERE id!=$login_id";
        }else{
            $sql = "SELECT * FROM users WHERE admin=1 AND id!=$login_id";
        }


            $result = mysqli_query($link, $sql);
            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    ?>  
                        <div id="noFound" onclick="window.location='home.php?to_id=<?php echo $row['id']?> &username=' + '<?php echo $row['username'] ?>';">
                            <img src='<?php echo $row['file']; ?>' id="imgUser" height="30" width="30">
                            <span id="linkToProfile"><?php echo $row["username"];if($row['admin'] == 1) 
                         { echo " (Admin)"; } ?></span></a> 
                        </div>
                    <?php
                }
            } else {
                echo "<div id='noFound'>¡No se encontraron usuarios!</div>";

            }
        
    } else if ($action == "verify_account") {
        $name = $_SESSION['username'];
        $acces = 1;

        if (!isset($_POST['email-verification'])) {
            $confirm_err = 'Por favor, confirma presionando la casilla de verificación.';
            header("location: verify-account.php?err_message=".$confirm_err."");
            $acces = 0;
        }

        if ($acces == 1) {
            $myemail = $_SESSION['email'];
            $id = $_SESSION['id'];

            $account_name = $_SESSION['username'];
            $account_id = $_SESSION['id'];
            $account_email = $_SESSION['email'];
            $actual_link = "https://$_SERVER[SERVER_NAME]/login.php?redirect_link=confirm-account.php?id=$account_id";

            $localhost = array(
                '127.0.0.1',
                '::1'
            );
            if (!in_array($_SERVER['REMOTE_ADDR'], $localhost)) {
                $mail = new PHPMailer();
                $mail->IsSMTP();
                $mail->SMTPDebug = 0;
                $mail->SMTPAuth = true;
                $mail->SMTPSecure = 'ssl';
                $mail->Host = "mail.lazarnarcis.ro";
                $mail->Port = 465;
                $mail->IsHTML(true);
                $mail->Username = "$email_gmail";
                $mail->Password = "$password_gmail";
                $mail->SetFrom("$email_gmail");
                $mail->Subject = "Verificación de cuenta - $account_name";
                $mail->Body = "Hola, <b>$account_name</b>,<br/>Por favor, confirma tu cuenta haciendo clic en este enlace: <a href='$actual_link'>$actual_link</a>";
                $mail->AddAddress("$account_email");
            
                if ($mail->send()) {
                    $sql = "INSERT INTO notifications (text, userid) VALUES ('Se ha enviado un correo electrónico de verificación de cuenta a <b>$myemail</b>.', '".$_SESSION['id']."')";
                    $query = mysqli_query($link, $sql); 
                    $err_message = "¡He enviado un correo electrónico a $myemail!";
                    header('location: profile.php?id='.$id.'&err_message='.$err_message.'');
                } else {
                    $confirm_err = "¡El correo electrónico no se envió!";
                    header("location: verify-account.php?err_message=".$confirm_err."");
                    $acces = 0;
                }
            } else {
                $err_message = "¡Solo puedes enviar correos electrónicos si tu proyecto no está en localhost!";
                header('location: profile.php?id='.$id.'&err_message='.$err_message.'');
            }
            
        }
    } else if ($action == "register") {
        $set_username = htmlspecialchars($_POST["username"]);
        $set_email = htmlspecialchars($_POST['email']);
        $set_password = htmlspecialchars($_POST['password']);
        $set_confirm_password = htmlspecialchars($_POST['confirm_password']);
        $set_file = htmlspecialchars($_FILES['image']['tmp_name']);
        $linkToInvite = $_POST['invite_link'];
        $invite_link = generateRandomString(10);
        $acces = 1;
        $input_data = "username=".$set_username."&email=".$set_email."&password=".$set_password."&confirm_password=".$set_confirm_password."";
        $err_message = "";

        if (empty($set_confirm_password)) {
            $err_message = "Por favor, confirma la contraseña."; 
            header("location: register.php?$input_data&err_message=".$err_message."");
            $acces = 0;    
        } else if ($set_password != $set_confirm_password) {
            $err_message = "Las contraseñas no coinciden.";
            header("location: register.php?$input_data&err_message=".$err_message."");
            $acces = 0;
        }
        

        if (empty($set_password)) {
            $err_message = "Por favor, introduce una contraseña."; 
            header("location: register.php?$input_data&err_message=".$err_message."");
            $acces = 0;    
        } else if (strlen($set_password) < 6) {
            $err_message = "La contraseña debe tener al menos 6 caracteres.";
            header("location: register.php?$input_data&err_message=".$err_message."");
            $acces = 0;
        } else if (strlen($set_password) > 18) {
            $err_message = "Contraseña demasiado larga (máximo 18 caracteres).";
            header("location: register.php?$input_data&err_message=".$err_message."");
            $acces = 0;
        } else if (!preg_match("#[0-9]+#", $set_password)) {
            $err_message = "¡La contraseña debe incluir al menos un número!";
            header("location: register.php?$input_data&err_message=".$err_message."");
            $acces = 0;
        } else if (!preg_match("#[a-zA-Z]+#", $set_password)) {
            $err_message = "¡La contraseña debe incluir al menos una letra!";
            header("location: register.php?$input_data&err_message=".$err_message."");
            $acces = 0;
        } else {
            $password = $set_password;
        }
        

        if (!empty($_FILES["image"]["name"])) { 
            $fileName = basename($_FILES["image"]["name"]); 
            $fileType = pathinfo($fileName, PATHINFO_EXTENSION);
            $allowTypes = array('jpg','png','jpeg','gif'); 
            
            if (in_array($fileType, $allowTypes)) { 
                $image_base64 = base64_encode(file_get_contents($_FILES["image"]["tmp_name"]));
                $base64 = 'data:image/jpg;base64,'.$image_base64; 
                $file_base64 = $base64;
            } else { 
                $err_message = 'Lo siento, solo se permiten archivos JPG, JPEG, PNG y GIF para cargar.'; 
                header("location: register.php?$input_data&err_message=".$err_message."");
                $acces = 0;
            } 
        } else { 
            $err_message = 'Por favor, selecciona un archivo de imagen para cargar.'; 
            header("location: register.php?$input_data&err_message=".$err_message."");
            $acces = 0;
        }
        

        if (empty($set_email)) {
            $err_message = "Por favor, introduce un correo electrónico.";  
            header("location: register.php?$input_data&err_message=".$err_message."");
            $acces = 0;   
        } else if (strlen($set_email) < 5) {
            $err_message = "¡Correo electrónico demasiado corto!";
            header("location: register.php?$input_data&err_message=".$err_message."");
            $acces = 0;
        } else if (strlen($set_email) > 50) {
            $err_message = "¡Correo electrónico demasiado largo!";
            header("location: register.php?$input_data&err_message=".$err_message."");
            $acces = 0;
        } else if (preg_match('/[A-Z]/', $set_email)) {
            $err_message = "El correo electrónico no puede contener letras mayúsculas.";
            header("location: register.php?$input_data&err_message=".$err_message."");
            $acces = 0;
        } else if (!filter_var($set_email, FILTER_VALIDATE_EMAIL)) {
            $err_message = "¡Por favor, introduce un correo electrónico válido!";
            header("location: register.php?$input_data&err_message=".$err_message."");
            $acces = 0;
        } else {
            $sql = "SELECT id FROM users WHERE email='$set_email'";
            $result = mysqli_query($link, $sql);
            if (mysqli_num_rows($result) > 0) {
                $err_message = "Este correo electrónico ya está en uso.";
                header("location: register.php?$input_data&err_message=".$err_message."");
                $acces = 0;
            } else {
                $email = $set_email;
            }
        }
        

        if (empty($set_username)) {
            $err_message = "Por favor, introduce un nombre de usuario.";
            header("location: register.php?$input_data&err_message=".$err_message."");
            $acces = 0;
        } else if (strlen($set_username) < 6) {
            $err_message = "El nombre de usuario debe tener al menos 6 caracteres.";
            header("location: register.php?$input_data&err_message=".$err_message."");
            $acces = 0;
        } else if (strlen($set_username) > 25) {
            $err_message = "Nombre de usuario demasiado largo.";
            header("location: register.php?$input_data&err_message=".$err_message."");
            $acces = 0;
        } else if (preg_match('/\s/', $set_username)) {
            $err_message = "Tu nombre de usuario no debe contener espacios en blanco.";
            header("location: register.php?$input_data&err_message=".$err_message."");
            $acces = 0;
        } else if (preg_match('/[A-Z]/', $set_username)) {
            $err_message = "El nombre no puede contener letras mayúsculas.";
            header("location: register.php?$input_data&err_message=".$err_message."");
            $acces = 0;
        } else {
            $sql = "SELECT id FROM users WHERE username='$set_username'";
            $result = mysqli_query($link, $sql);
            if (mysqli_num_rows($result) > 0) {
                $err_message = "Este nombre de usuario ya está en uso.";
                header("location: register.php?$input_data&err_message=".$err_message."");
                $acces = 0;
            } else {
                $username = $set_username;
            }
        }
        

        if ($acces == 1) {
            if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
                $serverip = $_SERVER['HTTP_CLIENT_IP'];
            } else if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $serverip = $_SERVER['HTTP_X_FORWARDED_FOR'];
            } else {
                $serverip = $_SERVER['REMOTE_ADDR'];
            }

            $localhost = array(
                '127.0.0.1',
                '::1'
            );

            $sql5 = "SELECT * FROM users WHERE invite_link='$linkToInvite'";
            $result5 = mysqli_query($link, $sql5);
            $row5 = mysqli_fetch_assoc($result5);
            $invited_by = 1;
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $sql = "INSERT INTO users (username, password, admin, send_message, email, file, ip, last_ip, logged, verified, invites, invite_link, invited_by) VALUES ('$username', '$password_hash', 0, 1, '$email', '$file_base64', '$serverip', '$serverip', 0, 0, 0, '$invite_link', '$invited_by')";
            mysqli_query($link, $sql);

            $sql2 = "SELECT * FROM users ORDER BY id DESC LIMIT 1";
            $res2 = mysqli_query($link, $sql2);
            $row2 = mysqli_fetch_assoc($res2);

            $username = $row2['username'];
            $email = $row2['email'];
            $id = $row2['id'];
            $admin = $row2['admin'];
            $send_message = $row2['send_message'];
            $created_at = $row2['created_at'];
            $bio = $row2['bio'];
            $file = $row2['file'];
            $email = $row2['email'];
            $founder = $row2['founder'];
            $banned = $row2['banned'];
            $logged = $row2['logged'];
            $ip = $row2['ip'];
            $last_ip = $row2['last_ip'];
            $verified = $row2['verified'];

            if (!in_array($_SERVER['REMOTE_ADDR'], $localhost)) {
                $domain = "https://$_SERVER[HTTP_HOST]";
                $date = date("l jS \of F Y h:i:s A");

                $mail = new PHPMailer();
                $mail->IsSMTP();
                $mail->SMTPDebug = 0;
                $mail->SMTPAuth = true;
                $mail->SMTPSecure = 'ssl';
                $mail->Host = "mail.lazarnarcis.ro";
                $mail->Port = 465;
                $mail->IsHTML(true);
                $mail->Username = "$email_gmail";
                $mail->Password = "$password_gmail";
                $mail->SetFrom("$email_gmail");
                $mail->Subject = "Gracias por registrarte - $domain";
                $mail->Body = "Gracias por registrarte en nuestro sitio, <b>$username</b>.<br>Este es un proyecto de código abierto (<a href='https://github.com/lazarnarcis/chat'>https://github.com/lazarnarcis/chat</a>). <br>La IP con la que te registraste es: $serverip.<br>La cuenta fue creada el: $date<br><br>Saludos,<br>Narcis.";

                $admins = array();
                $sql1 = "SELECT * FROM users WHERE admin=1 OR founder=1";
                $result1 = mysqli_query($link, $sql1);

                if (mysqli_num_rows($result1) > 0) {
                    while ($row = mysqli_fetch_assoc($result1)) {
                        $adminname = $row['email'];
                        array_push($admins, $adminname);
                    }
                }

                if (count($admins) > 0) {
                    for ($i = 0; $i < count($admins); $i++) {
                        $newAdminEmail = $admins[$i];
                        $mail = new PHPMailer();
                        $mail->IsSMTP();
                        $mail->SMTPDebug = 0;
                        $mail->SMTPAuth = true;
                        $mail->SMTPSecure = 'ssl';
                        $mail->Host = "mail.lazarnarcis.ro";
                        $mail->Port = 465;
                        $mail->IsHTML(true);
                        $mail->Username = "$email_gmail";
                        $mail->Password = "$password_gmail";
                        $mail->SetFrom("$email_gmail");
                        $mail->Subject = "Nueva cuenta ~ $username";
                        $linkUsername = "https://$_SERVER[SERVER_NAME]/login.php?redirect_link=profile.php?id=$id";
                        $mail->Body = "<b>$username</b> Acabo de crear una cuenta. <a href='$linkUsername' target='_blank'>Ver usuario (#$id)</a>";
                        $mail->AddAddress("$newAdminEmail");
                        $mail->send();
                    }
                }
                
            }

            if (!empty($linkToInvite) || $linkToInvite != "") {
                $sql = "SELECT * FROM users WHERE invite_link='$linkToInvite'";
                $result = mysqli_query($link, $sql);
                
                if (mysqli_num_rows($result) > 0) {
                    $row = mysqli_fetch_assoc($result);
                    $invited_id = $row['id'];
                    $sql = "INSERT INTO notifications (userid, text) VALUES ('$invited_id', '¡Excelente! $username ha creado una cuenta usando tu enlace de invitación!')";
                    mysqli_query($link, $sql);
                    $sql = "UPDATE users SET invites=invites+1 WHERE id='$invited_id'";
                    mysqli_query($link, $sql);
                }
                
            }
            
            $sql = "INSERT INTO chat (action, actiontext) VALUES ('1', '$username Acabo de crear una cuenta.')";
            mysqli_query($link, $sql);

            $_SESSION["loggedin"] = true;
            $_SESSION["id"] = $id;
            $_SESSION["username"] = $username;  
            $_SESSION["admin"] = $admin;  
            $_SESSION["send_message"] = $send_message;  
            $_SESSION["created_at"] = $created_at;
            $_SESSION["bio"] = $bio;  
            $_SESSION["file"] = $file;  
            $_SESSION["email"] = $email;  
            $_SESSION["founder"] = $founder;  
            $_SESSION["banned"] = $banned;  
            $_SESSION["logged"] = $logged;
            $_SESSION["ip"] = $ip;
            $_SESSION["last_ip"] = $last_ip;
            $_SESSION["verified"] = $verified;

            $sql = "INSERT INTO notifications (userid, text) VALUES ('$id', '¡Por favor verifica tu cuenta!')";
            mysqli_query($link, $sql);

            header("location: login.php");
        }
    }
    mysqli_close($link);
?>