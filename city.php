<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Geolocalización por IP Pública</title>
</head>
<body>
    <h1>Geolocalización por IP Pública</h1>
    <div id="ip-publica">IP Pública: Cargando...</div>
    <div id="ubicacion">Ubicación: Cargando...</div>

    <script>
        // Función para obtener la IP pública
        async function obtenerIPPublica() {
            try {
                const response = await fetch('https://api.ipify.org?format=json');
                const data = await response.json();
                
                const ipPublica = data.ip || 'No disponible';
                document.getElementById('ip-publica').textContent = `IP Pública: ${ipPublica}`;
                
                // Llamar a la función para obtener la ubicación
                obtenerUbicacionDesdeIP(ipPublica);
            } catch (error) {
                document.getElementById('ip-publica').textContent = `Error al obtener la IP pública: ${error.message}`;
            }
        }

        // Función para obtener la ubicación basada en la IP pública
        async function obtenerUbicacionDesdeIP(ip) {
            try {
                const response = await fetch(`https://ipwhois.app/json/${ip}`);
                const data = await response.json();
                
                const ciudad = data.city || 'No disponible';
                const region = data.region || 'No disponible';
                const pais = data.country || 'No disponible';
                const ubicacion = `${ciudad}, ${region}, ${pais}`;
                
                document.getElementById('ubicacion').textContent = `Ubicación: ${ubicacion}`;
            } catch (error) {
                document.getElementById('ubicacion').textContent = `Error al obtener la ubicación: ${error.message}`;
            }
        }

        // Llamar a la función al cargar la página
        obtenerIPPublica();
    </script>
</body>
</html>