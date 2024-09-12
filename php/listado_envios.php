<?php
// Conectar a la base de datos
$servername = getenv('DB_HOST') ?: 'localhost';
$username = getenv('DB_USER') ?: 'root';
$password = getenv('DB_PASS') ?: '';
$dbname = getenv('DB_NAME') ?: 'envios_clientes';
$port = getenv('DB_PORT') ?: '3306';

$conn = new mysqli($servername, $username, $password, $dbname, $port);

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Obtener el límite de registros seleccionados por el usuario (valor por defecto es 10)
$limit = isset($_GET['limit']) ? intval($_GET['limit']) : 10;

// Consulta SQL con límite de resultados según la selección
$sql = "SELECT item, id, nombre, estado, fecha_creacion FROM clientes ORDER BY item DESC LIMIT $limit";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado de Envíos</title>
    <style>
        /* Estilos para la tabla y los botones */
        body {
            font-family: Arial, sans-serif;
        }
        .container {
            margin: 50px auto;
            max-width: 800px;
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1);
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
        .pendiente {
            color: red;
            font-weight: bold;
        }
        .enviado {
            color: green;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Listado de Envíos</h1>

        <form method="GET" action="">
            <label for="limit">Mostrar:</label>
            <select name="limit" id="limit" onchange="this.form.submit()">
                <option value="10" <?php echo $limit == 10 ? 'selected' : ''; ?>>10</option>
                <option value="20" <?php echo $limit == 20 ? 'selected' : ''; ?>>20</option>
                <!-- Añadir más opciones si es necesario -->
            </select>
        </form>

        <table>
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Nombre</th>
                    <th>Estado</th>
                    <th>Fecha de Creación</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $item = $row['item']; 
                        $nombre = $row['nombre'];
                        $estado = $row['estado'];
                        $fecha_creacion = $row['fecha_creacion'];

                        // Definir clases de estado
                        $estadoClass = strtolower($estado) == 'pendiente' ? 'pendiente' : 'enviado';

                        echo "<tr>";
                        echo "<td>" . $item . "</td>";
                        echo "<td>" . $nombre . "</td>";
                        echo '<td class="' . $estadoClass . '">' . $estado . '</td>';
                        echo "<td>" . $fecha_creacion . "</td>";
                        // Enlace a ver los detalles del pedido, pasando el item como parámetro
                        echo '<td><a href="ver_pedido.html?id=' . $item . '">Ver Detalles</a></td>';
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='5'>No hay envíos</td></tr>";
                }

                // Cerrar la conexión
                $conn->close();
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>
