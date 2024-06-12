// users.js
export const searchBar = document.querySelector("#searchInput");
export const searchIcon = document.querySelector("#search-button");
export const usersList = document.querySelector(".users-list");


  searchIcon.onclick = ()=>{
    searchBar.classList.toggle("show");
    searchIcon.classList.toggle("active");
    searchBar.focus();
    if(searchBar.classList.contains("active")){
      searchBar.value = "";
      searchBar.classList.remove("active");
    }
  }


searchBar.onkeyup = ()=>{
  realizarBusqueda();
}


function   realizarBusqueda(sortDirection, hideUserNotMessage, showUserOnlyTopic,topicId) {

  //comprueba el parametro esta definido
  if (typeof sortDirection === 'undefined') {
    sortDirection = localStorage.getItem('orderByDate') || 'asc';
    console.log(sortDirection);
  }
  if (typeof hideUserNotMessage === 'undefined') {
    hideUserNotMessage = localStorage.getItem('hideUserNotMessage') || 'false';
  }
  if (typeof showUserOnlyTopic === 'undefined') {
    showUserOnlyTopic = localStorage.getItem('showUserOnlyTopic') || 'false';
  }
  if (typeof topicId === 'undefined') {
    topicId =  '0';
  }
  const searchTerm = searchBar.value;
  searchBar.classList.toggle("active", searchTerm !== "");
  const xhr = new XMLHttpRequest();
  xhr.open("POST", "php/search.php", true);
  xhr.onload = () => {
    if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
      usersList.innerHTML = xhr.response;
    }
  };

  xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  xhr.send(`searchTerm=${searchTerm}&filterUserNotMessage=${hideUserNotMessage}&sortDirection=${sortDirection}&showUserOnlyTopic=${showUserOnlyTopic}&topic_id=${topicId}`);
}


/*setInterval(() =>{
  let xhr = new XMLHttpRequest();
  xhr.open("GET", "php/users.php", true);
  xhr.onload = ()=>{
    if(xhr.readyState === XMLHttpRequest.DONE){
        if(xhr.status === 200){
          let data = xhr.response;
          if(!searchBar.classList.contains("active")){
            usersList.innerHTML = data;
          }
        }
    }
  }
  xhr.send();
}, 1000);*/



realizarBusqueda();




// Función para mostrar el cuadro de diálogo de SweetAlert con el correo electrónico actual
function mostrarDialogoEmail(emailActual) {
  // Mostrar un cuadro de diálogo de SweetAlert con un campo de entrada
  Swal.fire({
    title: `${emailActual}`,
    text: '¿Desea cambiar su dirección de correo electrónico?',
    input: 'email',
    inputPlaceholder: 'Nuevo correo electrónico',
    inputAttributes: {
      autocapitalize: 'off'
    },
    showCancelButton: true,
    confirmButtonText: 'Confirmar',
    cancelButtonText: 'Cancelar',
    showLoaderOnConfirm: true,
    preConfirm: (email) => {
      // Puedes hacer validación adicional del email aquí si es necesario
      return email;
    }
  }).then((result) => {
    if (result.isConfirmed) {
      // El usuario confirmó y proporcionó un email
      const nuevoEmail = result.value;
      cambiarEmail(nuevoEmail);
      location.reload();
      Swal.fire({
        title: '¡Gracias!',
        text: `Tu dirección de correo electrónico ha sido cambiada a: ${nuevoEmail}`,
        icon: 'success'
      });
    }
  });
}



// Función para cambiar el correo electrónico mediante una solicitud XMLHttpRequest a PHP



function elegirColorFondo() {
  Swal.fire({
      title: 'Cambiando Color de Fondo',
      html: `
          <input type="color" id="nuevoColor" class="swal2-input" value="#ffffff">
      `,
      showCancelButton: true,
      confirmButtonText: 'Guardar',
      cancelButtonText: 'Cancelar',
      preConfirm: () => {
          const nuevoColor = document.getElementById('nuevoColor').value;
          return { nuevoColor };
      }
  }).then((result) => {
      if (result.isConfirmed) {
          const nuevoColor = result.value.nuevoColor;
          //almacena en local storage
          localStorage.setItem('nuevoColor', nuevoColor);
          cambiarColorFondo(nuevoColor);
      }
  });
}
//
if (localStorage.getItem('nuevoColor')) {
  cambiarColorFondo(localStorage.getItem('nuevoColor'));
}

