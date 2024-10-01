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

// Obtener el 'item' del pedido desde la URL (ahora permitimos cadenas)
$item = isset($_GET['item']) ? $_GET['item'] : '';

if (!empty($item)) {
    // Preparar la consulta usando 'item'
    $sql = "SELECT * FROM clientes WHERE item = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $item);  // Cambiamos a "s" para cadenas de texto
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
    echo json_encode(['error' => 'ID de pedido no válido']);
}

$conn->close();
?>
