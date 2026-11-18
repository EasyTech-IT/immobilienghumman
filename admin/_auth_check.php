<?php

/**
 * admin/_auth_check.php
 * Einbinden am Anfang jeder Admin-Seite
 */
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../api/auth_helper.php';

if (empty($_SESSION['admin_id'])) {
    $next = urlencode($_SERVER['REQUEST_URI'] ?? (BASE . '/admin/index.php'));
    header('Location: ' . BASE . '/admin/login.php?reason=session_expired&next=' . $next);
    exit;
}

// Benutzer aus DB laden
$_pdo  = db();
if (!ensure_admin_auth_versioning($_pdo)) {
    http_response_code(503);
    exit('Authentifizierung ist vorübergehend nicht verfügbar.');
}
$_stmt = $_pdo->prepare('SELECT id, username, role, auth_version FROM admin_users WHERE id = ? AND is_active = 1');
$_stmt->execute([(int)$_SESSION['admin_id']]);
$admin = $_stmt->fetch();
if (
    !$admin
    || !isset($_SESSION['admin_auth_version'])
    || (int)$admin['auth_version'] !== (int)$_SESSION['admin_auth_version']
) {
    session_destroy();
    $next = urlencode($_SERVER['REQUEST_URI'] ?? (BASE . '/admin/index.php'));
    header('Location: ' . BASE . '/admin/login.php?reason=account_invalid&next=' . $next);
    exit;
}
