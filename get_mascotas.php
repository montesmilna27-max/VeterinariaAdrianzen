<?php
// includes/get_mascotas.php
require_once __DIR__ . '/../conexion.php';

header('Content-Type: application/json; charset=utf-8');

// 🔹 Validar y tipar entrada
$cliente_id = isset($_GET['cliente_id']) ? (int)$_GET['cliente_id'] : 0;

if ($cliente_id <= 0) {
    echo json_encode([]);
    exit;
}

// 🔹 Preparar y ejecutar consulta de manera segura
$stmt = $con->prepare("SELECT id, nombre FROM mascotas WHERE cliente_id = ? ORDER BY nombre");
if ($stmt === false) {
    // Manejo básico de error
    echo json_encode([]);
    exit;
}

$stmt->bind_param("i", $cliente_id);
$stmt->execute();

$res = $stmt->get_result();
$mascotas = [];

if ($res !== false) {
    while ($row = $res->fetch_assoc()) {
        // Forzar tipos para seguridad
        $mascotas[] = [
            'id' => (int)($row['id'] ?? 0),
            'nombre' => (string)($row['nombre'] ?? ''),
        ];
    }
}

// 🔹 Devolver JSON
echo json_encode($mascotas, JSON_UNESCAPED_UNICODE);
