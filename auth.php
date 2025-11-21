<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

function require_role($roles) {
    if (!is_array($roles)) {
        $roles = [$roles];
    }
    $rolUsuario = $_SESSION['user_rol'] ?? '';
    if (!in_array($rolUsuario, $roles, true)) {
        http_response_code(403);
        echo "Acceso denegado.";
        exit;
    }
}
