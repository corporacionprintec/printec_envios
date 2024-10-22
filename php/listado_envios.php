<?php
// Verificar si hay un mensaje de éxito en la URL
if (isset($_GET['msg']) && $_GET['msg'] == 'success') {
    echo "<div style='color: green; text-align: center;'>El pedido ha sido eliminado con éxito.</div>";
}

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

// Obtener el límite de registros seleccionados por el usuario (si no se selecciona, el valor por defecto es 10)
$limit = isset($_GET['limit']) ? intval($_GET['limit']) : 10;

// Consulta SQL con límite de resultados según la selección
$sql = "SELECT item, id, nombre, telefono, estado FROM clientes ORDER BY item DESC LIMIT $limit";
$result = $conn->query($sql);

if (!$result) {
    die("Error en la consulta: " . $conn->error);
}

// Función para crear el archivo VCF con todos los contactos
function generarVCF($clientes) {
    $filename = "todos_los_contactos.vcf";
    $contenidoVCF = "";

    foreach ($clientes as $cliente) {
        // Generar el contenido del archivo VCF por cada cliente
        $contenidoVCF .= "BEGIN:VCARD\r\n";
        $contenidoVCF .= "VERSION:3.0\r\n";
        $contenidoVCF .= "FN:" . $cliente['nombre'] . "\r\n";
        $contenidoVCF .= "TEL;TYPE=CELL:" . $cliente['telefono'] . "\r\n";
        $contenidoVCF .= "END:VCARD\r\n";
    }

    // Crear el archivo VCF y forzar su descarga
    header('Content-Type: text/vcard');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    echo $contenidoVCF;
}

// Si se solicita guardar todos los contactos
if (isset($_GET['guardar_todos_contactos'])) {
    $clientes = [];

    // Obtener todos los clientes de la consulta
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $clientes[] = ['nombre' => $row['nombre'], 'telefono' => $row['telefono']];
        }
    }

    generarVCF($clientes);
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado de Envíos</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
        }
        .container {
            margin: 50px auto;
            max-width: 1000px;
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #007bff;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        a {
            color: #007bff;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
        .copy-btn, .delete-btn {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 8px;
            cursor: pointer;
            border-radius: 5px;
            text-align: center;
            display: inline-block;
            width: 100%;
            margin-bottom: 5px;
        }
        .delete-btn {
            background-color: #dc3545;
        }
        .btn-group {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        /* Ocultar columna de teléfono */
        .telefono-col {
            display: none;
        }

        /* Estilos para el estado pendiente y enviado */
        .pendiente {
            color: red;
            font-weight: bold;
        }
        .enviado {
            color: green;
            font-weight: bold;
        }

        /* Media queries para pantallas pequeñas */
        @media (max-width: 768px) {
            .container {
                padding: 10px;
                max-width: 95%;
            }
            table {
                width: 100%;
                display: block;
                overflow-x: auto;
                white-space: nowrap;
            }
            th, td {
                font-size: 14px;
                padding: 10px;
            }
        }

        @media (max-width: 480px) {
            th, td {
                font-size: 12px;
                padding: 8px;
            }
            .copy-btn {
                padding: 6px 10px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Listado de Envíos</h1>

        <!-- Botón para guardar todos los contactos -->
        <form method="GET" action="">
            <input type="hidden" name="guardar_todos_contactos" value="1">
            <button type="submit" class="copy-btn">Guardar Todos los Contactos</button>
        </form>

        <!-- Buscador -->
        <input type="text" id="buscador" onkeyup="filtrarTabla()" placeholder="Buscar por nombre..." style="margin-bottom: 20px; padding: 10px; width: 100%; border: 1px solid #ddd; border-radius: 5px;">

        <!-- Filtro de cantidad de registros a mostrar -->
        <form method="GET" action="">
            <label for="limit">Mostrar:</label>
            <select name="limit" id="limit" onchange="this.form.submit()">
                <option value="10" <?php echo $limit == 10 ? 'selected' : ''; ?>>10</option>
                <option value="20" <?php echo $limit == 20 ? 'selected' : ''; ?>>20</option>
                <option value="50" <?php echo $limit == 50 ? 'selected' : ''; ?>>50</option>
                <option value="100" <?php echo $limit == 100 ? 'selected' : ''; ?>>100</option>
                <option value="1000" <?php echo $limit == 1000 ? 'selected' : ''; ?>>1000</option>
                <option value="10000" <?php echo $limit == 10000 ? 'selected' : ''; ?>>10000</option>
            </select>
        </form>

        <table id="tablaEnvios">
            <thead>
                <tr>
                    <th>Items</th>
                    <th>Nombre</th>
                    <th class="telefono-col">Teléfono</th> <!-- Oculto -->
                    <th>Estado</th>
                    <th>Ver Detalles</th>
                    <th>Ver Pedido</th>
                    <th>Eliminar</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $item = $row['item']; 
                        $nombre = $row['nombre'];
                        $telefono = $row['telefono'];
                        $estado = $row['estado'];

                        // URL para ver detalles
                        $urlVerDetalles = "https://printecenvios-production.up.railway.app/ver_pedido.html?item=" . $item;

                        // URL de confirmación generada dinámicamente usando el campo 'id'
                        $urlConfirmacion = "https://printecenvios-production.up.railway.app/confirmacion.html?id=" . $row['id'];

                        // Definir clase de estilo según el estado
                        $estadoClass = strtolower($estado) == 'pendiente' ? 'pendiente' : (strtolower($estado) == 'enviado' ? 'enviado' : '');

                        echo "<tr>";
                        echo "<td>" . $item . "</td>";
                        echo "<td>" . $nombre . "</td>";
                        echo "<td class='telefono-col'>" . $telefono . "</td>"; // Oculto
                        echo '<td class="' . $estadoClass . '">' . $estado . '</td>';

                        // Botón para ver detalles
                        echo '<td><a href="' . $urlVerDetalles . '" class="copy-btn">Ver Detalles</a></td>';
                        
                        // Botón para ver pedido
                        echo '<td><a href="' . $urlConfirmacion . '" class="copy-btn">Ver Pedido</a></td>';
                        
                        // Botón para eliminar pedido
                        echo '<td><button class="delete-btn" onclick="eliminarPedido(' . $item . ')">Eliminar</button></td>';
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='8'>No hay envíos</td></tr>";
                }

                // Cerrar la conexión a la base de datos
                if ($conn !== null && $conn->connect_error == null) {
                    $conn->close();
                }
                ?>
            </tbody>
        </table>
    </div>

    <script>
        // Función para confirmar y eliminar un pedido usando el campo item
        function eliminarPedido(item) {
            if (confirm("¿Estás seguro de eliminar este pedido?")) {
                var form = document.createElement('form');
                form.method = 'POST';
                form.action = 'eliminar_pedido.php';

                var input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'item';
                input.value = item;

                form.appendChild(input);
                document.body.appendChild(form);
                form.submit();
            }
        }

        // Función para filtrar la tabla por nombre
        function filtrarTabla() {
            var input = document.getElementById("buscador");
            var filtro = input.value.toLowerCase();
            var table = document.getElementById("tablaEnvios");
            var tr = table.getElementsByTagName("tr");

            for (var i = 1; i < tr.length; i++) { // Empezar en 1 para omitir el encabezado
                var tdNombre = tr[i].getElementsByTagName("td")[1]; // Columna de nombre
                if (tdNombre) {
                    var txtValue = tdNombre.textContent || tdNombre.innerText;
                    tr[i].style.display = txtValue.toLowerCase().indexOf(filtro) > -1 ? "" : "none";
                }
            }
        }
    </script>
</body>
</html>
