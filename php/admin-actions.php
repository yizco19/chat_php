<?php
session_start();
include_once "config.php";
include_once "functions.php";

if (!isset($_SESSION['is_super_admin']) || $_SESSION['is_super_admin'] != 1) {
    header("location: ../login.php");
    exit;
}

$action = isset($_GET['action']) ? $_GET['action'] : '';

switch ($action) {
    case 'activate':
        activateAdmin();
        break;
    case 'deactivate':
        deactivateAdmin();
        break;
    case 'get_all':
        getAllAdmins();
        break;
        case 'delete-user':
            $userId = isset($_POST['unique_id']) ? $_POST['unique_id'] : '';
            if ($userId != '' && $userId != 0 && $userId != null) {
                deleteUser($userId);
            } else {
                echo json_encode(['error' => 'ID no proporcionado']);
            }
            break;
        
    default:
        echo json_encode(['error' => 'Accion no valida']);
        break;
}
function deleteUser($userId){
    global $conn;

    //Elimina todos los mensajes del usuario
    $sql = mysqli_query($conn, "DELETE FROM messages WHERE incoming_msg_id = {$userId} OR outgoing_msg_id = {$userId}");
    if($sql){
        //Elimina todos los topics del usuario
        $sql = mysqli_query($conn, "DELETE FROM user_topics WHERE user_id = {$userId}");
        if($sql){
            //Elimina el usuario de la base de datos
            $sql = mysqli_query($conn, "DELETE FROM users WHERE unique_id = {$userId}");
            if($sql){
                echo "El usuario ha sido eliminado";
            }else{
                echo "Algo salió mal. ¡Inténtalo de nuevo!";
            }
        }else{
            echo "Algo salió mal. ¡Inténtalo de nuevo!";
        }
    }else{

        echo "Algo salió mal. ¡Inténtalo de nuevo!";
    }


}

function activateAdmin() {
    global $conn;

    if (isset($_POST['userId']) && !empty($_POST['userId'])) {
        $userId = mysqli_real_escape_string($conn, $_POST['userId']);
        $sql = mysqli_query($conn, "UPDATE users SET admin = 1 WHERE user_id = {$userId}");

        if ($sql) {
            echo "success";
        } else {
            echo "Algo salió mal. ¡Inténtalo de nuevo!";
        }
    } else {
        echo "Algo salió mal. ¡Inténtalo de nuevo!";
    }
}

function deactivateAdmin() {
    global $conn;

    if (isset($_POST['userId']) && !empty($_POST['userId'])) {
        $userId = mysqli_real_escape_string($conn, $_POST['userId']);
        $sql = mysqli_query($conn, "UPDATE users SET admin = 0 WHERE user_id = {$userId}");

        if ($sql) {
            $sql2 = mysqli_query($conn, "DELETE FROM user_topics WHERE user_id = {$userId}");
            if ($sql2) {
                echo "success";
            } else {
                echo "Algo salió mal. ¡Inténtalo de nuevo!";
            }
        } else {
            echo "Algo salió mal. ¡Inténtalo de nuevo!";
        }
    } else {
        echo "Algo salió mal. ¡Inténtalo de nuevo!";
    }
}

function getAllAdmins() {
    $conn = connect();

    try {
        $stmt = $conn->prepare("SELECT user_id, fname, lname FROM users WHERE admin = 1");
        $stmt->execute();
        $admins = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($admins);
    } catch (PDOException $e) {
        echo json_encode(['error' => 'Error al obtener los administradores: ' . $e->getMessage()]);
    }

    $conn = null; // Cerrar la conexión
}
?>
