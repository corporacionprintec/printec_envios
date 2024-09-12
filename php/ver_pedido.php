<?php
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

// Obtener el ID del pedido desde la URL
$item_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($item_id > 0) {
    // Preparar la consulta
    $sql = "SELECT * FROM clientes WHERE item = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $item_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $pedido = $result->fetch_assoc();
    } else {
        $pedido = null;
    }

    $stmt->close();
} else {
    $pedido = null;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalles del Pedido</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
        }
        .container {
            margin: 50px auto;
            max-width: 800px;
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        h1 {
            color: #333;
            font-size: 24px;
            margin-bottom: 20px;
        }
        .details {
            margin: 20px 0;
            border: 1px solid #ddd;
            padding: 20px;
            border-radius: 10px;
            text-align: left;
        }
        .details p {
            margin: 10px 0;
            font-size: 16px;
            color: #555;
        }
        .details p strong {
            color: #333;
        }
        .details a {
            color: #007bff;
            text-decoration: none;
        }
        .details a:hover {
            text-decoration: underline;
        }
        .details .highlight {
            background-color: #f1f1f1;
            padding: 10px;
            border-radius: 5px;
        }
        .logo {
            width: 150px;
            margin-bottom: 20px;
        }
        .input-group {
            margin: 20px 0;
            text-align: left;
        }
        .input-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .input-group input {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .btn {
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        .btn:disabled {
            background-color: #ccc;
            cursor: not-allowed;
        }
    </style>
    <script>
        function enableButton() {
            var comprobanteEnvio = document.getElementById('comprobanteEnvio').files.length > 0;
            var claveEnvio = document.getElementById('claveEnvio').value;
            var enviarButton = document.getElementById('enviarButton');

            if (comprobanteEnvio && claveEnvio) {
                enviarButton.disabled = false;
            } else {
                enviarButton.disabled = true;
            }
        }

        // Función para imprimir el membrete
        function printMembrete() {
            var printWindow = window.open('', '', 'height=600,width=800');
            var printDocument = printWindow.document;

            printDocument.open();
            printDocument.write('<html><head><title>Imprimir Membrete</title>');
            printDocument.write('<style>');
            printDocument.write('body { font-family: Arial, sans-serif; margin: 20px; padding: 0; }');
            printDocument.write('h1 { font-size: 32px; margin-bottom: 20px; }');
            printDocument.write('p { font-size: 27px; margin: 10px 0; line-height: 1.5; }');
            printDocument.write('.container { max-width: 800px; margin: 0 auto; }');
            printDocument.write('.logo { width: 200px; display: block; margin: 0 auto 20px; }');
            printDocument.write('.remitente { padding: 10px; border-radius: 5px; margin-bottom: 20px; }');
            printDocument.write('.destinatario { color: #dc3545; padding: 10px; border-radius: 5px; }'); // Rojo
            printDocument.write('</style>');
            printDocument.write('</head><body>');

            // Logo de la empresa (URL absoluta) con un evento onerror
            printDocument.write('<img src="https://printecenvios-production.up.railway.app/php/images_ventas/images.png" alt="Logo de la empresa" class="logo" onerror="alert(\'Error al cargar el logo\')">');

            // Datos del Remitente
            printDocument.write('<div class="container remitente">');
            printDocument.write('<h1>Datos del Remitente</h1>');
            printDocument.write('<p><strong>De:</strong> Eyter Yoel Rojas Sanchez</p>');
            printDocument.write('<p><strong>Dirección:</strong> Calle Huanuco N201 Ica-Ica-Ica</p>');
            printDocument.write('<p><strong>Cel:</strong> 966177851</p>');
            printDocument.write('<p><strong>RUC/DNI:</strong> 75532635</p>');
            printDocument.write('</div>');

            // Datos del Destinatario
            printDocument.write('<div class="container destinatario">');
            printDocument.write('<h1>Datos del Destinatario</h1>');
            printDocument.write('<p><strong>Nombre:</strong> ' + document.getElementById('nombre').innerText + '</p>');
            printDocument.write('<p><strong>DNI:</strong> ' + document.getElementById('dni').innerText + '</p>');
            printDocument.write('<p><strong>Teléfono:</strong> ' + document.getElementById('telefono').innerText + '</p>');
            printDocument.write('<p><strong>Tipo de Envío:</strong> ' + document.getElementById('tipoEnvio').innerText + '</p>');
            printDocument.write('<p><strong>Dirección:</strong> ' + document.getElementById('direccion').innerText + '</p>');
            printDocument.write('<p><strong>Agencia:</strong> ' + document.getElementById('agencia').innerText + '</p>');

            printDocument.write('</div>');
            printDocument.write('</body></html>');
            printDocument.close();

            printWindow.print();
        }
    </script>
</head>
<body>
    <div class="container">
        <!-- Logo de la empresa -->
        <img src="https://printecenvios-production.up.railway.app/php/images_ventas/images.png" alt="Logo de la empresa" class="logo">
        <h1>Detalles del Pedido</h1>
        <?php if ($pedido): ?>
            <div class="details">
                <p class="highlight"><strong>ID:</strong> <?php echo htmlspecialchars($pedido['item']); ?></p>
                <p><strong>Nombre:</strong> <span id="nombre"><?php echo htmlspecialchars($pedido['nombre']); ?></span></p>
                <p><strong>DNI:</strong> <span id="dni"><?php echo htmlspecialchars($pedido['dni']); ?></span></p>
                <p><strong>Teléfono:</strong> <span id="telefono"><?php echo htmlspecialchars($pedido['telefono']); ?></span></p>
                <p><strong>Tipo de Envío:</strong> <span id="tipoEnvio"><?php echo htmlspecialchars($pedido['envio']); ?></span></p>
                <p><strong>Dirección:</strong> <span id="direccion"><?php echo htmlspecialchars($pedido['direccion']); ?></span></p>
                <p><strong>Agencia:</strong> <span id="agencia"><?php echo htmlspecialchars($pedido['agencia']); ?></span></p>
                <p><strong>Estado:</strong> <?php echo htmlspecialchars($pedido['estado']); ?></p>
                
                <?php if ($pedido['compraMantenimiento'] == 'compra'): ?>
                    <p><strong>Productos:</strong> <?php echo htmlspecialchars($pedido['productos']); ?></p>
                <?php elseif ($pedido['compraMantenimiento'] == 'mantenimiento'): ?>
                    <p><strong>Producto a Realizar Mantenimiento:</strong> <?php echo htmlspecialchars($pedido['productoMantenimiento']); ?></p>
                    <p><strong>Detalle del Mantenimiento:</strong> <?php echo isset($pedido['razonMantenimiento']) ? htmlspecialchars($pedido['razonMantenimiento']) : 'No especificado'; ?></p>
                <?php endif; ?>

                <p><strong>Comprobante de Pago:</strong> 
                    <?php if ($pedido['comprobantePagoRuta']): ?>
                        <a href="/php/uploads/<?php echo htmlspecialchars($pedido['comprobantePagoRuta']); ?>" target="_blank">Ver Comprobante de Pago</a>
                    <?php else: ?>
                        No disponible
                    <?php endif; ?>
                </p>
            </div>

            <!-- Formulario para enviar comprobante de envío y clave de envío -->
            <form action="procesar_envio.php" method="post" enctype="multipart/form-data">
                <div class="input-group">
                    <label for="comprobanteEnvio">Enviar Comprobante de Envío</label>
                    <input type="file" id="comprobanteEnvio" name="comprobanteEnvio" accept="image/*" onchange="enableButton()">
                </div>
                <div class="input-group">
                    <label for="claveEnvio">Clave de Envío</label>
                    <input type="text" id="claveEnvio" name="claveEnvio" onkeyup="enableButton()">
                </div>
                <input type="hidden" name="item" value="<?php echo htmlspecialchars($pedido['item']); ?>">
                <button type="submit" id="enviarButton" class="btn" disabled>Enviar</button>
            </form>

            <!-- Botón para imprimir el membrete -->
            <button type="button" onclick="printMembrete()">Imprimir Membrete</button>
        <?php else: ?>
            <p>Pedido no encontrado.</p>
        <?php endif; ?>
    </div>
</body>
</html>
