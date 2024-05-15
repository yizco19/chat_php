<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (isset($_FILES['nueva_imagen'])) {
        $nuevaImagen = $_FILES['nueva_imagen'];
        
        $img_ext = strtolower(pathinfo($nuevaImagen['name'], PATHINFO_EXTENSION));
        
        $extensions = ["jpeg", "png", "jpg"];
        
        if (in_array($img_ext, $extensions)) {
            $time = time();
            $new_img_name = $time . '_' . $nuevaImagen['name'];
            
            if (move_uploaded_file($nuevaImagen['tmp_name'], "images/" . $new_img_name)) {
                include_once "config.php";
                session_start();
                $id = $_SESSION['unique_id'];
                $sql = "UPDATE users SET img = 'php/images/$new_img_name' WHERE unique_id = $id";
                $result = mysqli_query($conn, $sql);
                
                mysqli_close($conn);
                
                echo "¡La imagen se ha actualizado correctamente!";
            } else {
                http_response_code(500); 
                echo "Error: No se pudo mover la imagen al servidor.";
            }
        } else {
            http_response_code(400); 
            echo "Error: La extensión de la imagen no está permitida. Solo se permiten archivos JPEG, JPG y PNG.";
        }
    } else {
        http_response_code(400); 
        echo "Error: No se recibió la nueva imagen.";
    }
} else {
    http_response_code(405); 
    echo "Error: Método no permitido. Esta página solo acepta solicitudes POST.";
}
