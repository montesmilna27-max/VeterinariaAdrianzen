<?php
require_once __DIR__ . '/includes/auth.php';
require_role(['ADMIN', 'RECEPCION']);

require_once __DIR__ . '/conexion.php';

$busqueda = trim($_GET['q'] ?? '');

if ($busqueda !== '') {
    $like = "%{$busqueda}%";
    $stmt = $con->prepare(
        "SELECT id, nombre, telefono, email, direccion, creado_en
         FROM clientes
         WHERE nombre LIKE ? OR telefono LIKE ? OR email LIKE ?
         ORDER BY creado_en DESC"
    );
    $stmt->bind_param("sss", $like, $like, $like);
} else {
    $stmt = $con->prepare(
        "SELECT id, nombre, telefono, email, direccion, creado_en
         FROM clientes
         ORDER BY creado_en DESC"
    );
}

$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Clientes - VetCitas</title>
    <style>
        body { font-family: Arial, sans-serif; background: #fafafa; }
        header {
            background: #00796b; color: #fff; padding: 10px 20px;
            display: flex; justify-content: space-between; align-items: center;
        }
        a { color: #fff; text-decoration: none; margin-left: 15px; }
        main { padding: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; background: #fff; }
        th, td { border: 1px solid #ddd; padding: 8px; font-size: 0.9em; }
        th { background: #e0f2f1; text-align: left; }
        .top-bar { display: flex; justify-content: space-between; align-items: center; }
        .top-bar form { display: flex; gap: 5px; }
        input[type=text] { padding: 5px; }
        .btn { padding: 6px 10px; border: none; border-radius: 4px; cursor: pointer; }
        .btn-primary { background: #00796b; color:#fff; }
        .btn-secondary { background: #ccc; color:#000; }
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
        <a href="logout.php">Cerrar sesión</a>
    </div>
</header>
<main>
    <div class="top-bar">
        <h1>Clientes</h1>
        <div>
            <form method="get" action="clientes_list.php">
                <input type="text" name="q" placeholder="Buscar..." value="<?php echo htmlspecialchars($busqueda); ?>">
                <button class="btn btn-secondary" type="submit">Buscar</button>
            </form>
        </div>
        <div>
            <a href="cliente_nuevo.php" class="btn btn-primary">+ Nuevo cliente</a>
        </div>
    </div>

    <table>
        <thead>
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Teléfono</th>
            <th>Email</th>
            <th>Dirección</th>
            <th>Creado</th>
        </tr>
        </thead>
        <tbody>
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo (int)$row['id']; ?></td>
                    <td><?php echo htmlspecialchars($row['nombre']); ?></td>
                    <td><?php echo htmlspecialchars($row['telefono']); ?></td>
                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                    <td><?php echo htmlspecialchars($row['direccion']); ?></td>
                    <td><?php echo htmlspecialchars($row['creado_en']); ?></td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="6">No hay clientes registrados.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</main>
</body>
</html>

