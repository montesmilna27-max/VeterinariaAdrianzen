<?php
require_once __DIR__ . '/includes/auth.php';
require_role(['ADMIN','RECEPCION']);
require_once __DIR__ . '/conexion.php';
// cargar veterinarios activos
$vets = $con->query("SELECT id, nombre FROM usuarios WHERE rol = 'VET' AND activo = 1 ORDER BY nombre");
// Clientes
$clientes = $con->query("SELECT id, nombre FROM clientes ORDER BY nombre");
// Veterinarios
$vets = $con->query("SELECT id, nombre FROM usuarios WHERE rol = 'VET'");
?>
<?php include __DIR__ . '/includes/header.php'; ?>

<main style="padding:20px;">
    <h1>Nueva cita</h1>

    <form method="POST" action="cita_guardar.php" autocomplete="off">

        <label>Cliente:</label><br>
        <select name="cliente_id" id="cliente_id" required onchange="cargarMascotas(this.value)">
            <option value="">Seleccione...</option>
            <?php while($c = $clientes->fetch_assoc()): ?>
                <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['nombre']) ?></option>
            <?php endwhile; ?>
        </select><br><br>

        <label>Mascota:</label><br>
        <select name="mascota_id" id="mascota_id" required>
            <option value="">Seleccione un cliente primero…</option>
        </select><br><br>

        <label>Veterinario:</label><br>
        <select name="vet_id" required>
            <option value="">Seleccione...</option>
            <?php while($v = $vets->fetch_assoc()): ?>
                <option value="<?= $v['id'] ?>"><?= htmlspecialchars($v['nombre']) ?></option>
            <?php endwhile; ?>
        </select><br><br>

        <label>Fecha:</label><br>
        <input type="date" name="fecha" required><br><br>

        <label>Hora:</label><br>
        <input type="time" name="hora" required><br><br>

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
    if (!cliente_id) {
        document.getElementById('mascota_id').innerHTML =
            "<option value=''>Seleccione un cliente primero…</option>";
        return;
    }
    fetch("includes/get_mascotas.php?cliente_id=" + encodeURIComponent(cliente_id))
        .then(r => r.json())
        .then(data => {
            let sel = document.getElementById("mascota_id");
            sel.innerHTML = "<option value=''>Seleccione…</option>";
            data.forEach(m => {
                sel.innerHTML += `<option value="${m.id}">${m.nombre}</option>`;
            });
        });
}
</script>
