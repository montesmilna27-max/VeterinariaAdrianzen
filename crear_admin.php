<?php
require_once 'conexion.php';

$nombre  = 'Admin Principal';
$email   = 'admin@vet.local';
$usuario = 'admin';
$pass    = 'AdminSegura@2025';

$hash = password_hash($pass, PASSWORD_DEFAULT);

$stmt = $con->prepare(
  "INSERT INTO usuarios (nombre, email, usuario, password_hash, rol) 
   VALUES (?,?,?,?, 'ADMIN')"
);
$stmt->bind_param("ssss", $nombre, $email, $usuario, $hash);

if ($stmt->execute()) {
    echo "Admin creado. Usuario: admin / Clave: $pass";
} else {
    echo "Error: " . $con->error;
}
