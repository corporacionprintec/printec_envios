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
    $nombreCompleto = $nombre . " - CEE A TODOS LOS CONTACTOS";

    // Nombre del archivo VCF basado en el nombre del cliente
    $filename = $nombreCompleto . ".vcf";
    $filename = str_replace(' ', '_', $filename); // Quitar espacios del nombre

    // Generar el contenido del archivo VCF
    $contenidoVCF = "BEGIN:VCARD\r\n";
    $contenidoVCF .= "VERSION:3.0\r\n";
    $contenidoVCF .= "FN:" . $nombreCompleto . "\r\n";
    $contenidoVCF .= "TEL;TYPE=CELL:" . $telefono . "\r\n";
    $contenidoVCF .= "END:VCARD\r\n";

    // Crear el archivo VCF y forzar su descarga
    file_put_contents('/mnt/data/' . $filename, $contenidoVCF);
}

// Crear un archivo zip con todos los contactos
$zip = new ZipArchive();
$zipFilename = "/mnt/data/contactos.zip";

if ($zip->open($zipFilename, ZipArchive::CREATE) !== TRUE) {
    exit("No se puede abrir el archivo ZIP\n");
}

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $nombre = $row['nombre'];
        $telefono = $row['telefono'];
        $nombreCompleto = $nombre . " - CEE A TODOS LOS CONTACTOS";

        // Generar el archivo VCF para cada contacto
        $telefonoFormateado = preg_replace('/[^0-9]/', '', $telefono);
        $filename = str_replace(' ', '_', $nombreCompleto) . ".vcf";

        // Generar el contenido del archivo VCF
        $contenidoVCF = "BEGIN:VCARD\r\n";
        $contenidoVCF .= "VERSION:3.0\r\n";
        $contenidoVCF .= "FN:" . $nombreCompleto . "\r\n";
        $contenidoVCF .= "TEL;TYPE=CELL:" . $telefonoFormateado . "\r\n";
        $contenidoVCF .= "END:VCARD\r\n";

        // Agregar el archivo VCF al archivo ZIP
        $zip->addFromString($filename, $contenidoVCF);
    }
}

$zip->close();

// Descargar el archivo ZIP
header('Content-Type: application/zip');
header('Content-Disposition: attachment; filename="contactos.zip"');
header('Content-Length: ' . filesize($zipFilename));

readfile($zipFilename);

// Cerrar la conexión a la base de datos
$conn->close();
?>
