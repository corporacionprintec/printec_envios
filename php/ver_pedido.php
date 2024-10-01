<?php
// Conectar a la base de datos
$servername = getenv('DB_HOST') ?: 'localhost';
$username = getenv('DB_USER') ?: 'root';
$password = getenv('DB_PASS') ?: '';
$dbname = getenv('DB_NAME') ?: 'envios_clientes';
$port = getenv('DB_PORT') ?: '3306';

$conn = new mysqli($servername, $username, $password, $dbname, $port);

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Verificar si se ha enviado el ID
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Consulta para obtener los detalles del pedido
    $sql = "SELECT * FROM clientes WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
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
    echo json_encode(['error' => 'ID no válido']);
}

$conn->close();
?>
