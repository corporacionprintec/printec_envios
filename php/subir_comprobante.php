<?php
session_start(); // Inicia la sesión

// Conexión a la base de datos
$servername = getenv('DB_HOST') ?: 'localhost';
$username = getenv('DB_USER') ?: 'root';
$password = getenv('DB_PASS') ?: '';
$dbname = getenv('DB_NAME') ?: 'envios_clientes';
$port = getenv('DB_PORT') ?: '3306';

$conn = new mysqli($servername, $username, $password, $dbname, $port);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

$id = $_POST['id'];
$response = array("success" => false);

// Directorio donde se guardarán los archivos
$target_dir = __DIR__ . "/uploads/";

if (!file_exists($target_dir)) {
    mkdir($target_dir, 0777, true);
}

// Verificar si se subió el primer comprobante
if (isset($_FILES['comprobantePago']) && $_FILES['comprobantePago']['error'] == UPLOAD_ERR_OK) {
    $target_file = $target_dir . basename($_FILES["comprobantePago"]["name"]);

    if (move_uploaded_file($_FILES["comprobantePago"]["tmp_name"], $target_file)) {
        $comprobantePagoRuta = basename($_FILES["comprobantePago"]["name"]);

        // Actualizar la base de datos con la ruta del primer comprobante
        $sql = "UPDATE clientes SET comprobantePagoRuta = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);

        if ($stmt === false) {
            die('Error en la preparación de la consulta SQL: ' . $conn->error);
        }

        $stmt->bind_param("ss", $comprobantePagoRuta, $id);
        $stmt->execute();
        $stmt->close();

        $response["comprobantePagoRuta"] = $comprobantePagoRuta;
        $response["success"] = true;
    }
}

// Verificar si se subió el segundo comprobante
if (isset($_FILES['comprobantePago2']) && $_FILES['comprobantePago2']['error'] == UPLOAD_ERR_OK) {
    $target_file2 = $target_dir . basename($_FILES["comprobantePago2"]["name"]);

    if (move_uploaded_file($_FILES["comprobantePago2"]["tmp_name"], $target_file2)) {
        $comprobantePagoRuta2 = basename($_FILES["comprobantePago2"]["name"]);

        // Actualizar la base de datos con la ruta del segundo comprobante
        $sql = "UPDATE clientes SET comprobantePagoRuta2 = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);

        if ($stmt === false) {
            die('Error en la preparación de la consulta SQL: ' . $conn->error);
        }

        $stmt->bind_param("ss", $comprobantePagoRuta2, $id);
        $stmt->execute();
        $stmt->close();

        $response["comprobantePagoRuta2"] = $comprobantePagoRuta2;
        $response["success"] = true;
    }
}

$conn->close();

echo json_encode($response);
?>
