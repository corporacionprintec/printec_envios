<?php
// Verificar si se ha enviado el ID
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
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

    // Obtener el ID
    $id = intval($_POST['id']);

    // Eliminar el pedido de la base de datos
    $sql = "DELETE FROM clientes WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo "Pedido eliminado correctamente";
    } else {
        echo "Error al eliminar el pedido";
    }

    // Cerrar la conexión
    $stmt->close();
    $conn->close();

    // Redirigir de vuelta a la página de listado
    header("Location: listado_envios.php");
    exit();
} else {
    echo "ID no válido";
}
