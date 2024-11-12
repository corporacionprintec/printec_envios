<?php
session_start();

// Configuración de la conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "nombre_base_datos";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Verificar si el archivo fue enviado
if (!isset($_FILES['comprobanteEnvio']) || $_FILES['comprobanteEnvio']['error'] !== UPLOAD_ERR_OK) {
    die('Error al subir el archivo.');
}

// Crear carpeta 'uploads' si no existe
$uploadDir = 'uploads/';
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

// Ruta completa donde se guardará el archivo
$uploadFile = $uploadDir . basename($_FILES['comprobanteEnvio']['name']);

// Mover el archivo a la carpeta 'uploads'
if (move_uploaded_file($_FILES['comprobanteEnvio']['tmp_name'], $uploadFile)) {
    echo "Archivo subido con éxito.<br>";
    
    // Insertar la ruta del archivo en la base de datos
    $rutaComprobante = $conn->real_escape_string($uploadFile);
    $sql = "INSERT INTO tu_tabla (comprobanteEnvioRuta) VALUES ('$rutaComprobante')";

    if ($conn->query($sql) === TRUE) {
        echo "Ruta guardada en la base de datos exitosamente.";
    } else {
        echo "Error al guardar en la base de datos: " . $conn->error;
    }
} else {
    echo "Error al mover el archivo.";
}

// Cerrar conexión
$conn->close();
?>
