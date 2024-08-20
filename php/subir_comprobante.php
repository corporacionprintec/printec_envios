<?php
session_start(); // Inicia la sesión

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
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

    // Obtener ID del cliente
    $id = $_POST['id'];

    // Subir archivo de comprobante de pago
    $comprobantePagoRuta = '';
    if (isset($_FILES['comprobantePago']) && $_FILES['comprobantePago']['error'] == UPLOAD_ERR_OK) {
        $target_dir = __DIR__ . "/uploads/";
        $target_file = $target_dir . basename($_FILES["comprobantePago"]["name"]);
        
        // Verifica si la carpeta existe, si no, la crea
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        // Mueve el archivo a la ubicación deseada
        if (move_uploaded_file($_FILES["comprobantePago"]["tmp_name"], $target_file)) {
            $comprobantePagoRuta = basename($_FILES["comprobantePago"]["name"]);

            // Actualizar la base de datos con la ruta del comprobante
            $sql = "UPDATE clientes SET comprobantePagoRuta = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);

            if ($stmt === false) {
                echo json_encode(['error' => 'Error en la preparación de la consulta SQL: ' . $conn->error]);
                exit();
            }

            $stmt->bind_param("ss", $comprobantePagoRuta, $id);

            if ($stmt->execute()) {
                // Retornar respuesta en JSON para ser manejada en el frontend
                echo json_encode(['success' => true, 'comprobantePagoRuta' => $comprobantePagoRuta]);
            } else {
                echo json_encode(['error' => 'Error al actualizar la base de datos: ' . $stmt->error]);
            }

            $stmt->close();
        } else {
            echo json_encode(['error' => 'Error al subir el archivo.']);
        }
    } else {
        echo json_encode(['error' => 'Error al recibir el archivo.']);
    }

    $conn->close();
}
?>
