<?php

// 🛡️ Configuración de la cookie de sesión (seguridad)
ini_set('session.cookie_httponly', 1);      // Previene robo por JS (XSS)
ini_set('session.cookie_samesite', 'Lax');  // Previene CSRF
ini_set('session.use_only_cookies', 1);     // No usar SID en la URL
// ini_set('session.cookie_secure', 1);      // SOLO si usas HTTPS real (hosting)

session_start();
require_once __DIR__ . '/conexion.php';

$alerta = '';

// 🔐 Clave secreta de Google reCAPTCHA (v2 checkbox)
$recaptchaSecret = '6Le5oRMsAAAAAE88RSqWf05QJPzEtq__sL3allmr'; // <-- Pega aquí tu SECRET KEY

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario  = trim($_POST['usuario']  ?? '');
    $password = $_POST['password'] ?? '';
    $captchaResponse = $_POST['g-recaptcha-response'] ?? ''; // reCAPTCHA

    if ($usuario === '' || $password === '') {
        $alerta = 'Debe ingresar usuario y contraseña.';
    } elseif ($captchaResponse === '') {
        // El usuario no marcó "No soy un robot"
        $alerta = 'Por favor, confirme que no es un robot (complete el reCAPTCHA).';
    } else {

        // ✅ 1. Verificar el token de reCAPTCHA con Google
        $ip = $_SERVER['REMOTE_ADDR']      ?? '';
        $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';

        $url = 'https://www.google.com/recaptcha/api/siteverify';
        $data = [
            'secret'   => $recaptchaSecret,
            'response' => $captchaResponse,
            'remoteip' => $ip,
        ];

        $options = [
            'http' => [
                'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                'method'  => 'POST',
                'content' => http_build_query($data),
                'timeout' => 5
            ]
        ];

        $context = stream_context_create($options);
        $result  = @file_get_contents($url, false, $context);

        if ($result === false) {
            // Error al conectar con Google
            $alerta = 'Error al verificar el reCAPTCHA. Intente de nuevo.';
        } else {
            $resultData = json_decode($result, true);

            if (empty($resultData['success'])) {
                // reCAPTCHA inválido
                $alerta = 'Verificación de reCAPTCHA fallida. Intente de nuevo.';
            } else {
                // ✅ 2. Aquí recién se valida usuario/contraseña

                // Buscar usuario
                $stmt = $conn->prepare("
                    SELECT id, nombre, usuario, password_hash, rol, activo
                    FROM usuarios
                    WHERE usuario = :usuario
                    LIMIT 1
                ");
                $stmt->execute(['usuario' => $usuario]);
                $user = $stmt->fetch();

                if ($user && (int)$user['activo'] === 1 && password_verify($password, $user['password_hash'])) {

                    // Regenerar sesión para evitar fixation
                    session_regenerate_id(true);

                    $_SESSION['user_id']   = (int)$user['id'];
                    $_SESSION['user_name'] = $user['nombre'] ?: $user['usuario'];
                    $_SESSION['user_role'] = $user['rol']; // ADMIN o VET

                    // Registrar LOGIN_OK en auditoria
                    $stmtAud = $conn->prepare("
                        INSERT INTO auditoria (usuario_id, accion, detalle, ip, user_agent)
                        VALUES (:usuario_id, :accion, :detalle, :ip, :ua)
                    ");
                    $stmtAud->execute([
                        'usuario_id' => $user['id'],
                        'accion'     => 'LOGIN_OK',
                        'detalle'    => 'Inicio de sesión correcto',
                        'ip'         => $ip,
                        'ua'         => $ua,
                    ]);

                    header('Location: dashboard.php');
                    exit;

                } else {
                    $alerta = 'Usuario o contraseña incorrectos.';

                    // Registrar intento fallido
                    $uid     = $user['id'] ?? null;
                    $detalle = $user
                        ? 'Intento fallido para usuario existente: ' . $user['usuario']
                        : 'Intento fallido para usuario no existente: ' . $usuario;

                    $stmtAud = $conn->prepare("
                        INSERT INTO auditoria (usuario_id, accion, detalle, ip, user_agent)
                        VALUES (:usuario_id, :accion, :detalle, :ip, :ua)
                    ");
                    $stmtAud->execute([
                        'usuario_id' => $uid,
                        'accion'     => 'LOGIN_FAIL',
                        'detalle'    => $detalle,
                        'ip'         => $ip,
                        'ua'         => $ua,
                    ]);
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ingreso - VetCitas</title>
    <style>
        body { font-family: Arial, sans-serif; background:#f5f5f5; }
        .login-box {
            max-width: 360px; margin:60px auto; padding:20px;
            background:#fff; border-radius:6px;
            box-shadow:0 2px 6px rgba(0,0,0,.15);
        }
        h1 { font-size:1.3rem; margin-bottom:15px; }
        label { display:block; margin-top:10px; }
        input[type=text], input[type=password] {
            width:100%; padding:8px; box-sizing:border-box;
        }
        .btn {
            margin-top:15px; padding:8px 12px;
            background:#00796b; color:#fff; border:none;
            cursor:pointer; border-radius:4px;
            width:100%;
        }
        .alerta { color:#c00; margin-top:10px; }
        .captcha-box { margin-top:15px; }
    </style>

    <!-- ✅ Script oficial de Google reCAPTCHA v2 -->
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>
<body>
<div class="login-box">
    <h1>VetCitas - Iniciar sesión</h1>

    <?php if ($alerta): ?>
        <div class="alerta"><?php echo htmlspecialchars($alerta); ?></div>
    <?php endif; ?>

    <form method="post">
        <label for="usuario">Usuario</label>
        <input type="text" name="usuario" id="usuario" autocomplete="username" required>

        <label for="password">Contraseña</label>
        <input type="password" name="password" id="password"
               autocomplete="current-password" required>

        <!-- 🧩 reCAPTCHA (checkbox "No soy un robot") -->
        <div class="captcha-box">
            <div class="g-recaptcha" data-sitekey="6Le5oRMsAAAAAMxrKiXTyohLjf0RIvz5CGWbUkp0"></div>
        </div>

        <button type="submit" class="btn">Entrar</button>
    </form>
</div>
</body>
</html>
