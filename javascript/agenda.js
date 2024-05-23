document.getElementById('agendaIcon').addEventListener('click', function() {
    Swal.fire({
        title: 'Seleccione una opción',
        html: `
            <div>
                <img src="resource/user.png" alt="User" class="option-img" id="userOption" style="cursor: pointer; width: 100px; height: 100px; margin: 10px;">
                <img src="resource/topic.png" alt="Topics" class="option-img" id="topicsOption" style="cursor: pointer; width: 100px; height: 100px; margin: 10px;">
            </div>
        `,
        showConfirmButton: false
    });

    document.getElementById('userOption').addEventListener('click', function() {
        Swal.fire({
            title: 'Seleccione un administrador',
            text: 'Mostrando lista de administradores...',
            // Aquí agregarías el código para obtener y mostrar la lista de administradores
        }).then(result => {
            const filterCheckbox = document.getElementById('filterCheckboxInput'); // Obtener el elemento del checkbox
            filterCheckbox.checked = false; // Desactivar el checkbox
            getUsers();
        });
    });

    document.getElementById('topicsOption').addEventListener('click', function() {
        Swal.fire({
            title: 'Seleccione un topic',
            html: '<div class="swal2-content"></div>',
            showConfirmButton: false,
            didOpen: () => {
                gestionarTopics();
            }
        });
    });
});

function gestionarTopics() {
    // Realizar una petición AJAX para obtener los datos de los topics
    $.ajax({
        url: 'php/topics.php?action=get_all',
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            // Construir el contenido HTML de los botones

            var buttonsHtml = '<div class="swal2-content">';
            data.forEach(function(topic) {
                // Muestra una imagen y nombre de cada topic
                buttonsHtml += '<div class="topic-row" data-id="'+topic.id+'"><img src="' + topic.img + '" alt="' + topic.name + '" class="topic-img" style="cursor: pointer; height: 64px; width: 64px;" /> <p style="display: inline-block; width: 180px;">' + topic.name + '</p></div>';
            });
            buttonsHtml += '</div>';

            // Reemplaza el contenido actual con los topics
            Swal.update({
                html: buttonsHtml
            });

            // Agregar eventos click a los elementos topic-row
            $('.topic-row').click(function() {
                var topicId = $(this).data('id');
                seleccionarAdmin(topicId);
            });
        },
        error: function(xhr, status, error) {
            // Manejar errores de la petición AJAX
            console.error(xhr.responseText);
            Swal.fire({
                title: 'Error',
                text: 'Hubo un error al cargar los datos de los topics.',
                icon: 'error',
                confirmButtonText: 'Cerrar'
            });
        }
    });
}
function getUsers() {
    let xhr = new XMLHttpRequest();
    let filterCheckbox = document.getElementById('filterCheckboxInput');
    let url = "php/users.php?filterUserNotMessage=" + filterCheckbox.checked;
    xhr.open("GET", url, true);
    xhr.onload = () => {
      if (xhr.readyState === XMLHttpRequest.DONE) {
        if (xhr.status === 200) {
          let data = xhr.response;
          if (!searchBar.classList.contains("active")) {
            usersList.innerHTML = data;
          }
        }
      }
    };
    xhr.send();
  }
function seleccionarAdmin(topicId) {
    Swal.fire({
        title: 'Seleccione un administrador',
        text: 'Mostrando lista de administradores...',
        didOpen: () => {
            // Realizar una petición AJAX para obtener los administradores
            $.ajax({
                url: 'php/user-topics.php?action=get-admin-data&topic_id=' + topicId,
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    console.log(data);
                    var adminsHtml = '<div class="swal2-content">';
                    data.forEach(function(user) {
                        adminsHtml += '<div class="admin-row" data-id="'+user.user_id+'"><p style="cursor: pointer;">' + user.lname  + '</p><img </div>';
                        adminsHtml += '<img src="' + user.img + '" alt="' + user.lname + '" class="topic-img" style="cursor: pointer; height: 64px; width: 64px;" />';
                    });
                    adminsHtml += '</div>';

                    // Reemplaza el contenido actual con los administradores
                    Swal.update({
                        html: adminsHtml
                    });

                    // Agregar eventos click a los elementos admin-row
                    $('.admin-row').click(function() {
                        var adminId = $(this).data('id');
                        iniciarChat(topicId, adminId);
                    });
                },
                error: function(xhr, status, error) {
                    // Manejar errores de la petición AJAX
                    console.error(xhr.responseText);
                    Swal.fire({
                        title: 'Error',
                        text: 'Hubo un error al cargar los datos de los administradores.',
                        icon: 'error',
                        confirmButtonText: 'Cerrar'
                    });
                }
            });
        }
    });
}

function iniciarChat(topicId, adminId) {
    // Aquí puedes agregar el código para iniciar el chat con el topic y el administrador seleccionados
    //console.log('Iniciar chat con Topic ID:', topicId, 'y Admin ID:', adminId);
    //Swal.fire('Chat iniciado', `Chat iniciado con el Topic ID: ${topicId} y Admin ID: ${adminId}`, 'success');
    location.href = 'chat.php?user_id=' + adminId;
}
