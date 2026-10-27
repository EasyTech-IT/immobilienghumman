<?php

/**
 * api/upload.php – Bild-Upload Handler
 * Unterstützt: Cloudinary (falls konfiguriert) oder lokaler Upload nach /uploads/
 */
require_once __DIR__ . '/../includes/config.php';

header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-Upload-Version: 2026-07-08-v4');

// Nur POST erlaubt
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit(json_encode(['error' => 'Method not allowed']));
}

// Admin-Session prüfen
if (empty($_SESSION['admin_id'])) {
    http_response_code(401);
    exit(json_encode(['error' => 'Nicht authentifiziert. Bitte einloggen.']));
}

// CSRF prüfen
if (!csrf_verify($_POST['csrf_token'] ?? '')) {
    http_response_code(403);
    exit(json_encode(['error' => 'Ungültiges Sicherheitstoken.']));
}

// Upload-Kontext: image (default), floorplan, document
$kind = strtolower(trim((string)($_POST['kind'] ?? 'image')));
if (!in_array($kind, ['image', 'floorplan', 'document'], true)) {
    $kind = 'image';
}

$fileField = isset($_FILES['asset']) ? 'asset' : 'image';

// Datei prüfen
if (empty($_FILES[$fileField]) || $_FILES[$fileField]['error'] !== UPLOAD_ERR_OK) {
    $errCode = $_FILES[$fileField]['error'] ?? -1;
    $errMsg  = [
        UPLOAD_ERR_INI_SIZE   => 'Datei zu groß (php.ini limit)',
        UPLOAD_ERR_FORM_SIZE  => 'Datei zu groß (form limit)',
        UPLOAD_ERR_PARTIAL    => 'Upload unvollständig',
        UPLOAD_ERR_NO_FILE    => 'Keine Datei ausgewählt',
        UPLOAD_ERR_NO_TMP_DIR => 'Temp-Verzeichnis fehlt',
        UPLOAD_ERR_CANT_WRITE => 'Schreibfehler',
    ][$errCode] ?? "Upload-Fehler (Code $errCode)";
    http_response_code(400);
    exit(json_encode(['error' => $errMsg]));
}

$file = $_FILES[$fileField];

// Echter MIME-Typ prüfen (nicht nur Dateiendung)
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime  = finfo_file($finfo, $file['tmp_name']);
finfo_close($finfo);

$allowed = allowed_mimes_for_kind($kind);
if (!in_array($mime, $allowed, true)) {
    http_response_code(400);
    exit(json_encode(['error' => 'Dateityp nicht erlaubt für ' . $kind . '.']));
}

// Größe prüfen
$maxBytes = ($kind === 'document') ? 15 * 1024 * 1024 : 10 * 1024 * 1024;
if ($file['size'] > $maxBytes) {
    http_response_code(400);
    exit(json_encode(['error' => 'Datei zu groß. Maximum ' . (($kind === 'document') ? '15 MB' : '10 MB') . '.']));
}

// Cloudinary nur für reine Bildtypen nutzen; Dokumente/Floorplan-PDF lokal speichern.
$cloudConfigured = defined('CLOUDINARY_CLOUD_NAME') && CLOUDINARY_CLOUD_NAME !== '';
$isImageLike = strpos($mime, 'image/') === 0;
$usedCloudinary = false;
$uploadError = '';

if ($cloudConfigured && $isImageLike) {
    $cloudResult = upload_cloudinary($file['tmp_name'], $mime, $uploadError);
    if ($cloudResult) {
        $usedCloudinary = true;
        $result = $cloudResult;
    } else {
        error_log('Cloudinary fehlgeschlagen, versuche lokalen Upload als Fallback. Grund: ' . $uploadError);
        $localError = '';
        $result = upload_local($file, $mime, $kind, $localError);
        if (!$result) {
            $uploadError = 'Cloudinary: ' . ($uploadError ?: 'unbekannt') . ' | Local: ' . ($localError ?: 'unbekannt');
        }
    }
} else {
    $result = upload_local($file, $mime, $kind, $uploadError);
}

if (!$result) {
    http_response_code(500);
    $msg = 'Upload fehlgeschlagen (Cloudinary/Local).';
    if ($uploadError !== '') {
        $msg .= ' ' . $uploadError;
    }
    exit(json_encode(['error' => $msg]));
}

echo json_encode([
    'success' => true,
    'url'     => $result['url'],
    'thumb'   => $result['thumb'],
    'name'    => $result['name'] ?? basename(parse_url($result['url'], PHP_URL_PATH) ?: 'datei'),
    'mime'    => $mime,
    'kind'    => $kind,
    'source'  => $usedCloudinary ? 'cloudinary' : 'local',
]);

