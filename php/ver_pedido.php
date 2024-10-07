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

    // Depuración: verificar el valor de $item
    error_log("El valor de item es: " . $item);

    // Consulta para obtener los detalles del pedido
    $sql = "SELECT clientes.*, mantenimientos.producto, mantenimientos.detalle 
            FROM clientes 
            LEFT JOIN mantenimientos ON clientes.item = mantenimientos.item 
            WHERE clientes.item = ?";

    $stmt = $conn->prepare($sql);
    
    // Si hay un error en la preparación de la consulta, mostrarlo
    if (!$stmt) {
        error_log("Error en la preparación de la consulta: " . $conn->error);
        echo json_encode(['error' => 'Error en la consulta: ' . $conn->error]);  // Mostrar error
        exit;
    }

    $stmt->bind_param("i", $item);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $pedido = $result->fetch_assoc();
        echo json_encode($pedido);
    } else {
        // Depuración: no se encontró el pedido
        error_log("No se encontró el pedido con el item: " . $item);
        echo json_encode(['error' => 'Pedido no encontrado']);
    }

    $stmt->close();
} else {
    // Depuración: no se pasó un parámetro válido
    error_log("ID no válido");
    echo json_encode(['error' => 'ID no válido']);
}

$conn->close();
?>
