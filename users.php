<?php require_once "session.php"; ?>
<?php include_once "header.php"; ?>
<style>
  .users .search {
    margin: 20px 0;
    display: flex;
    position: relative;
    align-items: center;
    justify-content: space-between;
  }

  .users .search .text {
    margin-right: 0px;
    font-size: 18px;
  }

  .users .search input {
    margin-left: 0px;
    position: absolute;
    height: 42px;
    width: calc(100% - 50px);
    font-size: 16px;
    padding: 0 13px;
    border: 1px solid #e6e6e6;
    outline: none;
    border-radius: 5px 5px 5px 5px;
    opacity: 0;
    pointer-events: none;
    transition: all 0.2s ease;
  }

  .users .search input.show {
    opacity: 1;
    pointer-events: auto;
  }

  .users .search button {
    position: relative;
    z-index: 1;
    width: 47px;
    height: 42px;
    font-size: 17px;
    cursor: pointer;
    border: none;
    background: #fff;
    color: #333;
    outline: none;
    border-radius: 0 5px 5px 0;
    transition: all 0.2s ease;
  }


  .filter-checkbox {
    display: none;
  }

  .switch {
    position: relative;
    display: inline-block;
    width: 34px;
    height: 20px;
  }

  .switch input {
    opacity: 0;
    width: 0;
    height: 0;
  }

  .slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #ccc;
    transition: .4s;
  }

  .slider:before {
    position: absolute;
    content: "";
    height: 14px;
    width: 14px;
    left: 3px;
    bottom: 3px;
    background-color: white;
    transition: .4s;
  }

  input:checked+.slider {
    background-color: #77dd77;
  }

  input:focus+.slider {
    box-shadow: 0 0 1px #77dd77;
  }

  input:checked+.slider:before {
    transform: translateX(14px);
  }

  .slider.round {
    border-radius: 34px;
  }

  .slider.round:before {
    border-radius: 50%;
  }

  /* Estilos del modal */
  .modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    /* Fondo semitransparente */
  }

  /* Contenido del modal */
  .modal-content {
    background-color: #fefefe;
    margin: 20% auto;
    padding: 20px;
    border: 1px solid #ccc;
    border-radius: 5px;
    width: 390px;
    /* Ancho del modal */
  }

  /* Botón para cerrar el modal */
  .close {
    color: #aaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
  }

  .close:hover,
  .close:focus {
    color: black;
    text-decoration: none;
    cursor: pointer;
  }


  .assign-img {
    width: 20px;
    height: 20px;
  }

  /* Estilos generales para botones */
  .eliminar-btn,
  .editar-btn,
  .assign-btn {
    margin-right: 1px;
    border: none;
    border-radius: 5px;
    padding: 4px 4px;
    color: white;
    cursor: pointer;
    font-size: 15px;
    transition: background-color 0.3s, transform 0.3s;
  }

  /* Estilo para botón de eliminar */
  .eliminar-btn {
    background-color: #ff6961;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  }
  .assign-btn {
    background-color: #77dd77;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  }
  .assign-btn:hover {
    background-color: #6cd66c;
    transform: translateY(-2px);
  }
  .eliminar-btn:hover {
    background-color: #ff5c53;
    transform: translateY(-2px);
  }

  .eliminar-btn:active {
    background-color: #e15550;
    transform: translateY(0);
  }

  /* Estilo para botón de editar */
  .editar-btn {
    background-color: #77dd77;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  }

  .editar-btn:hover {
    background-color: #6cd66c;
    transform: translateY(-2px);
  }

  .editar-btn:active {
    background-color: #66cc66;
    transform: translateY(0);
  }

  /* Estilo adicional para todos los botones en su estado deshabilitado */
  button:disabled {
    background-color: #dddddd;
    cursor: not-allowed;
  }

  .user-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 10px;
  }

  .user-img {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    margin-right: 10px;
  }

  .user-name {
    width: 90px;
    flex: 1;
  }

  .status-text {
    display: inline-block;
    width: 50px;
    text-align: center;
    font-size: 14px;
  }

  .topics-list {
    overflow: hidden;
    /* Ocultar las barras de desplazamiento */
    overflow-x: scroll;
    display: flex;
    flex-direction: row;
    align-items: center;
    justify-content: space-around;
    margin-bottom: 20px;
    cursor: grab;
    /* Cambiar el cursor para indicar que se puede agarrar */
  }

  /* Cambiar el cursor cuando se está desplazando */
  .topics-list:active {
    cursor: grabbing;
  }

  .topic {
    padding: 10px 20px;
    border-radius: 20px;
    margin-right: 10px;

    cursor: pointer;
    transition: background-color 0.3s, transform 0.3s;
  }

  .topic:hover {
    background-color: #ddd;
    transform: translateY(-2px);
  }

  .topic:active {
    background-color: #ccc;
    transform: translateY(0);
  }

  .opcion-img {
    width: 35px;
    height: auto;
    border-radius: 0%;
  }

  header .search-button.active {
    background: #333;
    color: #fff;
  }

  .search button.active i::before {
    content: '\f00d';
  }

  header #search-button {
    position: relative;
    z-index: 1;
    width: 47px;
    height: 42px;
    font-size: 17px;
    cursor: pointer;
    border: none;
    background: #fff;
    color: #333;
    outline: none;
    border-radius: 0 5px 5px 0;
    transition: all 0.2s ease;
  }

  #search-button.active img {
    content: url('resource/marca-x.png');
    display: inline-block;
    /* Asegúrate de que el pseudo-elemento se muestre correctamente */

    /* Otros estilos para posicionar la imagen */
  }


  #searchInput {
    margin-left: 0px;
    position: absolute;
    height: 42px;

    font-size: 16px;
    padding: 0 13px;
    border: 1px solid #e6e6e6;
    outline: none;
    border-radius: 5px 5px 5px 5px;
    opacity: 0;
    pointer-events: none;
    transition: all 0.2s ease;
  }

  #searchInput.show {
    width: 100%;
    position: relative;
    opacity: 1;
    pointer-events: auto;
    z-index: 999;
  }

  .style-options-btn {
    border: none;
    background-color: transparent;

  }
  #profile-image{
    width: 60px;
  }
