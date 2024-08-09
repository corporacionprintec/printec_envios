<?php
// Obtener la ID del cliente de la URL
$id = $_GET['id'];

// Conectar a la base de datos
$conn = new mysqli("localhost", "root", "", "envios_clientes");

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Preparar la consulta
$sql = "SELECT nombre, dni, telefono, envio, direccion, agencia, compraMantenimiento, productos, productoMantenimiento, razonMantenimiento, comprobantePagoRuta, estado FROM clientes WHERE id = ?";
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    die("Error en la preparación de la consulta SQL: " . $conn->error);
}

$stmt->bind_param("s", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $data = $result->fetch_assoc();
    echo json_encode($data);
} else {
    echo json_encode(['error' => 'Cliente no encontrado']);
}

$stmt->close();
$conn->close();
?>
