<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/conexion.php';

$userName = $_SESSION['user_name'] ?? 'Usuario';
$userRole = $_SESSION['user_role'] ?? 'ADMIN'; // ADMIN o VET
$userId   = $_SESSION['user_id']   ?? null;

// Inicializar stats
$stats = [
    'total_clientes'   => 0,
    'total_mascotas'   => 0,
    'citas_hoy'        => 0,
    'citas_pendientes' => 0,
];

// --- Estadísticas para ADMIN ---
if ($userRole === 'ADMIN') {
    $stats['total_clientes'] = (int)($conn->query("SELECT COUNT(*) AS total FROM clientes")->fetch()['total'] ?? 0);
    $stats['total_mascotas'] = (int)($conn->query("SELECT COUNT(*) AS total FROM mascotas")->fetch()['total'] ?? 0);
    $stats['citas_hoy']      = (int)($conn->query("SELECT COUNT(*) AS total FROM citas WHERE DATE(fecha_hora) = CURDATE()")->fetch()['total'] ?? 0);

    $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM citas WHERE estado = :estado");
    $stmt->execute(['estado' => 'PENDIENTE']);
    $stats['citas_pendientes'] = (int)($stmt->fetch()['total'] ?? 0);

// --- Estadísticas para VET ---
} elseif ($userRole === 'VET' && $userId !== null) {

    $stmt = $conn->prepare(
        "SELECT COUNT(*) AS total
         FROM citas
         WHERE DATE(fecha_hora) = CURDATE()
           AND vet_id = :vet_id"
    );
    $stmt->execute(['vet_id' => $userId]);
    $stats['citas_hoy'] = (int)($stmt->fetch()['total'] ?? 0);

    $stmt = $conn->prepare(
        "SELECT COUNT(*) AS total
         FROM citas
         WHERE estado = :estado
           AND vet_id = :vet_id"
    );
    $stmt->execute(['estado' => 'PENDIENTE', 'vet_id' => $userId]);
    $stats['citas_pendientes'] = (int)($stmt->fetch()['total'] ?? 0);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel - VetCitas</title>
    <style>
        body { font-family: Arial, sans-serif; background: #fafafa; }
        header {
            background: #00796b; color: #fff; padding: 10px 20px;
            display: flex; justify-content: space-between; align-items: center;
        }
        a { color: #fff; text-decoration: none; margin-left: 15px; }
        main { padding: 20px; }
        .rol { font-size: 0.9em; opacity: .8; }

        /* Tarjetas */
        .cards { display: flex; gap: 20px; margin-top: 20px; }
        .card {
            background: #fff; padding: 20px; border-radius: 6px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            width: 200px;
        }
        .card h3 { margin: 0 0 10px 0; }
        .card p { font-size: 22px; margin: 0; font-weight: bold; }
    </style>
</head>
<body>
<header>
    <div>
        <strong>VetCitas</strong>
        <span class="rol">[<?php echo htmlspecialchars($userRole); ?>]</span>
    </div>
    <div>
        <?php echo htmlspecialchars($userName); ?>
        <a href="logout.php">Cerrar sesión</a>
    </div>
</header>
<main>
    <h1>Bienvenido, <?php echo htmlspecialchars($userName); ?></h1>

    <div style="margin:20px 0;">
        <?php if ($userRole === 'ADMIN'): ?>
            <a href="cliente_nuevo.php" style="padding:8px 12px;background:#00796b;color:#fff;border-radius:4px;text-decoration:none;margin-right:10px;">+ Nuevo cliente</a>
            <a href="mascota_nueva.php" style="padding:8px 12px;background:#00796b;color:#fff;border-radius:4px;text-decoration:none;margin-right:10px;">+ Nueva mascota</a>
            <a href="cita_nueva.php" style="padding:8px 12px;background:#00796b;color:#fff;border-radius:4px;text-decoration:none;">+ Nueva cita</a>
        <?php else: ?>
            <a href="cita_nueva.php" style="padding:8px 12px;background:#00796b;color:#fff;border-radius:4px;text-decoration:none;">+ Agendar nueva cita</a>
        <?php endif; ?>
    </div>

    <div style="display:flex;gap:20px;flex-wrap:wrap;">
        <?php if ($userRole === 'ADMIN'): ?>
            <a href="clientes_list.php" style="flex:1;min-width:200px;text-decoration:none;color:inherit;">
                <div class="card">
                    <strong>Clientes registrados</strong>
                    <p><?php echo $stats['total_clientes']; ?></p>
                </div>
            </a>

            <a href="mascotas_list.php" style="flex:1;min-width:200px;text-decoration:none;color:inherit;">
                <div class="card">
                    <strong>Mascotas registradas</strong>
                    <p><?php echo $stats['total_mascotas']; ?></p>
                </div>
            </a>
        <?php endif; ?>

        <a href="citas_list.php?filtro=hoy" style="flex:1;min-width:200px;text-decoration:none;color:inherit;">
            <div class="card">
                <strong>Citas de hoy</strong>
                <p><?php echo $stats['citas_hoy']; ?></p>
            </div>
        </a>

        <a href="citas_list.php?filtro=pendientes" style="flex:1;min-width:200px;text-decoration:none;color:inherit;">
            <div class="card">
                <strong>Citas pendientes</strong>
                <p><?php echo $stats['citas_pendientes']; ?></p>
            </div>
        </a>
    </div>
</main>
</body>
</html>
