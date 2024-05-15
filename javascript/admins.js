// JavaScript
$(document).ready(function() {
    $('.admin-img').click(function() {
        // Realizar una petición AJAX para obtener los datos de los usuarios
        $.ajax({
            url: 'php/users-all.php',
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                // Construir el contenido HTML de los botones
                var buttonsHtml = '<div class="swal2-content">';
data.forEach(function(user) {
    buttonsHtml += '<div class="user-row"><img src="' + user.img + '" alt="' + user.fname + ' ' + user.lname + '" class="user-img" /> <p style="display: inline-block; width: 180px;">' + user.fname + ' ' + user.lname + ' </p>';
    if (user.admin == 1) {
        buttonsHtml += '<button class="alta-btn" data-id="' + user.unique_id + '" disabled style="background-color: #dddddd;">Activar</button> <button class="baja-btn" data-id="' + user.unique_id + '" style="background-color: #ff6961;">Desactivar</button>';
    } else {
        buttonsHtml += '<button class="alta-btn" data-id="' + user.unique_id + '" style="background-color: #77dd77;">Activar</button> <button class="baja-btn" data-id="' + user.unique_id + '" disabled style="background-color: #dddddd;">Desactivar</button>';
    }
    buttonsHtml += '</div>'; // Esta línea cierra el div de user-row
});
buttonsHtml += '</div>'; // Esta línea cierra el div de swal2-content

                

                // Mostrar la SweetAlert con los botones
                Swal.fire({
                    title: 'Gestionar Administradores',
                    html: buttonsHtml,
                    icon: 'info'
                });

                // Agregar eventos clic a los botones de Alta
                $('.alta-btn').click(function() {
                    var userId = $(this).data('id');
                    activar(userId);
                });

                // Agregar eventos clic a los botones de Baja
                $('.baja-btn').click(function() {
                    var userId = $(this).data('id');
                    
                    desactivar(userId);
                });
            },
            error: function(xhr, status, error) {
                // Manejar errores de la petición AJAX
                console.error(xhr.responseText);
                Swal.fire({
                    title: 'Error',
                    text: 'Hubo un error al cargar los datos de los usuarios.',
                    icon: 'error',
                    confirmButtonText: 'Cerrar'
                });
            }
        });
    });
});

// Función para dar de alta a un usuario
function activar(userId) {
    $.post('php/activate-admin.php', { userId: userId }, function(response) {
        // Aquí puedes manejar la respuesta del servidor después de dar de alta al usuario
        console.log(response);
        // Por ejemplo, mostrar un mensaje de éxito
        Swal.fire('Usuario dado de alta exitosamente');
    }).fail(function(xhr, status, error) {
        // Manejar errores de la solicitud
        console.error(xhr.responseText);
        Swal.fire({
            title: 'Error',
            text: 'Hubo un error al dar de alta al usuario.',
            icon: 'error',
            confirmButtonText: 'Cerrar'
        });
    });
}

// Función para dar de baja a un usuario
function desactivar(userId) {
    $.post('php/desactivate-admin.php', { userId: userId }, function(response) {
        // Aquí puedes manejar la respuesta del servidor después de dar de baja al usuario
        console.log(response);
        // Por ejemplo, mostrar un mensaje de éxito
        Swal.fire('Usuario dado de baja exitosamente');
    }).fail(function(xhr, status, error) {
        // Manejar errores de la solicitud
        console.error(xhr.responseText);
        Swal.fire({
            title: 'Error',
            text: 'Hubo un error al dar de baja al usuario.',
            icon: 'error',
            confirmButtonText: 'Cerrar'
        });
    });
}
