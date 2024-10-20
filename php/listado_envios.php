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
        .btn-group {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }
        .contact-btn {
            background-color: #28a745; /* Verde para Guardar Contacto */
            color: white;
            border: none;
            padding: 10px 16px; /* Más grande */
            cursor: pointer;
            border-radius: 8px; /* Bordes más redondeados */
            text-align: center;
            display: inline-block;
            width: 100%;
            margin-bottom: 5px;
            font-size: 16px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1); /* Sombra para darle más énfasis */
        }
        .copy-btn, .delete-btn {
            padding: 8px;
            background-color: #17a2b8;
            color: white;
            border: none;
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
                overflow-x: auto; /* Permitir desplazamiento horizontal en pantallas pequeñas */
                white-space: nowrap; /* Mantener las filas en una sola línea en móviles */
            }
            th, td {
                font-size: 14px;
                padding: 10px;
            }
            .contact-btn, .copy-btn, .delete-btn {
                font-size: 14px;
                padding: 10px;
            }
        }

        @media (max-width: 480px) {
            th, td {
                font-size: 12px;
                padding: 8px;
            }
            .contact-btn, .copy-btn, .delete-btn {
                font-size: 12px;
                padding: 8px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Listado de Envíos</h1>

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
                    <th>Teléfono</th>
                    <th>Estado</th>
                    <th>Guardar Contacto</th>
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
                        echo "<td>" . $telefono . "</td>";
                        echo '<td class="' . $estadoClass . '">' . $estado . '</td>'; // Aplicar clase al estado

                        // Botón para guardar contacto
                        echo '<td><a href="listado_envios.php?guardar_contacto=1&nombre=' . urlencode($nombre) . '&telefono=' . urlencode($telefono) . '" class="contact-btn">Guardar Contacto</a></td>';

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
