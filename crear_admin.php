<?php
require_once 'conexion.php';

// Datos del administrador
$nombre  = 'Admin Principal';
$email   = 'admin@vet.local';
$usuario = 'admin';
$pass    = 'AdminSegura@2025';

// 1️⃣ Verificar si el usuario ya existe
$stmt = $con->prepare("SELECT id FROM usuarios WHERE usuario = ?");
$stmt->bind_param("s", $usuario);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    die("El usuario administrador '{$usuario}' ya existe.");
}

// 2️⃣ Hashear la contraseña
$hash = password_hash($pass, PASSWORD_DEFAULT);

// 3️⃣ Insertar usuario ADMIN
$stmt = $con->prepare(
    "INSERT INTO usuarios (nombre, email, usuario, password_hash, rol, activo) 
     VALUES (?, ?, ?, ?, 'ADMIN', 1)"
);
$stmt->bind_param("ssss", $nombre, $email, $usuario, $hash);

if ($stmt->execute()) {
    echo "✅ Administrador creado correctamente.<br>Usuario: <strong>{$usuario}</strong><br>Clave: <strong>{$pass}</strong>";
} else {
    echo "❌ Error al crear administrador: " . htmlspecialchars($stmt->error);
}

