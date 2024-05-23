<?php
session_start();

if (true) {
    $action = isset($_GET['action']) ? $_GET['action'] : '';

require_once 'functions.php';
    function getAllTopics() {
        $conn = connect();
        $stmt = $conn->query("SELECT * FROM topic");
        $topics = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $topics;
    }

    function getTopicById($id) {
        $conn = connect();
        $stmt = $conn->prepare("SELECT * FROM topic WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $topic = $stmt->fetch(PDO::FETCH_ASSOC);
        return $topic;
    }

    function insertTopic($name, $img) {
        $img_path = 'php/'. $img;
        $conn = connect();
        $stmt = $conn->prepare("INSERT INTO topic (name, img) VALUES (:name, :img)");
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':img',  $img_path);
        $stmt->execute();
        return $conn->lastInsertId();
    }

    function updateTopic($id, $name, $img) {
        $img_path = 'php/'. $img;
        $conn = connect();
        $stmt = $conn->prepare("UPDATE topic SET name = :name, img = :img WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':img',  $img_path);
        $stmt->execute();
        return $stmt->rowCount();
    }

    function deleteTopic($id) {
        $conn = connect();
        $stmt = $conn->prepare("DELETE FROM topic WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->rowCount();
    }

    switch ($action) {
        case 'get_all':
            $topics = getAllTopics();
            echo json_encode($topics);
            break;
        case 'get':
            $id = isset($_GET['id']) ? $_GET['id'] : '';
            if ($id) {
                $topic = getTopicById($id);
                echo json_encode($topic);
            } else {
                echo json_encode(['error' => 'ID no proporcionado']);
            }
            break;
        case 'insert':
            $name = isset($_POST['name']) ? $_POST['name'] : '';
            $img = isset($_FILES['img']) ? $_FILES['img'] : null;
            // Manejar la carga del archivo
            if ($img && $img['error'] == UPLOAD_ERR_OK) {
                $img_nombre = basename($img['name']);
                $ruta = "topics/" . $img_nombre;
                move_uploaded_file($img['tmp_name'], $ruta);
                $insertedId = insertTopic($name, $ruta);
                echo json_encode(['message' => 'Nuevo tema insertado con ID: ' . $insertedId]);
            } else {
                echo json_encode(['error' => 'Error al subir la imagen']);
            }
            break;
        case 'update':
            $id = isset($_POST['id']) ? $_POST['id'] : '';
            $name = isset($_POST['name']) ? $_POST['name'] : '';
            $img = isset($_FILES['img']) ? $_FILES['img'] : null;
            // Manejar la carga del archivPo
            if ($img && $img['error'] == UPLOAD_ERR_OK) {
                $img_nombre = basename($img['name']);
                $ruta = "topics/" . $img_nombre;
                move_uploaded_file($img['tmp_name'], $ruta);
                $updatedRows = updateTopic($id, $name, $ruta);
                echo json_encode(['message' => 'Registros actualizados: ' . $updatedRows]);
            } else {

                echo json_encode(['error' => 'Error al subir la imagen']);
            }
            break;
        case 'delete':
            $id = isset($_POST['id']) ? $_POST['id'] : '';
            if ($id) {
                $deletedRows = deleteTopic($id);
                echo json_encode(['message' => 'Registros eliminados: ' . $deletedRows]);
            } else {
                echo json_encode(['error' => 'ID no proporcionado']);
            }
            break;
        default:
            echo json_encode(['error' => 'Acción no válida']);
            break;
    }
} else {
    echo json_encode(['error' => 'No tienes permisos para acceder a topics.php']);
}
?>
