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

    // Obtener datos del formulario
    $nombre = $_POST['nombre'];
    $dni = $_POST['dni'];
    $telefono = $_POST['telefono'];
    $envio = $_POST['tipoRetiro']; // Aquí cambia "envio" por "tipoRetiro"
    $direccion = $_POST['direccion'];
    $agencia = $_POST['agencia'];
    $compraMantenimiento = $_POST['motivoEnvio']; // Aquí cambia "compraMantenimiento" por "motivoEnvio"
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

    // Subir archivo de comprobante de pago
    $comprobantePagoRuta = '';
    if (isset($_FILES['comprobantePago']) && $_FILES['comprobantePago']['error'] == UPLOAD_ERR_OK) {
        $target_dir = __DIR__ . "/../uploads/";
        $target_file = $target_dir . basename($_FILES["comprobantePago"]["name"]);
        if (move_uploaded_file($_FILES["comprobantePago"]["tmp_name"], $target_file)) {
            $comprobantePagoRuta = basename($_FILES["comprobantePago"]["name"]);
        } else {
            echo "Error al subir el archivo.";
        }
    }

    // Insertar datos en la base de datos
    $sql = "INSERT INTO clientes (id, nombre, dni, telefono, envio, direccion, agencia, compraMantenimiento, productos, productoMantenimiento, detalleMantenimiento, comprobantePagoRuta, fecha_creacion) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
    $stmt = $conn->prepare($sql);

    // Verificar si la preparación fue exitosa
    if ($stmt === false) {
        die("Error en la consulta SQL: " . $conn->error);
    }

    $stmt->bind_param("ssssssssssss", $uuid, $nombre, $dni, $telefono, $envio, $direccion, $agencia, $compraMantenimiento, $productos, $productoMantenimiento, $detalleMantenimiento, $comprobantePagoRuta);

    // Ejecutar la consulta
    if ($stmt->execute()) {
        // Redirigir a la página de confirmación
        header("Location: /printec/confirmacion.html?id=" . urlencode($uuid));
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
