<?php
// conexion.php  (PDO para vet_citas)

$dsn    = 'mysql:host=localhost;dbname=vet_citas;charset=utf8mb4';
$dbUser = 'root';
$dbPass = '';

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Errores como excepciones
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Fetch por defecto como array asociativo
    PDO::ATTR_EMULATE_PREPARES   => false,                  // Preparación nativa
];

try {
    $pdo = new PDO($dsn, $dbUser, $dbPass, $options);
} catch (PDOException $e) {
    // En desarrollo puedes usar: die('Error: ' . $e->getMessage());
    die('Error de conexión a la base de datos.');
}
