<?php
// Verificar si se ha enviado el 'item' a través del método POST
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

    // Obtener y validar el 'item'
    $item = intval($_POST['item']);  // Cambiar 'id' por 'item'
    if ($item <= 0) {
        die("ID de pedido no válido.");
    }

    // Eliminar el pedido de la base de datos
    $sql = "DELETE FROM clientes WHERE item = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die("Error en la preparación de la consulta: " . $conn->error);
    }
    $stmt->bind_param("i", $item);
    $stmt->execute();

    // Verificar si se eliminó correctamente
    if ($stmt->affected_rows > 0) {
        // Redirigir de vuelta a la página de listado con un mensaje de éxito
        $stmt->close();
        $conn->close();
        header("Location: listado_envios.php?msg=success");
        exit(); // Asegurarse de que el script se detenga después de redirigir
    } else {
        echo "Error: No se pudo eliminar el pedido o el pedido no existe.";
    }

    // Cerrar la conexión
    $stmt->close();
    $conn->close();
} else {
    echo "ID no válido o no se envió correctamente.";
}
