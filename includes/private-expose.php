<?php

function private_expose_storage_dir(): string
{
    return dirname(__DIR__, 3) . DIRECTORY_SEPARATOR . 'private-expose';
}

function private_expose_file(string $id): ?string
{
    if (!preg_match('/^[a-f0-9]{32}$/', $id)) return null;
    $root = realpath(private_expose_storage_dir());
    $file = realpath(private_expose_storage_dir() . DIRECTORY_SEPARATOR . $id . '.bin');
    if ($root === false || $file === false || strpos($file, $root . DIRECTORY_SEPARATOR) !== 0 || !is_file($file)) {
        return null;
    }
    return $file;
}

function private_expose_signed_url(string $id, int $expires): string
{
    if (!defined('AUTH_SECRET_CONFIGURED') || !AUTH_SECRET_CONFIGURED || !preg_match('/^[a-f0-9]{32}$/', $id)) {
        throw new RuntimeException('Private Exposé-Dokumente benötigen ein konfiguriertes Authentifizierungsgeheimnis.');
    }
    $signature = hash_hmac('sha256', $id . '|' . $expires, AUTH_SECRET);
    return rtrim(SITE_URL, '/') . '/api/private-expose-document.php?file=' . rawurlencode($id)
        . '&expires=' . $expires . '&sig=' . $signature;
}

function private_expose_signature_valid(string $id, int $expires, string $signature, ?int $now = null): bool
{
    $now = $now ?? time();
    return defined('AUTH_SECRET_CONFIGURED') && AUTH_SECRET_CONFIGURED
        && preg_match('/^[a-f0-9]{32}$/', $id)
        && $expires >= $now && $expires <= $now + 31 * 86400
        && preg_match('/^[a-f0-9]{64}$/', $signature)
        && hash_equals(hash_hmac('sha256', $id . '|' . $expires, AUTH_SECRET), $signature);
}

function private_expose_import_legacy_document(array $item): ?array
{
    $url = trim((string)($item['url'] ?? ''));
    $path = parse_url($url, PHP_URL_PATH);
    $prefix = rtrim(defined('BASE') ? BASE : '', '/') . '/uploads/';
    if (!is_string($path) || strpos($path, $prefix) !== 0) return null;
    $name = basename(substr($path, strlen($prefix)));
    if (!preg_match('/^document_[a-f0-9]{24}\.(pdf|doc|docx)$/', $name)) return null;

    $uploads = realpath(__DIR__ . '/../uploads');
    $source = realpath(__DIR__ . '/../uploads/' . $name);
    if ($uploads === false || $source === false || strpos($source, $uploads . DIRECTORY_SEPARATOR) !== 0 || !is_file($source)) {
        return null;
    }
    $mime = (new finfo(FILEINFO_MIME_TYPE))->file($source);
    if (!in_array($mime, [
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    ], true)) return null;

    $storage = private_expose_storage_dir();
    if (!is_dir($storage) && !mkdir($storage, 0700, true) && !is_dir($storage)) {
        throw new RuntimeException('Privater Exposé-Ordner konnte nicht erstellt werden.');
    }
    $id = bin2hex(random_bytes(16));
    if (!rename($source, $storage . DIRECTORY_SEPARATOR . $id . '.bin')) {
        throw new RuntimeException('Ein bisher öffentlich gespeichertes Exposé-Dokument konnte nicht geschützt werden.');
    }
    @chmod($storage, 0700);
    @chmod($storage . DIRECTORY_SEPARATOR . $id . '.bin', 0600);

    return [
        'url' => 'private:' . $id,
        'name' => trim((string)($item['name'] ?? $name)),
        'mime' => $mime,
        'label' => trim((string)($item['label'] ?? '')),
        'legacy_source' => $name,
    ];
}
