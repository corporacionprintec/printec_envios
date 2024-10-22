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

// Consulta para obtener todos los contactos
$sql = "SELECT nombre, telefono FROM clientes";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Crear un archivo VCF para todos los contactos
    header('Content-Type: text/vcard');
    header('Content-Disposition: attachment; filename="todos_los_contactos.vcf"');

    while ($row = $result->fetch_assoc()) {
        $nombre = $row['nombre'];
        $telefono = preg_replace('/[^0-9]/', '', $row['telefono']); // Eliminar caracteres no numéricos

        // Generar el contenido del archivo VCF
        echo "BEGIN:VCARD\r\n";
        echo "VERSION:3.0\r\n";
        echo "FN:" . $nombre . "\r\n";
        echo "TEL;TYPE=CELL:" . $telefono . "\r\n";
        echo "END:VCARD\r\n";
    }
} else {
    echo "No hay contactos para guardar.";
}

// Cerrar la conexión a la base de datos
if ($conn !== null && $conn->connect_error == null) {
    $conn->close();
}
?>
