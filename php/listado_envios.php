<?php
// Verificar si hay un mensaje de éxito en la URL
if (isset($_GET['msg']) && $_GET['msg'] == 'success') {
    echo "<div style='color: green; text-align: center;'>El pedido ha sido eliminado con éxito.</div>";
}

// Configuración de la base de datos
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

// Construir la consulta SQL para obtener hasta 100,000 registros sin filtros adicionales
$sql = "SELECT item, id, nombre, telefono, estado, fecha_creacion FROM clientes ORDER BY item DESC LIMIT 100000";
$result = $conn->query($sql);

if (!$result) {
    die("Error en la consulta: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado de Envíos</title>
    <style>
        /* Estilos generales */
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
            position: relative;
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
        .copy-btn, .delete-btn, .duplicate-btn{
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
        .duplicate-btn{
            background-color: #3346ff;
        }
        .guardar-contactos-btn {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 6px 12px;
            cursor: pointer;
            border-radius: 5px;
            font-size: 14px;
            position: absolute;
            top: 20px;
            right: 20px;
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
            .copy-btn, .delete-btn, .guardar-contactos-btn {
                font-size: 12px;
                padding: 8px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Listado de Envíos</h1>
        <!-- Botón para guardar todos los contactos -->
        <a href="guardar_todos_contactos.php" class="guardar-contactos-btn">Guardar Todos los Contactos</a>

        <!-- Buscador -->
        <input type="text" id="buscador" onkeyup="filtrarTabla()" placeholder="Buscar por nombre..." style="margin-bottom: 20px; padding: 10px; width: 100%; border: 1px solid #ddd; border-radius: 5px;">

        <table id="tablaEnvios">
            <thead>
                <tr>
                    <th>Items</th>
                    <th>Nombre</th>
                    <th>Estado</th>
                    <th>Fecha de Creación</th>
                    <th>Ver Detalles</th>
                    <th>Ver Pedido</th>
                    <th>Eliminar</th>
                    <th>Duplicar</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $item = $row['item'];
                        $nombre = $row['nombre'];
                        $estado = $row['estado'];
                        $fechaCreacion = $row['fecha_creacion'];

                        // URL para ver detalles
                        $urlVerDetalles = "https://printecenvios-production.up.railway.app/ver_pedido.html?item=" . $item;

                        // URL de confirmación generada dinámicamente usando el campo 'id'
                        $urlConfirmacion = "https://printecenvios-production.up.railway.app/confirmacion.html?id=" . $row['id'];

                        // Definir clase de estilo según el estado
                        $estadoClass = strtolower($estado) == 'pendiente' ? 'pendiente' : (strtolower($estado) == 'enviado' ? 'enviado' : '');

                        echo "<tr>";
                        echo "<td>" . $item . "</td>";
                        echo "<td>" . $nombre . "</td>";
                        echo "<td class='$estadoClass'>" . ucfirst($estado) . "</td>";
                        echo "<td>" . $fechaCreacion . "</td>";
                        // Botón para ver detalles
                        echo '<td><a href="' . $urlVerDetalles . '" class="copy-btn">Ver Detalles</a></td>';
                        
                        // Botón para ver pedido
                        echo '<td><a href="' . $urlConfirmacion . '" class="copy-btn">Ver Pedido</a></td>';
                        
                        // Botón para eliminar pedido
                        echo '<td><button class="delete-btn" onclick="eliminarPedido(' . $item . ')">Eliminar</button></td>';

                        // Botón para duplicar un los datos de un pedido
                        echo '<td><button class="duplicate-btn" onclick="duplicarPedido(' . $item . ')">Duplicar</button></td>';

                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='7'>No hay envíos</td></tr>";
                }

                $conn->close();
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

        function duplicarPedido(item) {
            if (confirm("¿Deseas duplicar este pedido?")) {
                var form = document.createElement('form');
                form.method = 'POST';
                form.action = 'duplicar_pedido.php';

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
    </script>
</body>
</html>
