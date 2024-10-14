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

// Verificar si se ha enviado el UID
if (isset($_GET['item'])) {
    $id = $_GET['item'];  // Usar 'item' como identificador, pero en realidad estamos filtrando por 'id' que es el UUID

    // Consulta para obtener los detalles del pedido desde la tabla 'clientes' usando el campo 'id' (UUID)
    $sql = "SELECT nombre, dni, telefono, envio, direccion, agencia, estado, productoMantenimiento, razonMantenimiento 
            FROM clientes 
            WHERE id = ?"; // Aquí cambiamos a 'id' que es el campo UUID

    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        echo json_encode(['error' => 'Error en la consulta: ' . $conn->error]);
        exit;
    }

    // Vinculamos el UUID como string
    $stmt->bind_param("s", $id);  // 's' indica que estamos esperando un string (UUID)
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
