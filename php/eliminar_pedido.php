<?php
// Verificar si se ha enviado el item
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['item'])) {
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

    // Obtener el item
    $item = intval($_POST['item']);  // Cambiar 'id' por 'item'

    // Eliminar el pedido de la base de datos
    $sql = "DELETE FROM clientes WHERE item = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $item);
    $stmt->execute();

    // Verificar si se eliminó correctamente
    if ($stmt->affected_rows > 0) {
        // Redirigir de vuelta a la página de listado
        $stmt->close();
        $conn->close();
        header("Location: listado_envios.php");
        exit(); // Asegúrate de que el script se detenga después de redirigir
    } else {
        echo "Error al eliminar el pedido";
    }

    // Cerrar la conexión
    $stmt->close();
    $conn->close();
} else {
    echo "ID no válido";
}
