<?php
    session_start();

        $action = isset($_GET['action']) ? $_GET['action'] : '';

    require_once 'functions.php';
    switch ($action) {
        case 'start-conversation':
            startConversation();
            break;
        case 'delete-conversation':
            deleteConversation();
            break;
            default:
            echo json_encode(['error' => 'Acción no válida']);
            break;
    }
    startConversation(){
        $top = isset($_POST['conversation_id'])?$_POST['conversation_id']:'';
        $userId = isset($_SESSION['user_id'])?$_SESSION['user_id']:'';
        if($conversationId && $userId){
            $conn = connect();
            $Query = "UPDATE conversations SET status = 1 WHERE id = $conversationId AND user_id = $userId";
            $result = mysqli_query($conn,$Query);
            if($result){
                echo json_encode(['message'=>'Conversación iniciada']);
            }else{
                echo json_encode(['error'=>'No se pudo iniciar la conversación']);
            }
        }else{
            echo json_encode(['error'=>'No se pudo iniciar la conversación']);
        }
    }