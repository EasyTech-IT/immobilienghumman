<?php

define('SITE_URL', 'https://example.test');
define('AUTH_SECRET', 'test-secret-with-more-than-thirty-two-characters');
define('AUTH_SECRET_CONFIGURED', true);
require_once __DIR__ . '/../includes/private-expose.php';

$id = '0123456789abcdef0123456789abcdef';
$now = 2000000000;
$expires = $now + 3600;
$signature = hash_hmac('sha256', $id . '|' . $expires, AUTH_SECRET);
$url = private_expose_signed_url($id, $expires);
parse_str((string)parse_url($url, PHP_URL_QUERY), $query);

$checks = [
    'Gültige, kurz befristete Signatur wird akzeptiert' => private_expose_signature_valid($id, $expires, $signature, $now),
    'Abgelaufene Signatur wird abgewiesen' => !private_expose_signature_valid($id, $expires, $signature, $expires + 1),
    'Manipulierte Signatur wird abgewiesen' => !private_expose_signature_valid($id, $expires, str_repeat('0', 64), $now),
    'Datei-ID wird im Downloadlink weitergereicht' => ($query['file'] ?? '') === $id && isset($query['sig']),
];
foreach ($checks as $name => $passed) {
    echo ($passed ? 'OK: ' : 'FEHLER: ') . $name . PHP_EOL;
}
exit(in_array(false, $checks, true) ? 1 : 0);
