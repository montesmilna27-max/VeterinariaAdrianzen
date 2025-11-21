<?php
require_once __DIR__ . '/conexion.php';

// Datos del veterinario
$nombre  = 'Dr. Tony';
$email   = 'vet1@vet.local';
$usuario = 'vet1';
$clave   = 'VetSeguro@2025';
$rol     = 'VET';

// 1️⃣ Verificar si ya existe el usuario
$stmt = $con->prepare("SELECT id FROM usuarios WHERE usuario = ?");
$stmt->bind_param("s", $usuario);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    die("El usuario veterinario '{$usuario}' ya existe.");
}

// 2️⃣ Hashear la contraseña
$password_hash = password_hash($clave, PASSWORD_DEFAULT);

// 3️⃣ Insertar usuario
$stmt = $con->prepare(
    "INSERT INTO usuarios (nombre, email, usuario, password_hash, rol, activo)
     VALUES (?, ?, ?, ?, ?, 1)"
);
$stmt->bind_param("sssss", $nombre, $email, $usuario, $password_hash, $rol);

if ($stmt->execute()) {
    echo "✅ Veterinario creado correctamente.<br>Usuario: <strong>{$usuario}</strong><br>Clave: <strong>{$clave}</strong>";
} else {
    echo "❌ Error al crear veterinario: " . htmlspecialchars($stmt->error);
}

