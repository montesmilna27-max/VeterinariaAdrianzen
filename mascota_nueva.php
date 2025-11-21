<?php
require_once __DIR__ . '/includes/auth.php';
require_role(['ADMIN', 'RECEPCION']);
require_once __DIR__ . '/conexion.php';

// Obtener clientes para el combo
$clientes = $con->query("SELECT id, nombre FROM clientes ORDER BY nombre ASC");

$errores = [];
$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cliente_id = (int)($_POST['cliente_id'] ?? 0);
    $nombre     = trim($_POST['nombre'] ?? '');
    $especie    = trim($_POST['especie'] ?? '');
    $raza       = trim($_POST['raza'] ?? '');
    $fecha_nac  = trim($_POST['fecha_nac'] ?? '');
    $notas      = trim($_POST['notas'] ?? '');

    if ($cliente_id <= 0) {
        $errores[] = 'Debe seleccionar un cliente.';
    }

    if ($nombre === '') {
        $errores[] = 'El nombre de la mascota es obligatorio.';
    }

    // Normalizamos especie a un conjunto limitado
    $especiesPermitidas = ['PERRO','GATO','OTRO'];
    if ($especie === '' || !in_array($especie, $especiesPermitidas, true)) {
        $errores[] = 'La especie seleccionada no es válida.';
    }

    // Validar fecha (si viene)
    if ($fecha_nac !== '') {
        $d = DateTime::createFromFormat('Y-m-d', $fecha_nac);
        if (!$d || $d->format('Y-m-d') !== $fecha_nac) {
            $errores[] = 'La fecha de nacimiento no tiene un formato válido.';
        }
    } else {
        $fecha_nac = null;
    }

    if (empty($errores)) {
        $stmt = $con->prepare(
            "INSERT INTO mascotas (cliente_id, nombre, especie, raza, fecha_nac, notas)
             VALUES (?, ?, ?, ?, ?, ?)"
        );
        $stmt->bind_param(
            "isssss",
            $cliente_id,
            $nombre,
            $especie,
            $raza,
            $fecha_nac,
            $notas
        );

        if ($stmt->execute()) {
            $mensaje = 'Mascota registrada correctamente.';
            // limpiar formulario
            $cliente_id = 0;
            $nombre = $especie = $raza = $fecha_nac = $notas = '';
        } else {
            $errores[] = 'Error al guardar la mascota.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nueva Mascota - VetCitas</title>
    <style>
        body { font-family: Arial, sans-serif; background:#fafafa; }
        header {
            background:#00796b; color:#fff; padding:10px 20px;
            display:flex; justify-content:space-between; align-items:center;
        }
        header a { color:#fff; text-decoration:none; margin-left:15px; }
        main { padding:20px; }
        form {
            max-width:600px; background:#fff; padding:15px;
            border-radius:8px; box-shadow:0 0 4px rgba(0,0,0,.1);
        }
        label { display:block; margin-top:10px; }
        input[type=text], input[type=date], select, textarea {
            width:100%; padding:8px; margin-top:5px; box-sizing:border-box;
        }
        button {
            margin-top:15px; padding:8px 15px; border:none;
            border-radius:4px; background:#00796b; color:#fff; cursor:pointer;
        }
        .alert-error { color:#c62828; margin-top:10px; }
        .alert-ok { color:#2e7d32; margin-top:10px; }
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
        <a href="mascotas_list.php">Mascotas</a>
        <a href="clientes_list.php">Clientes</a>
        <a href="dashboard.php">Inicio</a>
        <a href="logout.php">Cerrar sesión</a>
    </div>
</header>
<main>
    <h1>Nueva Mascota</h1>

    <?php if ($errores): ?>
        <div class="alert-error">
            <?php foreach ($errores as $e) echo htmlspecialchars($e) . "<br>"; ?>
        </div>
    <?php endif; ?>

    <?php if ($mensaje): ?>
        <div class="alert-ok"><?php echo htmlspecialchars($mensaje); ?></div>
    <?php endif; ?>

    <form method="post" action="mascota_nueva.php" autocomplete="off">

        <label for="cliente_id">Cliente propietario *</label>
        <select name="cliente_id" id="cliente_id" required>
            <option value="">Seleccione...</option>
            <?php
            // Volvemos a obtener clientes para el select
            $clientes->data_seek(0);
            while ($c = $clientes->fetch_assoc()):
                $sel = (isset($cliente_id) && $cliente_id == $c['id']) ? 'selected' : '';
            ?>
                <option value="<?php echo $c['id']; ?>" <?php echo $sel; ?>>
                    <?php echo htmlspecialchars($c['nombre']); ?>
                </option>
            <?php endwhile; ?>
        </select>

        <label for="nombre">Nombre de la mascota *</label>
        <input type="text" name="nombre" id="nombre"
               value="<?php echo htmlspecialchars($nombre ?? ''); ?>" required>

        <label for="especie">Especie *</label>
        <select name="especie" id="especie" required>
            <?php
            $opcEspecies = ['PERRO' => 'Perro', 'GATO' => 'Gato', 'OTRO' => 'Otro'];
            $espSel = $especie ?? '';
            foreach ($opcEspecies as $val => $label):
                $sel = ($espSel === $val) ? 'selected' : '';
            ?>
                <option value="<?php echo $val; ?>" <?php echo $sel; ?>>
                    <?php echo $label; ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="raza">Raza</label>
        <input type="text" name="raza" id="raza"
               value="<?php echo htmlspecialchars($raza ?? ''); ?>">

        <label for="fecha_nac">Fecha de nacimiento</label>
        <input type="date" name="fecha_nac" id="fecha_nac"
               value="<?php echo htmlspecialchars($fecha_nac ?? ''); ?>">

        <label for="notas">Notas</label>
        <textarea name="notas" id="notas" rows="3"><?php
            echo htmlspecialchars($notas ?? '');
        ?></textarea>

        <button type="submit">Guardar</button>
    </form>
</main>
</body>
</html>
