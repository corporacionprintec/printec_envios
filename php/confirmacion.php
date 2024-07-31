<?php
session_start(); // Inicia la sesión

// Verificar si los datos del cliente están en la sesión
if (!isset($_SESSION['cliente'])) {
    echo "No se encontraron datos del cliente.";
    exit();
}

$cliente = $_SESSION['cliente'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmación de Pedido</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
        }
        .whatsapp-icon {
            position: fixed;
            bottom: 20px;
            right: 20px;
        }
        .whatsapp-icon img {
            width: 50px;
            height: 50px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Confirmación de Pedido</h1>
        <p><strong>Nombre y Apellido:</strong> <?php echo htmlspecialchars($cliente['nombre']); ?></p>
        <p><strong>DNI:</strong> <?php echo htmlspecialchars($cliente['dni']); ?></p>
        <p><strong>Teléfono:</strong> <?php echo htmlspecialchars($cliente['telefono']); ?></p>
        <p><strong>Tipo de Envío:</strong> <?php echo htmlspecialchars($cliente['envio']); ?></p>
        <p><strong>Dirección:</strong> <?php echo htmlspecialchars($cliente['direccion']); ?></p>
        <p><strong>Agencia:</strong> <?php echo htmlspecialchars($cliente['agencia']); ?></p>
        <p><strong>Compra o Mantenimiento:</strong> <?php echo htmlspecialchars($cliente['compraMantenimiento']); ?></p>

        <?php if ($cliente['compraMantenimiento'] === 'compra'): ?>
            <p><strong>Productos:</strong> <?php echo htmlspecialchars($cliente['productos']); ?></p>
        <?php elseif ($cliente['compraMantenimiento'] === 'mantenimiento'): ?>
            <p><strong>Producto a Realizar Mantenimiento:</strong> <?php echo htmlspecialchars($cliente['productoMantenimiento']); ?></p>
            <p><strong>Detalle del Mantenimiento:</strong> <?php echo htmlspecialchars($cliente['detalleMantenimiento']); ?></p>
        <?php endif; ?>

        <p><strong>Comprobante de Pago:</strong> <a href="<?php echo htmlspecialchars($cliente['comprobantePagoRuta']); ?>" target="_blank">Ver comprobante</a></p>

        <p><strong>Estado del Pedido:</strong> Pendiente</p>
    </div>

    <!-- Icono de WhatsApp -->
    <div class="whatsapp-icon">
        <a href="https://wa.me/966177851" target="_blank" title="En caso de alguna equivocación en sus datos, comuníquese con nosotros">
        <img src="images_ventas/whatsapp.png" alt="whatsapp">
        </a>
    </div>
</body>
</html>
