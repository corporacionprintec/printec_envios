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

    if ($conn->connect_error) {
        die("Conexión fallida: " . $conn->connect_error);
    }

    // Obtener datos del formulario
    $nombre = $_POST['nombre'];
    $dni = $_POST['dni'];
    $telefono = $_POST['telefono'];
    $envio = $_POST['tipoRetiro'];
    $direccion = $_POST['direccion'];
    $agencia = $_POST['agencia'];
    $compraMantenimiento = $_POST['motivoEnvio'];
    $productos = isset($_POST['productos']) ? $_POST['productos'] : '';
    $productoMantenimiento = isset($_POST['productoMantenimiento']) ? $_POST['productoMantenimiento'] : '';
    $razonMantenimiento = isset($_POST['razonMantenimiento']) ? $_POST['razonMantenimiento'] : '';

    // Generar UUID v4
    function generateUUIDv4() {
        $data = random_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // set version to 4
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // set variant to RFC 4122
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }

    $uuid = generateUUIDv4();

    // Procesar archivo de comprobante de pago
    $comprobantePago = NULL; // Inicializar como NULL
    if (isset($_FILES['comprobantePago']) && $_FILES['comprobantePago']['error'] == UPLOAD_ERR_OK) {
        // Validar el tamaño del archivo (por ejemplo, 5 MB máximo)
        if ($_FILES['comprobantePago']['size'] > 5 * 1024 * 1024) {
            die("El archivo de comprobante de pago es demasiado grande.");
        }

        // Leer el contenido del archivo
        $comprobantePago = file_get_contents($_FILES['comprobantePago']['tmp_name']);
    }

    // Ajuste en la consulta y vinculación
    $sql = "INSERT INTO clientes (
        id, 
        nombre, 
        dni, 
        telefono, 
        envio, 
        direccion, 
        agencia, 
        compraMantenimiento, 
        productos, 
        productoMantenimiento, 
        razonMantenimiento, 
        comprobantePago, 
        fecha_creacion
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";

    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        die('Error en la preparación de la consulta SQL: ' . $conn->error);
    }
    // Ajusta el número de variables para que coincida
    $stmt->bind_param(
        "ssssssssssss", // 12 parámetros
        $uuid, 
        $nombre, 
        $dni, 
        $telefono, 
        $envio, 
        $direccion, 
        $agencia, 
        $compraMantenimiento, 
        $productos, 
        $productoMantenimiento, 
        $razonMantenimiento, 
        $comprobantePago
    );

    // Ejecuta la consulta
    if ($stmt->execute()) {
        header("Location: /confirmacion.html?id=" . urlencode($uuid));
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
