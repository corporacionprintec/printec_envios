<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['item'])) {
    // Conexión a la base de datos
    $servername = getenv('DB_HOST') ?: 'localhost';
    $username = getenv('DB_USER') ?: 'root';
    $password = getenv('DB_PASS') ?: '';
    $dbname = getenv('DB_NAME') ?: 'envios_clientes';
    $port = getenv('DB_PORT') ?: '3306';

    $conn = new mysqli($servername, $username, $password, $dbname, $port);

    if ($conn->connect_error) {
        die("Conexión fallida: " . $conn->connect_error);
    }

    // Obtener el pedido original
    $item = $_POST['item'];
    $sqlPedido = "SELECT nombre, dni, telefono, direccion, agencia, productos, productoMantenimiento, razonMantenimiento FROM clientes WHERE item = ?";
    $stmtPedido = $conn->prepare($sqlPedido);

    if (!$stmtPedido) {
        die("Error al preparar la consulta de pedido: " . $conn->error);
    }

    $stmtPedido->bind_param("i", $item);
    $stmtPedido->execute();
    $result = $stmtPedido->get_result();

    if ($result->num_rows === 0) {
        die("No se encontró el pedido para el item: " . $item);
    }

    $pedido = $result->fetch_assoc();
    $stmtPedido->close();

    // Generar UUID
    function generateUUIDv4() {
        $data = random_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // set version to 4
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // set variant to RFC 4122
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }

    $uuid = generateUUIDv4();

    // Asegurar valores no nulos
    $pedido['direccion'] = $pedido['direccion'] ?? '';
    $pedido['agencia'] = $pedido['agencia'] ?? '';
    $pedido['productos'] = $pedido['productos'] ?? '';
    $pedido['productoMantenimiento'] = $pedido['productoMantenimiento'] ?? '';
    $pedido['razonMantenimiento'] = $pedido['razonMantenimiento'] ?? '';

    // Insertar el nuevo cliente en la base de datos
    $sqlCliente = "INSERT INTO clientes (id, nombre, dni, telefono, direccion, agencia, productos, productoMantenimiento, razonMantenimiento, fecha_creacion) 
                   VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
    $stmtCliente = $conn->prepare($sqlCliente);

    if (!$stmtCliente) {
        die("Error al preparar la consulta de cliente: " . $conn->error);
    }

    $stmtCliente->bind_param(
        "sssssssss",
        $uuid,
        $pedido['nombre'],
        $pedido['dni'],
        $pedido['telefono'],
        $pedido['direccion'],
        $pedido['agencia'],
        $pedido['productos'],
        $pedido['productoMantenimiento'],
        $pedido['razonMantenimiento']
    );

    if ($stmtCliente->execute()) {
        header("Location: /confirmacion.html?id=" . urlencode($uuid));
        exit();
    } else {
        echo "Error al registrar el cliente: " . $stmtCliente->error;
    }

    $stmtCliente->close();
    $conn->close();
}
?>
