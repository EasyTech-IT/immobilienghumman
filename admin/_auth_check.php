<?php

/**
 * admin/_auth_check.php
 * Einbinden am Anfang jeder Admin-Seite
 */
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';

if (empty($_SESSION['admin_id'])) {
    $next = urlencode($_SERVER['REQUEST_URI'] ?? (BASE . '/admin/index.php'));
    header('Location: ' . BASE . '/admin/login.php?reason=session_expired&next=' . $next);
    exit;
}

// Benutzer aus DB laden
$_pdo  = db();
$_stmt = $_pdo->prepare('SELECT id, username, role FROM admin_users WHERE id = ? AND is_active = 1');
$_stmt->execute([$_SESSION['admin_id']]);
$admin = $_stmt->fetch();
if (!$admin) {
    session_destroy();
    $next = urlencode($_SERVER['REQUEST_URI'] ?? (BASE . '/admin/index.php'));
    header('Location: ' . BASE . '/admin/login.php?reason=account_invalid&next=' . $next);
    exit;
}
