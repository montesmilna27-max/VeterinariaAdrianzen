<?php
// auth.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificar si el usuario está logueado
if (empty($_SESSION['user_id'])) {
    // Opcional: guardar URL para redirigir después de login
    $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'] ?? 'dashboard.php';
    header('Location: login.php');
    exit;
}

/**
 * Verifica si el usuario tiene alguno de los roles permitidos
 *
 * @param string|array $roles Roles permitidos, puede ser string o array
 */
function require_role($roles) {
    if (!is_array($roles)) {
        $roles = [$roles];
    }

    $rolUsuario = $_SESSION['user_rol'] ?? '';

    if (!in_array($rolUsuario, $roles, true)) {
        // Configurar código HTTP 403 y mostrar mensaje amigable
        http_response_code(403);
        echo "<h1>Acceso denegado</h1>";
        echo "<p>No tienes permisos para acceder a esta página.</p>";
        echo '<p><a href="dashboard.php">Volver al panel</a></p>';
        exit;
    }
}
?>
