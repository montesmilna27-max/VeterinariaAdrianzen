<?php
/** @var PDO $pdo */
require_once __DIR__ . '/includes/auth.php';
require_role(['ADMIN','RECEPCION']);
require_once __DIR__ . '/conexion.php';

// Bloquear acceso directo
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: citas_list.php");
    exit;
}

// --------------------
// Sanitización
// --------------------
$cliente_id = (int)($_POST['cliente_id'] ?? 0);
$mascota_id = (int)($_POST['mascota_id'] ?? 0);
$vet_id     = (int)($_POST['vet_id'] ?? 0);
$fecha      = trim($_POST['fecha'] ?? '');
$hora       = trim($_POST['hora'] ?? '');
$motivo     = trim($_POST['motivo'] ?? '');
$creada_por = (int)($_SESSION['user_id'] ?? 0);

$errores = [];

// --------------------
// Validaciones
// --------------------
if ($cliente_id <= 0)  $errores[] = "Debe seleccionar un cliente.";
if ($mascota_id <= 0)  $errores[] = "Debe seleccionar una mascota.";
if ($vet_id <= 0)      $errores[] = "Debe seleccionar un veterinario.";
if ($fecha === '' || $hora === '') $errores[] = "Debe indicar fecha y hora.";
if ($motivo === '')    $errores[] = "Debe indicar el motivo de la cita.";

$fecha_hora = $fecha . ' ' . $hora . ':00';

// Validación de formato datetime
$dt = DateTime::createFromFormat('Y-m-d H:i:s', $fecha_hora);
if (!$dt || $dt->format('Y-m-d H:i:s') !== $fecha_hora) {
    $errores[] = "Fecha u hora no válidas.";
}

// --------------------
// Validar disponibilidad del veterinario
// --------------------
if (empty($errores)) {
    $sql = "
        SELECT COUNT(*) AS total
        FROM citas
        WHERE vet_id = :vet_id
          AND fecha_hora = :fecha_hora
          AND estado IN ('PENDIENTE','CONFIRMADA')
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'vet_id'     => $vet_id,
        'fecha_hora' => $fecha_hora
    ]);

    $row = $stmt->fetch();

    if ($row && (int)$row['total'] > 0) {
        $errores[] = "El veterinario ya tiene una cita en esa fecha y hora.";
    }
}

// --------------------
// Mostrar errores
// --------------------
if (!empty($errores)) {
    echo "<h3>Errores al registrar la cita</h3>";
    foreach ($errores as $e) {
        echo htmlspecialchars($e) . "<br>";
    }
    echo '<br><a href="cita_nueva.php">Volver</a>';
    exit;
}

// --------------------
// Insertar cita
// --------------------
$sql = "
    INSERT INTO citas (mascota_id, vet_id, fecha_hora, motivo, creada_por)
    VALUES (:mascota_id, :vet_id, :fecha_hora, :motivo, :creada_por)
";

$stmt = $pdo->prepare($sql);
$stmt->execute([
    'mascota_id' => $mascota_id,
    'vet_id'     => $vet_id,
    'fecha_hora' => $fecha_hora,
    'motivo'     => $motivo,
    'creada_por' => $creada_por
]);

header("Location: citas_list.php");
exit;

