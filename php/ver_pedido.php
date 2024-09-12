<?php
header('Content-Type: application/json');

// Conectar a la base de datos
$servername = getenv('DB_HOST') ?: 'localhost';
$username = getenv('DB_USER') ?: 'root';
$password = getenv('DB_PASS') ?: '';
$dbname = getenv('DB_NAME') ?: 'envios_clientes';
$port = getenv('DB_PORT') ?: '3306';

$conn = new mysqli($servername, $username, $password, $dbname, $port);

if ($conn->connect_error) {
    die(json_encode(['error' => 'Conexión fallida']));
}

// Obtener el ID del pedido desde la URL (si está disponible)
$item_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($item_id > 0) {
    // Si se proporciona un ID, obtener solo ese pedido
    $sql = "SELECT * FROM clientes WHERE item = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $item_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $pedido = $result->fetch_assoc();
        echo json_encode($pedido);
    } else {
        echo json_encode(['error' => 'Pedido no encontrado']);
    }
    $stmt->close();
} else {
    // Si no se proporciona un ID, obtener todos los pedidos
    $sql = "SELECT * FROM clientes";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $pedidos = [];
        while ($row = $result->fetch_assoc()) {
            $pedidos[] = $row; // Guardar cada pedido en el array
        }
        echo json_encode($pedidos); // Devolver todos los pedidos
    } else {
        echo json_encode(['error' => 'No se encontraron pedidos']);
    }
}

$conn->close();
