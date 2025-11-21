<?php
require_once __DIR__ . '/includes/auth.php';
require_role(['ADMIN', 'RECEPCION']);

/** @var mysqli $con */
require_once __DIR__ . '/conexion.php';

// Inicializamos variables
$cliente_id = 0;
$nombre = $especie = $raza = $fecha_nac = $notas = '';

$clientes = $con->query("SELECT id, nombre FROM clientes ORDER BY nombre ASC");

$errores = [];
$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cliente_id = (int)($_POST['cliente_id'] ?? 0);
    $nombre     = trim($_POST['nombre'] ?? '');
    $especie    = trim($_POST['especie'] ?? '');
    $raza       = trim($_POST['raza'] ?? '');
    $fecha_nac  = trim($_POST['fecha_nac'] ?? '');
    $notas      = trim($_POST['notas'] ?? '');

    if ($cliente_id <= 0) $errores[] = 'Debe seleccionar un cliente.';
    if ($nombre === '') $errores[] = 'El nombre de


