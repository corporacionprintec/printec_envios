<?php
header('Content-Type: application/json');

// Conectar a la base de datos
$servername = getenv('DB_HOST') ?: 'localhost';
$username = getenv('DB_USER') ?: 'root';
$password = getenv('DB_PASS') ?: '';
$dbname = getenv('DB_NAME') ?: 'envios_clientes';
$port = getenv('DB_PORT') ?: '3306';

$conn = new mysqli($servername, $username, $password, $dbname, $port);

if ($conn->connect_error) {
    die(json_encode(['error' => 'Conexión fallida: ' . $conn->connect_error]));
}

// Verificar que el formulario se envió correctamente
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $item_id = intval($_POST['id']);
    $claveEnvio = $_POST['claveEnvio'];

    // Verificar que el archivo del comprobante fue subido correctamente
    if (isset($_FILES['comprobanteEnvio']) && $_FILES['comprobanteEnvio']['error'] === UPLOAD_ERR_OK) {
        $comprobante = $_FILES['comprobanteEnvio'];
        
        // Directorio donde se guardará el comprobante
        $uploadDir = 'uploads/';
        $comprobanteNombre = basename($comprobante['name']);
        $uploadFilePath = $uploadDir . $comprobanteNombre;

        // Mover el archivo a la carpeta destino
        if (move_uploaded_file($comprobante['tmp_name'], $uploadFilePath)) {
            
            // Insertar los datos en la base de datos
            $sql = "INSERT INTO envios (item_id, clave_envio, comprobante_ruta) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('iss', $item_id, $claveEnvio, $comprobanteNombre);

            if ($stmt->execute()) {
                // Redireccionar a listado_envios.php con la clave de envío
                header("Location: listado_envios.php?claveEnvio=$claveEnvio");
                exit();
            } else {
                echo json_encode(['error' => 'Error al guardar en la base de datos: ' . $conn->error]);
            }
        } else {
            echo json_encode(['error' => 'Error al mover el archivo subido.']);
        }
    } else {
        echo json_encode(['error' => 'Error al subir el archivo del comprobante.']);
    }
} else {
    echo json_encode(['error' => 'Método de solicitud no válido.']);
}

$conn->close();
?>
