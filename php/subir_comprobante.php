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
    if (isset($_FILES['comprobantePago']) && $_FILES['comprobantePago']['error'] == UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/';
        $uploadFile = $uploadDir . basename($_FILES['comprobantePago']['name']);
        
        // Mover el archivo subido a la carpeta de destino
        if (move_uploaded_file($_FILES['comprobantePago']['tmp_name'], $uploadFile)) {
            // Actualizar la ruta del comprobante, la clave de envío y el estado a 'Enviado'
            $sql = "UPDATE clientes SET comprobanteEnvioRuta = ?, claveEnvio = ?, estado = 'Enviado' WHERE item = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssi", $uploadFile, $claveEnvio, $item_id);

            if ($stmt->execute()) {
                // Redirigir a la lista de envíos
                echo json_encode(['success' => true, 'redirect_url' => 'lista_envios.php']);
            } else {
                echo json_encode(['error' => 'Error al guardar en la base de datos: ' . $conn->error]);
            }

            $stmt->close();
        } else {
            echo json_encode(['error' => 'Error al mover el archivo subido']);
        }
    } else {
        echo json_encode(['error' => 'Por favor, selecciona un archivo válido']);
    }
}

$conn->close();
?>
