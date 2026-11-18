<?php

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require_once __DIR__ . '/../includes/subscribers.php';
require_once __DIR__ . '/../includes/db.php';

$deleted = subscribers_cleanup_expired(db());
printf("Deleted %d expired newsletter records.\n", $deleted);
