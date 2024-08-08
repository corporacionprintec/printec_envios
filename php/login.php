<?php
session_start();
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

    $sql = "SELECT * FROM usuarios WHERE email = ? AND password = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        die("Error en la preparación de la declaración: " . $conn->error);
    }

    $stmt->bind_param("ss", $email, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $_SESSION['loggedin'] = true;
        $_SESSION['email'] = $email;
        header("Location: listado_envios.php");
        exit();
    } else {
        echo "Correo electrónico o contraseña incorrectos.";
    }

    $stmt->close();
}

$conn->close();
?>
