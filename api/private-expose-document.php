<?php

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/private-expose.php';

header('X-Content-Type-Options: nosniff');
header('Cache-Control: private, no-store, max-age=0');
header('Referrer-Policy: no-referrer');

$id = (string)($_GET['file'] ?? '');
$expires = filter_var($_GET['expires'] ?? null, FILTER_VALIDATE_INT);
$signature = (string)($_GET['sig'] ?? '');
if (!$expires || !private_expose_signature_valid($id, $expires, $signature)) {
    http_response_code(404);
    exit;
}

$file = private_expose_file($id);
if ($file === null) {
    http_response_code(404);
    exit;
}

$mime = (new finfo(FILEINFO_MIME_TYPE))->file($file);
$extensions = [
    'application/pdf' => 'pdf',
    'application/msword' => 'doc',
    'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
];
if (!isset($extensions[$mime])) {
    error_log('Rejected unexpected MIME type in private expose storage.');
    http_response_code(404);
    exit;
}

header('Content-Type: ' . $mime);
header('Content-Disposition: attachment; filename="expose-dokument.' . $extensions[$mime] . '"');
header('Content-Length: ' . filesize($file));
readfile($file);
