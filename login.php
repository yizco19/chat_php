<?php
require_once "session.php";
require_once 'auth_config.php';
?>

<?php include_once "header.php"; ?>
<?php
  if(isset($_COOKIE['email']) && isset($_COOKIE['password'])){
    $email = $_COOKIE['email']; 
    $password = $_COOKIE['password'];
  }else{
    $email = "";
    $password = "";
  }
?>
<style>
   /* Estilos adicionales personalizados */
   .google-login {
      margin-top: 20px;
      display: flex;
      justify-content: center;
    }
    .btn-google {
      background-color: #DB4437;
      color: white;
      border: none;
      padding: 10px 20px;
      border-radius: 5px;
      font-size: 16px;
      cursor: pointer;
    }
    .btn-google:hover {
      background-color: #c1351d;
    }
</style>
<body>
  <div class="wrapper">
    <section class="form login">
      <header>Chat</header>
      <form action="#" method="POST" enctype="multipart/form-data" autocomplete="off">
        <div class="error-text"></div>
        <div class="field input">
          <label>Dirección de Correo Electrónico</label>
          <input type="text" name="email" placeholder="Ingresa tu Correo Registrado" required value="<?php echo $email; ?>">
        </div>
        <div class="field input">
          <label>Contraseña</label>
          <input type="password" name="password" placeholder="Ingresa tu Contraseña" required value="<?php echo $password; ?>">
          <i class="fas fa-eye"></i>
        </div>
        <div class="field button">
          <input type="submit" name="submit" value="Acceder">
        </div>
        <input type="checkbox" name="rememberMe" id="rememberMe"> <label for="rememberMe">Recuérdame</label>
      </form>
      <div class="link">Aún no te has registrado? <a href="register.php">Regístrate ahora!</a></div>
      <!--
      <div class="google-login">
        <button class="btn-google"><i class="fab fa-google"></i> <a href="<?php echo $client->createAuthUrl(); ?>" style="color: white;">Iniciar Sesion con Google</a></button>
      </div>
  -->
    </section>

  </div>

<?php include_once "footer.php"; ?>
<script src="javascript/pass-show-hide.js"></script>
  <script src="javascript/login.js"></script>
  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>