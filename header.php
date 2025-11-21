<?php
require_once __DIR__ . '/auth.php';

// Nombre y rol desde sesión
$userName = $_SESSION['user_name'] ?? 'Usuario';
$userRole = $_SESSION['user_role'] ?? 'ADMIN'; // ADMIN o VET
?>
<header style="
    background:#00796b;
    color:#fff;
    padding:10px 20px;
    display:flex;
    justify-content:space-between;
    align-items:center;
">
    <div>
        <strong>VetCitas</strong>
        <span style="font-size:0.9em;opacity:.8;">[<?php echo htmlspecialchars($userRole); ?>]</span>

        <?php if ($userRole === 'ADMIN'): ?>
            <!-- Menú para ADMIN -->
            <a href="dashboard.php" style="color:#fff;margin-left:20px;text-decoration:none;">Inicio</a>
            <a href="clientes_list.php" style="color:#fff;margin-left:10px;text-decoration:none;">Clientes</a>
            <a href="mascotas_list.php" style="color:#fff;margin-left:10px;text-decoration:none;">Mascotas</a>
            <a href="citas_list.php" style="color:#fff;margin-left:10px;text-decoration:none;">Citas</a>
        <?php else: ?>
            <!-- Menú para VET -->
            <a href="dashboard.php" style="color:#fff;margin-left:20px;text-decoration:none;">Inicio</a>
            <a href="mascotas_list.php" style="color:#fff;margin-left:10px;text-decoration:none;">Mis Mascotas</a>
            <a href="citas_list.php" style="color:#fff;margin-left:10px;text-decoration:none;">Mis Citas</a>
        <?php endif; ?>
    </div>

    <div>
        <?php echo htmlspecialchars($userName); ?>
        <a href="logout.php" style="color:#fff;margin-left:15px;text-decoration:none;">Cerrar sesión</a>
    </div>
</header>

