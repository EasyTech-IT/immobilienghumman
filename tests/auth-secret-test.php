<?php
define('AUTH_SECRET', 'HIER_AUTH_SECRET_64_ZEICHEN_IN_LOCAL_CONFIG');
require_once __DIR__ . '/../api/auth_helper.php';

$failures = [];
if (AUTH_SECRET_CONFIGURED) $failures[] = 'Placeholder secret was marked as configured';
if (create_token(['id' => 1]) !== false) $failures[] = 'Token was created with a placeholder secret';
if (verify_token('header.payload.signature') !== false) $failures[] = 'Token was accepted with a placeholder secret';
foreach ([
    'HIER_AUTH_SECRET_64_ZEICHEN_IN_LOCAL_CONFIG',
    'change-me-to-a-random-64-char-string-in-production',
    'HIER_ZUFAELLIGEN_STRING_64_ZEICHEN_EINTRAGEN',
] as $placeholder) {
    if (auth_secret_is_configured($placeholder)) $failures[] = 'A known placeholder secret was marked as configured';
}

if ($failures) {
    fwrite(STDERR, implode(PHP_EOL, $failures) . PHP_EOL);
    exit(1);
}

echo "Auth secret fail-closed checks passed.\n";
