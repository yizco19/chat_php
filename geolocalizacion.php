<!DOCTYPE html>
<html>
<head>
  <title>Geolocalización</title>
  <script>
function getCityName(lat, lon) {
  var url = `https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${lat}&lon=${lon}`;

  fetch(url)
    .then(response => response.json())
    .then(data => {
      if (data && data.address && data.address.city) {
        document.getElementById("location").innerHTML = data.address.city;
      } else if (data && data.address && data.address.town) {
        document.getElementById("location").innerHTML = data.address.town;
      } else if (data && data.address && data.address.village) {
        document.getElementById("location").innerHTML = data.address.village;
      } else {
        document.getElementById("location").innerHTML = "No se pudo determinar la ubicación.";
      }
    })
    .catch(error => {
      document.getElementById("location").innerHTML = "Error al obtener la ubicación: " + error;
    });
}
</script>

</head>
<body>

<button onclick="getLocation()">Obtener Ubicación</button>
<p id="location"></p>

<script>
function getLocation() {
  if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(showPosition, showError);
  } else {
    document.getElementById("location").innerHTML = "La geolocalización no es soportada por este navegador.";
  }
}

function showPosition(position) {
  var lat = position.coords.latitude;
  var lon = position.coords.longitude;
  getCityName(lat, lon);
}

function showError(error) {
  switch(error.code) {
    case error.PERMISSION_DENIED:
      document.getElementById("location").innerHTML = "Permiso denegado por el usuario.";
      break;
    case error.POSITION_UNAVAILABLE:
      document.getElementById("location").innerHTML = "Información de ubicación no disponible.";
      break;
    case error.TIMEOUT:
      document.getElementById("location").innerHTML = "La solicitud de ubicación ha caducado.";
      break;
    case error.UNKNOWN_ERROR:
      document.getElementById("location").innerHTML = "Ocurrió un error desconocido.";
      break;
  }
}
</script>

</body>
</html>
