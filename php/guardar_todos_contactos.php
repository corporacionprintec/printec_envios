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

// Función para crear el archivo VCF para cada contacto
function generarVCF($nombre, $telefono) {
    // Asegurarse de que el número no contenga espacios ni guiones
    $telefono = preg_replace('/[^0-9]/', '', $telefono);

    // Agregar el texto adicional al nombre
    $nombreCompleto = $nombre . " - CEE";

    // Nombre del archivo VCF basado en el nombre del cliente
    $filename = $nombreCompleto . ".vcf";
    $filename = str_replace(' ', '_', $filename); // Quitar espacios del nombre

    // Generar el contenido del archivo VCF
    $contenidoVCF = "BEGIN:VCARD\r\n";
    $contenidoVCF .= "VERSION:3.0\r\n";
    $contenidoVCF .= "FN:" . $nombreCompleto . "\r\n";
    $contenidoVCF .= "TEL;TYPE=CELL:" . $telefono . "\r\n";
    $contenidoVCF .= "END:VCARD\r\n";

    // Descargar el archivo VCF
    header('Content-Type: text/vcard');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    echo $contenidoVCF;
}

// Generar y descargar cada archivo VCF
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $nombre = $row['nombre'];
        $telefono = $row['telefono'];

        // Generar y descargar el archivo VCF para cada contacto
        generarVCF($nombre, $telefono);
    }
}

// Cerrar la conexión a la base de datos
$conn->close();
?>