function editarColor(color) {
  localStorage.setItem('nuevoColor', color);
  // Cambia el color de fondo del body
  document.body.style.backgroundColor = color;

}



function cambiarNombre() {
  // Obtener los detalles del usuario
  getDetailUser()
    .then(userData => {
      // Obtener el nombre y apellido actual del usuario
      const nombreActual = userData.fname;
      const apellidoActual = userData.lname;

      // Mostrar un cuadro de diálogo de SweetAlert para cambiar el nombre y apellido
      Swal.fire({
        title: 'Cambiando Nombre y Apellido',
        html: `
          <input id="nuevoNombre" class="swal2-input" placeholder="Nuevo nombre" value="${nombreActual}">
          <input id="nuevoApellido" class="swal2-input" placeholder="Nuevo apellido" value="${apellidoActual}">
        `,
        showCancelButton: true,
        confirmButtonText: 'Guardar',
        cancelButtonText: 'Cancelar',
        preConfirm: () => {
          const nuevoNombre = document.getElementById('nuevoNombre').value;
          const nuevoApellido = document.getElementById('nuevoApellido').value;
          // Aquí puedes agregar lógica para validar los nuevos datos si es necesario
          return { nuevoNombre, nuevoApellido };
        }
      }).then((result) => {
        if (result.isConfirmed) {
          // El usuario confirmó y proporcionó nuevos datos de nombre y apellido
          const nuevoNombre = result.value.nuevoNombre;
          const nuevoApellido = result.value.nuevoApellido;
          // Aquí puedes enviar una solicitud para cambiar el nombre y apellido utilizando `nuevoNombre` y `nuevoApellido`
          editarNombre(nuevoNombre, nuevoApellido);
          Swal.fire('¡Nombre y apellido actualizados!', `Has cambiado tu nombre y apellido.`, 'success');
          location.reload();
        }
        else if(result.isCancelled){
          mostrarOpciones();
        }
      });
    })
    .catch(error => {
      // Manejar errores si ocurre algún problema al obtener los detalles del usuario
      console.error(error);
    });
}
import { createCancelButtonHtml } from "./script.js";

      // Función para cambiar la imagen de perfil utilizando SweetAlert.
      function cambiarImagen() {
        const profileImage = document.getElementById('profile-image');
        var imageUrl = profileImage.src;
        var imageHtml = createCancelButtonHtml();
        
        imageHtml += "<div class='swal2-title'>Cambiar avatar</div>";
        imageHtml += `
            <img id="swal-profile-image" src="${imageUrl}" alt="Imagen de Perfil" style="max-width: 100%; max-height: 300px;">
            <div class="file-upload-wrapper" style="margin-top: 40px;">
                <button type="button" id="guardarImagen" class="btn btn-primary" >Guardar</button>
                <button type="button" class="btn-upload">Subir archivo</button>
                <input type="file" id="profile-image-input" accept="image/*" style="display: none;"/>
            </div>
           
        `;
    
        Swal.fire({
            html: imageHtml,
            showConfirmButton: false,
            didOpen: () => {
                // Activar el input de archivo cuando se hace clic en el botón de subida personalizado dentro de SweetAlert.
                document.querySelector('.swal2-popup .btn-upload').addEventListener('click', function() {
                    document.getElementById('profile-image-input').click();
                });
    
                // Mostrar vista previa de la imagen seleccionada dentro de SweetAlert.
                document.getElementById('profile-image-input').addEventListener('change', function(event) {
                    var file = event.target.files[0];
                    var reader = new FileReader();
    
                    reader.onload = (e) => {
                        document.getElementById('swal-profile-image').src = e.target.result;
                    };
    
                    reader.readAsDataURL(file);
                });
    
                // Manejar el evento del botón de cancelar.
                document.getElementById('cancelButton').addEventListener('click', function() {
                    Swal.close();
                });
    
                // Manejar el evento del botón de guardar.
                document.getElementById('guardarImagen').addEventListener('click', function() {
                    const file = document.getElementById('profile-image-input').files[0];
                    if (file) {
                        editarImagen(file);
                        Swal.fire('¡Imagen de perfil actualizada!', 'Has seleccionado una nueva imagen.', 'success').then(() => {
                            // Actualizar la imagen de perfil sin recargar toda la página.
                            profileImage.src = URL.createObjectURL(file);
                        });
                    }
                });
            },
            preConfirm: () => {
                // Obtener el archivo subido para procesarlo.
                return document.getElementById('profile-image-input').files[0];
            }
        });
    }
    
