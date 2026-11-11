<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';

$slug = trim((string)($_GET['slug'] ?? ''));
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$property = null;

if ($slug !== '' || $id > 0) {
    try {
        $stmt = db()->prepare($slug !== ''
            ? 'SELECT * FROM properties WHERE slug = ? LIMIT 1'
            : 'SELECT * FROM properties WHERE id = ? LIMIT 1');
        $stmt->execute([$slug !== '' ? $slug : $id]);
        $property = $stmt->fetch() ?: null;
    } catch (Throwable $e) {
        error_log('Exposé konnte nicht geladen werden: ' . $e->getMessage());
        http_response_code(503);
        $error = 'Das Exposé ist derzeit nicht verfügbar. Bitte versuchen Sie es später erneut.';
    }
}

if (!$property && !isset($error)) {
    http_response_code(404);
    $error = 'Dieses Exposé wurde nicht gefunden.';
}

require __DIR__ . '/includes/expose-template.php';
