<?php
header('Content-Type: application/json');

// Validar la solicitud
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => 'Método no permitido']);
    exit;
}

$item = $_POST['item'] ?? null;
$claveEnvio = $_POST['claveEnvio'] ?? null;

// Validar entrada
if (!$item || !$claveEnvio) {
    echo json_encode(['error' => 'Faltan datos requeridos']);
    exit;
}

if (!isset($_FILES['comprobanteEnvio']) || $_FILES['comprobanteEnvio']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['error' => 'Archivo no proporcionado o inválido']);
    exit;
}

// Conexión a la base de datos
$servername = getenv('DB_HOST') ?: 'localhost';
$username = getenv('DB_USER') ?: 'root';
$password = getenv('DB_PASS') ?: '';
$dbname = getenv('DB_NAME') ?: 'envios_clientes';
$port = getenv('DB_PORT') ?: '3306';

$conn = new mysqli($servername, $username, $password, $dbname, $port);

if ($conn->connect_error) {
    echo json_encode(['error' => 'Conexión fallida: ' . $conn->connect_error]);
    exit;
}

// Leer el archivo enviado
$comprobanteEnvio = file_get_contents($_FILES['comprobanteEnvio']['tmp_name']);

// Actualizar la base de datos
$sql = "UPDATE clientes 
        SET comprobanteEnvio = ?, claveEnvio = ?, estado = ? 
        WHERE item = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo json_encode(['error' => 'Error en la preparación de la consulta: ' . $conn->error]);
    exit;
}

// Define el estado como "enviado"
$estado = "enviado";

// Vincular los parámetros, incluido el nuevo estado
$stmt->bind_param("sbsi", $comprobanteEnvio, $claveEnvio, $estado, $item);

// Enviar los datos binarios
$stmt->send_long_data(0, $comprobanteEnvio);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['error' => 'Error al actualizar el pedido: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?>
