<?php
/** @var PDO $pdo */
require_once __DIR__ . '/includes/auth.php';
require_role(['ADMIN','RECEPCION']);
require_once __DIR__ . '/conexion.php';

try {
    $stmt = $pdo->query("SELECT id, nombre FROM usuarios WHERE rol='VET' AND activo=1 ORDER BY nombre");
    /** @var array<int, array{id:int|string, nombre:string}> $vets */
    $vets = $stmt ? $stmt->fetchAll() : [];

    $stmt = $pdo->query("SELECT id, nombre FROM clientes ORDER BY nombre");
    /** @var array<int, array{id:int|string, nombre:string}> $clientes */
    $clientes = $stmt ? $stmt->fetchAll() : [];

} catch (PDOException $e) {
    die('Error al cargar datos.');
}
?>

<?php include __DIR__ . '/includes/header.php'; ?>

<main style="padding:20px;">
<h1>Nueva cita</h1>

<form method="POST" action="cita_guardar.php">

<label>Cliente:</label>
<select name="cliente_id" id="cliente_id" required onchange="cargarMascotas(this.value)">
    <option value="">Seleccione...</option>
    <?php foreach($clientes as $c): ?>
        <option value="<?= (int)$c['id'] ?>"><?= htmlspecialchars((string)$c['nombre']) ?></option>
    <?php endforeach; ?>
</select><br><br>

<label>Mascota:</label>
<select name="mascota_id" id="mascota_id" required>
    <option value="">Seleccione un cliente primero…</option>
</select><br><br>

<label>Veterinario:</label>
<select name="vet_id" required>
    <option value="">Seleccione...</option>
    <?php foreach($vets as $v): ?>
        <option value="<?= (int)$v['id'] ?>"><?= htmlspecialchars((string)$v['nombre']) ?></option>
    <?php endforeach; ?>
</select><br><br>

<label>Fecha:</label>
<input type="date" name="fecha" required><br><br>

<label>Hora:</label>
<input type="time" name="hora" required><br><br>

<label>Motivo:</label>
<input type="text" name="motivo" required><br><br>

<button>Guardar</button>
</form>
</main>

<script>
function cargarMascotas(cliente_id){
    if(!cliente_id){
        document.getElementById('mascota_id').innerHTML = "<option>Seleccione un cliente primero…</option>";
        return;
    }
    fetch("includes/get_mascotas.php?cliente_id="+encodeURIComponent(cliente_id))
        .then(r=>r.json())
        .then(data=>{
            let s = document.getElementById("mascota_id");
            s.innerHTML = "<option value=''>Seleccione…</option>";
            data.forEach(m=> s.innerHTML += `<option value="${m.id}">${m.nombre}</option>`);
        });
}
</script>


