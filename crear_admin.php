<?php
/** @var PDO $pdo */
require_once __DIR__ . '/conexion.php';

// Datos del administrador
$nombre  = 'Admin Principal';
$email   = 'admin@vet.local';
$usuario = 'admin';
$pass    = 'AdminSegura@2025';

try {

    // 1️⃣ Verificar si el usuario ya existe
    $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE usuario = :usuario");
    $stmt->execute(['usuario' => $usuario]);

    if ($stmt->fetch()) {
        die("El usuario administrador '{$usuario}' ya existe.");
    }

    // 2️⃣ Hashear la contraseña
    $hash = password_hash($pass, PASSWORD_DEFAULT);

    // 3️⃣ Insertar usuario ADMIN
    $stmt = $pdo->prepare(
        "INSERT INTO usuarios (nombre, email, usuario, password_hash, rol, activo)
         VALUES (:nombre, :email, :usuario, :hash, 'ADMIN', 1)"
    );

    $stmt->execute([
        'nombre'  => $nombre,
        'email'   => $email,
        'usuario' => $usuario,
        'hash'    => $hash,
    ]);

    echo "✅ Administrador creado correctamente.<br>Usuario: <strong>{$usuario}</strong><br>Clave: <strong>{$pass}</strong>";

} catch (PDOException $e) {
    echo "❌ Error al crear administrador: " . htmlspecialchars($e->getMessage());
}

