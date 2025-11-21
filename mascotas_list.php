<?php
/** @var mysqli $con */
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/conexion.php';

// Solo ADMIN y RECEPCION verán este módulo
require_role(['ADMIN', 'RECEPCION']);

$sql = "SELECT m.id, m.nombre, m.especie, m.raza, m.fecha_nac, m.creado_en,
               c.nombre AS cliente
        FROM mascotas m
        INNER JOIN clientes c ON m.cliente_id = c.id
        ORDER BY m.creado_en DESC";

$result = $con->query($sql);
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Mascotas - VetCitas</title>
<style>
/* ... tu CSS ... */
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
    <p><a href="mascota_nueva.php" class="btn">+ Nueva mascota</a></p>
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
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo (int)$row['id']; ?></td>
                    <td><?php echo htmlspecialchars($row['nombre']); ?></td>
                    <td><?php echo htmlspecialchars($row['especie']); ?></td>
                    <td><?php echo htmlspecialchars($row['raza']); ?></td>
                    <td><?php echo htmlspecialchars($row['fecha_nac']); ?></td>
                    <td><?php echo htmlspecia
