<?php
// Conectar a la base de datos
$servername = getenv('DB_HOST') ?: 'localhost';
$username = getenv('DB_USER') ?: 'root';
$password = getenv('DB_PASS') ?: '';
$dbname = getenv('DB_NAME') ?: 'envios_clientes';
$port = getenv('DB_PORT') ?: '3306';

$conn = new mysqli($servername, $username, $password, $dbname, $port);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $nuevo_estado = $_POST['nuevo_estado'];

    $sql = "UPDATE clientes SET estado = '$nuevo_estado' WHERE id = $id";
    
    if ($conn->query($sql) === TRUE) {
        header("Location: index.php?msg=estado_actualizado");
    } else {
        echo "Error al actualizar el estado: " . $conn->error;
    }
}

$conn->close();
?>
