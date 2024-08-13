<?php
// Establecer el tipo de contenido a JSON
header('Content-Type: application/json');

// Obtener la ID del cliente de la URL
$id = isset($_GET['id']) ? $_GET['id'] : null;

if (!$id) {
    echo json_encode(['error' => 'ID no proporcionado']);
    exit;
}

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
    echo json_encode(['error' => 'Conexión fallida: ' . $conn->connect_error]);
    exit;
}

// Preparar la consulta
$sql = "SELECT nombre, dni, telefono, envio, direccion, agencia, compraMantenimiento, productos, productoMantenimiento, razonMantenimiento, comprobantePagoRuta, estado, comprobanteEnvioRuta, claveEnvio FROM clientes WHERE id = ?";
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    echo json_encode(['error' => 'Error en la preparación de la consulta SQL: ' . $conn->error]);
    exit;
}

$stmt->bind_param("s", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $data = $result->fetch_assoc();
    echo json_encode($data);
} else {
    echo json_encode(['error' => 'Cliente no encontrado']);
}

$stmt->close();
$conn->close();
?>
