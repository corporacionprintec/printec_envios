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
    $envio = $_POST['envio'];
    $direccion = isset($_POST['direccion']) ? $_POST['direccion'] : '';
    $agencia = isset($_POST['agencia']) ? $_POST['agencia'] : '';
    $compraMantenimiento = $_POST['compraMantenimiento'];
    $productos = isset($_POST['productos']) ? $_POST['productos'] : '';
    $detalleMantenimiento = isset($_POST['detalleMantenimiento']) ? $_POST['detalleMantenimiento'] : '';
    $productoMantenimiento = isset($_POST['productoMantenimiento']) ? $_POST['productoMantenimiento'] : '';

    // Subir comprobante de pago
    if (isset($_FILES['comprobantePago']) && $_FILES['comprobantePago']['error'] == UPLOAD_ERR_OK) {
        $tmp_name = $_FILES['comprobantePago']['tmp_name'];
        $comprobantePago = basename($_FILES['comprobantePago']['name']);
        move_uploaded_file($tmp_name, "uploads/$comprobantePago");
    } else {
        $comprobantePago = '';
    }

    // Generar UUID v4
    function generateUUIDv4() {
        $data = random_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
        
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
    $uuid = generateUUIDv4();

    // Insertar datos en la base de datos
    $sql = "INSERT INTO clientes (id, nombre, dni, telefono, envio, direccion, agencia, compraMantenimiento, productos, detalleMantenimiento, productoMantenimiento, comprobantePago)
            VALUES ('$uuid', '$nombre', '$dni', '$telefono', '$envio', '$direccion', '$agencia', '$compraMantenimiento', '$productos', '$detalleMantenimiento', '$productoMantenimiento', '$comprobantePago')";

    if ($conn->query($sql) === TRUE) {
        // Redirigir a confirmacion.php con UUID en la URL
        header("Location: confirmacion.php?id=$uuid");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $conn->close();
}
?>
