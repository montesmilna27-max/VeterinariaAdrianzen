<?php
// includes/get_mascotas.php
require_once __DIR__ . '/../conexion.php';

header('Content-Type: application/json; charset=utf-8');

$cliente_id = isset($_GET['cliente_id']) ? (int)$_GET['cliente_id'] : 0;

if ($cliente_id <= 0) {
    echo json_encode([]);
    exit;
}

// OJO: tu tabla se llama "mascotas" y tiene campo "cliente_id"
$stmt = $con->prepare("SELECT id, nombre FROM mascotas WHERE cliente_id = ? ORDER BY nombre");
$stmt->bind_param("i", $cliente_id);
$stmt->execute();
$res = $stmt->get_result();

$mascotas = [];
while ($row = $res->fetch_assoc()) {
    $mascotas[] = $row;
}

echo json_encode($mascotas);
