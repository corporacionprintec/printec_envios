<?php
require 'vendor/autoload.php'; // Incluye la librería de Google API

session_start(); // Necesario para manejar las sesiones en PHP

function obtenerClienteGoogle() {
    $client = new Google_Client();
    $client->setAuthConfig('client_secret_200770608361-vn8spggg4bfoj3chlg2diaqducnib9eq.apps.googleusercontent.com.json');
    $client->setRedirectUri('http://localhost/subir_comprobante_drive.php'); // Cambiar según tu URL
    $client->addScope(Google_Service_Drive::DRIVE_FILE);
    $client->setAccessType('offline');
    $client->setPrompt('select_account consent');

    // Verificar si ya tenemos un token guardado en sesión
    if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
        $client->setAccessToken($_SESSION['access_token']);
    } else {
        if (!isset($_GET['code'])) {
            // Si no tenemos el código de autenticación, generamos la URL para autenticarse con Google
            $authUrl = $client->createAuthUrl();
            header('Location: ' . filter_var($authUrl, FILTER_SANITIZE_URL));
            exit;
        } else {
            // Intercambiamos el código por un token de acceso
            $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
            $_SESSION['access_token'] = $token;
            $client->setAccessToken($token);
        }
    }

    // Si el token ha expirado, lo refrescamos
    if ($client->isAccessTokenExpired()) {
        $refreshTokenSaved = $client->getRefreshToken();
        $client->fetchAccessTokenWithRefreshToken($refreshTokenSaved);
        $_SESSION['access_token'] = $client->getAccessToken();
    }

    return $client;
}

function subirArchivo($nombreArchivo, $rutaArchivo, $tipoMime) {
    $client = obtenerClienteGoogle();
    $service = new Google_Service_Drive($client);

    $fileMetadata = new Google_Service_Drive_DriveFile(array(
        'name' => $nombreArchivo,
        'parents' => array("ID_DE_TU_CARPETA_EN_DRIVE") // Cambia por el ID de la carpeta destino
    ));

    $content = file_get_contents($rutaArchivo);

    $file = $service->files->create($fileMetadata, array(
        'data' => $content,
        'mimeType' => $tipoMime,
        'uploadType' => 'multipart',
        'fields' => 'id'
    ));

    printf("Archivo subido con ID: %s\n", $file->id);
}

// Comprobamos si se ha enviado el formulario de subida
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['comprobanteEnvio'])) {
    $nombreArchivo = $_FILES['comprobanteEnvio']['name'];
    $rutaArchivo = $_FILES['comprobanteEnvio']['tmp_name'];
    $tipoMime = $_FILES['comprobanteEnvio']['type'];

    subirArchivo($nombreArchivo, $rutaArchivo, $tipoMime);
}
?>
