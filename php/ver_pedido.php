<?php
header('Content-Type: application/json');

// Conectar a la base de datos
$servername = getenv('DB_HOST') ?: 'localhost';
$username = getenv('DB_USER') ?: 'root';
$password = getenv('DB_PASS') ?: '';
$dbname = getenv('DB_NAME') ?: 'envios_clientes';
$port = getenv('DB_PORT') ?: '3306';

$conn = new mysqli($servername, $username, $password, $dbname, $port);

// Verificar la conexión
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['error' => 'Conexión fallida a la base de datos']);
    exit;
}

// Obtener el ID del pedido desde la URL
$item_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($item_id > 0) {
    // Preparar la consulta SQL
    $sql = "SELECT item, nombre, dni, telefono, tipo_envio, direccion, agencia, estado FROM clientes WHERE item = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $item_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Verificar si el pedido existe
    if ($result->num_rows > 0) {
        $pedido = $result->fetch_assoc();
        echo json_encode($pedido);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Pedido no encontrado']);
    }

    $stmt->close();
} else {
    http_response_code(400);
    echo json_encode(['error' => 'ID no válido']);
}

$conn->close();
?>
