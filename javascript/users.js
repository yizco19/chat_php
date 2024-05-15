const searchBar = document.querySelector(".search input"),
searchIcon = document.querySelector(".search button"),
usersList = document.querySelector(".users-list");

searchIcon.onclick = ()=>{
  searchBar.classList.toggle("show");
  searchIcon.classList.toggle("active");
  searchBar.focus();
  if(searchBar.classList.contains("active")){
    searchBar.value = "";
    searchBar.classList.remove("active");
  }
}
// Obtener elementos del DOM
const filterIcon = document.getElementById('filterIcon');
const modal = document.getElementById('myModal');
const closeModal = document.getElementsByClassName('close')[0];
const filterCheckbox = document.getElementById('filterCheckboxInput');

// Mostrar el modal al hacer clic en el icono de filtro
filterIcon.addEventListener('click', () => {
  modal.style.display = 'block';
});

// Cerrar el modal al hacer clic en el botón de cerrar
closeModal.addEventListener('click', () => {
  modal.style.display = 'none';
  realizarBusqueda();
});

// Cerrar el modal al hacer clic fuera del contenido del modal
window.addEventListener('click', (event) => {
  if (event.target === modal) {
    modal.style.display = 'none';
    realizarBusqueda();
  }
});


searchBar.onkeyup = ()=>{
  realizarBusqueda();
}


function realizarBusqueda() {
  let searchTerm = searchBar.value;
  let sortDirection = document.getElementById("sortSelect").value; // Obtener la dirección de ordenamiento
  if (searchTerm != "") {
    searchBar.classList.add("active");
  } else {
    searchBar.classList.remove("active");
  }
  let xhr = new XMLHttpRequest();
  xhr.open("POST", "php/search.php", true);
  xhr.onload = () => {
    if (xhr.readyState === XMLHttpRequest.DONE) {
      if (xhr.status === 200) {
        let data = xhr.response;
        usersList.innerHTML = data;
      }
    }
  };
  xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  xhr.send("searchTerm=" + searchTerm + "&filterUserNotMessage=" + filterCheckbox.checked + "&sortDirection=" + sortDirection); // Agregar el parámetro para la dirección de ordenamiento
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



getUsers();

const profileImage = document.getElementById('profile-image');
profileImage.addEventListener('click', function() {
mostrarOpciones();


});


function getUsers() {
  let xhr = new XMLHttpRequest();
  let filterCheckbox = document.getElementById('filterCheckboxInput');
  alert(filterCheckbox.checked);
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


function mostrarOpciones() {


  // Mostrar un cuadro de diálogo personalizado con tres opciones
  Swal.fire({
    title: 'Seleccione una opción:',
    showCancelButton: true,
    cancelButtonText: 'Cancelar',
    html: `
      <div class="swal2-actions">
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
        <img id="color-picker" src="resource/color-picker.png" style="cursor: pointer; height: 64px;
        width: 64px;">
           </button>
      </div>
    `
  });
}
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

function cambiarColorFondo(color) {
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


function cambiarImagen() {
  var imageUrl = profileImage.src;
  Swal.fire({
    imageUrl: imageUrl,
    title: 'Cambiar Imagen de Perfil',
    html: '<input type="file" id="profile-image-input" accept="image/*" >',
    showCancelButton: true,
    confirmButtonText: 'Guardar',
    cancelButtonText: 'Cancelar',
    preConfirm: () => {
    }
}).then((result) => {
       if(result.isConfirmed){
        editarImagen(document.getElementById('profile-image-input').files[0]);
        Swal.fire('¡Imagen de perfil actualizada!', `Has seleccionado una nueva imagen.`, 'success');
      }
});
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
