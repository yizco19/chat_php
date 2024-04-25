<?php
require_once "session.php";
?>

<?php include_once "header.php"; ?>

<body>
  <div class="wrapper">
    <section class="form login">
      <header>Chat</header>
      <form action="#" method="POST" enctype="multipart/form-data" autocomplete="off">
        <div class="error-text"></div>
        <div class="field input">
          <label>Dirección de Correo Electrónico</label>
          <input type="text" name="email" placeholder="Ingresa tu Correo Registrado" required>
        </div>
        <div class="field input">
          <label>Contraseña</label>
          <input type="password" name="password" placeholder="Ingresa tu Contraseña" required>
          <i class="fas fa-eye"></i>
        </div>
        <div class="field button">
          <input type="submit" name="submit" value="Chatear">
        </div>
      </form>
      <div class="link">Aún no te has registrado? <a href="register.php">Regístrate acá</a></div>
    </section>
  </div>

<?php include_once "footer.php"; ?>
<script src="javascript/pass-show-hide.js"></script>
  <script src="javascript/login.js"></script>
</body>

</html>