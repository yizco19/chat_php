<?php
session_start();
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
  header("location: login.php?redirect_link=home.php");
  exit;
}
if (isset($_GET['to_id'])) {
  $id = $_GET['to_id'];
  $_SESSION['to_id'] = $id;
}

$err_message = "";
if (!empty($_GET['err_message'])) {
  $err_message = $_GET['err_message'];
}
$send_message = $_SESSION['send_message'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport"
    content="user-scalable=no, initial-scale=1, maximum-scale=1, minimum-scale=1, width=device-width, height=device-height">
  <title>Home</title>
  <link rel="shortcut icon" href="logos/logo.png" type="image/x-icon">
  <script src="jquery/jquery.js"></script>
  <link rel="stylesheet" href="css/home.css?v=<?php echo time(); ?>">
  <style>
    .container {
      display: flex;
      width: 100%;
      height: 100%;

    }
  </style>
  <?php
// Verifica si existe el parámetro 'to_id' en la URL
if(isset($_GET['to_id'])) {
    // Si 'to_id' está presente, asigna su valor a la variable $id
    $id = $_GET['to_id'];
?>
    <!-- Si 'to_id' está presente, oculta el contenedor de usuarios solo en pantallas con un ancho máximo de 767px -->
    <style>
        @media only screen and (max-width: 767px) {
            .users-container {
                display: none;
            }
        }
    </style>
<?php
} else {
    // Si 'to_id' no está presente, muestra el contenedor de chat solo en pantallas con un ancho máximo de 767px
?>
    <!-- Si 'to_id' no está presente, muestra el contenedor de chat solo en pantallas con un ancho máximo de 767px -->
    <style>
        @media only screen and (max-width: 767px) {
            .chat-container {
                display: block; /* Asegura que el contenedor de chat esté visible */
            }
        }
    </style>
<?php
}
?>

</head>

<body>
  <?php require_once ("header.php"); ?>
  <div class="container">

    <?php if ($_SESSION['admin'] !== 12): ?>
      <div class="users-container">
        <?php require_once ("users.php"); ?>
      </div>
    <?php endif; ?>
    <div class="home-page" style="display: block; margin: auto;">
      <h2 id="err_message"><?php echo $err_message; ?></h2>
      <h1 id="general-chat">Chat General</h1>
      <div id="messages"></div>
      <form>
        <div id="inputs">
          <textarea type="text" name="message" id="message" placeholder="Escribe un mensaje..." autocomplete="off"
            autofocus></textarea>
          <?php
          if ($_SESSION['send_message'] == 1) {
            ?>
            <p id="press_to_send">Pulsa para enviar</p>
            <?php
          } else if ($_SESSION['send_message'] == 2) {
            ?>
              <input type="image" name="submit" src="logos/send.svg" alt="Submit" />
            <?php
          }
          ?>
          <!-- Campo de entrada de archivo -->
          <input type="file" name="archivo" id="archivo" style="display: none;">
          <!-- Botón para seleccionar archivo -->
          <button type="button" id="select-button" onclick="document.getElementById('archivo').click()">Seleccionar
            archivo</button>
          <!-- Contenedor para mostrar el nombre del archivo seleccionado -->
          <div id="file-info" style="display: none;">
            <span id="file-name"></span>
            <button type="button" onclick="cancelarArchivo()">Cancelar</button>
          </div>
        </div>
      </form>
    </div>
  </div>
  <script>
    function showOptionsForMessage(val) {
      let msj = document.getElementById("showTimes" + val);
      msj.style = "display: inline;";
    }
    function unshowOptionsForMessage(val) {
      let msj = document.getElementById("showTimes" + val);
      msj.style = "display: none;";
    }
    var start = 0;
    var path = location.href.substring(0, location.href.lastIndexOf("/") + 1);
    var send_message = path + '/actions.php?action=send_message';
    var load_chat = path + '/actions.php?action=load_chat';

    $(document).ready(function () {
      $('textarea').keyup(function (e) {
        if (e.key == "Enter" && <?php echo $send_message; ?> == 1) {
          $("form").submit();
        }
      });

      load();
      $("form").submit(function (e) {
        e.preventDefault(); // Prevenir el envío del formulario por defecto

        var formData = new FormData(this); // Crear objeto FormData para enviar datos del formulario, incluido el archivo

        $.ajax({
          url: send_message,
          type: 'POST',
          data: formData,
          processData: false, // Evitar que jQuery procese los datos automáticamente
          contentType: false, // Evitar que jQuery establezca el tipo de contenido
          success: function (response) {
            // Manejar la respuesta si es necesario
            console.log(response);
          }
        });

        // Limpiar el campo de mensaje después de enviar
        $("#message").val(null);
        // Limpiar el campo de archivo
        cancelarArchivo();

      });
    });
    function load() {

      $.get(load_chat + '&start=' + start, function (result) {
        if (result.items) {
          result.items.forEach(item => {

            start = item.id;
            console.log(item.id);
            $.post("actions.php?action=show_loaded_chat&message_id=" + item.id, $(this).serialize()).done(function (data) {
              $("#messages").append(data);
              $("#messages").animate({ scrollTop: $("#messages")[0].scrollHeight }, 0);
            });
          });
        }
        load();
      });

    }
    setTimeout(() => {
      let p = document.getElementById("err_message");

      if (p.innerHTML != "") {
        p.innerHTML = "";
      }
    }, 7500);

    function showFileName() {
      var input = document.getElementById('archivo');
      var fileInfo = document.getElementById('file-info');
      var fileName = document.getElementById('file-name');
      var selectButton = document.getElementById('select-button');
      if (input.files.length > 0) {
        fileName.textContent = 'Archivo seleccionado: ' + input.files[0].name;
        fileInfo.style.display = 'block';
        selectButton.style.display = 'none';
      } else {
        fileInfo.style.display = 'none';
        selectButton.style.display = 'inline';
      }
    }

    // Función para cancelar la selección del archivo
    function cancelarArchivo() {
      var input = document.getElementById('archivo');
      var fileInfo = document.getElementById('file-info');
      var selectButton = document.getElementById('select-button');
      input.value = ''; // Limpiar la selección del archivo
      fileInfo.style.display = 'none'; // Ocultar el contenedor del nombre del archivo
      selectButton.style.display = 'inline'; // Mostrar el botón de selección de archivo
    }

    // Ejecutar la función showFileName cuando se cambie el archivo seleccionado
    document.getElementById('archivo').addEventListener('change', showFileName);

  </script>


</body>

</html>