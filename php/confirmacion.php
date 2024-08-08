<?php
header('Content-Type: application/json');

// Conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "envios_clientes";

$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Obtener el UUID desde la URL
$id = isset($_GET['id']) ? $_GET['id'] : '';

if ($id) {
    // Consultar los datos del cliente
    $sql = "SELECT nombre, dni, telefono, envio, direccion, agencia, compraMantenimiento, productos, productoMantenimiento, detalleMantenimiento, comprobantePagoRuta, estado FROM clientes WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $data = $result->fetch_assoc();
        echo json_encode($data);
    } else {
        echo json_encode(["error" => "Cliente no encontrado."]);
    }

    $stmt->close();
} else {
    echo json_encode(["error" => "ID del cliente no proporcionado."]);
}

$conn->close();
?>