function updateFileName(inputElement) {
  inputElement.setCustomValidity('');
  if (!inputElement.value) {
    inputElement.setCustomValidity(' ');
  }
}
function cambiarEmail() {
  // Obtener los detalles del usuario
  getDetailUser()
    .then(userData => {
      // Obtener el correo electrónico actual del usuario
      const emailActual = userData.email;

      // Mostrar un cuadro de diálogo de SweetAlert para cambiar el correo electrónico
      Swal.fire({
        title: 'Cambiando Correo',
        html: `
          <label for="nuevoEmail">Correo actual: ${emailActual}</label>
          <input id="nuevoEmail" class="swal2-input" placeholder="${emailActual}">
        `,
        showCancelButton: true,
        confirmButtonText: 'Guardar',
        cancelButtonText: 'Cancelar',
        preConfirm: () => {
          const nuevoEmail = document.getElementById('nuevoEmail').value;
          // Aquí puedes agregar lógica para validar los nuevos datos si es necesario
          return { nuevoEmail };
        }
      }).then((result) => {
        if (result.isConfirmed) {
          // El usuario confirmó y proporcionó nuevos datos de correo electrónico
          const nuevoEmail = result.value.nuevoEmail;
          // Llamar a la función para editar el correo electrónico
          editarEmail(nuevoEmail)
            .then(() => {
              // Mostrar mensaje de éxito
              Swal.fire('¡Correo actualizado!', `Has cambiado tu correo electrónico a: ${nuevoEmail}`, 'success');
            })
            .catch(error => {
              // Manejar errores al editar el correo electrónico
              console.error('Error al cambiar el correo electrónico:', error);
              Swal.fire('¡Error!', 'No se pudo cambiar el correo electrónico.', 'error');
            });
        }
      });
    })
    .catch(error => {
      // Manejar errores si ocurre un problema al obtener los detalles del usuario
      console.error('Error al obtener los detalles del usuario:', error);
      Swal.fire('¡Error!', 'No se pudo obtener los detalles del usuario.', 'error');
    });
}


function editarEmail(nuevoEmail) {
  // Crear una instancia de XMLHttpRequest
  let xhr = new XMLHttpRequest();

  // Configurar la solicitud
  xhr.open('POST', 'php/cambiar-email.php', true);
  xhr.setRequestHeader('Content-Type', 'application/json');

  // Definir el manejador de eventos para la carga
  xhr.onload = function() {
    if (xhr.status >= 200 && xhr.status < 300) {
      // Procesar la respuesta del servidor PHP si es necesario
      console.log('Correo electrónico cambiado con éxito:', xhr.responseText);
    } else {
      console.error('Error al cambiar el correo electrónico:', xhr.statusText);
    }
  };

  // Definir el manejador de eventos para los errores de red
  xhr.onerror = function() {
    console.error('Error de red al cambiar el correo electrónico.');
  };

  // Enviar la solicitud con los datos JSON en el cuerpo
  xhr.send(JSON.stringify({ nuevo_email: nuevoEmail }));
}

function getDetailUser() {
  return new Promise((resolve, reject) => {
    var xhr = new XMLHttpRequest();

    // Configurar la solicitud
    xhr.open('GET', 'php/detail-user.php', true);

    // Definir el manejador de eventos para la carga
    xhr.onload = function() {
      if (xhr.status >= 200 && xhr.status < 300) {
        // Procesar la respuesta del servidor PHP
        const userData = JSON.parse(xhr.responseText);
        resolve(userData); // Resuelve la promesa con los datos del usuario
      } else {
        reject('Error al obtener los detalles del usuario: ' + xhr.statusText);
      }
    };    

    // Definir el manejador de eventos para los errores de red
    xhr.onerror = function() {
      reject('Error de red al obtener los detalles del usuario.');
    };

    // Enviar la solicitud
    xhr.send();
  });
}

