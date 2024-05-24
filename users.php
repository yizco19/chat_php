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

  .users .search button.active {
    background: #333;
    color: #fff;
  }

  .search button.active i::before {
    content: '\f00d';
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
    width: 30%;
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

  .assign-btn {
    background: none;
    border: none;
    cursor: pointer;
    margin-left: 10px;
  }

  .assign-img {
    width: 20px;
    height: 20px;
  }

  /* Estilos generales para botones */
  .eliminar-btn,
  .editar-btn {
    border: none;
    border-radius: 5px;
    padding: 10px 20px;
    color: white;
    cursor: pointer;
    font-size: 16px;
    transition: background-color 0.3s, transform 0.3s;
  }

  /* Estilo para botón de eliminar */
  .eliminar-btn {
    background-color: #ff6961;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
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

  

</style>
<?php if (isset($_SESSION['is_super_admin']) && $_SESSION['is_super_admin'] == 1) : ?>
  <style>
      
      .topic-row {
        display: flex;
        align-items: center;
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
    <?php endif;?>
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
          <script>
            const profileImage = document.getElementById('profile-image');
            profileImage.addEventListener('click', function() {
              mostrarOpciones();
            });

            function mostrarOpciones() {
              var viewOpciones = `<div class="swal2-actions">
      <button id="cambiarNombre" onclick="cambiarNombre()" style="border: none; background: none;">
        <img src="resource/cambiar_nombre.png">
      </button>
      <button id="cambiarImagen" onclick="cambiarImagen()" style="border: none; background: none;">
        <img src="resource/cambiar_imagen.png">
      </button>
      <button id="cambiarEmail" onclick="cambiarEmail()" style="border: none; background: none;">
        <img src="resource/mailing.png">
      </button>
      <button id="cambiarColorFondo" onclick="elegirColorFondo()" style="border: none; background: none;">
        <img id="color-picker" src="resource/color-picker.png" style="cursor: pointer; height: 64px; width: 64px;">
      </button>`;
              <?php if (isset($_SESSION['is_super_admin']) && $_SESSION['is_super_admin'] == 1) : ?>
                //agrega una button para gestionar adminstradores
                viewOpciones += `<button id="gestionarTopics" onclick="gestionarTopics()" style="border: none; background: none;">
        <img src="resource/topic.png" style="cursor: pointer; height: 64px; width: 64px;">
      </button>`;
              <?php endif; ?>

              viewOpciones += `</div>`;

              // Mostrar un cuadro de diálogo personalizado con cuatro opciones
              Swal.fire({
                title: 'Seleccione una opción:',
                showCancelButton: true,
                cancelButtonText: 'Cancelar',
                html: viewOpciones
              });
            }
          </script>
          <div class="details">
            <span><?php echo $row['fname'] . " " . $row['lname'] ?></span>
            <p><?php echo $row['status']; ?></p>
          </div>


        </div>
        <?php if (isset($_SESSION['is_super_admin']) && $_SESSION['is_super_admin'] == 1) : ?>
          <div style="display: flex; justify-content: center; align-items: center;">
            <img src="resource/admins.png" alt="admins" id="gestionarAdminstradores" class="admin-img">
            <script src="javascript/admins.js"></script>
            <link rel="stylesheet" type="text/css" href="css/admins.css">
            <a href="php/logout.php?logout_id=<?php echo $row['unique_id']; ?>" class="logout">
              <span class="logout-text">Cerrar Sesión</span>
              <span class="fas fa-sign-out-alt"></span>
            </a>
          </div>
        <?php else : ?>
          <a href="php/logout.php?logout_id=<?php echo $row['unique_id']; ?>" class="logout">
            <span class="logout-text">Cerrar Sesión</span>
            <span class="fas fa-sign-out-alt"></span>
          </a>
        <?php endif; ?>



      </header>

      <div>

        <div class="search">
          <div class="options">
            <i class="fas fa-filter" id="filterIcon" style="margin-right: 20px;"></i>
            <?php if (
              isset($_SESSION['is_super_admin']) && $_SESSION['is_super_admin'] == 0
              && isset($_SESSION['admin']) && $_SESSION['admin'] == 0
            ) : ?>
              <i class="fa-solid fa-address-book" id="agendaIcon"></i>
              <script src="javascript/agenda.js"></script>
              <style>
                .option-img {
                  width: 100px;
                  height: 100px;
                  cursor: pointer;
                  margin: 10px;
                  display: inline-block;
                }

                .option-img:hover {
                  border: 2px solid #007BFF;
                  border-radius: 10px;
                }
              </style>
            <?php endif; ?>

          </div>
          <span class="text">Seleccione un usuario</span>
          <input type="text" placeholder="Buscar por nombre...">
          <button><i class="fas fa-search"></i></button>

        </div>

        <div class="modal" id="myModal">
          <div class="modal-content">
            <span class="close">&times;</span>
            <div style="display: flex; flex-direction: column; align-items: center;">
               
            <div style="margin-bottom: 10px; display: flex; align-items: center;" id="filtroMensaje">
                <label for="filterCheckboxInput" style="margin-right: 10px;">Mostrar solo usuarios con mensajes</label>
                <input type="checkbox" id="filterCheckboxInput">
              </div>
              <?php if (isset($_SESSION['admin']) && $_SESSION['admin'] == 0): ?>
    <script>
        console.log('qweqweqe' + <?php echo $_SESSION['admin']; ?>);
        let filterCheckbox = document.getElementById('filterCheckboxInput');
        filterCheckbox.checked = true;
    </script>
    <style>
      #filtroMensaje{
        visibility: hidden;
      }
    </style>
<?php endif; ?>

              <div class="container mt-5">
                <div class="form-group col">
                  <label for="sortSelect">Ordenar por fecha:</label>
                  <div class="col-sm-10">
                    <select id="sortSelect" class="form-control">
                      <option value="asc">Ascendente</option>
                      <option value="desc">Descendente</option>
                    </select>
                  </div>
                </div>
              </div>
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

  <script src="javascript/users.js"></script>
  <?php include_once "footer.php"; ?>
</body>

</html>