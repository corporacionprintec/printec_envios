<?php
session_start(); // Inicia la sesión

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Conexión a la base de datos
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "envios_clientes";

    $conn = new mysqli($servername, $username, $password, $dbname);

    // Verificar conexión
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
    $detalleMantenimiento = isset($_POST['detalleMantenimiento']) ? $_POST['detalleMantenimiento'] : '';

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
        $target_dir = "uploads/";
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
    $stmt->bind_param("ssssssssssss", $uuid, $nombre, $dni, $telefono, $envio, $direccion, $agencia, $compraMantenimiento, $productos, $productoMantenimiento, $detalleMantenimiento, $comprobantePagoRuta);
    
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
