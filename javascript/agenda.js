document.getElementById('add-user-topic').addEventListener('click', function() {
    var style_box_agenda = "display: flex; flex-direction: column; align-items: center; margin: 10px; border: 6px solid gray; border-radius: 20px; padding: 10px;";
    var style_cancel_button = "cursor: pointer; width: 28px; height: 28px;";
    var style_option_img = "cursor: pointer; width: 100px; height: 100px;";
    
    var agendahtml = `
        <div style="display: flex; flex-direction: column; align-items: flex-end;">
            <img src="resource/marca-x.png" alt="Cancel" class="option-img" id="cancelButton" style="${style_cancel_button}">
        </div>
        <div style="display: flex; justify-content: center; align-items: center; flex-wrap: wrap;">
            <div style="${style_box_agenda}">
                <img src="resource/nueva-cuenta.png" alt="User" class="option-img" id="userOption" style="${style_option_img}">
                <span>Usuario</span>
            </div>
            <div style="${style_box_agenda}">
                <img src="resource/red.png" alt="Topics" class="option-img" id="topicsOption" style="${style_option_img}">
                <span>Otro Topic</span>
            </div>
        </div>
    `;
    
    Swal.fire({
        html: agendahtml
       ,
        showConfirmButton: false
    });
    document.getElementById('cancelButton').addEventListener('click', function() {
        Swal.close();
    });
    document.getElementById('userOption').addEventListener('click', function() {
            const filterCheckbox = document.getElementById('filterCheckboxInput'); // Obtener el elemento del checkbox
            filterCheckbox.checked = false; // Desactivar el checkbox
            getUsers();
            Swal.close();
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
                buttonsHtml += '<div class="topic-row" data-id="'+topic.id+'">';
                buttonsHtml += topic.img;
                
                buttonsHtml +='<p style="display: inline-block; width: 180px;">' + topic.name + '</p></div>';
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
    let url = "php/users.php?filterUserNotMessage=" + filterCheckbox.checked + "&action=get_all";
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
                        adminsHtml += '<div class="admin-row" data-id="'+user.unique_id+'"><p style="cursor: pointer;">' + user.lname  + '</p>';
                        adminsHtml += '<img src="' + user.img + '" alt="' + user.lname + '" class="topic-img" style="cursor: pointer; height: 64px; width: 64px;" />';
                        adminsHtml += '</div>';
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
    location.href = 'chat.php?topic_id=' + topicId + '&user_id=' + adminId;
}
