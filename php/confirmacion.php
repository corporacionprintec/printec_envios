<?php
header('Content-Type: application/json');

// Obtener el ID del cliente de la URL
$id = isset($_GET['id']) ? $_GET['id'] : null;

if (!$id) {
    echo json_encode(['error' => 'ID no proporcionado']);
    exit;
}

// Configuración de la base de datos
$servername = getenv('DB_HOST') ?: 'localhost';
$username = getenv('DB_USER') ?: 'root';
$password = getenv('DB_PASS') ?: '';
$dbname = getenv('DB_NAME') ?: 'envios_clientes';
$port = getenv('DB_PORT') ?: '3306';

// Función para manejar respuestas de error
function sendError($message) {
    echo json_encode(['error' => $message]);
    exit;
}

// Conexión a la base de datos
$conn = new mysqli($servername, $username, $password, $dbname, $port);

// Verificar la conexión
if ($conn->connect_error) {
    sendError('Conexión fallida: ' . $conn->connect_error);
}

// Consulta SQL para obtener los datos del cliente
$sql = "SELECT 
            nombre, 
            dni, 
            telefono, 
            envio, 
            direccion, 
            agencia, 
            compraMantenimiento, 
            productos, 
            productoMantenimiento, 
            razonMantenimiento, 
            comprobantePago, 
            estado, 
            comprobanteEnvio, 
            claveEnvio 
        FROM clientes 
        WHERE id = ?";
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    sendError('Error en la preparación de la consulta SQL: ' . $conn->error);
}

$stmt->bind_param("s", $id);  // Usa 's' para cadenas
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $data = $result->fetch_assoc();

    // Convertir el contenido binario de comprobantePago a Base64
    if (!empty($data['comprobantePago'])) {
        $data['comprobantePago'] = 'data:image/jpeg;base64,' . base64_encode($data['comprobantePago']);
    }

    // Convertir el contenido binario de comprobanteEnvio a Base64 (si es necesario)
    if (!empty($data['comprobanteEnvio'])) {
        $data['comprobanteEnvio'] = 'data:image/jpeg;base64,' . base64_encode($data['comprobanteEnvio']);
    }

    echo json_encode($data);
} else {
    sendError('Cliente no encontrado');
}

$stmt->close();
$conn->close();
?>
