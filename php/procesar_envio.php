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
    // Obtener los datos del formulario
    $item = intval($_POST['item']); // Asegúrate de que el valor 'item' se recibe correctamente
    $claveEnvio = $_POST['claveEnvio'];

    // Verificación de item (depuración)
    if (!$item) {
        echo "Error: item no recibido correctamente.";
        exit();
    }

    // Manejar la subida de la imagen (comprobante de envío)
    $comprobanteEnvioRuta = '';
    if (isset($_FILES['comprobanteEnvio']) && $_FILES['comprobanteEnvio']['error'] == UPLOAD_ERR_OK) {
        $target_dir = "uploads/";  // Directorio donde se guardará el archivo
        $target_file = $target_dir . basename($_FILES["comprobanteEnvio"]["name"]);
        
        // Mover el archivo subido al directorio deseado
        if (move_uploaded_file($_FILES["comprobanteEnvio"]["tmp_name"], $target_file)) {
            $comprobanteEnvioRuta = basename($_FILES["comprobanteEnvio"]["name"]);  // Guardar solo el nombre del archivo
        } else {
            echo "Error al subir el archivo.";
            exit();
        }
    }

    // Actualizar la base de datos con la información del envío
    $sql = "UPDATE clientes SET comprobanteEnvioRuta = ?, claveEnvio = ?, estado = 'enviado' WHERE item = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $comprobanteEnvioRuta, $claveEnvio, $item);

    // Ejecutar la consulta
    if ($stmt->execute()) {
        // Redirigir a la página de confirmación con el ID del pedido
        header("Location: ../confirmacion.html?id=$item");
        exit();
    } else {
        // Mostrar error si algo falla
        echo "Error al actualizar el pedido: " . $stmt->error;
    }

    // Cerrar la declaración
    $stmt->close();
}

// Cerrar la conexión a la base de datos
$conn->close();
?>
