<?php
session_start(); // Inicia la sesión

// Datos de la conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "envios_clientes";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Obtener datos del formulario
$nombre = isset($_POST['nombre']) ? $_POST['nombre'] : 'No definido';
$dni = isset($_POST['dni']) ? $_POST['dni'] : 'No definido';
$envio = isset($_POST['envio']) ? $_POST['envio'] : 'No definido';
$direccion = isset($_POST['direccion']) ? $_POST['direccion'] : 'No definido';
$agencia = isset($_POST['agencia']) ? $_POST['agencia'] : 'No definido';
$compraMantenimiento = isset($_POST['compraMantenimiento']) ? $_POST['compraMantenimiento'] : 'No definido';
$productos = isset($_POST['productos']) ? $_POST['productos'] : 'No definido';
$productoMantenimiento = isset($_POST['productoMantenimiento']) ? $_POST['productoMantenimiento'] : 'No definido';
$detalleMantenimiento = isset($_POST['detalleMantenimiento']) ? $_POST['detalleMantenimiento'] : 'No definido';
$comprobantePago = isset($_FILES['comprobantePago']) ? $_FILES['comprobantePago'] : null;

// Verificar que el archivo se haya subido correctamente
if ($comprobantePago && $comprobantePago['error'] === UPLOAD_ERR_OK) {
    // Crear el directorio uploads si no existe
    if (!file_exists('uploads')) {
        mkdir('uploads', 0777, true);
    }

    // Procesar la imagen del comprobante de pago
    $comprobantePagoRuta = 'uploads/' . basename($comprobantePago['name']);
    if (!move_uploaded_file($comprobantePago['tmp_name'], $comprobantePagoRuta)) {
        die("Error al mover el archivo.");
    }
} else {
    die("Error al subir el archivo: " . ($comprobantePago ? $comprobantePago['error'] : 'Archivo no enviado'));
}

// Preparar y verificar la consulta
$query = "INSERT INTO clientes (nombre, dni, envio, direccion, agencia, compraMantenimiento, productos, productoMantenimiento, detalleMantenimiento, comprobantePagoRuta) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($query);

if (!$stmt) {
    die("Error en la preparación de la consulta: " . $conn->error);
}

// Bind y ejecutar la consulta
$stmt->bind_param("ssssssssss", $nombre, $dni, $envio, $direccion, $agencia, $compraMantenimiento, $productos, $productoMantenimiento, $detalleMantenimiento, $comprobantePagoRuta);

if ($stmt->execute()) {
    // Almacenar los datos en la sesión
    $_SESSION['cliente'] = array(
        'nombre' => $nombre,
        'dni' => $dni,
        'envio' => $envio,
        'direccion' => $direccion,
        'agencia' => $agencia,
        'compraMantenimiento' => $compraMantenimiento,
        'productos' => $productos,
        'productoMantenimiento' => $productoMantenimiento,
        'detalleMantenimiento' => $detalleMantenimiento,
        'comprobantePagoRuta' => $comprobantePagoRuta
    );
    
    // Redirigir a la página de confirmación
    header('Location: confirmacion.php');
    exit();
} else {
    echo "Error: " . $stmt->error;
}

// Cerrar la conexión
$stmt->close();
$conn->close();
?>
