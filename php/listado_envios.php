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
            max-width: 800px;
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
            background-color: #007bff;
            color: white;
            border: none;
            padding: 8px 12px;
            cursor: pointer;
            border-radius: 5px;
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
        /* Ocultar la columna de Fecha de Creación */
        th:nth-child(4), td:nth-child(4) {
            display: none;
        }
        /* Media queries para ajustar el diseño en pantallas pequeñas */
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
    <script>
        // Funciones JavaScript aquí
    </script>
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
                <!-- Otras opciones aquí -->
            </select>
        </form>

        <table id="tablaEnvios">
            <thead>
                <tr>
                    <th>Items</th>
                    <th>Nombre</th>
                    <th>Estado</th>
                    <th>Fecha de Creación</th> <!-- Mantén la columna aquí -->
                    <th>Acción</th>
                    <th>Copiar Enlace</th>
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
                        $fecha_creacion = $row['fecha_creacion']; // Mantén esta variable

                        // URL para ver detalles
                        $urlVerDetalles = "https://printecenvios-production.up.railway.app/ver_pedido.html?item=" . $item;

                        // URL de confirmación generada dinámicamente usando el campo 'id'
                        $urlConfirmacion = "https://printecenvios-production.up.railway.app/confirmacion.html?id=" . $row['id'];

                        // Definir clase de estilo según el estado
                        $estadoClass = strtolower($estado) == 'pendiente' ? 'pendiente' : (strtolower($estado) == 'enviado' ? 'enviado' : '');

                        echo "<tr>";
                        echo "<td>" . $item . "</td>";
                        echo "<td>" . $nombre . "</td>";
                        echo '<td class="' . $estadoClass . '">' . $estado . '</td>'; // Aplicar clase al estado
                        echo "<td>" . $fecha_creacion . "</td>"; // Mantén la fecha aquí
                        echo '<td><a href="' . $urlVerDetalles . '">Ver Detalles</a></td>';
                        echo '<td><input type="hidden" id="link_' . $item . '" value="' . $urlConfirmacion . '">
                        <button class="copy-btn" onclick="copyToClipboard(\'link_' . $item . '\')">Copiar enlace</button></td>';
                        echo '<td><button class="delete-btn" onclick="eliminarPedido(' . $item . ')">Eliminar</button></td>';
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='7'>No hay envíos</td></tr>";
                }

                // Cerrar la conexión a la base de datos
                if ($conn !== null && $conn->connect_error == null) {
                    $conn->close();
                }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>
