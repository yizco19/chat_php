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
    function getContactList(){
        $conn = connect();
        $unique_id = $_SESSION['unique_id'];
        $sql = "SELECT t.*, m.msg, u.fname, u.lname,u.unique_id
                FROM topic t
                JOIN (
                    SELECT m.*
                    FROM messages m
                    INNER JOIN (
                        SELECT incoming_msg_id, MAX(created_at) AS max_created_at
                        FROM messages
                        WHERE outgoing_msg_id = :unique_id AND topic_id IS NOT NULL
                        GROUP BY topic_id
                    ) max_dates ON m.incoming_msg_id = max_dates.incoming_msg_id AND m.created_at = max_dates.max_created_at
                ) m ON t.id = m.topic_id
                JOIN users u ON m.incoming_msg_id = u.unique_id";
    
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':unique_id', $unique_id);
        $stmt->execute();
        $topics = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $output = "";;

        if(count($topics) == 0){
            $output .= "No users are available to chat";
        }elseif(count($topics) > 0){
            include_once "data-topic.php";
        }
        echo $output;
    }
    


    function insertTopic($name, $img,$letra) {
        if($letra==false){
            $img_path = 'php/'. $img;
        }
        $img_path='letra/'.$img;
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
            //comprueba si img es un file o un letra de texto
            
            $img = isset($_FILES['img']) ? $_FILES['img'] : null;
            // Manejar la carga del archivo
            if ($img && $img['error'] == UPLOAD_ERR_OK) {
                $img_nombre = basename($img['name']);
                $ruta = "topics/" . $img_nombre;
                move_uploaded_file($img['tmp_name'], $ruta);
                $insertedId = insertTopic($name, $ruta,false);
                echo json_encode(['message' => 'Nuevo tema insertado con ID: ' . $insertedId]);
            } else {
                if(isset($_POST['img']) && $_POST['img']!= '' && $_POST['img']!= null && $_POST['img']!= 'null' ){
                
                    $result = insertTopic($name, $_POST['img'],true);
                    echo json_encode(['message' => 'Nuevo tema insertado con ID: '. $result]);
                    exit;
                }
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
            
        case 'get-contact-list':

                getContactList();
                break;
                
            

        default:
            echo json_encode(['error' => 'Acción no válida']);
            break;
    }
} else {
    echo json_encode(['error' => 'No tienes permisos para acceder a topics.php']);
}
