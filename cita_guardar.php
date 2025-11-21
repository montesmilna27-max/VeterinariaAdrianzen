<?php
require_once __DIR__ . '/includes/auth.php';
require_role(['ADMIN','RECEPCION']);
require_once __DIR__ . '/conexion.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: citas_list.php");
    exit;
}

$cliente_id = (int)($_POST['cliente_id'] ?? 0);   // solo para validación
$mascota_id = (int)($_POST['mascota_id'] ?? 0);
$vet_id     = (int)($_POST['vet_id'] ?? 0);
$fecha      = trim($_POST['fecha'] ?? '');
$hora       = trim($_POST['hora'] ?? '');
$motivo     = trim($_POST['motivo'] ?? '');
$creada_por = (int)$_SESSION['user_id'];

$errores = [];

// Validaciones básicas
if ($cliente_id <= 0) $errores[] = "Debe seleccionar un cliente.";
if ($mascota_id <= 0) $errores[] = "Debe seleccionar una mascota.";
if ($vet_id <= 0) $errores[] = "Debe seleccionar un veterinario.";
if ($fecha === '' || $hora === '') $errores[] = "Debe indicar fecha y hora.";
if ($motivo === '') $errores[] = "Debe indicar el motivo de la cita.";

$fecha_hora = $fecha . ' ' . $hora . ':00';

// Validar formato fecha/hora
$d = DateTime::createFromFormat('Y-m-d H:i:s', $fecha_hora);
if (!$d || $d->format('Y-m-d H:i:s') !== $fecha_hora) {
    $errores[] = "Fecha u hora no válidas.";
}

// ¿Ya existe cita para ese vet en esa fecha/hora?
if (empty($errores)) {
    $stmt = $con->prepare("
        SELECT COUNT(*) AS total
        FROM citas
        WHERE vet_id = ?
          AND fecha_hora = ?
          AND estado IN ('PENDIENTE','CONFIRMADA')
    ");
    $stmt->bind_param("is", $vet_id, $fecha_hora);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_assoc();
    if ($res['total'] > 0) {
        $errores[] = "El veterinario ya tiene una cita en esa fecha y hora.";
    }
    $stmt->close();
}

if (!empty($errores)) {
    // Mostramos errores simple; luego se puede mejorar UX
    echo "<h3>Errores al registrar la cita</h3>";
    foreach ($errores as $e) {
        echo htmlspecialchars($e) . "<br>";
    }
    echo '<br><a href="cita_nueva.php">Volver</a>';
    exit;
}

// Insertar cita
$stmt = $con->prepare("
    INSERT INTO citas (mascota_id, vet_id, fecha_hora, motivo, creada_por)
    VALUES (?, ?, ?, ?, ?)
");
$stmt->bind_param("iissi", $mascota_id, $vet_id, $fecha_hora, $motivo, $creada_por);
$stmt->execute();
$stmt->close();

header("Location: citas_list.php");
exit;
