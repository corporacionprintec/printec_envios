<?php
header('Content-Type: application/json');

// Conectar a la base de datos
$servername = getenv('DB_HOST') ?: 'localhost';
$username = getenv('DB_USER') ?: 'root';
$password = getenv('DB_PASS') ?: '';
$dbname = getenv('DB_NAME') ?: 'envios_clientes';
$port = getenv('DB_PORT') ?: '3306';

$conn = new mysqli($servername, $username, $password, $dbname, $port);

// Verificar conexión a la base de datos
if ($conn->connect_error) {
    die(json_encode(['error' => 'Conexión fallida: ' . $conn->connect_error]));
}

// Obtener el ID del cliente desde la URL
$item_id = isset($_GET['id']) ? $_GET['id'] : null;

// Registrar el ID recibido en los logs para depurar
error_log("ID recibido: " . $item_id);

if (!empty($item_id)) {
    // Preparar la consulta para obtener los detalles del cliente
    $sql = "SELECT nombre, dni, telefono, envio, direccion, agencia, estado, comprobanteEnvioRuta, claveEnvio 
            FROM clientes 
            WHERE item = ?";
    $stmt = $conn->prepare($sql);
    
    // Verificar si la consulta fue preparada correctamente
    if (!$stmt) {
        echo json_encode(['error' => 'Error en la preparación de la consulta: ' . $conn->error]);
        exit;
    }

    // Vincular parámetros y ejecutar la consulta
    $stmt->bind_param("s", $item_id); // Tipo 's' para cadenas
    $stmt->execute();
    $result = $stmt->get_result();

    // Verificar si se encontraron resultados
    if ($result->num_rows > 0) {
        // Obtener los datos del cliente y enviarlos en formato JSON
        $cliente = $result->fetch_assoc();
        echo json_encode($cliente);
    } else {
        echo json_encode(['error' => 'Pedido no encontrado']);
    }

    // Cerrar el statement
    $stmt->close();
} else {
    echo json_encode(['error' => 'ID no válido o no presente']);
}

// Cerrar la conexión a la base de datos
$conn->close();
?>
