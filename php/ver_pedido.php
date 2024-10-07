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

// Verificar si se ha enviado el item
if (isset($_GET['item'])) {
    $item = $_GET['item'];

    // Consulta para obtener los detalles del pedido desde la tabla 'clientes'
    $sql = "SELECT nombre, dni, telefono, envio, direccion, agencia, estado, productoMantenimiento, compraMantenimiento 
            FROM clientes 
            WHERE item = ?"; // Asegúrate de que 'item' sea el campo correcto

    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        echo json_encode(['error' => 'Error en la consulta: ' . $conn->error]);
        exit;
    }

    $stmt->bind_param("i", $item);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $pedido = $result->fetch_assoc();

        // Verificar si los campos de mantenimiento están vacíos
        if (empty($pedido['productoMantenimiento'])) {
            $pedido['productoMantenimiento'] = 'No disponible';
        }
        if (empty($pedido['compraMantenimiento'])) {
            $pedido['compraMantenimiento'] = 'No disponible';
        }

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
