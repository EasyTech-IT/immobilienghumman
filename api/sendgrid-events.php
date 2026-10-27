<?php

/**
 * api/sendgrid-events.php
 * Empfaengt SendGrid Event Webhook Daten fuer Bounce/Deliverability-Monitoring.
 */
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed'], JSON_UNESCAPED_UNICODE);
    exit;
}

$token = trim((string)($_SERVER['HTTP_X_WEBHOOK_TOKEN'] ?? ($_GET['token'] ?? '')));
if ($token === '' || !hash_equals((string)MAIL_WEBHOOK_TOKEN, $token)) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized'], JSON_UNESCAPED_UNICODE);
    exit;
}

$raw = file_get_contents('php://input');
$events = json_decode($raw, true);
if (!is_array($events)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid payload'], JSON_UNESCAPED_UNICODE);
    exit;
}

$normalized = [];
foreach ($events as $event) {
    if (!is_array($event)) continue;

    $normalized[] = [
        'event_type' => substr((string)($event['event'] ?? 'unknown'), 0, 50),
        'email' => substr((string)($event['email'] ?? ''), 0, 255),
        'sg_message_id' => substr((string)($event['sg_message_id'] ?? ''), 0, 255),
        'reason' => substr((string)($event['reason'] ?? ($event['response'] ?? '')), 0, 1000),
        'status' => substr((string)($event['status'] ?? ''), 0, 80),
        'raw_json' => json_encode($event, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
        'occurred_at' => isset($event['timestamp']) && is_numeric($event['timestamp'])
            ? date('Y-m-d H:i:s', (int)$event['timestamp'])
            : date('Y-m-d H:i:s'),
    ];
}

if (!$normalized) {
    http_response_code(204);
    exit;
}

try {
    $pdo = db();
    $stmt = $pdo->prepare(
        'INSERT INTO mail_events (event_type, email, sg_message_id, reason, status, raw_json, occurred_at, created_at)
         VALUES (?, ?, ?, ?, ?, ?, ?, NOW())'
    );

    foreach ($normalized as $row) {
        $stmt->execute([
            $row['event_type'],
            $row['email'],
            $row['sg_message_id'],
            $row['reason'],
            $row['status'],
            $row['raw_json'],
            $row['occurred_at'],
        ]);
    }
} catch (Throwable $e) {
    error_log('sendgrid-events insert error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'DB write failed'], JSON_UNESCAPED_UNICODE);
    exit;
}

http_response_code(204);
