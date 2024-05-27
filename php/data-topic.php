<?php

foreach ($topics as $topic) {
    // Aquí puedes acceder a cada columna de la fila actual
    $topicId = $topic['id'];
    $topicName = $topic['name'];
    $topicImg = $topic['img'];
    $message = $topic['msg'];
    $unique_id = $topic['unique_id'];
    $firstName = $topic['fname'];
    $lastName = $topic['lname'];
    //$userImg = $topic['img'];

    // Ahora puedes mostrar esta información como desees, por ejemplo:
        $output.= '<div class="topic">';
        $output.= '<a href="chat.php?topic_id='. $topicId. '&user_id='.$unique_id.' " >';
         if (strpos($topicImg, 'php/') === 0) {
            // Si la imagen comienza con 'php/', la mostramos como una imagen simple
        $output.= '<img src="' . $topicImg . '" alt="' . $topic['name'] . '" style=" height: 64px; width: 64px;" /> ';
        } else if (strpos($topicImg, 'letra/') === 0) {
            // Si la imagen comienza con 'letra/', la dividimos y mostramos como un círculo con la letra y color
            $cadena = substr($topicImg, 6); // Corta la parte "letra/" y toma el resto
            $subarray = explode("/", $cadena);
            $output.= '<div class="circulo" style=" height: 40px; width: 40px; background: ' . $subarray[1] . '"><span class="letra">' . $subarray[0] . '</span></div>';
        } else {
            // De lo contrario, mostramos la imagen como una imagen simple
            $output.= '<img src="' . $topicImg . '" alt="' . $topic['name'] . '" style=" height: 40px; width: 40px;" /> ';
        }
        $output.='<span class="topic-name">'.  $topicName.'</span>' ;
         //$output.= '<p>' . $message . '</p>';
         //$output.= '<p>User: ' . $firstName . ' ' . $lastName . '</p>';
         //$output.= '<img src="' . $userImg . '" alt="User Image">';
         $output.= '</a>';
         $output.= '</div>';
}

