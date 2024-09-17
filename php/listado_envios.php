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
            max-width: 100%;
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            color: #333;
        }
        /* Contenedor de la tabla para permitir scroll horizontal en dispositivos móviles */
        .table-responsive {
            width: 100%;
            overflow-x: auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
            white-space: nowrap; /* Evitar que el texto se rompa en varias líneas */
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
        .copy-btn {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 8px 12px;
            cursor: pointer;
            border-radius: 5px;
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
        /* Estilos específicos para pantallas pequeñas (móviles) */
        @media screen and (max-width: 600px) {
            th, td {
                padding: 8px;
                font-size: 12px;
            }
            .copy-btn {
                padding: 6px 10px;
                font-size: 12px;
            }
        }
    </style>
    <script>
        // Función para copiar el enlace al portapapeles
        function copyToClipboard(id) {
            var copyText = document.getElementById(id);
            navigator.clipboard.writeText(copyText.value).then(() => {
                alert("Enlace copiado al portapapeles");
            }).catch(err => {
                console.error('Error al copiar al portapapeles: ', err);
            });
        }
    </script>
</head>
<body>
    <div class="container">
        <h1>Listado de Envíos</h1>

        <!-- Filtro de cantidad de registros a mostrar -->
        <form method="GET" action="">
            <label for="limit">Mostrar:</label>
            <select name="limit" id="limit" onchange="this.form.submit()">
                <option value="10" <?php echo $limit == 10 ? 'selected' : ''; ?>>10</option>
                <option value="20" <?php echo $limit == 20 ? 'selected' : ''; ?>>20</option>
                <option value="50" <?php echo $limit == 50 ? 'selected' : ''; ?>>50</option>
                <option value="100" <?php echo $limit == 100 ? 'selected' : ''; ?>>100</option>
                <option value="500" <?php echo $limit == 500 ? 'selected' : ''; ?>>500</option>
                <option value="1000" <?php echo $limit == 1000 ? 'selected' : ''; ?>>1000</option>
                <option value="10000" <?php echo $limit == 10000 ? 'selected' : ''; ?>>10,000</option>
            </select>
        </form>

        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Items</th>
                        <th>Nombre</th>
                        <th>Estado</th>
                        <th>Fecha de Creación</th> <!-- Nueva columna para la fecha -->
                        <th>Acción</th>
                        <th>Copiar Enlace</th> <!-- Nueva columna -->
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            $item = $row['item']; 
                            $id = $row['id']; 
                            $nombre = $row['nombre'];
                            $estado = $row['estado'];
                            $fecha_creacion = $row['fecha_creacion'];

                            // URL de confirmación generada dinámicamente usando el id
                            $urlConfirmacion = "https://printecenvios-production.up.railway.app/confirmacion.html?id=" . $id;

                            // Definir clase de estilo según el estado
                            $estadoClass = strtolower($estado) == 'pendiente' ? 'pendiente' : (strtolower($estado) == 'enviado' ? 'enviado' : '');

                            echo "<tr>";
                            echo "<td>" . $item . "</td>";
                            echo "<td>" . $nombre . "</td>";
                            echo '<td class="' . $estadoClass . '">' . $estado . '</td>'; // Aplicar clase al estado
                            echo "<td>" . $fecha_creacion . "</td>";
                            // Cambiar el enlace a ver_pedido.html con la URL correcta
                            echo '<td><a href="https://printecenvios-production.up.railway.app/ver_pedido.html?id=' . $item . '">Ver Detalles</a></td>';
                            echo '<td><input type="hidden" id="link_' . $id . '" value="' . $urlConfirmacion . '">
                            <button class="copy-btn" onclick="copyToClipboard(\'link_' . $id . '\')">Copiar enlace</button></td>';
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='6'>No hay envíos</td></tr>";
                    }

                    // Cerrar la conexión a la base de datos
                    if ($conn !== null && $conn->connect_error == null) {
                        $conn->close();
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
