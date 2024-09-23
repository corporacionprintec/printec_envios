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
    die(json_encode(['error' => 'Conexión fallida: ' . $conn->connect_error]));
}

// Obtener el ID del pedido desde la URL
$item_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($item_id > 0) {
    // Preparar la consulta SQL
    $sql = "SELECT * FROM clientes WHERE item = ?";
    if ($stmt = $conn->prepare($sql)) {
        // Vincular el parámetro
        $stmt->bind_param("i", $item_id);
        $stmt->execute();
        $result = $stmt->get_result();

        // Comprobar si se encuentra el pedido
        if ($result->num_rows > 0) {
            $pedido = $result->fetch_assoc();
            echo json_encode($pedido);
        } else {
            echo json_encode(['error' => 'Pedido no encontrado']);
        }

        // Cerrar la declaración
        $stmt->close();
    } else {
        // Mostrar el error en la consulta
        echo json_encode(['error' => 'Error en la consulta: ' . $conn->error]);
    }
} else {
    echo json_encode(['error' => 'ID no válido']);
}

// Cerrar la conexión a la base de datos
$conn->close();
