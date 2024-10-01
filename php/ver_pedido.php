<?php
// Verificar si se ha enviado el item
if (isset($_GET['item'])) {
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

    // Obtener el item
    $item = intval($_GET['item']); // Cambiar 'item' por el parámetro correcto

    // Consulta para obtener los detalles del pedido
    $sql = "SELECT * FROM clientes WHERE item = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $item);
    $stmt->execute();
    $result = $stmt->get_result();

    // Verificar si se encontró el pedido
    if ($result->num_rows > 0) {
        $pedido = $result->fetch_assoc();
        // Enviar respuesta en formato JSON
        header('Content-Type: application/json');
        echo json_encode($pedido);
    } else {
        // Enviar error si no se encuentra el pedido
        header('Content-Type: application/json');
        echo json_encode(['error' => 'ID de pedido no válido']);
    }

    // Cerrar la conexión
    $stmt->close();
    $conn->close();
} else {
    // Enviar error si no se ha enviado el ID
    header('Content-Type: application/json');
    echo json_encode(['error' => 'ID no proporcionado']);
}
?>
