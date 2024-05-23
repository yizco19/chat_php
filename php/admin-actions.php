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
    default:
        echo json_encode(['error' => 'Acción no válida']);
        break;
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
