<?php
/** @var PDO $pdo */
require_once __DIR__ . '/includes/auth.php';
require_role(['ADMIN','RECEPCION']);
require_once __DIR__ . '/conexion.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: citas_list.php");
    exit;
}

$cliente_id = (int)($_POST['cliente_id'] ?? 0);
$mascota_id = (int)($_POST['mascota_id'] ?? 0);
$vet_id     = (int)($_POST['vet_id'] ?? 0);
$fecha      = trim($_POST['fecha'] ?? '');
$hora       = trim($_POST['hora'] ?? '');
$motivo     = trim($_POST['motivo'] ?? '');
$creada_por = (int)($_SESSION['user_id'] ?? 0);

$errores = [];

if ($cliente_id <= 0) $errores[] = "Debe seleccionar un cliente.";
if ($mascota_id <= 0) $errores[] = "Debe seleccionar una mascota.";
if ($vet_id <= 0)     $errores[] = "Debe seleccionar un veterinario.";
if ($fecha==='' || $hora==='') $errores[] = "Debe indicar fecha y hora.";
if ($motivo==='') $errores[] = "Debe indicar el motivo.";

$fecha_hora = $fecha.' '.$hora.':00';
$dt = DateTime::createFromFormat('Y-m-d H:i:s', $fecha_hora);
if (!$dt || $dt->format('Y-m-d H:i:s') !== $fecha_hora)
    $errores[] = "Fecha u hora no válidas.";

if (empty($errores)) {
    $stmt = $pdo->prepare("
        SELECT COUNT(*) AS total
        FROM citas
        WHERE vet_id = :vet_id AND fecha_hora = :fh
          AND estado IN ('PENDIENTE','CONFIRMADA')
    ");
    $stmt->execute(['vet_id'=>$vet_id,'fh'=>$fecha_hora]);

    /** @var array{total:int}|false $row */
    $row = $stmt->fetch();
    $total = $row !== false ? (int)$row['total'] : 0;

    if ($total > 0)
        $errores[] = "El veterinario ya tiene cita en esa fecha y hora.";
}

if ($errores) {
    echo "<h3>Errores</h3>";
    foreach($errores as $e) echo htmlspecialchars($e)."<br>";
    echo '<a href="cita_nueva.php">Volver</a>';
    exit;
}

$stmt = $pdo->prepare("
    INSERT INTO citas (mascota_id, vet_id, fecha_hora, motivo, creada_por)
    VALUES (:mascota_id, :vet_id, :fh, :motivo, :creada_por)
");
$stmt->execute([
    'mascota_id'=>$mascota_id,
    'vet_id'=>$vet_id,
    'fh'=>$fecha_hora,
    'motivo'=>$motivo,
    'creada_por'=>$creada_por
]);

header("Location: citas_list.php");
exit;