function allowed_mimes_for_kind(string $kind): array
{
    if ($kind === 'document') {
        return [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        ];
    }

    if ($kind === 'floorplan') {
        return [
            'image/jpeg',
            'image/png',
            'image/webp',
            'application/pdf',
        ];
    }

    return ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
}

/* ── Cloudinary Upload ────────────────────────────────────── */
function upload_cloudinary(string $tmp, string $mime, string &$error = ''): ?array
{
    $cloud  = CLOUDINARY_CLOUD_NAME;
    $key    = CLOUDINARY_API_KEY;
    $secret = CLOUDINARY_API_SECRET;
    $ts     = time();

    // Signatur erstellen (alphabetisch sortiert)
    $paramsToSign = "folder=immobilien&quality=auto:good&timestamp={$ts}";
    $sig = sha1($paramsToSign . $secret);

    $ch = curl_init("https://api.cloudinary.com/v1_1/{$cloud}/image/upload");
    curl_setopt_array($ch, [
        CURLOPT_POST           => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 30,
        CURLOPT_POSTFIELDS     => [
            'file'      => new CURLFile($tmp, $mime),
            'api_key'   => $key,
            'timestamp' => $ts,
            'signature' => $sig,
            'folder'    => 'immobilien',
            'quality'   => 'auto:good',
        ],
    ]);
    $res = curl_exec($ch);
    $err = curl_error($ch);
    $httpCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if (!$res || $err) {
        $error = 'Cloudinary Transportfehler: ' . ($err ?: 'keine Antwort');
        error_log("Cloudinary upload error: $err");
        return null;
    }

    $d = json_decode($res, true);
    if (empty($d['secure_url'])) {
        $apiMsg = is_array($d) && isset($d['error']['message']) ? (string)$d['error']['message'] : '';
        $error = 'Cloudinary HTTP ' . $httpCode . ($apiMsg !== '' ? ' - ' . $apiMsg : '');
        error_log("Cloudinary response: $res");
        return null;
    }

    // Transformierte URLs erzeugen
    $base  = str_replace('/upload/', '/upload/f_auto,q_auto:good,w_1920/', $d['secure_url']);
    $thumb = str_replace('/upload/', '/upload/f_auto,q_auto,w_400,h_300,c_fill/', $d['secure_url']);

    return ['url' => $base, 'thumb' => $thumb];
}

/* ── Lokaler Upload ───────────────────────────────────────── */
function upload_local(array $file, string $mime, string $kind = 'image', string &$error = ''): ?array
{
    $uploadDir = __DIR__ . '/../uploads/';
    if (!is_dir($uploadDir)) {
        if (!mkdir($uploadDir, 0755, true)) {
            error_log("Cannot create uploads directory: $uploadDir");
            $error = 'Uploads-Ordner kann nicht erstellt werden.';
            return null;
        }
    }

    if (!is_writable($uploadDir)) {
        @chmod($uploadDir, 0755);
    }
    if (!is_writable($uploadDir)) {
        $error = 'Uploads-Ordner nicht schreibbar.';
        error_log("Uploads directory not writable: $uploadDir");
        return null;
    }

    // .htaccess erstellen um PHP-Ausführung in uploads/ zu verhindern (IONOS/FPM-kompatibel)
    $htaccess = $uploadDir . '.htaccess';
    $safeHtaccess = "Options -Indexes\n"
        . "RemoveHandler .php .php3 .php4 .php5 .phtml .phar\n"
        . "<FilesMatch \"\\.(php|php3|php4|php5|phtml|phar)$\">\n"
        . "    Require all denied\n"
        . "</FilesMatch>\n";

    $currentHtaccess = file_exists($htaccess) ? (string)@file_get_contents($htaccess) : '';
    if ($currentHtaccess !== $safeHtaccess) {
        @file_put_contents($htaccess, $safeHtaccess);
    }

    $ext = match ($mime) {
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp',
        'image/gif'  => 'gif',
        'application/pdf' => 'pdf',
        'application/msword' => 'doc',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
        default      => 'jpg',
    };

    $prefix = in_array($kind, ['floorplan', 'document'], true) ? $kind : 'image';
    $filename = $prefix . '_' . bin2hex(random_bytes(12)) . '.' . $ext;
    $dest     = $uploadDir . $filename;

    if (!move_uploaded_file($file['tmp_name'], $dest)) {
        error_log("Cannot move uploaded file to: $dest");
        $error = 'Datei konnte nicht in uploads verschoben werden.';
        return null;
    }

    $url = BASE . '/uploads/' . $filename;
    return ['url' => $url, 'thumb' => $url, 'name' => $file['name'] ?? $filename];
}
