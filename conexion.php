<?php
// conexion.php  (para vet_citas)

$dsn    = 'mysql:host=localhost;dbname=vet_citas;charset=utf8mb4';
$dbUser = 'root';
$dbPass = '';

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $conn = new PDO($dsn, $dbUser, $dbPass, $options);
} catch (PDOException $e) {
    // En producción NO muestres el mensaje real
    die('Error de conexión a la base de datos.');
}

