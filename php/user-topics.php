<?php
session_start();

// Función para conectarse a la base de datos usando PDO
require_once 'functions.php';

// Verificar si el usuario tiene permisos de administrador
/*
if (!isset($_SESSION['is_super_admin']) || $_SESSION['is_super_admin'] != 1) {
    echo json_encode(['error' => 'No tienes permisos para acceder a esta funcionalidad']);
    exit;
}
*/
// Obtener el tipo de acción
$action = isset($_GET['action']) ? $_GET['action'] : '';

switch ($action) {
    case 'get':
        getUserTopics();
        break;
        case 'get-topics-data':
            $userId = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;
            if ($userId == 0) {
                echo json_encode(['error' => 'ID de usuario no proporcionado']);
                exit;
            }
        
            $topics= getTopicsData($userId);
            echo json_encode($topics);
            break;
            case 'get-admin-data':
                $topicId = isset($_GET['topic_id'])? intval($_GET['topic_id']) : 0;
                if ($topicId == 0) {
                   echo json_encode(['error' => 'ID de tema no proporcionado']);
                   exit;
                }
                $admins = getAdminByTopics($topicId);
                echo json_encode($admins);
                break;
    case 'admin-topics':
        adminTopicsUpdate();
        break;
    case 'update':
    case 'delete':
        deleteUserTopic();
        break;
    case 'add':
        addUserTopic();
        break;
    default:
        echo json_encode(['error' => 'Acción no válida']);
        break;
}
function getAdminByTopics($topicId) {
    $conn = connect();
    $topicId = intval($topicId);
    $Query ="    SELECT 
    ut.*,u.fname,u.lname,u.img,u.unique_id
FROM user_topics ut
JOIN  users u ON
    ut.user_id = u.user_id AND ut.topic_id = :topic_id";
    $stmt = $conn->prepare($Query);
    $stmt->bindParam(':topic_id', $topicId, PDO::PARAM_INT);
    $stmt->execute();
    $admins = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $admins;
}
// Función para administrar los temas del usuario
function adminTopicsUpdate() {
    // Verificar si se proporcionaron los parámetros necesarios
    if (!isset($_POST['userId']) || !isset($_POST['topicId']) || !isset($_POST['checked'])) {
        echo json_encode(['error' => 'Faltan parámetros']);
        exit;
    }

    // Obtener los parámetros de la solicitud
    $userId = intval($_POST['userId']);
    $topicId = intval($_POST['topicId']);
    $checked = $_POST['checked'] === 'true' ? 1 : 0; // Convertir 'true' a 1 y 'false' a 0

    // Crear la conexión a la base de datos
    $conn = connect();

    // si checked es verdadero, agregar el tema al usuario
    if ($checked) {
        $stmt = $conn->prepare("INSERT INTO user_topics (user_id, topic_id) VALUES (?, ?)");
        $stmt->bindValue(1, $userId, PDO::PARAM_INT);
        $stmt->bindValue(2, $topicId, PDO::PARAM_INT);
    } else { // sino, eliminar el tema del usuario
        $stmt = $conn->prepare("DELETE FROM user_topics WHERE user_id = ? AND topic_id = ?");
        $stmt->bindValue(1, $userId, PDO::PARAM_INT);
        $stmt->bindValue(2, $topicId, PDO::PARAM_INT);
    }

    // Ejecutar la consulta preparada
    $result = $stmt->execute();

    // Verificar si la modificación fue exitosa
    if ($result) {
        echo json_encode(['success' => 'Modificación exitosa']);
    } else {
        echo json_encode(['error' => 'Hubo un error al realizar la modificación']);
    }

    // Cerrar la conexión
    $stmt->closeCursor();
    $conn = null;
}

