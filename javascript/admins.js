$(document).ready(function() {
    $('.admin-img').click(function() {
        // Realizar una petición AJAX para obtener los datos de los usuarios
        $.ajax({
            url: 'php/users-all.php',
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                // Construir el contenido HTML de los usuarios con interruptores y botón de asignar topics
                var usersHtml = '<div class="swal2-content">';
                data.forEach(function(user) {
                    usersHtml += '<div class="user-row">';
                    usersHtml += '<img src="' + user.img + '" alt="' + user.fname + ' ' + user.lname + '" class="user-img" />';
                    usersHtml += '<p class="user-name">' + user.fname + ' ' + user.lname + '</p>';
                    usersHtml += '<p class="user-name">Admin</p>';
                    usersHtml += '<label class="switch">';

                    usersHtml += '<input type="checkbox" class="toggle-switch" data-id="' + user.unique_id + '" ' + (user.admin == 1 ? 'checked' : '') + '>';
                    usersHtml += '<span class="slider round"></span>';
                    usersHtml += '</label>';
                    usersHtml += '<p class="user-name">Topics</p>';
                    usersHtml += '<button class="assign-btn" data-id="' + user.user_id + '" data-name="'+ user.fname + ' ' + user.lname+' "><img src="resource/topic.png" alt="Asignar Topics" class="user-img"></button>';

                    usersHtml += '</div>'; // Esta línea cierra el div de user-row
                });
                usersHtml += '</div>'; // Esta línea cierra el div de swal2-content

                // Mostrar la SweetAlert con los usuarios, el interruptor y el botón de asignar topics
                Swal.fire({
                    title: 'Gestionar Administradores',
                    html: usersHtml,
                    icon: 'info'
                });

                // Agregar eventos change a los interruptores
                $('.toggle-switch').change(function() {
                    var userId = $(this).data('id');
                    if (this.checked) {
                        activar(userId);
                    } else {
                        desactivar(userId);
                    }
                });

                // Agregar eventos clic a los botones de asignar topics
                $('.assign-btn').click(function() {
                    var userId = $(this).data('id');
                    var name = $(this).data('name');
                    asignarTopics(userId,name);
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

    // click a id gestionarTopics
});


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
function asignarTopics(userId,name) {
    gestionarTopicsAdmin(userId,name);
}
function gestionarTopicsAdmin(userId, name) {
    console.log(name);
    // Realizar una petición AJAX para obtener los datos de los topics
    $.ajax({
        url: 'php/user-topics.php?action=get-topics-data&userId=' + userId,
        type: 'GET',
        dataType: 'json',
        success: function (data) {
            // Construir el contenido HTML de los botones
            var buttonsHtml = '<div class="swal2-content">';
            data.forEach(function (topic) {
                // Muestra una imagen y nombre de cada topic y en su derecha para eliminar o editar
                buttonsHtml += '<div class="topic-row"><img src="' + topic.img + '" alt="' + topic.name + '" class="topic-img" style="cursor: pointer; height: 64px; width: 64px;" /> <p style="display: inline-block; width: 180px;">' + topic.name + '</p>';
                // Toggle switch para cada topic
                buttonsHtml += '<label class="switch">';
                buttonsHtml += '<input type="checkbox" class="topic-toggle" data-id="' + topic.id + '" ' + (topic.user_id !== null ? 'checked' : '') + '>';

                buttonsHtml += '<span class="slider round"></span>';
                buttonsHtml += '</label>';
                buttonsHtml += '</div>'; // Cierra el div de topic-row
            });
            buttonsHtml += '</div>'; // Cierra el div de swal2-content

            // Mostrar la SweetAlert con los botones
            Swal.fire({
                title: 'Gestionar Topics de ' + name,
                html: buttonsHtml,
                icon: 'info',
            });

            // Agregar eventos change a los interruptores
            $('.topic-toggle').change(function() {
                var topicId = $(this).data('id');
                adminTopics(userId, topicId,this.checked);
            });
        },
        error: function (xhr, status, error) {
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
function adminTopics(userId, topicId, checked) {
    $.post('php/user-topics.php?action=admin-topics', { userId:
        userId,
        topicId: topicId,
        checked: checked
    }, function(response) {
        // Aquí puedes manejar la respuesta del servidor después de dar de alta al usuario
        console.log(response);
        // Por ejemplo, mostrar un mensaje de éxito
        Swal.fire('Modificacion exitosamente').then((result) => {
        // volver a cargar los datos de los topics
        gestionarTopicsAdmin(userId);
        })

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
                // Muestra una imagen y nombre de cada topic y en su derecha para eliminar o editar
                buttonsHtml += '<div class="topic-row"><img src="' + topic.img + '" alt="' + topic.name + '" class="topic-img"style="cursor: pointer; height: 64px; width: 64px;" /> <p style="display: inline-block; width: 180px;">' + topic.name + '</p>';
                buttonsHtml += '<button class="eliminar-btn" data-id="' + topic.id + '" style="background-color: #ff6961;">Eliminar</button> <button class="editar-btn" data-id="' + topic.id + '" style="background-color: #77dd77;">Editar</button>';
                buttonsHtml += '</div>'; // Esta línea cierra el div de topic-row
            });
            buttonsHtml += '</div>'; // Esta línea cierra el div de swal2-content

            // Agregar el botón para agregar un nuevo topic
            buttonsHtml += '<button class="agregar-btn" style="background-color: #4CAF50; color: white; padding: 10px 20px; margin-top: 20px;">Agregar Nuevo Topic</button>';

            // Mostrar la SweetAlert con los botones
            Swal.fire({
                title: 'Gestionar Topics',
                html: buttonsHtml,
                icon: 'info'
            });

            // Agregar eventos clic a los botones de Eliminar
            $('.eliminar-btn').click(function() {
                var topicId = $(this).data('id');

                eliminar(topicId);
            });

            // Agregar eventos clic a los botones de Editar
            $('.editar-btn').click(function() {
                var topicId = $(this).data('id');
                editar(topicId);
            });

            // Agregar evento clic al botón de Agregar
            $('.agregar-btn').click(function() {
                agregarTopic();
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
// Función para dar de alta a un usuario
function eliminar(topicId) {
    $.post('php/topics.php?action=delete', { id: topicId }, function(response) {
        // Aquí puedes manejar la respuesta del servidor después de dar de alta al usuario
        console.log(response);
        // Por ejemplo, mostrar un mensaje de éxito
        Swal.fire({
            title: 'Topic eliminado exitosamente',
            icon:'success',
            confirmButtonText: 'Cerrar'
        }).then((result) => {
            if (result.isConfirmed) {
                gestionarTopics();
            }
        });
        

    }).fail(function(xhr, status, error) {
        console.error(xhr.responseText);
        Swal.fire({
            title: 'Error',
            text: 'Hubo un error al eliminar el topic.',
            icon: 'error',
            confirmButtonText: 'Cerrar'
        });
    });
}
// Función para editar un topic
function editar(topicId) {
    $.get('php/topics.php?action=get&id=' + topicId, function(topic) {
        Swal.fire({
            title: 'Editar Topic',
            html: `<input type="text" id="topic-name" class="swal2-input" value="${topic.name}">
                   <input type="file" id="topic-img" class="swal2-input" accept="image/*">`,
            showCancelButton: true, // Agregado el botón de cancelar
            focusConfirm: false,
            preConfirm: () => {
                const name = document.getElementById('topic-name').value;
                const img = document.getElementById('topic-img').files[0];
                return { name: name, img: img };
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const formData = new FormData();
                formData.append('id', topicId);
                formData.append('name', result.value.name);
                formData.append('img', result.value.img);
                $.ajax({
                    url: 'php/topics.php?action=update',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        console.log(response);
                        Swal.fire('Topic actualizado exitosamente').then((result) => {
                            if (result.isConfirmed) {
                                gestionarTopics();
                            }
                        });
                    },
                    error: function(xhr, status, error) {
                        console.error(xhr.responseText);
                        Swal.fire({
                            title: 'Error',
                            text: 'Hubo un error al actualizar el topic.',
                            icon: 'error',
                            confirmButtonText: 'Cerrar'
                        });
                    }
                });
            } else if (result.dismiss === Swal.DismissReason.cancel) { // Manejar la cancelación
                gestionarTopics();
            }
        });
    }, 'json').fail(function(xhr, status, error) {
        console.error(xhr.responseText);
        Swal.fire({
            title: 'Error',
            text: 'Hubo un error al cargar los datos del topic.',
            icon: 'error',
            confirmButtonText: 'Cerrar'
        });
    });
}


// Función para agregar un nuevo topic
function agregarTopic() {
    Swal.fire({
        title: 'Agregar Nuevo Topic',
        html: '<input type="text" id="new-topic-name" class="swal2-input" placeholder="Nombre del Topic">' +
              '<input type="file" id="new-topic-img" class="swal2-input" accept="image/*" placeholder="Imagen del Topic">',
        showCancelButton: true,
        confirmButtonText: 'Agregar',
        cancelButtonText: 'Cancelar',
        showLoaderOnConfirm: true,
        preConfirm: () => {
            const name = $('#new-topic-name').val();
            const img = $('#new-topic-img')[0].files[0];
            if (name==null || name=='') {
                Swal.showValidationMessage('Por favor ingresa el nombre.');
            }
            if (!img) {
                // Mostrar la confirmación dentro de la primera SweetAlert
                return Swal.mixin({
                    customClass: {
                        confirmButton: 'swal2-confirm-custom',
                        cancelButton: 'swal2-cancel-custom'
                    },
                    buttonsStyling: false
                }).fire({
                    title: 'Imagen defecto',
                    html: '<div class="circulo"><span class="letra">'+name.substring(0,1)+'</span></div>',
                    showCancelButton: true,
                    confirmButtonText: 'Sí, continuar',
                    cancelButtonText: 'Cancelar',
                    allowOutsideClick: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        return { name: name, img: null };
                    }else{
                        return { name: null, img: null };
                    }
                });
            }

            // Verificar si se seleccionó una imagen
            return { name: name, img: img };
            
        },
        allowOutsideClick: () => !Swal.isLoading()
    }).then((result) => {
        if (result.isConfirmed) {
            const formData = new FormData();
            formData.append('name', result.value.name);
            formData.append('img', result.value.img);
            $.ajax({
                url: 'php/topics.php?action=insert',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    console.log(response);
                    Swal.fire('Nuevo topic agregado exitosamente');
                    gestionarTopics(); // Recargar la lista de topics después de agregar uno nuevo
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                    Swal.fire({
                        title: 'Error',
                        text: 'Hubo un error al agregar el nuevo topic.',
                        icon: 'error',
                        confirmButtonText: 'Cerrar'
                    });
                }
            });
        }
    });
}
