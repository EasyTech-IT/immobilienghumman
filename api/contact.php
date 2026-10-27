<?php

/**
 * api/contact.php
 * ================
 * Kontaktanfragen entgegennehmen und per E-Mail weiterleiten
 * 
 * POST /api/contact.php?action=inquiry   – Anfrage zu einer Immobilie
 * POST /api/contact.php?action=general   – Allgemeine Kontaktanfrage
 * GET  /api/contact.php?action=inquiries – Liste der Anfragen (Auth)
 */
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/auth_helper.php';

header('Content-Type: application/json; charset=utf-8');
// CORS: nur eigene Domain erlaubt
$allowedOrigins = [
    'https://www.immobilienghumman.de',
    'https://immobilienghumman.de'
];
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
if (in_array($origin, $allowedOrigins, true)) {
    header('Access-Control-Allow-Origin: ' . $origin);
    header('Vary: Origin');
}
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Access-Control-Allow-Credentials: true');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

$action = $_GET['action'] ?? 'general';
$method = $_SERVER['REQUEST_METHOD'];
$clientIp = $_SERVER['HTTP_CF_CONNECTING_IP'] ?? ($_SERVER['REMOTE_ADDR'] ?? '0.0.0.0');

// ── Neue Anfrage ─────────────────────────────────────────────
if (in_array($action, ['inquiry', 'general']) && $method === 'POST') {
    // Auto-detect JSON vs FormData (multipart/form-data or urlencoded)
    $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
    $isJson = stripos($contentType, 'application/json') !== false;

    if ($isJson) {
        $data = json_decode(file_get_contents('php://input'), true) ?? [];
    } else {
        $data = $_POST;
        // Verify CSRF token for form submissions
        if (!csrf_verify($data['csrf_token'] ?? '')) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Ungültiger Sicherheitstoken. Bitte laden Sie die Seite neu.'], JSON_UNESCAPED_UNICODE);
            exit;
        }
    }

    // Support separate vorname/nachname fields or single name field
    $vorname  = trim($data['vorname']  ?? '');
    $nachname = trim($data['nachname'] ?? '');
    $name     = ($vorname || $nachname)
        ? trim($vorname . ' ' . $nachname)
        : trim($data['name'] ?? '');

    $email    = trim($data['email']   ?? '');
    $phone    = trim($data['phone']   ?? '');
    $message  = trim($data['message'] ?? '');
    $formType = trim($data['form_type'] ?? '');
    $propId   = isset($data['property_id']) ? (int)$data['property_id'] : null;
    $subjectInput = trim((string)($data['subject'] ?? $data['betreff'] ?? ''));

    // If no message provided, auto-generate from form type
    if (empty($message) && $formType) {
        $message = 'Anfrage über Formular: ' . $formType;
    }

    // Honeypot-Feld prüfen (Spam-Schutz)
    if (!empty($data['website'])) {
        http_response_code(200);
        echo json_encode(['success' => true, 'message' => 'Anfrage gesendet']);
        exit;
    }

    // Cloudflare Turnstile
    $turnstileToken = trim((string)($data['cf-turnstile-response'] ?? ''));
    if (!verify_turnstile_token($turnstileToken, $clientIp)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Sicherheitspruefung fehlgeschlagen. Bitte erneut versuchen.',
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $errors = [];
    if (empty($name))                                                     $errors[] = 'Name ist erforderlich';
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL))      $errors[] = 'Gültige E-Mail-Adresse ist erforderlich';

    if (!empty($errors)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => implode(', ', $errors), 'errors' => $errors], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // Rate limiting: max 5 Anfragen in 10 Minuten pro IP (DB-backed, nicht manipulierbar)
    $ip = $clientIp;
    try {
        $pdo = db();
        // Alten Eintrag bereinigen (älter als 10 Min) oder aktualisieren
        $rl = $pdo->prepare('SELECT hit_count, window_start FROM rate_limits WHERE ip_address=? AND action="contact" LIMIT 1');
        $rl->execute([$ip]);
        $rlRow = $rl->fetch();

        if ($rlRow) {
            $windowAge = time() - strtotime($rlRow['window_start']);
            if ($windowAge > 600) {
                // Fenster abgelaufen – Reset
                $pdo->prepare('UPDATE rate_limits SET hit_count=1, window_start=NOW() WHERE ip_address=? AND action="contact"')
                    ->execute([$ip]);
            } else {
                if ((int)$rlRow['hit_count'] >= 5) {
                    http_response_code(429);
                    echo json_encode(['success' => false, 'message' => 'Zu viele Anfragen. Bitte warten Sie einige Minuten.'], JSON_UNESCAPED_UNICODE);
                    exit;
                }
                $pdo->prepare('UPDATE rate_limits SET hit_count=hit_count+1 WHERE ip_address=? AND action="contact"')
                    ->execute([$ip]);
            }
        } else {
            $pdo->prepare('INSERT INTO rate_limits (ip_address, action, hit_count, window_start) VALUES (?, "contact", 1, NOW())')
                ->execute([$ip]);
        }
    } catch (Exception $e) {
        // Rate-limit-Tabelle existiert noch nicht – Session-Fallback
        error_log('Rate limit DB error: ' . $e->getMessage());
        if (
            isset($_SESSION['contact_count'][$ip]) &&
            $_SESSION['contact_count'][$ip]['count'] >= 5 &&
            $_SESSION['contact_count'][$ip]['time'] > time() - 600
        ) {
            http_response_code(429);
            echo json_encode(['success' => false, 'message' => 'Zu viele Anfragen. Bitte warten Sie einige Minuten.'], JSON_UNESCAPED_UNICODE);
            exit;
        }
        $_SESSION['contact_count'][$ip] = [
            'count' => ($_SESSION['contact_count'][$ip]['count'] ?? 0) + 1,
            'time'  => time(),
        ];
    }

    // Collect additional form-specific fields for the email body
    $extraFields = [
        'art',
        'einheiten',
        'adresse',
        'anlass',
        'kreditbetrag',
        'budget',
        'immobilientyp',
        'wohnflaeche',
        'baujahr',
        'kaufpreis',
        'standort',
        'property_type',
        'address',
        'area',
        'year_built',
        'rooms',
        'condition',
        'analysis_type'
    ];
    $extraInfo = '';
    foreach ($extraFields as $field) {
        if (!empty($data[$field])) {
            $extraInfo .= ucfirst($field) . ': ' . $data[$field] . "\n";
        }
    }

    // In Datenbank speichern
    $createdAt = date('Y-m-d H:i:s');
    try {
        $pdo  = db();

        $propertyTitle = '';
        $propertySlug = '';
        $propertyUrl = trim((string)($data['property_link'] ?? ''));
        if ($propId) {
            $propStmt = $pdo->prepare('SELECT title, slug FROM properties WHERE id = ? LIMIT 1');
            $propStmt->execute([$propId]);
            $prop = $propStmt->fetch();
            if ($prop) {
                $propertyTitle = trim((string)($prop['title'] ?? ''));
                $propertySlug = trim((string)($prop['slug'] ?? ''));
                if ($propertyUrl === '' && $propertySlug !== '') {
                    $propertyUrl = rtrim(SITE_URL, '/') . '/immobilie/' . rawurlencode($propertySlug);
                }
            }
        }

        $subject = $subjectInput;
        if ($subject === '') {
            if ($propId && $propertyTitle !== '') {
                $subject = 'Immobilienanfrage: ' . $propertyTitle;
            } elseif ($formType !== '') {
                $subject = 'Anfrage über Formular: ' . $formType;
            } else {
                $subject = 'Allgemeine Kontaktanfrage';
            }
        }

        if ($propertyTitle !== '') {
            $extraInfo .= "Immobilie: " . $propertyTitle . "\n";
        }
        if ($propertyUrl !== '') {
            $extraInfo .= "Immobilien-Link: " . $propertyUrl . "\n";
        }
        $extraInfo .= "Betreff: " . $subject . "\n";

        $stmt = $pdo->prepare('
            INSERT INTO contact_inquiries (name, email, phone, message, property_id, ip_address, created_at)
            VALUES (?, ?, ?, ?, ?, ?, NOW())
        ');
        $stmt->execute([$name, $email, $phone, $message . ($extraInfo ? "\n\nWeitere Angaben:\n" . $extraInfo : ''), $propId, $ip]);
    } catch (Exception $e) {
        error_log('DB Error contact: ' . $e->getMessage());
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Ihre Anfrage konnte nicht gespeichert werden. Bitte versuchen Sie es erneut.'
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // E-Mail senden
    $subject = MAIL_SUBJECT_PREFIX . ' Neue Anfrage von ' . $name;
    if ($formType) $subject .= ' (' . $formType . ')';
    if ($propId)   $subject .= " (Immobilie #$propId)";

    $mailBody  = "Neue Kontaktanfrage von: $name\n";
    $mailBody .= "Zeitpunkt: $createdAt\n";
    $mailBody .= "E-Mail: $email\n";
    $mailBody .= "Telefon: " . ($phone ?: 'nicht angegeben') . "\n";
    if ($propId) $mailBody .= "Immobilie-ID: $propId\n";
    $mailBody .= "\nNachricht:\n$message\n";
    if ($extraInfo) $mailBody .= "\nWeitere Angaben:\n$extraInfo";
    $mailBody .= "\n---\nGesendet ueber www.immobilienghumman.de\n";

    $successPayload = ['success' => true, 'message' => 'Ihre Anfrage wurde erfolgreich gesendet. Wir melden uns in Kürze!'];

    // Antwort immer sofort zurückgeben, damit der Browser nicht in einen Timeout läuft.
    ignore_user_abort(true);
    echo json_encode($successPayload, JSON_UNESCAPED_UNICODE);
    if (session_status() === PHP_SESSION_ACTIVE) {
        session_write_close();
    }

    if (function_exists('fastcgi_finish_request')) {
        fastcgi_finish_request();
    } else {
        while (ob_get_level() > 0) {
            @ob_end_flush();
        }
        @flush();
    }

    $adminSent = app_mail_text(MAIL_TO, $subject, $mailBody, $email);
    if (!$adminSent) {
        error_log('Contact mail (admin) konnte nicht gesendet werden.');
    }

    $confirmHtml = get_confirmation_email_html($name, $formType ?: 'Allgemeine Kontaktanfrage');
    $confirmSent = app_mail_html($email, 'Vielen Dank für Ihre Nachricht – ' . SITE_NAME, $confirmHtml);
    if (!$confirmSent) {
        error_log('Contact mail (confirmation) konnte nicht gesendet werden.');
    }

    exit;
}

// ── Liste (Admin) ────────────────────────────────────────────
if ($action === 'inquiries' && $method === 'GET') {
    require_auth();
    $pdo = db();
    $limit = min((int)($_GET['limit'] ?? 50), 200);
    $offset = max((int)($_GET['offset'] ?? 0), 0);
    $status = trim((string)($_GET['status'] ?? ''));
    $q = trim((string)($_GET['q'] ?? ''));

    $where = [];
    $params = [];
    if ($status !== '') {
        $where[] = 'status = ?';
        $params[] = $status;
    }
    if ($q !== '') {
        $where[] = '(name LIKE ? OR email LIKE ? OR phone LIKE ? OR message LIKE ?)';
        $like = '%' . $q . '%';
        $params[] = $like;
        $params[] = $like;
        $params[] = $like;
        $params[] = $like;
    }

    $whereSql = empty($where) ? '' : ('WHERE ' . implode(' AND ', $where));

    $countStmt = $pdo->prepare('SELECT COUNT(*) FROM contact_inquiries ' . $whereSql);
    $countStmt->execute($params);
    $total = (int)$countStmt->fetchColumn();

    $stmt = $pdo->prepare('SELECT * FROM contact_inquiries ' . $whereSql . ' ORDER BY created_at DESC LIMIT ' . $limit . ' OFFSET ' . $offset);
    $stmt->execute($params);
    $rows = $stmt->fetchAll();

    echo json_encode(['inquiries' => $rows, 'total' => $total, 'limit' => $limit, 'offset' => $offset], JSON_UNESCAPED_UNICODE);
    exit;
}

if ($action === 'inquiry' && $method === 'GET') {
    require_auth();
    $inqId = (int)($_GET['id'] ?? 0);
    if ($inqId <= 0) {
        http_response_code(400);
        echo json_encode(['error' => 'ID fehlt'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $pdo = db();
    $stmt = $pdo->prepare('SELECT * FROM contact_inquiries WHERE id = ? LIMIT 1');
    $stmt->execute([$inqId]);
    $row = $stmt->fetch();
    if (!$row) {
        http_response_code(404);
        echo json_encode(['error' => 'Anfrage nicht gefunden'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    echo json_encode(['inquiry' => $row], JSON_UNESCAPED_UNICODE);
    exit;
}

if ($action === 'inquiry' && $method === 'PUT') {
    require_auth();
    $inqId = (int)($_GET['id'] ?? 0);
    if ($inqId <= 0) {
        http_response_code(400);
        echo json_encode(['error' => 'ID fehlt'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $payload = json_decode(file_get_contents('php://input'), true) ?: [];
    $allowedStatus = ['new', 'read', 'replied'];
    $status = trim((string)($payload['status'] ?? ''));
    if (!in_array($status, $allowedStatus, true)) {
        http_response_code(400);
        echo json_encode(['error' => 'Ungültiger Status'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $pdo = db();
    $pdo->prepare('UPDATE contact_inquiries SET status = ? WHERE id = ?')->execute([$status, $inqId]);
    echo json_encode(['success' => true, 'message' => 'Status aktualisiert'], JSON_UNESCAPED_UNICODE);
    exit;
}

if ($action === 'inquiry' && $method === 'DELETE') {
    require_auth();
    $inqId = (int)($_GET['id'] ?? 0);
    if ($inqId <= 0) {
        http_response_code(400);
        echo json_encode(['error' => 'ID fehlt'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $pdo = db();
    $pdo->prepare('DELETE FROM contact_inquiries WHERE id = ?')->execute([$inqId]);
    echo json_encode(['success' => true, 'message' => 'Anfrage gelöscht'], JSON_UNESCAPED_UNICODE);
    exit;
}

http_response_code(400);
echo json_encode(['error' => 'Unbekannte Aktion'], JSON_UNESCAPED_UNICODE);
