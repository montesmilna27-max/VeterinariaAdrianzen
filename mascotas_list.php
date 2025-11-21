<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/conexion.php';

// Solo ADMIN y RECEPCION verán este módulo
require_role(['ADMIN', 'RECEPCION']);

// CONSULTA PDO
$stmt = $pdo->prepare("
    SELECT m.id, m.nombre, m.especie, m.raza, m.fecha_nac, m.creado_en,
           c.nombre AS cliente
    FROM mascotas m
    INNER JOIN clientes c ON m.cliente_id = c.id
    ORDER BY m.creado_en DESC
");
$stmt->execute();
$mascotas = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mascotas - VetCitas</title>
    <style>
        body { font-family: Arial, sans-serif; background:#fafafa; margin:0; }
        header {
            background:#00796b; color:#fff; padding:10px 20px;
            display:flex; justify-content:space-between; align-items:center;
        }
        header a { color:#fff; text-decoration:none; margin-left:15px; }
        main { padding:20px; max-width:900px; margin:auto; }
        table { width:100%; border-collapse:collapse; background:#fff; margin-top:15px; }
        th, td { border:1px solid #ddd; padding:8px; font-size:.9em; }
        th { background:#e0f2f1; text-align:left; }
        .btn {
            padding:6px 10px; border:none; border-radius:4px;
            background:#00796b; color:#fff; text-decoration:none;
        }
        .btn:hover { background:#006054; }
    </style>
</head>
<body>
<header>
    <div>
        <strong>VetCitas</strong>
        <span style="font-size:.9em;opacity:.8;">[<?php echo htmlspecialchars($_SESSION['user_rol']); ?>]</span>
    </div>
    <div>
        <?php echo htmlspecialchars($_SESSION['user_name']); ?>
        <a href="dashboard.php">Inicio</a>
        <a href="clientes_list.php">Clientes</a>
        <a href="logout.php">Cerrar sesión</a>
    </div>
</header>

<main>
    <h1>Mascotas</h1>

    <p>
        <a href="mascota_nueva.php" class="btn">+ Nueva mascota</a>
    </p>

    <table>
        <thead>
        <tr>
            <th>ID</th>
            <th>Mascota</th>
            <th>Especie</th>
            <th>Raza</th>
            <th>Fecha nac.</th>
            <th>Cliente</th>
            <th>Creado</th>
        </tr>
        </thead>
        <tbody>
        <?php if (!empty($mascotas)): ?>
            <?php foreach ($mascotas as $row): ?>
                <tr>
                    <td><?= (int)$row['id'] ?></td>
                    <td><?= htmlspecialchars($row['nombre']) ?></td>
                    <td><?= htmlspecialchars($row['especie']) ?></td>
                    <td><?= htmlspecialchars($row['raza']) ?></td>
                    <td><?= htmlspecialchars($row['fecha_nac']) ?></td>
                    <td><?= htmlspecialchars($row['cliente']) ?></td>
                    <td><?= htmlspecialchars($row['creado_en']) ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="7">No hay mascotas registradas.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</main>
</body>
</html>
