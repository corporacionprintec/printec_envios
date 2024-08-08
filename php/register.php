<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "trabajadores";  // Asegúrate de usar la base de datos correcta

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "INSERT INTO usuarios (email, password) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $email, $password);

    if ($stmt->execute()) {
        header("Location: ../login.html");
        exit();
    } else {
        echo "Error al registrar el usuario.";
    }

    $stmt->close();
}

$conn->close();
?>
