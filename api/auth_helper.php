<?php

/**
 * api/auth_helper.php
 * ====================
 * JWT-freie Session-basierte Authentifizierung
 */
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';

function ensure_admin_auth_versioning(PDO $pdo): bool
{
    static $checked = false;
    if ($checked) return true;

    try {
        $stmt = $pdo->query("SHOW COLUMNS FROM admin_users LIKE 'auth_version'");
        if (!$stmt->fetch()) {
            $pdo->exec('ALTER TABLE admin_users ADD COLUMN auth_version INT UNSIGNED NOT NULL DEFAULT 0');
        }

        $stmt = $pdo->prepare('SELECT TRIGGER_NAME FROM information_schema.TRIGGERS WHERE TRIGGER_SCHEMA = DATABASE() AND TRIGGER_NAME = ?');
        $stmt->execute(['admin_users_auth_version']);
        if (!$stmt->fetchColumn()) {
            $pdo->exec(
                'CREATE TRIGGER admin_users_auth_version BEFORE UPDATE ON admin_users FOR EACH ROW '
                . 'SET NEW.auth_version = IF(OLD.is_active <> NEW.is_active, OLD.auth_version + 1, NEW.auth_version)'
            );
        }

        $checked = true;
        return true;
    } catch (Throwable $e) {
        try {
            $stmt = $pdo->query("SHOW COLUMNS FROM admin_users LIKE 'auth_version'");
            $hasColumn = (bool)$stmt->fetch();
            $stmt = $pdo->prepare('SELECT TRIGGER_NAME FROM information_schema.TRIGGERS WHERE TRIGGER_SCHEMA = DATABASE() AND TRIGGER_NAME = ?');
            $stmt->execute(['admin_users_auth_version']);
            if ($hasColumn && $stmt->fetchColumn()) {
                $checked = true;
                return true;
            }
        } catch (Throwable $checkError) {
            error_log('Could not verify admin_users.auth_version column: ' . $checkError->getMessage());
        }
        error_log('Could not ensure admin auth versioning: ' . $e->getMessage());
        return false;
    }
}

function auth_admin_matches(PDO $pdo, int $id, int $version): bool
{
    $stmt = $pdo->prepare('SELECT id FROM admin_users WHERE id = ? AND is_active = 1 AND auth_version = ?');
    $stmt->execute([$id, $version]);
    return (bool)$stmt->fetchColumn();
}

function auth_current_identity(PDO $pdo): array|false
{
    if (isset($_SESSION['admin_id'], $_SESSION['admin_auth_version'])) {
        $id = (int)$_SESSION['admin_id'];
        $version = (int)$_SESSION['admin_auth_version'];
        if ($id > 0 && $version >= 0 && auth_admin_matches($pdo, $id, $version)) {
            return ['id' => $id, 'version' => $version, 'role' => (string)($_SESSION['admin_role'] ?? 'admin')];
        }
    }

    $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
    if (preg_match('/^Bearer\s+(.+)$/i', $authHeader, $matches)) {
        $payload = verify_token($matches[1]);
        if (
            $payload !== false
            && isset($payload['id'], $payload['ver'], $payload['role'])
            && is_numeric($payload['id'])
            && is_numeric($payload['ver'])
            && (int)$payload['id'] > 0
            && (int)$payload['ver'] >= 0
            && auth_admin_matches($pdo, (int)$payload['id'], (int)$payload['ver'])
        ) {
            return [
                'id' => (int)$payload['id'],
                'version' => (int)$payload['ver'],
                'role' => (string)$payload['role'],
            ];
        }
    }

    return false;
}

function auth_revoke_admin(PDO $pdo, int $id, int $version): bool
{
    $stmt = $pdo->prepare('UPDATE admin_users SET auth_version = auth_version + 1 WHERE id = ? AND auth_version = ?');
    $stmt->execute([$id, $version]);
    return $stmt->rowCount() > 0;
}

/**
 * Prüft ob der aktuelle Request authentifiziert ist.
 * Gibt 401 zurück wenn nicht authentifiziert.
 */
function require_auth(): void
{
    $pdo = db();
    if (!ensure_admin_auth_versioning($pdo)) {
        http_response_code(503);
        echo json_encode(['error' => 'Authentifizierung ist vorübergehend nicht verfügbar'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $identity = auth_current_identity($pdo);
    if ($identity !== false) {
        $_SESSION['admin_id'] = $identity['id'];
        $_SESSION['admin_auth_version'] = $identity['version'];
        $_SESSION['admin_role'] = $identity['role'];
        return;
    }

    http_response_code(401);
    echo json_encode(['error' => 'Nicht authentifiziert'], JSON_UNESCAPED_UNICODE);
    exit;
}

/**
 * Einfaches HMAC-basiertes Token erzeugen
 */
function create_token(array $payload): string|false
{
    if (!AUTH_SECRET_CONFIGURED) return false;

    $secret  = AUTH_SECRET;
    $header  = base64_encode(json_encode(['alg' => 'HS256', 'typ' => 'JWT']));
    $payload['exp'] = time() + 86400; // 24 h
    $body    = base64_encode(json_encode($payload));
    $sig     = base64_encode(hash_hmac('sha256', "$header.$body", $secret, true));
    return "$header.$body.$sig";
}

/**
 * Token verifizieren – gibt Payload zurück oder false
 */
function verify_token(string $token): array|false
{
    if (!AUTH_SECRET_CONFIGURED) return false;

    $parts = explode('.', $token);
    if (count($parts) !== 3) return false;

    [$header, $body, $sig] = $parts;
    $expected = base64_encode(hash_hmac('sha256', "$header.$body", AUTH_SECRET, true));

    if (!hash_equals($expected, $sig)) return false;

    $decoded = base64_decode($body, true);
    $payload = $decoded === false ? null : json_decode($decoded, true);
    if (
        !is_array($payload)
        || !isset($payload['id'], $payload['ver'], $payload['role'], $payload['exp'])
        || !is_numeric($payload['id'])
        || !is_numeric($payload['ver'])
        || !is_numeric($payload['exp'])
        || (int)$payload['exp'] <= time()
    ) return false;

    return $payload;
}
