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
    die("Conexión fallida: " . $conn->connect_error);
}

// Verificar si se ha enviado el UID o un valor numérico
if (isset($_GET['item'])) {
    $item = $_GET['item'];

    // Validar si el 'item' es un UUID o un número
    if (preg_match('/^[a-f0-9\-]{36}$/', $item)) {
        // Si es un UUID, hacer la consulta por 'id' (UUID)
        $sql = "SELECT nombre, dni, telefono, envio, direccion, agencia, estado, productoMantenimiento, razonMantenimiento 
                FROM clientes 
                WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $item);  // 's' para UUID (string)
    } elseif (is_numeric($item)) {
        // Si es numérico, hacer la consulta por 'item' (número entero)
        $sql = "SELECT nombre, dni, telefono, envio, direccion, agencia, estado, productoMantenimiento, razonMantenimiento 
                FROM clientes 
                WHERE item = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $item);  // 'i' para integer (número entero)
    } else {
        echo json_encode(['error' => 'ID no válido']);
        exit;
    }

    // Ejecutar la consulta y obtener el resultado
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $pedido = $result->fetch_assoc();

        // Verificar y asignar "No disponible" si algún campo está vacío
        foreach ($pedido as $key => $value) {
            if (empty($value)) {
                $pedido[$key] = 'No disponible';
            }
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
