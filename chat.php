<?php
require_once "session.php";
?>
<?php include_once "header.php"; ?>

<head>
<style>
  .gtranslate_wrapper {
  transform: translate(-100%, -45%);
    position: fixed;

    
    z-index: 9999;  
}
div#gt_float_wrapper {transform: scale(0.8);}
@media screen and (max-width: 350px) {
  .gtranslate_wrapper {
    position: fixed;
    left: 0;
    bottom: 0;
  }
  
}
</style>
</head>

<body>
  <div class="wrapper">
    <section class="chat-area">
      <header style="display: flex; justify-content: space-between;">
        <?php
        // Evitar inyección de SQL usando mysqli_prepare
        $user_id = mysqli_real_escape_string($conn, $_GET['user_id']);
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
          <a href="users.php" class="back-icon"><i class="fas fa-arrow-left"></i></a>
          <!-- Escapar el atributo alt con htmlspecialchars para evitar XSS -->
          <img src="php/images/<?php echo htmlspecialchars($row['img']); ?>" alt="Profile Image" id="profile-image">
          <div class="details" style="margin-left: 10px;">
            <!-- Escapar los datos del usuario con htmlspecialchars para evitar XSS -->
            <span style="display: block;"><?php echo htmlspecialchars($row['fname'] . " " . $row['lname']); ?></span>
            <p style="display: block;"><?php echo htmlspecialchars($row['status']); ?></p>
          </div>
        </div>

        <!-- Agregar estilo en línea para el contenedor de traducción -->
        <div>
        <div class="gtranslate_wrapper"></div>
        </div>

        <script>
          window.gtranslateSettings = {
            "default_language": "es",
            "languages": ["es", "en", "sw", "xh"],
            "wrapper_selector": ".gtranslate_wrapper",
            "switcher_horizontal_position": "inline",
          };
        </script>
        <script src="https://cdn.gtranslate.net/widgets/latest/float.js" defer></script>
      </header>


      <div class="chat-box">

      </div>
      <img id="file-preview" class="file-preview">
      <form action="#" class="typing-area" enctype="multipart/form-data" method="POST">

        <input type="text" class="incoming_id" name="incoming_id" value="<?php echo $user_id; ?>" hidden>
        <input type="text" name="message" class="input-field" placeholder="Escribe tu mensaje aquí..." autocomplete="off">
        <button class="send-btn"><i class="fab fa-telegram-plane"></i></button>
        <!-- Campo de entrada de archivo -->
        <input type="file" class="attachment" id="attachment" name="attachment" style="display: none;">
        <!-- Botón para seleccionar archivo -->
        <button class="adjuntarBtn active"><i class="fas fa-paperclip"></i></button>
        <!-- Contenedor para mostrar el nombre del archivo seleccionado -->
        <div id="file-info" style="display: none;">
          <span id="file-name"></span>
          <button type="button" class="cancelarBtn active" onclick="cancelarArchivo()"><i class="fas fa-times"></i></button>
        </div>
        <button type="button" class="videollamadaBtn active" style="background-color: red"><i class="fas fa-video"></i></button>
        <input type="submit" value="Enviar" style="display: none;">
      </form>

    </section>
  </div>

  <script src="javascript/chat.js"></script>
</body>

</html>