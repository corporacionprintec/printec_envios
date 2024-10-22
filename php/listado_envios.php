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

// Función para crear el archivo VCF
function generarVCF($nombre, $telefono) {
    $telefono = preg_replace('/[^0-9]/', '', $telefono); // Asegurar que solo haya números
    $filename = str_replace(' ', '_', $nombre) . ".vcf"; // Nombre del archivo VCF
    $contenidoVCF = "BEGIN:VCARD\r\nVERSION:3.0\r\nFN:$nombre\r\nTEL;TYPE=CELL:$telefono\r\nEND:VCARD\r\n";
    
    // Forzar la descarga del archivo
    header('Content-Type: text/vcard');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    echo $contenidoVCF;
}

if (isset($_GET['guardar_contacto'])) {
    $nombre = $_GET['nombre'];
    $telefono = $_GET['telefono'];
    generarVCF($nombre, $telefono);
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
            background-color: #17a2b8;
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
        .copy-btn {
            width: 100%;
        }
        /* Botón Guardar Todos los Contactos */
        .save-all-btn {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 5px 10px; /* Tamaño reducido */
            cursor: pointer;
            border-radius: 5px;
            font-size: 14px; /* Tamaño de fuente más pequeño */
            display: block;
            margin: 0 auto 20px auto;
            width: auto;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
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
                overflow-x: auto; /* Permitir desplazamiento horizontal */
                white-space: nowrap;
            }
            th, td {
                font-size: 14px;
                padding: 10px;
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
        <button class="save-all-btn" onclick="guardarTodosLosContactos()">Guardar Todos los Contactos</button>

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
                        $estado = $row['estado'];

                        $urlVerDetalles = "https://printecenvios-production.up.railway.app/ver_pedido.html?item=" . $item;
                        $urlConfirmacion = "https://printecenvios-production.up.railway.app/confirmacion.html?id=" . $row['id'];
                        $estadoClass = strtolower($estado) == 'pendiente' ? 'pendiente' : (strtolower($estado) == 'enviado' ? 'enviado' : '');

                        echo "<tr>";
                        echo "<td>" . $item . "</td>";
                        echo "<td>" . $nombre . "</td>";
                        echo '<td class="' . $estadoClass . '">' . $estado . '</td>';
                        echo '<td><a href="' . $urlVerDetalles . '" class="copy-btn">Ver Detalles</a></td>';
                        echo '<td><a href="' . $urlConfirmacion . '" class="copy-btn">Ver Pedido</a></td>';
                        echo '<td><button class="delete-btn" onclick="eliminarPedido(' . $item . ')">Eliminar</button></td>';
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='6'>No hay envíos</td></tr>";
                }

                if ($conn !== null && $conn->connect_error == null) {
                    $conn->close();
                }
                ?>
            </tbody>
        </table>
    </div>

    <script>
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

        function filtrarTabla() {
            var input = document.getElementById("buscador");
            var filtro = input.value.toLowerCase();
            var table = document.getElementById("tablaEnvios");
            var tr = table.getElementsByTagName("tr");

            for (var i = 1; i < tr.length; i++) {
                var tdNombre = tr[i].getElementsByTagName("td")[1];
                if (tdNombre) {
                    var txtValue = tdNombre.textContent || tdNombre.innerText;
                    tr[i].style.display = txtValue.toLowerCase().indexOf(filtro) > -1 ? "" : "none";
                }
            }
        }

        function guardarTodosLosContactos() {
            <?php
            $sqlContactos = "SELECT nombre, telefono FROM clientes";
            $resultContactos = $conn->query($sqlContactos);

            if ($resultContactos->num_rows > 0) {
                while ($contacto = $resultContactos->fetch_assoc()) {
                    echo "window.location.href = 'listado_envios.php?guardar_contacto=1&nombre=" . urlencode($contacto['nombre']) . "&telefono=" . urlencode($contacto['telefono']) . "';";
                }
            }
            ?>
        }
    </script>
</body>
</html>
