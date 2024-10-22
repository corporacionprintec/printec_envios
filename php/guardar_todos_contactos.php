<?php
// Verificar si las variables de entorno están definidas (producción) o usar valores por defecto (desarrollo local)
$servername = getenv('DB_HOST') ?: 'localhost';
$username = getenv('DB_USER') ?: 'root';
$password = getenv('DB_PASS') ?: '';
$dbname = getenv('DB_NAME') ?: 'envios_clientes';
$port = getenv('DB_PORT') ?: '3306';

// Conexión a la base de datos
$conn = new mysqli($servername, $username, $password, $dbname, $port);

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Consulta para obtener todos los nombres y teléfonos de los contactos
$sql = "SELECT nombre, telefono FROM clientes";
$result = $conn->query($sql);

if (!$result) {
    die("Error en la consulta: " . $conn->error);
}

// Comenzar a crear el contenido del archivo VCF unificado
$contenidoVCF = "";

// Crear la vCard para cada contacto y agregarla al archivo unificado
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $nombre = $row['nombre'];
        $telefono = preg_replace('/[^0-9]/', '', $row['telefono']); // Limpiar el número de teléfono

        // Agregar el texto "CEE A TODOS LOS CONTACTOS"
        $nombreCompleto = $nombre . " - CEE ";

        // Generar el contenido de la vCard para este contacto
        $contenidoVCF .= "BEGIN:VCARD\r\n";
        $contenidoVCF .= "VERSION:3.0\r\n";
        $contenidoVCF .= "FN:" . $nombreCompleto . "\r\n";
        $contenidoVCF .= "TEL;TYPE=CELL:" . $telefono . "\r\n";
        $contenidoVCF .= "END:VCARD\r\n";
    }
}

// Nombre del archivo VCF unificado
$filename = "todos_los_contactos.vcf";

// Forzar la descarga del archivo VCF
header('Content-Type: text/vcard');
header('Content-Disposition: attachment; filename="' . $filename . '"');
echo $contenidoVCF;

// Cerrar la conexión a la base de datos
$conn->close();
?>
