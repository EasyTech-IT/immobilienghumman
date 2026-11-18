<?php

/**
 * api/auth.php
 * =============
 * Login / Logout / Profil
 * 
 * POST /api/auth.php?action=login   – Login (username + password)
 * POST /api/auth.php?action=logout  – Logout
 * GET  /api/auth.php?action=me      – Aktueller Benutzer
 */
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/auth_helper.php';

header('Content-Type: application/json; charset=utf-8');
// CORS: nur eigene Domain erlaubt
$allowedOrigins = [
    'https://www.immobilienghumman.de',
    'https://immobilienghumman.de'
];
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
if (in_array($origin, $allowedOrigins, true)) {
    header('Access-Control-Allow-Origin: ' . $origin);
    header('Vary: Origin');
}
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Access-Control-Allow-Credentials: true');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

$action = $_GET['action'] ?? '';
$method = $_SERVER['REQUEST_METHOD'];

// ── Login ───────────────────────────────────────────────────
if ($action === 'login' && $method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $username = trim($data['username'] ?? '');
    $password = $data['password'] ?? '';

    if (empty($username) || empty($password)) {
        http_response_code(400);
        echo json_encode(['error' => 'Benutzername und Passwort sind erforderlich']);
        exit;
    }

    if (!AUTH_SECRET_CONFIGURED) {
        http_response_code(503);
        echo json_encode(['error' => 'Die sichere Authentifizierung ist nicht konfiguriert.']);
        exit;
    }

    // Rate limiting: max 5 Versuche in 15 Minuten (session-basiert)
    if (!isset($_SESSION['login_attempts'])) {
        $_SESSION['login_attempts'] = 0;
        $_SESSION['login_lockout']  = 0;
    }
    if ($_SESSION['login_lockout'] > time()) {
        $wait = ceil(($_SESSION['login_lockout'] - time()) / 60);
        http_response_code(429);
        echo json_encode(['error' => "Zu viele Fehlversuche. Bitte warten Sie $wait Minute(n)."]);
        exit;
    }

    $pdo = db();
    if (!ensure_admin_auth_versioning($pdo)) {
        http_response_code(503);
        echo json_encode(['error' => 'Authentifizierung ist vorübergehend nicht verfügbar']);
        exit;
    }
    $stmt = $pdo->prepare('SELECT id, username, password_hash, role, auth_version FROM admin_users WHERE username = ? AND is_active = 1');
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($password, $user['password_hash'])) {
        $_SESSION['login_attempts']++;
        if ($_SESSION['login_attempts'] >= 5) {
            $_SESSION['login_lockout']  = time() + 900; // 15 min
            $_SESSION['login_attempts'] = 0;
        }
        http_response_code(401);
        echo json_encode(['error' => 'Ungültige Anmeldedaten']);
        exit;
    }

    // Erfolgreich – Session + Token
    $_SESSION['login_attempts'] = 0;
    session_regenerate_id(false);
    $_SESSION['admin_id']       = $user['id'];
    $_SESSION['admin_auth_version'] = (int)$user['auth_version'];
    $_SESSION['admin_role']     = $user['role'];
    session_write_close();

    // Letzten Login aktualisieren
    $pdo->prepare('UPDATE admin_users SET last_login = NOW() WHERE id = ?')
        ->execute([$user['id']]);

    $token = create_token(['id' => $user['id'], 'ver' => (int)$user['auth_version'], 'role' => $user['role']]);
    if ($token === false) {
        http_response_code(503);
        echo json_encode(['error' => 'Die sichere Authentifizierung ist nicht verfügbar.']);
        exit;
    }

    echo json_encode([
        'token'   => $token,
        'user'    => ['id' => $user['id'], 'username' => $user['username'], 'role' => $user['role']],
        'message' => 'Erfolgreich angemeldet',
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// ── Logout ──────────────────────────────────────────────────
if ($action === 'logout' && $method === 'POST') {
    $pdo = db();
    if (!ensure_admin_auth_versioning($pdo)) {
        http_response_code(503);
        echo json_encode(['error' => 'Abmeldung konnte serverseitig nicht abgeschlossen werden']);
        exit;
    }
    $credentials = [];
    if (isset($_SESSION['admin_id'], $_SESSION['admin_auth_version'])) {
        $id = (int)$_SESSION['admin_id'];
        $version = (int)$_SESSION['admin_auth_version'];
        if ($id > 0 && $version >= 0) $credentials[$id . ':' . $version] = [$id, $version];
    }
    $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
    if (preg_match('/^Bearer\s+(.+)$/i', $authHeader, $matches)) {
        $payload = verify_token($matches[1]);
        if (
            $payload !== false
            && isset($payload['id'], $payload['ver'])
            && is_numeric($payload['id'])
            && is_numeric($payload['ver'])
            && (int)$payload['id'] > 0
            && (int)$payload['ver'] >= 0
        ) {
            $id = (int)$payload['id'];
            $version = (int)$payload['ver'];
            $credentials[$id . ':' . $version] = [$id, $version];
        }
    }
    foreach ($credentials as [$id, $version]) {
        auth_revoke_admin($pdo, $id, $version);
    }
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $p = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
    }
    session_destroy();
    echo json_encode(['message' => 'Erfolgreich abgemeldet'], JSON_UNESCAPED_UNICODE);
    exit;
}

// ── Me ──────────────────────────────────────────────────────
if ($action === 'me' && $method === 'GET') {
    require_auth();
    $pdo  = db();
    $stmt = $pdo->prepare('SELECT id, username, role, last_login FROM admin_users WHERE id = ? AND is_active = 1');
    $stmt->execute([$_SESSION['admin_id']]);
    $user = $stmt->fetch();
    if (!$user) {
        http_response_code(404);
        echo json_encode(['error' => 'Benutzer nicht gefunden']);
        exit;
    }
    echo json_encode(['user' => $user], JSON_UNESCAPED_UNICODE);
    exit;
}

http_response_code(400);
echo json_encode(['error' => 'Unbekannte Aktion'], JSON_UNESCAPED_UNICODE);
