<?php

/**
 * api/auth_helper.php
 * ====================
 * JWT-freie Session-basierte Authentifizierung
 */
require_once __DIR__ . '/../includes/config.php';

/**
 * Prüft ob der aktuelle Request authentifiziert ist.
 * Gibt 401 zurück wenn nicht authentifiziert.
 */
function require_auth(): void
{
    // Option 1: Session-basiert (für Browser-Anfragen)
    if (!empty($_SESSION['admin_id'])) return;

    // Option 2: Bearer-Token (für JS-Anfragen von Admin-Panel)
    $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
    if (preg_match('/^Bearer\s+(.+)$/i', $authHeader, $matches)) {
        $token = $matches[1];
        $payload = verify_token($token);
        if ($payload !== false) {
            $_SESSION['admin_id']   = $payload['id'];
            $_SESSION['admin_role'] = $payload['role'];
            return;
        }
    }

    http_response_code(401);
    echo json_encode(['error' => 'Nicht authentifiziert'], JSON_UNESCAPED_UNICODE);
    exit;
}

/**
 * Einfaches HMAC-basiertes Token erzeugen
 */
function create_token(array $payload): string
{
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
    $parts = explode('.', $token);
    if (count($parts) !== 3) return false;

    [$header, $body, $sig] = $parts;
    $expected = base64_encode(hash_hmac('sha256', "$header.$body", AUTH_SECRET, true));

    if (!hash_equals($expected, $sig)) return false;

    $payload = json_decode(base64_decode($body), true);
    if (!$payload || (isset($payload['exp']) && $payload['exp'] < time())) return false;

    return $payload;
}
