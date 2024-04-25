<?php
session_start();
//Import PHPMailer classes into the global namespace
//These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
require_once 'config.php';

//Load Composer's autoloader
require_once  '../vendor/autoload.php';
require_once 'functions.php';
$incoming_id = $_GET['incoming_id'];
$outgoing_id = $_SESSION['unique_id'];
$username = $_SESSION['username'];
$sql = "SELECT * FROM users WHERE unique_id = {$incoming_id} and admin = 1";
$query = mysqli_query($conn, $sql);
if (mysqli_num_rows($query) > 0) {
    $row = mysqli_fetch_assoc($query);
    $admin = $row['admin'];
    $email = $row['email'];
    if ($admin == 1) {
        sleep(10);
        //comprueba si hay mensaje no visto
        $sql2 = "SELECT * FROM messages WHERE incoming_msg_id = {$incoming_id} AND outgoing_msg_id = {$outgoing_id} AND is_seen = 0";
        echo $sql2;
        $query2 = mysqli_query($conn, $sql2);
        if (mysqli_num_rows($query2) > 0) {
        sendEmail($email,$username);
    }else{
        echo "No hay mensajes no vistos";
    }


    }
}else{
    echo "Error";
}

function sendEmail($email,$username){
    try {
        //Create an instance; passing `true` enables exceptions
        $mail = new PHPMailer(true);
    
    
        //Server settings
        $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
        $mail->isSMTP();                                            //Send using SMTP
        $mail->Host       = 'smtp.gmail.com';                     //Set the SMTP server to send through
        $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
        $mail->Username   = 'zhiyang520679@gmail.com';                     //SMTP username
        $mail->Password   = 'ydisjjcsynxemusr';                               //SMTP password
        $mail->SMTPSecure = 'ssl';            //Enable implicit TLS encryption
        $mail->Port       = 465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`
    
        //Recipients
        $mail->setFrom('zhiyang520679@gmail.com');
        $mail->addAddress($email);     //Add a recipient
        $mail->isHTML(true);                                  //Set email format to HTML
        $mail->Subject = 'Notificacion de nuevo mensaje de ' . $username;  
        $mail->Body    = $username . 'te envio un mensaje';
    
        $mail->send();
        echo 'Message has been sent';
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}