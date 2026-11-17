<?php

/**
 * api/properties.php
 * ==================
 * REST-Endpunkt für Immobilien (GET/POST/PUT/DELETE)
 *
 * GET    /api/properties.php           – Liste (mit Filtern)
 * GET    /api/properties.php?id=123    – Einzelne Immobilie per Datenbank-ID
 * GET    /api/properties.php?number=1123 – Legacy-URL auflösen
 * POST   /api/properties.php           – Neue Immobilie anlegen  (Auth erforderlich)
 * PUT    /api/properties.php?id=123    – Immobilie aktualisieren (Auth erforderlich)
 * DELETE /api/properties.php?id=123   – Immobilie löschen       (Auth erforderlich)
 */
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/property-url.php';
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

$method = $_SERVER['REQUEST_METHOD'];
$id     = isset($_GET['id']) ? (int)$_GET['id'] : null;
$slug   = trim((string)($_GET['slug'] ?? ''));

// ── Hilfsfunktionen ────────────────────────────────────────
function json_ok(array $data, int $code = 200): void
{
    http_response_code($code);
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}
function json_error(string $msg, int $code = 400): void
{
    http_response_code($code);
    echo json_encode(['error' => $msg], JSON_UNESCAPED_UNICODE);
    exit;
}

// Bilder-JSON zu Array
function decode_images($raw): array
{
    if (empty($raw)) return [];
    $decoded = json_decode($raw, true);
    return is_array($decoded) ? $decoded : [];
}

// Ausstattung-JSON zu Array
function decode_features($raw): array
{
    if (empty($raw)) return [];
    $decoded = json_decode($raw, true);
    return is_array($decoded) ? $decoded : [];
}

function decode_extra_data($raw): array
{
    if (empty($raw)) return [];
    $decoded = json_decode($raw, true);
    return is_array($decoded) ? $decoded : [];
}

function properties_has_extra_data(PDO $pdo): bool
{
    static $checked = false;
    static $has = false;
    if ($checked) {
        return $has;
    }

    $checked = true;
    try {
        $stmt = $pdo->query("SHOW COLUMNS FROM properties LIKE 'extra_data'");
        $has = (bool)$stmt->fetch();
    } catch (Throwable $e) {
        $has = false;
    }

    return $has;
}

// Immobilien-Row → API-Objekt
function row_to_property(array $row): array
{
    $row['images']    = decode_images($row['images'] ?? '');
    $row['features']  = decode_features($row['features'] ?? '');
    $row['extra_data'] = decode_extra_data($row['extra_data'] ?? '');
    $row['featured']  = (bool)$row['featured'];
    $row['show_on_homepage'] = (bool)$row['featured'];
    $row['price']     = (float)$row['price'];
    $row['rooms']     = (float)$row['rooms'];
    $row['area']      = (float)$row['area'];

    $row['address_public'] = (string)($row['location'] ?? '');
    $row['address'] = '';
    $row['extra_data']['hide_exact_address'] = true;
    unset($row['extra_data']['address_public_note'], $row['extra_data']['legacy_slugs']);

    return $row;
}

