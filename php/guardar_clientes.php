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

    // Insertar datos en la base de datos
    $sql = "INSERT INTO clientes (nombre, dni, telefono, envio, direccion, agencia, compraMantenimiento, productos, detalleMantenimiento, productoMantenimiento, comprobantePago)
            VALUES ('$nombre', '$dni', '$telefono', '$envio', '$direccion', '$agencia', '$compraMantenimiento', '$productos', '$detalleMantenimiento', '$productoMantenimiento', '$comprobantePago')";

    if ($conn->query($sql) === TRUE) {
        // Guardar los datos en la sesión
        $_SESSION['cliente'] = [
            'nombre' => $nombre,
            'dni' => $dni,
            'telefono' => $telefono,
            'envio' => $envio,
            'direccion' => $direccion,
            'agencia' => $agencia,
            'compraMantenimiento' => $compraMantenimiento,
            'productos' => $productos,
            'detalleMantenimiento' => $detalleMantenimiento,
            'productoMantenimiento' => $productoMantenimiento,
            'comprobantePago' => $comprobantePago,
            'comprobantePagoRuta' => "uploads/$comprobantePago" // Ruta para mostrar en confirmacion.php
        ];

        // Redirigir a confirmacion.php
        header("Location: confirmacion.php");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $conn->close();
}
?>