function editarNombre(nuevoNombre, nuevoApellido) {
  console.log(nuevoNombre, nuevoApellido);
  // Crear una instancia de XMLHttpRequest
  let xhr = new XMLHttpRequest();

  // Configurar la solicitud
  xhr.open('POST', 'php/editar-nombre.php?nuevoNombre='+nuevoNombre+'&nuevoApellido='+nuevoApellido, true);
  xhr.setRequestHeader('Content-Type', 'application/json');

  // Definir el manejador de eventos para la carga
  xhr.onload = function() {
    if (xhr.status >= 200 && xhr.status < 300) {
      // Procesar la respuesta del servidor PHP si es necesario
      console.log('Nombre actualizado con válido:', xhr.responseText);
    } else {
      console.error('Error al cambiar el nombre:', xhr.statusText);
    }
  };

  // Definir el manejador de eventos para los errores de red
  xhr.onerror = function() {
    console.error('Error de red al cambiar el nombre.');
  };

  // Enviar la solicitud con los datos JSON en el cuerpo
  xhr.send();
}
function editarImagen(nuevaImagen) {
  // Crear una instancia de XMLHttpRequest
  let xhr = new XMLHttpRequest();

  // Configurar la solicitud
  xhr.open('POST', 'php/editar-imagen.php', true);

  // Definir el manejador de eventos para la carga
  xhr.onload = function() {
      if (xhr.status >= 200 && xhr.status < 300) {
          // Procesar la respuesta del servidor PHP si es necesario
          console.log('Imagen actualizada con éxito:', xhr.responseText);
      } else {
          console.error('Error al cambiar la imagen:', xhr.statusText);
      }
  };

  // Definir el manejador de eventos para los errores de red
  xhr.onerror = function() {
      console.error('Error de red al cambiar la imagen.');
  };

  // Crear un objeto FormData y adjuntar la nueva imagen
  let formData = new FormData();
  formData.append('nueva_imagen', nuevaImagen);

  // Enviar la solicitud con los datos de FormData en el cuerpo
  xhr.send(formData);
}

$(document).ready(function() {
  // Manejar el evento de clic simple
  $(document).on('click', '.topic', function(event) {
    var topicId = $(this).data('id'); // Obtener el data-id
    realizarBusqueda(null, null, null, topicId);
  });

  // Manejar el evento de doble clic
  $(document).on('dblclick', '.topic', function(event) {
    realizarBusqueda();
  });
});

function generarSwitch(id, label, checked) {
  //comprueba si en localStorage hay un valor para el switch
  if(checked == undefined || checked == null){
    var checked = localStorage.getItem(id) === "true" ? "checked" : "";
  }
  
  return `
    <div style="display: flex; align-items: center;">
      <label class="switch" style="margin-right: 10px;">
        <input type="checkbox" ${checked} id="${id}" >
        <span class="slider round"></span>
      </label>
      <label for="${id}">${label}</label>
    </div>`;
}



