<?php
session_start();
require_once __DIR__ . '/conexion.php'; // para registrar auditoría (opcional)

$userId = $_SESSION['user_id'] ?? null;
$ip     = $_SERVER['REMOTE_ADDR']      ?? '';
$ua     = $_SERVER['HTTP_USER_AGENT'] ?? '';

// Registrar LOGOUT en auditoria si hay usuario y conexión
if ($userId && isset($conn)) {
    $stmt = $conn->prepare("
        INSERT INTO auditoria (usuario_id, accion, detalle, ip, user_agent)
        VALUES (:usuario_id, :accion, :detalle, :ip, :ua)
    ");
    $stmt->execute([
        'usuario_id' => $userId,
        'accion'     => 'LOGOUT',
        'detalle'    => 'Cierre de sesión',
        'ip'         => $ip,
        'ua'         => $ua,
    ]);
}

// Limpiar sesión
$_SESSION = [];
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(), '',
        time() - 42000,
        $params['path'], $params['domain'],
        $params['secure'], $params['httponly']
    );
}
session_destroy();

// Volver al login
header('Location: login.php');
exit;

