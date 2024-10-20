<?php
// Obtener los datos del cliente desde la URL
$nombre = isset($_GET['nombre']) ? $_GET['nombre'] : 'Nombre desconocido';
$telefono = isset($_GET['telefono']) ? $_GET['telefono'] : '';

// Crear el contenido del vCard
$vcard = "BEGIN:VCARD\n";
$vcard .= "VERSION:3.0\n";
$vcard .= "FN:{$nombre}\n"; // Nombre completo
$vcard .= "TEL;TYPE=cell:{$telefono}\n"; // Teléfono
$vcard .= "END:VCARD\n";

// Configurar las cabeceras para la descarga del archivo
header('Content-Type: text/vcard');
header('Content-Disposition: attachment; filename="contacto.vcf"');

// Imprimir el contenido del vCard
echo $vcard;
?>