document.getElementById('settings').addEventListener('click', function() {
  getDetailUser().then((userData) => {
      showOptions(userData);
  }).catch((error) => {
    Swal.fire({
      icon: 'error',
      title: 'Error',
      text: error
    }).then(() => {
      window.location.reload();
      console.error(error);
    })
  })

});
function showOptions(userData) {
  var fnameActual = userData.fname;
  var lnameActual = userData.lname;
  var emailActual = userData.email;
  var style_box_agenda = "display: flex; flex-direction: column; align-items: center; margin: 10px; border: 6px solid gray; border-radius: 20px; padding: 10px;";
  if(localStorage.getItem('nuevoColor')) {
    var color = localStorage.getItem('nuevoColor');
    var checkedColor = "checked";
  }else {
    var checkedColor = "";
  }
  if(localStorage.getItem('orderByDate')){
    var currentValue = localStorage.getItem('orderByDate');
    var checkedOrderByDate = "checked";
  }else {
    var checkedOrderByDate = "";
    var currentValue = "asc";
  }
// Estilo CSS para los inputs
var text_input_style = 'style="max-width: 150px; padding: 0 .75em; border: 1px solid #ccc; border-radius: 4px; margin-right: 10px;" ';

  // Mostrar la ventana de configuración
  const settingshtml = `
  ${createCancelButtonHtml()}
    ${generarSwitch('nameSwitch', 'Nombre y apellidos','checked')}
    <div style="display: flex; justify-content: center;">
      <input type="text"  id="firstName" value="${fnameActual}" ${text_input_style} >
      <input type="text"  id="lastName" value="${lnameActual}"  ${text_input_style}>
    </div>
    ${generarSwitch('emailSwitch', 'Correo actual','checked')}
    <div>
      <input type="text" id="email" class="swal2-input" value="${emailActual}" style="    width: auto; height:auto; ">
    </div>
    ${generarSwitch('colorSwitch', 'Color',checkedColor)}
    <div>
      <input type="color" id="color" placeholder="Color" value="${color}">
    </div>
    ${generarOrderByDateSelectConValor(currentValue,checkedOrderByDate)}
    ${generarSwitch('hideUserNotMessage', 'Ocultar usuarios sin mensajes')}
    ${generarSwitch('showUserOnlyTopic', 'Mostrar solo usuarios con tema')}
  `;

  Swal.fire({
    html: settingshtml,
    showConfirmButton: true,
    confirmButtonText: 'Guardar Configuración', // Cambia el texto del botón de confirmación
    confirmButtonColor: '#4caf50', // Cambia el color del botón de confirmación
  }).then((result) => {
    if (result.isConfirmed) {
      // Implementa la lógica para guardar la configuración

      const nameSwitch = document.getElementById('nameSwitch');
      const emailSwitch = document.getElementById('emailSwitch');
      const colorSwitch = document.getElementById('colorSwitch');
      const orderByDateSwitch = document.getElementById('orderByDateSwitch');
      const hideUserNotMessage = document.getElementById('hideUserNotMessage');
      const showUserOnlyTopic = document.getElementById('showUserOnlyTopic');
      const sortSelect = document.getElementById('sortSelect');

      if (nameSwitch.checked) {
        const firstName = document.getElementById('firstName').value;
        const lastName = document.getElementById('lastName').value;
        //comprueba si el nombre es  igual al nombre actual
        if (firstName !== fnameActual || lastName !== lnameActual) {
          editarNombre(firstName, lastName);
        }

      }

      if (emailSwitch.checked) {
        const email = document.getElementById('email').value;
        //comprueba si el email es valido y igual al email actual
        if (validarEmail(email) && email !== emailActual) {
          editarEmail(email);
        }

      }

      if (colorSwitch.checked) {
        const color = document.getElementById('color').value;
        editarColor(color);
      }
      
      if (orderByDateSwitch.checked) {
        const sortDirection = document.getElementById('sortSelect').value;
        localStorage.setItem('orderByDate', sortDirection);
      }else{
        const sortDirection ='asc';
      }
      
      localStorage.setItem('hideUserNotMessage', hideUserNotMessage.checked);
      localStorage.setItem('showUserOnlyTopic', showUserOnlyTopic.checked);

      realizarBusqueda(sortSelect.value, hideUserNotMessage.checked, showUserOnlyTopic.checked);
      //reload
      location.reload();
      // Cerrar la ventana
      Swal.close();
    }
  });

  document.getElementById('cancelButton').addEventListener('click', function() {
    Swal.close();
  });
}
function validarEmail(email) {
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  return emailRegex.test(email);
}
function generarOrderByDateSelectConValor(currentValue,checkedOrderByDate) {
  console.log(currentValue,checkedOrderByDate);
  return `
  ${generarSwitch('orderByDateSwitch', 'Ordenar por fecha',checkedOrderByDate)}
  <div>
    <div class="col-sm-10">
    <select id="sortSelect" class="form-control">
    <option value="asc" ${currentValue === 'asc' ? 'selected' : ''}>Ascendente</option>
    <option value="desc" ${currentValue === 'desc' ? 'selected' : ''}>Descendente</option>
</select>
    </div>
  </div>
  `;
}
export const profileImage = document.getElementById('profile-image');
profileImage.addEventListener('click', function() {
  cambiarImagen();
});