</style>
<?php if (isset($_SESSION['is_super_admin']) && $_SESSION['is_super_admin'] == 1) : ?>
  <style>
    .topic-row {
      display: flex;
      align-items: center;
      overflow: hidden;
      margin-bottom: 10px;
    }

    .topic-img {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      margin-right: 10px;
    }

    .topic-name {
      flex: 1;
    }
  </style>
<?php endif; ?>

<body>
  <div class="wrapper">
    <section class="users">
      <header>
        <div class="content">
          <?php

          $sql = mysqli_query($conn, "SELECT * FROM users WHERE unique_id = {$_SESSION['unique_id']}");
          if (mysqli_num_rows($sql) > 0) {
            $row = mysqli_fetch_assoc($sql);
          }
          ?>
          <img src="<?php echo $row['img']; ?>" alt="" id="profile-image">

          <div class="details">
            <span><?php echo $row['fname'] . " " . $row['lname'] ?></span>

            <a href="php/logout.php?logout_id=<?php echo $row['unique_id']; ?>" class="logout">
              <span class="logout-text" style="font-size: 15px;">Cerrar Sesión</span>
              <i class="fas fa-sign-out-alt"></i>
            </a>
          </div>


        </div>
       


        <div>
          <?php if (
            isset($_SESSION['is_super_admin']) && $_SESSION['is_super_admin'] == 0
            &&  isset($_SESSION['admin']) && $_SESSION['admin'] == 0
          ) : ?>

            <button id="add-user-topic" class="style-options-btn"><img src="resource/anadir.png" alt="logout" id="logout" class="opcion-img"style="border-radius:0%;"> </button>
            <script src="javascript/agenda.js"></script>
          <?php endif; ?>


          <button id="search-button"> <img src="resource/lupa (2).png" alt="logout" id="logout" class="opcion-img" style="border-radius:0%;"></button>

          <?php if (isset($_SESSION['is_super_admin']) && $_SESSION['is_super_admin'] == 1) : ?>
          <button class="style-options-btn">
            <img src="resource/red.png" alt="admins" id="gestionarTopics"   class="opcion-img" style="border-radius:0%;">
            <script src="javascript/admins.js" type="module"></script>
            <link rel="stylesheet" type="text/css" href="css/admins.css">

            </button>

        <?php endif; ?>
        <button id="settings" class="style-options-btn">
            <img src="resource/ajuste.png" alt="logout" class="opcion-img" style="border-radius:0%;">
          </button>
        </div>

      </header>
      <input type="text" placeholder="Buscar por nombre..." id="searchInput">
      <div class="topics-list">

        <!-- Agrega más temas según sea necesario -->
      </div>

      <script src="javascript/topics.js"></script>

      <div>


        <div class="modal" id="myModal">
          <div class="modal-content">
            <span class="close">&times;</span>
            <div style="display: flex; flex-direction: column; align-items: center;">

              <div style="margin-bottom: 10px; display: flex; align-items: center;" id="filtroMensaje">
                <label for="filterCheckboxInput" style="margin-right: 10px;">Mostrar solo usuarios con mensajes</label>
                <input type="checkbox" id="filterCheckboxInput">
              </div>
              <?php if (isset($_SESSION['admin']) && $_SESSION['admin'] == 0) : ?>
                <script>
                  console.log('qweqweqe' + <?php echo $_SESSION['admin']; ?>);
                  let filterCheckbox = document.getElementById('filterCheckboxInput');
                  filterCheckbox.checked = true;
                </script>
                <style>
                  #filtroMensaje {
                    visibility: hidden;
                  }
                </style>
              <?php endif; ?>

            
            </div>
          </div>
        </div>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/switchery/0.8.2/switchery.min.js"></script>
        <link href="https://cdnjs.cloudflare.com/ajax/libs/switchery/0.8.2/switchery.min.css" rel="stylesheet" />
        <script>
          var elem = document.querySelector('#filterCheckboxInput');
          var switchery = new Switchery(elem);
        </script>


      </div>
      <div class="users-list">

      </div>
    </section>
  </div>

  <script  src="javascript/users.js"  type="module" ></script>
  <?php include_once "footer.php"; ?>
</body>

</html>