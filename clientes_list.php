<?php
require_once __DIR__ . '/includes/auth.php';
require_role(['ADMIN', 'RECEPCION']);
require_once __DIR__ . '/conexion.php';

/** @var PDO $pdo */

$busqueda = trim($_GET['q'] ?? '');

try {
    if ($busqueda !== '') {
        $like = "%{$busqueda}%";
        $stmt = $pdo->prepare("
            SELECT id, nombre, telefono, email, direccion, creado_en
            FROM clientes
            WHERE nombre LIKE :like 
               OR telefono LIKE :like 
               OR email LIKE :like
            ORDER BY creado_en DESC
        ");
        $stmt->execute(['like' => $like]);
    } else {
        $stmt = $pdo->query("
            SELECT id, nombre, telefono, email, direccion, creado_en
            FROM clientes
            ORDER BY creado_en DESC
        ");
    }

    /** @var array<int, array{id:int|string, nombre:string, telefono:string, email:string, direccion:string, creado_en:string}> $clientes */
    $clientes = $stmt ? $stmt->fetchAll() : [];

} catch (PDOException $e) {
    die('Error al obtener clientes.');
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Clientes - VetCitas</title>
</head>
<body>
<h1>Clientes</h1>

<form method="get">
    <input type="text" name="q" value="<?= htmlspecialchars($busqueda) ?>">
    <button>Buscar</button>
</form>

<table>
    <tr>
        <th>ID</th><th>Nombre</th><th>Teléfono</th><th>Email</th><th>Dirección</th><th>Creado</th>
    </tr>

    <?php if ($clientes): ?>
        <?php foreach ($clientes as $row): ?>
            <?php /** @var array{id:int|string} $row */ ?>
            <tr>
                <td><?= (int)$row['id'] ?></td>
                <td><?= htmlspecialchars((string)$row['nombre']) ?></td>
                <td><?= htmlspecialchars((string)$row['telefono']) ?></td>
                <td><?= htmlspecialchars((string)$row['email']) ?></td>
                <td><?= htmlspecialchars((string)$row['direccion']) ?></td>
                <td><?= htmlspecialchars((string)$row['creado_en']) ?></td>
            </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr><td colspan="6">No hay clientes registrados.</td></tr>
    <?php endif; ?>
</table>
</body>
</html>


