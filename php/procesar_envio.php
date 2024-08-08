<?php
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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $item = intval($_POST['item']);
    $claveEnvio = $_POST['claveEnvio'];

    // Manejar la subida de la imagen
    $comprobanteEnvioRuta = '';
    if (isset($_FILES['comprobanteEnvio']) && $_FILES['comprobanteEnvio']['error'] == UPLOAD_ERR_OK) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["comprobanteEnvio"]["name"]);
        if (move_uploaded_file($_FILES["comprobanteEnvio"]["tmp_name"], $target_file)) {
            $comprobanteEnvioRuta = basename($_FILES["comprobanteEnvio"]["name"]);
        } else {
            echo "Error al subir el archivo.";
            exit();
        }
    }

    // Actualizar el pedido
    $sql = "UPDATE clientes SET comprobanteEnvioRuta = ?, claveEnvio = ?, estado = 'enviado' WHERE item = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $comprobanteEnvioRuta, $claveEnvio, $item);

    if ($stmt->execute()) {
        // Redirigir a la página de detalles del pedido con el estado actualizado
        header("Location: ver_pedido.php?id=$item");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>
