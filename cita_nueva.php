<?php
/** @var PDO $pdo */
require_once __DIR__ . '/includes/auth.php';
require_role(['ADMIN','RECEPCION']);
require_once __DIR__ . '/conexion.php';

try {
    // --- Veterinarios ---
    $stmt = $pdo->query("
        SELECT id, nombre 
        FROM usuarios 
        WHERE rol = 'VET' AND activo = 1 
        ORDER BY nombre
    ");
    /** @var array<int, array<string, mixed>> $vets */
    $vets = $stmt ? $stmt->fetchAll() : [];

    // --- Clientes ---
    $stmt = $pdo->query("SELECT id, nombre FROM clientes ORDER BY nombre");
    /** @var array<int, array<string, mixed>> $clientes */
    $clientes = $stmt ? $stmt->fetchAll() : [];

} catch (PDOException $e) {
    die('Error al cargar datos.');
}
?>
<?php include __DIR__ . '/includes/header.php'; ?>

<main style="padding:20px;">
    <h1>Nueva cita</h1>

    <form method="POST" action="cita_guardar.php" autocomplete="off">

        <!-- Cliente -->
        <label>Cliente:</label><br>
        <select name="cliente_id" id="cliente_id" required onchange="cargarMascotas(this.value)">
            <option value="">Seleccione...</option>
            <?php foreach($clientes as $c): ?>
                <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['nombre']) ?></option>
            <?php endforeach; ?>
        </select><br><br>

        <!-- Mascotas -->
        <label>Mascota:</label><br>
        <select name="mascota_id" id="mascota_id" required>
            <option value="">Seleccione un cliente primero…</option>
        </select><br><br>

        <!-- Veterinario -->
        <label>Veterinario:</label><br>
        <select name="vet_id" required>
            <option value="">Seleccione...</option>
            <?php foreach ($vets as $v): ?>
                <option value="<?= $v['id'] ?>"><?= htmlspecialchars($v['nombre']) ?></option>
            <?php endforeach; ?>
        </select><br><br>

        <!-- Fecha -->
        <label>Fecha:</label><br>
        <input type="date" name="fecha" required><br><br>

        <!-- Hora -->
        <label>Hora:</label><br>
        <input type="time" name="hora" required><br><br>

        <!-- Motivo -->
        <label>Motivo:</label><br>
        <input type="text" name="motivo" required><br><br>

        <button type="submit"
                style="background:#00796b;color:#fff;padding:8px 14px;border:none;border-radius:4px;">
            Guardar
        </button>
    </form>
</main>

<script>
function cargarMascotas(cliente_id) {
    const sel = document.getElementById("mascota_id");

    if (!cliente_id) {
        sel.innerHTML = "<option value=''>Seleccione un cliente primero…</option>";
        return;
    }

    fetch("includes/get_mascotas.php?cliente_id=" + encodeURIComponent(cliente_id))
        .then(r => r.json())
        .then(data => {
            sel.innerHTML = "<option value=''>Seleccione…</option>";
            data.forEach(m => {
                sel.innerHTML += `<option value="${m.id}">${m.nombre}</option>`;
            });
        })
        .catch(() => {
            sel.innerHTML = "<option value=''>Error cargando mascotas</option>";
        });
}
</script>

