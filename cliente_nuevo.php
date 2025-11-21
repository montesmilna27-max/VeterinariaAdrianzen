<?php
require_once __DIR__ . '/includes/auth.php';
require_role(['ADMIN', 'RECEPCION']);

require_once __DIR__ . '/conexion.php'; // PDO

$errores = [];
$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nombre    = trim($_POST['nombre'] ?? '');
    $telefono  = trim($_POST['telefono'] ?? '');
    $email     = trim($_POST['email'] ?? '');
    $direccion = trim($_POST['direccion'] ?? '');

    if ($nombre === '') {
        $errores[] = 'El nombre es obligatorio.';
    }

    if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errores[] = 'El correo no es válido.';
    }

    if (empty($errores)) {
        try {
            $stmt = $conn->prepare(
                "INSERT INTO clientes (nombre, telefono, email, direccion)
                 VALUES (:nombre, :telefono, :email, :direccion)"
            );
            $stmt->execute([
                'nombre'    => $nombre,
                'telefono'  => $telefono,
                'email'     => $email,
                'direccion' => $direccion,
            ]);

            $mensaje = 'Cliente registrado correctamente.';
            // limpiar campos
            $nombre = $telefono = $email = $direccion = '';

        } catch (PDOException $e) {
            $errores[] = 'Error al guardar el cliente.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nuevo Cliente - VetCitas</title>
    <style>
        body { font-family: Arial, sans-serif; background: #fafafa; }
        header {
            background: #00796b; color: #fff; padding: 10px 20px;
            display: flex; justify-content: space-between; align-items: center;
        }
        a { color: #fff; text-decoration: none; margin-left: 15px; }
        main { padding: 20px; }
        form { max-width: 500px; background:#fff; padding:15px; border-radius:8px; box-shadow:0 0 4px rgba(0,0,0,.1); }
        label { display:block; margin-top:10px; }
        input[type=text], input[type=email], textarea {
            width:100%; padding:8px; margin-top:5px; box-sizing:border-box;
        }
        button { margin-top:15px; padding:8px 15px; border:none; border-radius:4px; background:#00796b; color:#fff; cursor:pointer; }
        .alert-error { color:#c62828; margin-top:10px; }
        .alert-ok { color:#2e7d32; margin-top:10px; }
    </style>
</head>
<body>
<header>
    <div>
        <strong>VetCitas</strong>
        <span style="font-size:.9em;opacity:.8;">[<?php echo htmlspecialchars($_SESSION['user_role']); ?>]</span>
    </div>
    <div>
        <?php echo htmlspecialchars($_SESSION['user_name']); ?>
        <a href="clientes_list.php">Clientes</a>
        <a href="dashboard.php">Inicio</a>
        <a href="logout.php">Cerrar sesión</a>
    </div>
</header>
<main>
    <h1>Nuevo Cliente</h1>

    <?php if ($errores): ?>
        <div class="alert-error">
            <?php foreach ($errores as $e) echo htmlspecialchars($e) . "<br>"; ?>
        </div>
    <?php endif; ?>

    <?php if ($mensaje): ?>
        <div class="alert-ok"><?php echo htmlspecialchars($mensaje); ?></div>
    <?php endif; ?>

    <form method="post" action="cliente_nuevo.php" autocomplete="off">
        <label for="nombre">Nombre *</label>
        <input type="text" name="nombre" id="nombre" required
               value="<?php echo htmlspecialchars($nombre ?? ''); ?>">

        <label for="telefono">Teléfono</label>
        <input type="text" name="telefono" id="telefono"
               value="<?php echo htmlspecialchars($telefono ?? ''); ?>">

        <label for="email">Correo electrónico</label>
        <input type="email" name="email" id="email"
               value="<?php echo htmlspecialchars($email ?? ''); ?>">

        <label for="direccion">Dirección</label>
        <textarea name="direccion" id="direccion" rows="3"><?php
            echo htmlspecialchars($direccion ?? '');
        ?></textarea>

        <button type="submit">Guardar</button>
    </form>
</main>
</body>
</html>

