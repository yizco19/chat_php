const form = document.querySelector(".typing-area"),
  incoming_id = form.querySelector(".incoming_id").value,
  topic_id = form.querySelector(".topic_id").value,
  inputField = form.querySelector(".input-field"),
  sendBtn = form.querySelector(".send-btn"),
  adjuntarBtn = form.querySelector(".adjuntarBtn"),
  chatBox = document.querySelector(".chat-box");
const previewImage = document.getElementById('file-preview');
const openImageBtn = document.querySelector('.open-image');
const videoLlamadaBtn = document.querySelector('.videollamadaBtn');


form.onsubmit = (e) => {
  e.preventDefault();
}
sendBtn.classList.add("active");
inputField.focus();
inputField.onkeyup = () => {
  if (inputField.value != "") {
    sendBtn.classList.add("active");
  } else {
    sendBtn.classList.remove("active");
    //si ha selecionado un file tambien se active
    if (adjuntarBtn.style.display == 'inline') {
      sendBtn.classList.add("active");
    }
  }
}

sendBtn.onclick = () => {
  //comrprobamos si hay un archivo adjunto o mensaje
  if (inputField.value != "" || adjuntarBtn.style.display == 'inline') {
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "php/insert-chat.php", true);
    xhr.onload = () => {
      if (xhr.readyState === XMLHttpRequest.DONE) {
        if (xhr.status === 200) {
          console.log("response" + xhr.responseText);
          getMessages();
          inputField.value = "";
          scrollToBottom();
          cancelarArchivo();
          /*sendEmail()
          .then((response) => {
            console.log("La solicitud fue exitosa. Respuesta:", response);
          })
          .catch((error) => {
            console.error("La solicitud falló con el siguiente error:", error);
          });*/

        }
      }
    }
    let formData = new FormData(form);
    xhr.send(formData);
  }
}
async function sendEmail() {
  return new Promise((resolve, reject) => {
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "php/send-email.php?incoming_id=" + incoming_id, true);
    xhr.onload = () => {
      if (xhr.readyState === XMLHttpRequest.DONE) {
        if (xhr.status === 200) {
          resolve(xhr.responseText);
        } else {
          reject("Error en la solicitud: " + xhr.statusText);
        }
      }
    };
    xhr.onerror = () => {
      reject("Error de red");
      console.log("Error de red");
    };
    let formData = new FormData(form);
    xhr.send(formData);
  });
}
chatBox.onmouseenter = () => {
  chatBox.classList.add("active");
}

chatBox.onmouseleave = () => {
  chatBox.classList.remove("active");
}

setInterval(() => {
  getMessages();
}, 1000);
//getMessages();

function getMessages() {
  let xhr = new XMLHttpRequest();
  xhr.open("POST", "php/get-chat.php", true);
  xhr.onload = () => {
    if (xhr.readyState === XMLHttpRequest.DONE) {
      if (xhr.status === 200) {
        let data = xhr.response;
        if (data != "") {
          chatBox.innerHTML = data;
          scrollToBottom();
        }
      }
    }
  };
  xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  xhr.send("incoming_id=" + incoming_id + "&topic_id=" + topic_id);
  showImageMessage();
}


function scrollToBottom() {
  chatBox.scrollTop = chatBox.scrollHeight;
}

adjuntarBtn.onclick = () => {
  document.querySelector(".attachment").click();

}

function showFileName() {
  var input = document.getElementById('attachment');
  var fileInfo = document.getElementById('file-info');
  var fileName = document.getElementById('file-name');
  var selectButton = document.getElementById('select-button');
  const preview = document.getElementById('file-preview');

  if (input.files.length > 0) {
    if (input.files[0].type.includes('image')) {
      // Mostrar la vista previa de la imagen
      const fileReader = new FileReader();
      fileReader.onload = event => {
        preview.setAttribute('src', event.target.result);
        preview.setAttribute('alt', input.files[0].name);
        preview.style.display = 'block';
      }
      fileReader.readAsDataURL(input.files[0]);
    } else {
      // Si no es una imagen, mostrar una imagen predeterminada
      preview.setAttribute('src', 'php/images/default.png');
      preview.setAttribute('alt', 'Imagen predeterminada');
    }

    fileName.textContent = input.files[0].name;
    sendBtn.classList.add("active");
    adjuntarBtn.style.display = 'none';
    fileInfo.style.display = 'block';
  } else {
    // Si no se selecciona ningún archivo, ocultar la vista previa
    fileInfo.style.display = 'none';
    selectButton.style.display = 'inline';
    preview.style.display = 'none'; // Ocultar la vista previa
  }
}


// Función para cancelar la selección del archivo
function cancelarArchivo() {
  var input = document.getElementById('attachment');
  var fileInfo = document.getElementById('file-info');
  var fileName = document.getElementById('file-name');
  input.value = ''; // Limpiar la selección del archivo
  fileInfo.style.display = 'none'; // Ocultar el contenedor del nombre del archivo
  sendBtn.classList.remove("active");
  adjuntarBtn.style.display = 'inline';
  previewImage.style.display = 'none';
  fileName.textContent = '';


}

// Ejecutar la función showFileName cuando se cambie el archivo seleccionado
document.getElementById('attachment').addEventListener('change', showFileName);

document.getElementById('profile-image').addEventListener('click', function () {
  var imageUrl = this.src;

  Swal.fire({
    imageUrl: imageUrl,
    imageHeight: '300px',
    imageWidth: 'auto',
    confirmButtonText: 'Cerrar'
  });
});
showImageMessage();

