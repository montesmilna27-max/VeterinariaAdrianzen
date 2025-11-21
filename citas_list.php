<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/conexion.php';

require_role(['ADMIN','RECEPCION','VET']);

try {
    $sql = "
        SELECT ct.id,
               ct.fecha_hora,
               ct.estado,
               ct.motivo,
               m.nombre   AS mascota,
               c.nombre   AS cliente,
               u.nombre   AS vet
        FROM citas ct
        JOIN mascotas m ON ct.mascota_id = m.id
        JOIN clientes c ON m.cliente_id = c.id
        JOIN usuarios u ON ct.vet_id = u.id
        ORDER BY ct.fecha_hora DESC
    ";

    $stmt = $conn->query($sql);
    $citas = $stmt->fetchAll(); // PDO::FETCH_ASSOC por defecto

} catch (PDOException $e) {
    die('Error al obtener las citas.');
}

?>
<?php include __DIR__ . '/includes/header.php'; ?>

<main style="padding:20px;">
    <h1>Citas</h1>

    <p>
        <a href="cita_nueva.php"
           style="background:#00796b;color:#fff;padding:8px 14px;border-radius:4px;text-decoration:none;">
           + Nueva cita
        </a>
    </p>

    <table border="1" cellpadding="8" cellspacing="0" width="100%">
        <tr style="background:#e0f2f1;">
            <th>ID</th>
            <th>Fecha</th>
            <th>Hora</th>
            <th>Cliente</th>
            <th>Mascota</th>
            <th>Veterinario</th>
            <th>Motivo</th>
            <th>Estado</th>
        </tr>

        <?php if (!empty($citas)): ?>
            <?php foreach($citas as $row): 
                $fh = strtotime($row['fecha_hora']);
                $fecha = date('Y-m-d', $fh);
                $hora  = date('H:i', $fh);
            ?>
                <tr>
                    <td><?= (int)$row['id'] ?></td>
                    <td><?= $fecha ?></td>
                    <td><?= $hora ?></td>
                    <td><?= htmlspecialchars($row['cliente']) ?></td>
                    <td><?= htmlspecialchars($row['mascota']) ?></td>
                    <td><?= htmlspecialchars($row['vet']) ?></td>
                    <td><?= htmlspecialchars($row['motivo']) ?></td>
                    <td><?= htmlspecialchars($row['estado']) ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="8">No hay citas registradas.</td>
            </tr>
        <?php endif; ?>
    </table>
</main>
