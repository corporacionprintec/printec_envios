<?php

require 'vendor/autoload.php';

use Google\Client;
use Google\Service\Drive;

// Ruta del archivo JSON de credenciales
$credentialsPath = 'client_secret_200770608361-vn8spggg4bfoj3chlg2diaqducnib9eq.apps.googleusercontent.com.json';

// Verificar si el archivo fue enviado
if (!isset($_FILES['comprobanteEnvio']) || $_FILES['comprobanteEnvio']['error'] !== UPLOAD_ERR_OK) {
    die('Error al subir el archivo.');
}

// Iniciar el cliente de Google
$client = new Client();
$client->setAuthConfig($credentialsPath);
$client->addScope(Drive::DRIVE_FILE);
$client->setAccessType('offline');

// Redirigir al usuario para autorización OAuth si es necesario
if (!isset($_SESSION['access_token']) && !isset($_GET['code'])) {
    $authUrl = $client->createAuthUrl();
    header('Location: ' . filter_var($authUrl, FILTER_SANITIZE_URL));
    exit();
}

// Intercambiar código por token de acceso
if (isset($_GET['code'])) {
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    $_SESSION['access_token'] = $token;
} else {
    $client->setAccessToken($_SESSION['access_token']);
}

// Subir el archivo a Google Drive
$driveService = new Drive($client);

// Crear archivo en Drive
$fileMetadata = new Drive\File([
    'name' => $_FILES['comprobanteEnvio']['name'],
    'parents' => ['1t3ueZ7y0cAy4jhAyZCrzPkuse3O2lLkw'] // ID de la carpeta de destino en Google Drive
]);

$content = file_get_contents($_FILES['comprobanteEnvio']['tmp_name']);
$file = $driveService->files->create($fileMetadata, [
    'data' => $content,
    'mimeType' => $_FILES['comprobanteEnvio']['type'],
    'uploadType' => 'multipart',
    'fields' => 'id'
]);

// Mostrar el ID del archivo subido
echo 'Archivo subido con éxito. ID del archivo: ' . $file->id;