videoLlamadaBtn.addEventListener('click', function () {
  var codigoUsuario = "1234567890";
  getDetailUser()
    .then(userData => {
      // Obtener el nombre y apellido actual del usuario
      var nombreActual = userData.fname;
      var apellidoActual = userData.lname;
      console.log("Nombre actual:", nombreActual);
      console.log("Apellido actual:", apellidoActual);
      codigoUsuario = nombreActual + apellidoActual;
      console.log("Codigo de Usuario actualizado:", codigoUsuario);

      console.log("Codigo de Usuario fuera actualizado:", codigoUsuario);

      var fechaHoraActual = new Date();

      var anio = fechaHoraActual.getFullYear();
      var mes = fechaHoraActual.getMonth() + 1;
      var dia = fechaHoraActual.getDate();
      var horas = fechaHoraActual.getHours();
      var minutos = fechaHoraActual.getMinutes();
      var segundos = fechaHoraActual.getSeconds();

      mes = mes < 10 ? "0" + mes : mes;
      dia = dia < 10 ? "0" + dia : dia;
      horas = horas < 10 ? "0" + horas : horas;
      minutos = minutos < 10 ? "0" + minutos : minutos;
      segundos = segundos < 10 ? "0" + segundos : segundos;

      var fechaHoraFormateada = "" + anio + mes + dia + horas + minutos + segundos;

      var enlaceVideollamada = "https://meet.jit.si/" + codigoUsuario + fechaHoraFormateada;
      Swal.fire({
        title: "Enlace de videollamada",
        imageHeight: '300px',
        imageWidth: 'auto',
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Enviar enlace de videollamada"
      }).then((result) => {
        if (result.isConfirmed) {
          enviarVideoLlamada(enlaceVideollamada);
        }
      })
    })
    .catch(error => {
      // Manejar errores si ocurre un problema al obtener los detalles del usuario
      console.error(error);
    });

})


function showImageMessage() {
  $(document).ready(function () {
    $(".imagenFile").on("click", function (e) {
      e.preventDefault(); // Evitar que el enlace se abra directamente

      var imagenSrc = $(this)[0].getAttribute("src");
      console.log(imagenSrc);
      Swal.fire({
        imageUrl: imagenSrc,
        imageHeight: '300px',
        imageWidth: 'auto',
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Descargar"
      }).then((result) => {
        if (result.isConfirmed) {
          var link = document.createElement('a');
          link.href = imagenSrc;
          link.setAttribute('download', ''); // Establecer el atributo de descarga
          link.style.display = 'none';

          // Agregar el enlace al documento y hacer clic en él
          document.body.appendChild(link);
          link.click();

          // Limpiar el enlace temporal
          document.body.removeChild(link);
        }
      });
    });
  });
}

function enviarVideoLlamada(enlace) {
  // Realizar una solicitud para enviar el enlace de videollamada al servidor
  var xhr = new XMLHttpRequest();
  xhr.open("POST", "php/send-video-call.php?incoming_id=" + incoming_id, true);
  xhr.setRequestHeader("Content-Type", "application/json");

  // Datos a enviar al servidor
  var data = {
    enlaceVideollamada: enlace,
    incoming_id: incoming_id,
  };
  console.log(data);

  xhr.onreadystatechange = function () {
    if (xhr.readyState === XMLHttpRequest.DONE) {
      if (xhr.status === 200) {
        console.log("Enlace de videollamada enviado correctamente.");
        console.log(xhr.responseText);
      } else {
        console.error("Error al enviar el enlace de videollamada.");


      }
    }
  };

  // Convertir el objeto de datos a formato JSON y enviarlo
  xhr.send(JSON.stringify(data));
}

function getDetailUser() {
  return new Promise((resolve, reject) => {
    var xhr = new XMLHttpRequest();

    // Configurar la solicitud
    xhr.open('GET', 'php/detail-user.php', true);

    // Definir el manejador de eventos para la carga
    xhr.onload = function () {
      if (xhr.status >= 200 && xhr.status < 300) {
        // Procesar la respuesta del servidor PHP
        const userData = JSON.parse(xhr.responseText);
        resolve(userData); // Resuelve la promesa con los datos del usuario
      } else {
        reject('Error al obtener los detalles del usuario: ' + xhr.statusText);
      }
    };

    // Definir el manejador de eventos para los errores de red
    xhr.onerror = function () {
      reject('Error de red al obtener los detalles del usuario.');
    };

    // Enviar la solicitud
    xhr.send();
  });
}


document.getElementById("informactionUser").addEventListener("click", function () {
  // Obtener el unique_id del usuario (puedes obtenerlo de una manera específica)
  let uniqueId = incoming_id; // Asegúrate de tener el valor correcto de incoming_id

  // Realizar la solicitud AJAX
  fetch('php/user-detail.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded',
    },
    body: `unique_id=${uniqueId}`
  })
    .then(response => response.json())
    .then(data => {
      if (data) {
        let htmlContent = `<p><strong>Nombre:</strong> ${data.fname} ${data.lname}</p>
        <p><strong>Email:</strong> ${data.email}</p>`;
        if (data.localizacion !== null) {
          htmlContent += `<p><strong>Localización:</strong> ${data.localizacion}</p>`;
        }
        if (data.img) {
          htmlContent += `<img src="${data.img}" style="    width: 300px;"> `;
        }
        // Mostrar SweetAlert con la información del usuario
        Swal.fire({
          title: 'Información del Usuario',
          html: htmlContent,
          icon: 'info'
        });
      } else {
        Swal.fire({
          title: 'Error',
          text: 'No se pudo obtener la información del usuario.',
          icon: 'error'
        });
      }
    })
    .catch(error => {
      console.error('Error:', error);
      Swal.fire({
        title: 'Error',
        text: 'Hubo un problema con la solicitud.',
        icon: 'error'
      });
    });
});
