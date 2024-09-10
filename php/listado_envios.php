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
    </style>
</head>
<body>
    <div class="container">
        <h1>Listado de Envíos</h1>
        <table>
            <thead>
                <tr>
                    <th>Items</th>
                    <th>Nombre</th>
                    <th>Estado</th>
                    <th>Acción</th>
                    <th>Enlace de Confirmación</th> <!-- Nueva columna -->
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $item = $row['item']; // Mantiene el item numérico en la columna
                        $id = $row['id']; // Usamos el id alfanumérico para el enlace
                        $nombre = $row['nombre'];
                        $estado = $row['estado'];

                        // URL de confirmación generada dinámicamente usando el id (uid)
                        $urlConfirmacion = "https://printecenvios-production.up.railway.app/confirmacion.html?id=" . $id;

                        echo "<tr>";
                        echo "<td>" . $item . "</td>"; // Se muestra el 'item' en la columna de Items
                        echo "<td>" . $nombre . "</td>";
                        echo "<td>" . $estado . "</td>";
                        echo '<td><a href="ver_pedido.php?id=' . $item . '">Ver Detalles</a></td>';
                        echo '<td><a href="' . $urlConfirmacion . '" target="_blank">' . $urlConfirmacion . '</a></td>'; // Enlace usando el id
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='5'>No hay envíos</td></tr>";
                }
                $conn->close();
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>