// ── GET ─────────────────────────────────────────────────────
if ($method === 'GET') {
    if (isset($_GET['number'])) {
        if (!is_string($_GET['number'])) {
            json_error('Ungültige Objektnummer');
        }
        $publicNumber = filter_var($_GET['number'], FILTER_VALIDATE_INT);
        if ($publicNumber === false || $publicNumber < 1111) {
            json_error('Ungültige Objektnummer');
        }
        $id = $publicNumber - 1110;
    }

    $pdo = db();

    // Einzelne Immobilie
    if ($id || $slug !== '') {
        if ($id) {
            $stmt = $pdo->prepare("SELECT * FROM properties WHERE id = ? AND status = 'available' LIMIT 1");
            $stmt->execute([$id]);
        } else {
            $hasExtraData = properties_has_extra_data($pdo);
            $slugCondition = 'slug = ?';
            $params = [$slug];
            if ($hasExtraData) {
                $slugCondition = "(slug = ? OR JSON_CONTAINS(JSON_EXTRACT(extra_data, '$.legacy_slugs'), JSON_QUOTE(?)))";
                $params = [$slug, $slug];
            }
            $stmt = $pdo->prepare("SELECT * FROM properties WHERE $slugCondition AND status = 'available' LIMIT 1");
            $stmt->execute($params);
        }
        $row = $stmt->fetch();
        if (!$row) {
            require_auth();
            if ($id) {
                $stmt = $pdo->prepare('SELECT * FROM properties WHERE id = ? LIMIT 1');
                $stmt->execute([$id]);
            } else {
                $hasExtraData = properties_has_extra_data($pdo);
                $slugCondition = 'slug = ?';
                $params = [$slug];
                if ($hasExtraData) {
                    $slugCondition = "(slug = ? OR JSON_CONTAINS(JSON_EXTRACT(extra_data, '$.legacy_slugs'), JSON_QUOTE(?)))";
                    $params = [$slug, $slug];
                }
                $stmt = $pdo->prepare("SELECT * FROM properties WHERE $slugCondition LIMIT 1");
                $stmt->execute($params);
            }
            $row = $stmt->fetch();
        }
        if (!$row) json_error('Immobilie nicht gefunden', 404);
        json_ok(['property' => row_to_property($row)]);
    }

    // Liste mit optionalen Filtern
    $where  = ['1=1'];
    $params = [];

    if (isset($_GET['status']) && !is_string($_GET['status'])) {
        json_error('Ungültiger Statusfilter');
    }
    $status = trim((string)($_GET['status'] ?? ''));
    if ($status === '') {
        $where[]  = 'status = ?';
        $params[] = 'available';
    } else {
        if ($status !== 'available') require_auth();
        $where[]  = 'status = ?';
        $params[] = $status;
    }
    if (!empty($_GET['type'])) {
        $where[]  = 'type = ?';
        $params[] = $_GET['type'];
    }
    if (!empty($_GET['offer_type'])) {
        $where[]  = 'offer_type = ?';
        $params[] = $_GET['offer_type'];
    }
    if (!empty($_GET['location'])) {
        $where[]  = 'location LIKE ?';
        $params[] = '%' . $_GET['location'] . '%';
    }
    if (!empty($_GET['min_price'])) {
        $where[]  = 'price >= ?';
        $params[] = (float)$_GET['min_price'];
    }
    if (!empty($_GET['max_price'])) {
        $where[]  = 'price <= ?';
        $params[] = (float)$_GET['max_price'];
    }
    if (!empty($_GET['min_rooms'])) {
        $where[]  = 'rooms >= ?';
        $params[] = (float)$_GET['min_rooms'];
    }
    if (!empty($_GET['featured'])) {
        $where[]  = 'featured = 1';
    }

    $allowedSort = ['newest' => 'created_at DESC', 'oldest' => 'created_at ASC', 'price_asc' => 'price ASC', 'price_desc' => 'price DESC'];
    $sortKey = $_GET['sort'] ?? 'newest';
    $orderBy = $allowedSort[$sortKey] ?? 'created_at DESC';

    $limit  = min((int)($_GET['limit']  ?? 100), 200);
    $offset = max((int)($_GET['offset'] ?? 0), 0);

    $sql = 'SELECT * FROM properties WHERE ' . implode(' AND ', $where)
        . ' ORDER BY ' . $orderBy
        . ' LIMIT ' . $limit . ' OFFSET ' . $offset;

    $countSql = 'SELECT COUNT(*) FROM properties WHERE ' . implode(' AND ', $where);

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $rows = $stmt->fetchAll();

    $countStmt = $pdo->prepare($countSql);
    $countStmt->execute($params);
    $total = (int)$countStmt->fetchColumn();

    json_ok([
        'properties' => array_map('row_to_property', $rows),
        'total'      => $total,
        'limit'      => $limit,
        'offset'     => $offset,
    ]);
}

