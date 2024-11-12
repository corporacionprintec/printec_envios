<?php
// Configuración de la base de datos
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

// Verificar si el estado y el ID están establecidos en la solicitud
if (isset($_POST['id']) && isset($_POST['nuevo_estado'])) {
    $id = $conn->real_escape_string($_POST['id']);
    $nuevo_estado = $conn->real_escape_string($_POST['nuevo_estado']);

    // Actualizar el estado en la base de datos
    $sql = "UPDATE clientes SET estado = '$nuevo_estado' WHERE id = '$id'";
    if ($conn->query($sql) === TRUE) {
        header("Location: listado_envios.php?msg=estado_actualizado");
    } else {
        echo "Error al actualizar el estado: " . $conn->error;
    }
} else {
    echo "Datos incompletos para actualizar el estado.";
}

$conn->close();
?>
