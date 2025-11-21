<?php
session_start();

/**
 * @param string[] $roles
 * @return void
 */
function require_role(array $roles): void
{
    $userRole = $_SESSION['user_role'] ?? null;

    if ($userRole === null || !in_array($userRole, $roles, true)) {
        header("Location: no_autorizado.php");
        exit;
    }
}


