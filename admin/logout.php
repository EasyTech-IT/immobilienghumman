<?php

/**
 * admin/logout.php
 */
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../api/auth_helper.php';
$revocationFailed = false;
if (isset($_SESSION['admin_id'], $_SESSION['admin_auth_version'])) {
    try {
        $pdo = db();
        if (ensure_admin_auth_versioning($pdo)) {
            auth_revoke_admin($pdo, (int)$_SESSION['admin_id'], (int)$_SESSION['admin_auth_version']);
        } else {
            $revocationFailed = true;
        }
    } catch (Throwable $e) {
        error_log('Could not revoke admin credentials during logout: ' . $e->getMessage());
        $revocationFailed = true;
    }
}
$_SESSION = [];
if (ini_get('session.use_cookies')) {
    $p = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
}
session_destroy();
header('Location: ' . BASE . '/admin/login.php?logout=1' . ($revocationFailed ? '&reason=revocation_failed' : ''));
exit;