// ── POST ────────────────────────────────────────────────────
if ($method === 'POST') {
    require_auth();
    $pdo  = db();
    $hasExtraData = properties_has_extra_data($pdo);
    $data = json_decode(file_get_contents('php://input'), true);
    if (!$data) json_error('Ungültige JSON-Daten');

    $required = ['title', 'type', 'offer_type', 'price', 'location'];
    foreach ($required as $field) {
        if (empty($data[$field])) json_error("Pflichtfeld fehlt: $field");
    }

    $featured = !empty($data['featured']) || !empty($data['show_on_homepage']) ? 1 : 0;

    if ($hasExtraData) {
        $stmt = $pdo->prepare('
            INSERT INTO properties (title, slug, short_description, description, type, offer_type,
                price, warm_rent, deposit, area, rooms, bathrooms, location, zip, address,
                available_from, features, images, main_image, video_url, extra_data, status, featured, created_at)
            VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,NOW())
        ');
        $stmt->execute([
            $data['title'],
            property_unique_slug($pdo, (string)$data['title'], null, $hasExtraData),
            $data['short_description'] ?? '',
            $data['description']       ?? '',
            $data['type'],
            $data['offer_type'],
            (float)$data['price'],
            (float)($data['warm_rent'] ?? 0),
            (float)($data['deposit']   ?? 0),
            (float)($data['area']      ?? 0),
            (float)($data['rooms']     ?? 0),
            (int)  ($data['bathrooms'] ?? 1),
            $data['location'],
            $data['zip']               ?? '',
            $data['address']           ?? '',
            $data['available_from']    ?? null,
            json_encode($data['features'] ?? [], JSON_UNESCAPED_UNICODE),
            json_encode($data['images']   ?? [], JSON_UNESCAPED_UNICODE),
            $data['main_image']        ?? '',
            $data['video_url']         ?? null,
            json_encode($data['extra_data'] ?? [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            $data['status']            ?? 'available',
            $featured,
        ]);
    } else {
        $stmt = $pdo->prepare('
            INSERT INTO properties (title, slug, short_description, description, type, offer_type,
                price, warm_rent, deposit, area, rooms, bathrooms, location, zip, address,
                available_from, features, images, main_image, video_url, status, featured, created_at)
            VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,NOW())
        ');
        $stmt->execute([
            $data['title'],
            property_unique_slug($pdo, (string)$data['title'], null, $hasExtraData),
            $data['short_description'] ?? '',
            $data['description']       ?? '',
            $data['type'],
            $data['offer_type'],
            (float)$data['price'],
            (float)($data['warm_rent'] ?? 0),
            (float)($data['deposit']   ?? 0),
            (float)($data['area']      ?? 0),
            (float)($data['rooms']     ?? 0),
            (int)  ($data['bathrooms'] ?? 1),
            $data['location'],
            $data['zip']               ?? '',
            $data['address']           ?? '',
            $data['available_from']    ?? null,
            json_encode($data['features'] ?? [], JSON_UNESCAPED_UNICODE),
            json_encode($data['images']   ?? [], JSON_UNESCAPED_UNICODE),
            $data['main_image']        ?? '',
            $data['video_url']         ?? null,
            $data['status']            ?? 'available',
            $featured,
        ]);
    }

    json_ok(['id' => (int)$pdo->lastInsertId(), 'message' => 'Immobilie erstellt'], 201);
}

// ── PUT ─────────────────────────────────────────────────────
if ($method === 'PUT') {
    require_auth();
    if (!$id) json_error('ID fehlt');

    $data = json_decode(file_get_contents('php://input'), true);
    if (!$data) json_error('Ungültige JSON-Daten');

    $pdo = db();
    $hasExtraData = properties_has_extra_data($pdo);
    $currentStmt = $pdo->prepare($hasExtraData
        ? 'SELECT slug, title, extra_data FROM properties WHERE id = ? LIMIT 1'
        : 'SELECT slug, title FROM properties WHERE id = ? LIMIT 1');
    $currentStmt->execute([$id]);
    $current = $currentStmt->fetch();
    if (!$current) {
        json_error('Immobilie nicht gefunden', 404);
    }

    $slug = $hasExtraData
        ? property_unique_slug($pdo, (string)($data['title'] ?? $current['title']), $id)
        : (string)$current['slug'];
    $data['slug'] = $slug;
    if ($hasExtraData && ($slug !== (string)$current['slug'] || array_key_exists('extra_data', $data))) {
        $currentExtraData = decode_extra_data($current['extra_data'] ?? '');
        $incomingExtraData = $data['extra_data'] ?? $currentExtraData;
        if (is_string($incomingExtraData)) {
            $incomingExtraData = decode_extra_data($incomingExtraData);
        }
        if (!is_array($incomingExtraData)) {
            $incomingExtraData = $currentExtraData;
        }
        $legacySlugs = array_merge(
            is_array($currentExtraData['legacy_slugs'] ?? null) ? $currentExtraData['legacy_slugs'] : [],
            is_array($incomingExtraData['legacy_slugs'] ?? null) ? $incomingExtraData['legacy_slugs'] : []
        );
        if ($slug !== (string)$current['slug'] && (string)$current['slug'] !== '') {
            $legacySlugs[] = (string)$current['slug'];
        }
        $incomingExtraData['legacy_slugs'] = array_values(array_unique(array_filter(
            $legacySlugs,
            static fn($value): bool => is_string($value) && $value !== '' && $value !== $slug
        )));
        $data['extra_data'] = $incomingExtraData;
    }

    $allowed = [
        'title',
        'slug',
        'short_description',
        'description',
        'type',
        'offer_type',
        'price',
        'warm_rent',
        'deposit',
        'area',
        'rooms',
        'bathrooms',
        'location',
        'zip',
        'address',
        'available_from',
        'features',
        'images',
        'main_image',
        'video_url',
        'extra_data',
        'status',
        'featured'
    ];

    if (array_key_exists('show_on_homepage', $data) && !array_key_exists('featured', $data)) {
        $data['featured'] = !empty($data['show_on_homepage']) ? 1 : 0;
    }

    $sets   = [];
    $params = [];
    foreach ($allowed as $col) {
        if (!array_key_exists($col, $data)) continue;
        $val = $data[$col];
        if (in_array($col, ['features', 'images']) && is_array($val)) {
            $val = json_encode($val, JSON_UNESCAPED_UNICODE);
        }
        if ($col === 'extra_data' && !$hasExtraData) continue;
        if ($col === 'extra_data' && is_array($val)) {
            $val = json_encode($val, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }
        $sets[]   = "$col = ?";
        $params[] = $val;
    }
    if (empty($sets)) json_error('Keine Felder zum Aktualisieren');

    $params[] = $id;
    $pdo->prepare('UPDATE properties SET ' . implode(', ', $sets) . ' WHERE id = ?')
        ->execute($params);

    json_ok(['message' => 'Immobilie aktualisiert']);
}

// ── DELETE ──────────────────────────────────────────────────
if ($method === 'DELETE') {
    require_auth();
    if (!$id) json_error('ID fehlt');

    $pdo = db();
    $pdo->prepare('DELETE FROM properties WHERE id = ?')->execute([$id]);

    json_ok(['message' => 'Immobilie gelöscht']);
}

json_error('Methode nicht erlaubt', 405);
