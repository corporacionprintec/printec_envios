<?php
// Obtener la ID del cliente de la URL
header('Content-Type: application/json');
$id = $_GET['id'];

// Verificar si las variables de entorno están definidas (producción) o usar valores por defecto (desarrollo local)
$servername = getenv('DB_HOST') ?: 'localhost';
$username = getenv('DB_USER') ?: 'root';
$password = getenv('DB_PASS') ?: '';
$dbname = getenv('DB_NAME') ?: 'envios_clientes';
$port = getenv('DB_PORT') ?: '3306';

// Conexión a la base de datos
$conn = new mysqli($servername, $username, $password, $dbname, $port);

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Preparar la consulta
$sql = "SELECT nombre, dni, telefono, envio, direccion, agencia, compraMantenimiento, productos, productoMantenimiento, razonMantenimiento, comprobantePagoRuta, estado, comprobanteEnvioRuta, claveEnvio FROM clientes WHERE id = ?";
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    die("Error en la preparación de la consulta SQL: " . $conn->error);
}

$stmt->bind_param("s", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $data = $result->fetch_assoc();

    // Generar la respuesta en JSON con todos los datos, incluyendo comprobante de envío y clave de envío
    echo json_encode([
        'nombre' => $data['nombre'],
        'dni' => $data['dni'],
        'telefono' => $data['telefono'],
        'envio' => $data['envio'],
        'direccion' => $data['direccion'],
        'agencia' => $data['agencia'],
        'compraMantenimiento' => $data['compraMantenimiento'],
        'productos' => $data['productos'],
        'productoMantenimiento' => $data['productoMantenimiento'],
        'razonMantenimiento' => $data['razonMantenimiento'],
        'comprobantePagoRuta' => $data['comprobantePagoRuta'],
        'estado' => $data['estado'],
        'comprobanteEnvioRuta' => $data['comprobanteEnvioRuta'], // Nueva información
        'claveEnvio' => $data['claveEnvio'] // Nueva información
    ]);
} else {
    echo json_encode(['error' => 'Cliente no encontrado']);
}

$stmt->close();
$conn->close();
?>
