
<?php
require_once "session.php";
require_once "php/functions.php";
?>
<?php include_once "header.php"; ?>
<head>
<style>

</style>
</head>

<body>
  
<!--
<div class="gtranslate_wrapper"></div>
<script>window.gtranslateSettings = {"default_language":"en","languages":["en","fr","de","it","es"],"wrapper_selector":".gtranslate_wrapper","horizontal_position":"right","vertical_position":"top"}</script>
<script src="https://cdn.gtranslate.net/widgets/latest/popup.js" defer></script>-->
  <div class="wrapper">
    <section class="chat-area">
      <header style="  display: flex;
  align-items: center;
  padding-bottom: 20px;
  border-bottom: 1px solid #e6e6e6;
  justify-content: space-between;">
        <?php
        // Evitar inyección de SQL usando mysqli_prepare
        $user_id = mysqli_real_escape_string($conn, $_GET['user_id']);
        $topic_id = isset($_GET['topic_id'])? mysqli_real_escape_string($conn, $_GET['topic_id']) : null;
        $sql = mysqli_query($conn, "SELECT * FROM users WHERE unique_id = {$user_id}");
        if (mysqli_num_rows($sql) > 0) {
          $row = mysqli_fetch_assoc($sql);
        } else {
          // Redirigir si no se encuentra el usuario
          header("location: users.php");
          exit; // Terminar la ejecución del script después de la redirección
        }
        ?>
        <div style="display: flex; align-items: center;">
          <a href="users.php" class="back-icon" style="margin-right: 5px;"><i class="fas fa-arrow-left"></i></a>
          <!-- Escapar el atributo alt con htmlspecialchars para evitar XSS -->
          <?php if($topic_id != null && $topic_id != ""): ?>
            <?php echo getImgById($topic_id); ?>
          <?php endif; ?>
          <img src="<?php echo htmlspecialchars($row['img']); ?>" alt="Profile Image" id="profile-image">
          <div class="details" style="margin-left: 10px;">
            <!-- Escapar los datos del usuario con htmlspecialchars para evitar XSS -->
            <span style="display: block;"><?php echo htmlspecialchars($row['fname'] . " " . $row['lname']); ?></span>
            <p style="display: block;"><?php echo htmlspecialchars($row['status']); ?></p>
          </div>

          </div>
          <div class="userInfo" style="display: flex;justify-content: center;align-items: center;">
            <img src="resource/info.png" alt="infor" id="informactionUser" class="admin-img" style="
    width: 64px;
    height: 64px;
">
        </div>


      </header>


      <div class="chat-box">

      </div>
      <div><img id="file-preview" class="file-preview">
      <span id="file-name" style="  position: fixed; transform:translate(35%, -135%)"></span></div>
      
      <form action="#" class="typing-area" enctype="multipart/form-data" method="POST">

        <input type="text" class="incoming_id" name="incoming_id" value="<?php echo $user_id; ?>" hidden>
        <input type="text" class="topic_id" name="topic_id" value="<?php echo $topic_id; ?>" hidden>
        <input type="text" name="message" class="input-field" placeholder="Escribe tu mensaje aquí..." autocomplete="off">
        <button class="send-btn" style="background-color: green;"><i class="fab fa-telegram-plane"></i></button>
        <!-- Campo de entrada de archivo -->
        <input type="file" class="attachment" id="attachment" name="attachment" style="display: none;">
        <!-- Botón para seleccionar archivo -->
        <button class="adjuntarBtn active" style="background-color: gray;"><i class="fas fa-paperclip"></i></button>
        <!-- Contenedor para mostrar el nombre del archivo seleccionado -->
        <div id="file-info" style="display: none;">

          <button type="button" class="cancelarBtn active" onclick="cancelarArchivo()"><i class="fas fa-times"></i></button>
        </div>
        <button type="button" class="videollamadaBtn active" style="background-color: blue"><i class="fas fa-video"></i></button>
        <input type="submit" value="Enviar" style="display: none;">
      </form>

    </section>
  </div>

  <script src="javascript/chat.js" type="module"></script>
</body>

</html>