function getTopicsData($userId) {
    $conn = connect();
    // Obtener información del usuario
    $userQuery = "SELECT t.*, ut.user_id FROM topic t LEFT JOIN user_topics ut ON t.id = ut.topic_id AND ut.user_id = :user_id ";
    $stmt = $conn->prepare($userQuery);
    $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
    $stmt->execute();
    $topics = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($topics as $key => &$topic) {
        if (strpos($topic['img'], 'php/') === 0) {
            // Si la imagen comienza con 'php/', la mostramos como una imagen simple
            $topic['img'] = '<img src="' . $topic['img'] . '" alt="' . $topic['name'] . '" class="topic-img" style="cursor: pointer; height: 64px; width: 64px;" /> ';
        } else if (strpos($topic['img'], 'letra/') === 0) {
            // Si la imagen comienza con 'letra/', la dividimos y mostramos como un círculo con la letra y color
            $cadena = substr($topic['img'], 6); // Corta la parte "letra/" y toma el resto
            $subarray = explode("/", $cadena);
            $topic['img'] = '<div class="circulo" style="cursor: pointer; height: 64px; width: 64px; background: ' . $subarray[1] . '"><span class="letra">' . $subarray[0] . '</span></div>';
        } else {
            // De lo contrario, mostramos la imagen como una imagen simple
            $topic['img'] = '<img src="' . $topic['img'] . '" alt="' . $topic['name'] . '" class="topic-img" style="cursor: pointer; height: 64px; width: 64px;" /> ';
        }
    }
    return $topics;
}

function getUserTopics() {
    $conn = connect();
    $userId = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;
    if ($userId == 0) {
        echo json_encode(['error' => 'ID de usuario no proporcionado']);
        exit;
    }

    // Obtener información del usuario
    $userQuery = "SELECT user_id, fname, lname FROM users WHERE user_id = :user_id";
    $stmt = $conn->prepare($userQuery);
    $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Obtener todos los topics
    $topicsQuery = "SELECT id, name, img FROM topic";
    $stmt = $conn->query($topicsQuery);
    $topics = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Obtener los topics asignados al usuario
    $userTopicsQuery = "SELECT topic_id FROM user_topics WHERE user_id = :user_id";
    $stmt = $conn->prepare($userTopicsQuery);
    $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
    $stmt->execute();
    $userTopics = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);

    // Construir la respuesta JSON
    $response = [
        'user' => $user,
        'topics' => $topics,
        'userTopics' => $userTopics
    ];

    echo json_encode($response);
    $conn = null; // Cerrar la conexión
}

function deleteUserTopic() {
    $conn = connect();
    $userId = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;
    $topicId = isset($_POST['topic_id']) ? intval($_POST['topic_id']) : 0;

    if ($userId == 0 || $topicId == 0) {
        echo json_encode(['error' => 'ID de usuario o topic no proporcionado']);
        exit;
    }

    // Eliminar el topic asignado al usuario
    $deleteQuery = "DELETE FROM user_topics WHERE user_id = :user_id AND topic_id = :topic_id";
    $stmt = $conn->prepare($deleteQuery);
    $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
    $stmt->bindParam(':topic_id', $topicId, PDO::PARAM_INT);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        echo json_encode(['message' => 'Topic eliminado del usuario']);
    } else {
        echo json_encode(['error' => 'No se pudo eliminar el topic del usuario']);
    }

    $conn = null; // Cerrar la conexión
}

function addUserTopic() {
    $conn = connect();
    $userId = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;
    $topicId = isset($_POST['topic_id']) ? intval($_POST['topic_id']) : 0;

    if ($userId == 0 || $topicId == 0) {
        echo json_encode(['error' => 'ID de usuario o topic no proporcionado']);
        exit;
    }

    // Agregar el topic asignado al usuario
    $insertQuery = "INSERT INTO user_topics (user_id, topic_id) VALUES (:user_id, :topic_id)";
    $stmt = $conn->prepare($insertQuery);
    $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
    $stmt->bindParam(':topic_id', $topicId, PDO::PARAM_INT);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        echo json_encode(['message' => 'Topic asignado al usuario']);
    } else {
        echo json_encode(['error' => 'No se pudo asignar el topic al usuario']);
    }

    $conn = null; // Cerrar la conexión
}
?>
