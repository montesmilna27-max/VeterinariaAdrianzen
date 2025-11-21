<?php
require_once __DIR__ . '/includes/auth.php';
require_role(['ADMIN','RECEPCION','VET']);
require_once __DIR__ . '/conexion.php';

/** @var PDO $pdo */

try {
    $stmt = $pdo->query("
        SELECT ct.id, ct.fecha_hora, ct.estado, ct.motivo,
               m.nombre AS mascota,
               c.nombre AS cliente,
               u.nombre AS vet
        FROM citas ct
        JOIN mascotas m ON ct.mascota_id = m.id
        JOIN clientes c ON m.cliente_id = c.id
        JOIN usuarios u ON ct.vet_id = u.id
        ORDER BY ct.fecha_hora DESC
    ");

    /** @var array<int, array{
        id:int|string,
        fecha_hora:string,
        estado:string,
        motivo:string,
        mascota:string,
        cliente:string,
        vet:string
    }> $citas */
    $citas = $stmt ? $stmt->fetchAll() : [];

} catch (PDOException $e) {
    die('Error al obtener las citas.');
}
?>
<?php include __DIR__ . '/includes/header.php'; ?>

<main style="padding:20px;">
<h1>Citas</h1>

<a href="cita_nueva.php">Nueva cita</a>

<table border="1" width="100%">
<tr>
    <th>ID</th><th>Fecha</th><th>Hora</th>
    <th>Cliente</th><th>Mascota</th><th>Veterinario</th>
    <th>Motivo</th><th>Estado</th>
</tr>

<?php foreach ($citas as $row): ?>
    <?php
        /** @var array{fecha_hora:string} $row */
        $fechaHora = (string)$row['fecha_hora'];
        $fh = strtotime($fechaHora);
        $fecha = date('Y-m-d', $fh);
        $hora  = date('H:i', $fh);
    ?>
    <tr>
        <td><?= (int)$row['id'] ?></td>
        <td><?= $fecha ?></td>
        <td><?= $hora ?></td>
        <td><?= htmlspecialchars((string)$row['cliente']) ?></td>
        <td><?= htmlspecialchars((string)$row['mascota']) ?></td>
        <td><?= htmlspecialchars((string)$row['vet']) ?></td>
        <td><?= htmlspecialchars((string)$row['motivo']) ?></td>
        <td><?= htmlspecialchars((string)$row['estado']) ?></td>
    </tr>
<?php endforeach; ?>
</table>
</main>
