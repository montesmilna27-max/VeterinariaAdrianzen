<?php
require_once __DIR__ . '/conexion.php';

$nombre  = 'Dr. Tony';
$email   = 'vet1@vet.local';
$usuario = 'vet1';
$clave   = 'VetSeguro@2025';
$rol     = 'VET';

// Verificar si ya existe
$stmt = $con->prepare("SELECT id FROM usuarios WHERE usuario = ?");
$stmt->bind_param("s", $usuario);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    die("El usuario veterinario ya existe.");
}

$password_hash = password_hash($clave, PASSWORD_DEFAULT);

$stmt = $con->prepare(
    "INSERT INTO usuarios (nombre, email, usuario, password_hash, rol, activo)
     VALUES (?, ?, ?, ?, ?, 1)"
);
$stmt->bind_param("sssss", $nombre, $email, $usuario, $password_hash, $rol);

if ($stmt->execute()) {
    echo "Veterinario creado. Usuario: {$usuario} / Clave: {$clave}";
} else {
    echo "Error al crear veterinario: " . $stmt->error;
}
