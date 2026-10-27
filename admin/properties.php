<?php

/**
 * admin/properties.php – Immobilien verwalten
 */
require_once __DIR__ . '/_auth_check.php';

$pdo    = db();
require_once __DIR__ . '/../includes/subscribers.php';

/**
 * Leitet sofort weiter und versendet danach (im Hintergrund) die Benachrichtigungen an Abonnenten.
 */
function redirect_and_notify(string $url, int $propertyId, bool $notify): void
{
    if (!$notify) {
        header('Location: ' . $url);
        exit;
    }
    ignore_user_abort(true);
    @set_time_limit(0);
    header('Location: ' . $url);
    header('Connection: close');
    header('Content-Length: 0');
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
    try {
        $res = subscribers_notify_property(db(), $propertyId);
        error_log(sprintf('Abonnenten-Benachrichtigung Immobilie #%d: %d gesendet, %d fehlgeschlagen', $propertyId, $res['sent'], $res['failed']));
    } catch (Throwable $e) {
        error_log('Abonnenten-Benachrichtigung fehlgeschlagen: ' . $e->getMessage());
    }
    exit;
}
$action = $_GET['action'] ?? 'list';
$id     = isset($_GET['id']) ? (int)$_GET['id'] : null;
$msg    = '';
$listSearch = trim((string)($_GET['q'] ?? ''));
$listStatus = trim((string)($_GET['status'] ?? ''));
$listSort = trim((string)($_GET['sort'] ?? 'newest'));

function ensure_extra_data_column(PDO $pdo): bool
{
    static $checked = false;
    static $hasColumn = false;

    if ($checked) {
        return $hasColumn;
    }

    $checked = true;
    try {
        $stmt = $pdo->query("SHOW COLUMNS FROM properties LIKE 'extra_data'");
        $hasColumn = (bool)$stmt->fetch();
        if (!$hasColumn) {
            $pdo->exec('ALTER TABLE properties ADD COLUMN extra_data JSON NULL AFTER video_url');
            $hasColumn = true;
        }
    } catch (Throwable $e) {
        error_log('Could not ensure properties.extra_data column: ' . $e->getMessage());
        $hasColumn = false;
    }

    return $hasColumn;
}

function normalize_upload_list($raw): array
{
    $decoded = json_decode((string)$raw, true);
    if (!is_array($decoded)) {
        return [];
    }

    $items = [];
    foreach ($decoded as $item) {
        if (!is_array($item)) {
            continue;
        }
        $url = trim((string)($item['url'] ?? ''));
        if ($url === '') {
            continue;
        }
        $items[] = [
            'url'  => $url,
            'name' => trim((string)($item['name'] ?? '')),
            'mime' => trim((string)($item['mime'] ?? '')),
        ];
    }

    return array_values($items);
}

$hasExtraDataColumn = ensure_extra_data_column($pdo);

// ── Löschen ──────────────────────────────────────────────────
if ($action === 'delete' && $id) {
    if (!csrf_verify($_GET['token'] ?? '')) {
        $msg = 'error:Ungültiges Sicherheitstoken.';
    } else {
        $pdo->prepare('DELETE FROM properties WHERE id = ?')->execute([$id]);
        header('Location: ' . BASE . '/admin/properties.php?deleted=1');
        exit;
    }
}

if ($action === 'toggle_visibility' && $id) {
    if (!csrf_verify($_GET['token'] ?? '')) {
        $msg = 'error:Ungültiges Sicherheitstoken.';
    } else {
        $stmt = $pdo->prepare('SELECT status FROM properties WHERE id = ? LIMIT 1');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        if ($row) {
            $newStatus = ($row['status'] === 'available') ? 'inactive' : 'available';
            $pdo->prepare('UPDATE properties SET status = ? WHERE id = ?')->execute([$newStatus, $id]);
            header('Location: ' . BASE . '/admin/properties.php?toggled=1');
            exit;
        }
    }
}

// ── Speichern (neu / bearbeiten) ────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && in_array($action, ['new', 'edit'])) {
    if (!csrf_verify($_POST['csrf_token'] ?? '')) {
        $msg = 'error:Ungültiges Sicherheitstoken.';
    } else {
        $d = $_POST;
        $features = array_filter(array_map('trim', explode("\n", $d['features_text'] ?? '')));
        // Support new drag-and-drop uploader (images_json) and legacy textarea (images_text)
        if (!empty($d['images_json'])) {
            $imagesRaw = json_decode($d['images_json'], true) ?: [];
            // Normalize: accept both plain URL strings and {url, label} objects
            $images = array_values(array_filter(array_map(function ($img) {
                if (is_string($img)) return trim($img) !== '' ? ['url' => trim($img)] : null;
                if (is_array($img) && !empty($img['url'])) return $img;
                return null;
            }, $imagesRaw)));
        } else {
            // Legacy: plain URL lines
            $images = array_values(array_filter(array_map(function ($url) {
                return trim($url) !== '' ? ['url' => trim($url)] : null;
            }, explode("\n", $d['images_text'] ?? ''))));
        }

        $floorplans = normalize_upload_list($d['floorplans_json'] ?? '[]');
        $documents = normalize_upload_list($d['documents_json'] ?? '[]');

        $energyCertificateType = trim((string)($d['energy_certificate_type'] ?? ''));
        $energyValueKind = trim((string)($d['energy_value_kind'] ?? ''));
        $energyValueRaw = trim((string)($d['energy_value'] ?? ''));
        $energyValue = ($energyValueRaw === '' ? null : (float)$energyValueRaw);
        $energyClass = trim((string)($d['energy_efficiency_class'] ?? ''));
        $energySource = trim((string)($d['energy_source'] ?? ''));
        $buildingYearRaw = trim((string)($d['building_year'] ?? ''));
        $buildingYear = ($buildingYearRaw === '' ? null : (int)$buildingYearRaw);

        $validationErrors = [];

        $extraData = [
            'energy_certificate_type' => $energyCertificateType,
            'energy_value_kind' => $energyValueKind,
            'energy_value' => $energyValue,
            'energy_efficiency_class' => $energyClass,
            'energy_source' => $energySource,
            'building_year' => $buildingYear,
            'floor' => trim((string)($d['floor'] ?? '')),
            'has_elevator' => !empty($d['has_elevator']),
            'last_modernization_year' => (($d['last_modernization_year'] ?? '') !== '' ? (int)$d['last_modernization_year'] : null),
            'house_fee' => (($d['house_fee'] ?? '') !== '' ? (float)$d['house_fee'] : null),
            'parking_info' => trim((string)($d['parking_info'] ?? '')),
            'commission_required' => !empty($d['commission_required']),
            'commission_amount' => (($d['commission_amount'] ?? '') !== '' ? (float)$d['commission_amount'] : null),
            'commission_unit' => trim((string)($d['commission_unit'] ?? 'percent')),
            'commission_payer' => trim((string)($d['commission_payer'] ?? '')),
            'hide_exact_address' => !empty($d['hide_exact_address']),
            'address_public_note' => trim((string)($d['address_public_note'] ?? '')),
            'floorplans' => $floorplans,
            'documents' => $documents,
        ];

        if (!empty($validationErrors)) {
            $msg = 'error:' . implode(' ', $validationErrors);
        }

        $fields = [
            $d['title'] ?? '',
            slugify($d['title'] ?? ''),
            $d['short_description'] ?? '',
            $d['description'] ?? '',
            $d['type'] ?? 'Wohnung',
            $d['offer_type'] ?? 'Miete',
            (float)($d['price'] ?? 0),
            (float)($d['extra_costs'] ?? 0),
            (float)($d['heating_costs'] ?? 0),
            (float)($d['warm_rent'] ?? 0),
            (float)($d['deposit'] ?? 0),
            ($d['area'] !== '' ? (float)$d['area'] : null),
            (float)($d['rooms'] ?? 0),
            (int)($d['bathrooms'] ?? 1),
            $d['location'] ?? '',
            $d['zip'] ?? '',
            $d['address'] ?? '',
            $d['available_from'] ?? '',
            json_encode(array_values($features), JSON_UNESCAPED_UNICODE),
            json_encode($images, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            !empty($d['main_image']) ? $d['main_image'] : (!empty($images[0]['url']) ? $images[0]['url'] : ''),
            ($d['video_url'] !== '' ? $d['video_url'] : null),
            ($hasExtraDataColumn ? json_encode($extraData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : null),
            $d['status'] ?? 'available',
            !empty($d['featured']) ? 1 : 0,
        ];

        if ($msg === '') {
            if ($action === 'new') {
                if ($hasExtraDataColumn) {
                    $pdo->prepare('INSERT INTO properties (title,slug,short_description,description,type,offer_type,price,extra_costs,heating_costs,warm_rent,deposit,area,rooms,bathrooms,location,zip,address,available_from,features,images,main_image,video_url,extra_data,status,featured,created_at) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,NOW())')
                        ->execute($fields);
                } else {
                    array_splice($fields, 22, 1);
                    $pdo->prepare('INSERT INTO properties (title,slug,short_description,description,type,offer_type,price,extra_costs,heating_costs,warm_rent,deposit,area,rooms,bathrooms,location,zip,address,available_from,features,images,main_image,video_url,status,featured,created_at) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,NOW())')
                        ->execute($fields);
                }

                $newId = $pdo->lastInsertId();
                redirect_and_notify(BASE . "/admin/properties.php?action=edit&id=$newId&saved=1", (int)$newId, ($d['status'] ?? 'available') === 'available' && !empty($d['notify_subscribers']));
            } else {
                $fields[] = $id;
                if ($hasExtraDataColumn) {
                    $pdo->prepare('UPDATE properties SET title=?,slug=?,short_description=?,description=?,type=?,offer_type=?,price=?,extra_costs=?,heating_costs=?,warm_rent=?,deposit=?,area=?,rooms=?,bathrooms=?,location=?,zip=?,address=?,available_from=?,features=?,images=?,main_image=?,video_url=?,extra_data=?,status=?,featured=? WHERE id=?')
                        ->execute($fields);
                } else {
                    array_splice($fields, 22, 1);
                    $pdo->prepare('UPDATE properties SET title=?,slug=?,short_description=?,description=?,type=?,offer_type=?,price=?,extra_costs=?,heating_costs=?,warm_rent=?,deposit=?,area=?,rooms=?,bathrooms=?,location=?,zip=?,address=?,available_from=?,features=?,images=?,main_image=?,video_url=?,status=?,featured=? WHERE id=?')
                        ->execute($fields);
                }
                redirect_and_notify(BASE . "/admin/properties.php?action=edit&id=$id&saved=1", (int)$id, ($d['status'] ?? 'available') === 'available' && !empty($d['notify_subscribers']));
            }
        }
    }
}

function slugify(string $text): string
{
    $text = mb_strtolower($text, 'UTF-8');
    $text = str_replace(['ä', 'ö', 'ü', 'ß'], ['ae', 'oe', 'ue', 'ss'], $text);
    $text = preg_replace('/[^a-z0-9]+/', '-', $text);
    return trim($text, '-');
}

// ── Daten laden ──────────────────────────────────────────────
$property = null;
if ($action === 'edit' && $id) {
    $property = $pdo->prepare('SELECT * FROM properties WHERE id = ?');
    $property->execute([$id]);
    $property = $property->fetch();
    if (!$property) {
        $action = 'list';
    }
}

$extraData = [];
if ($action !== 'list') {
    $rawExtraData = $property['extra_data'] ?? '{}';
    $extraData = json_decode((string)$rawExtraData, true);
    if (!is_array($extraData)) {
        $extraData = [];
    }
}

$existingFloorplans = normalize_upload_list(json_encode($extraData['floorplans'] ?? [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
$existingDocuments = normalize_upload_list(json_encode($extraData['documents'] ?? [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
$properties = [];
if ($action === 'list') {
    $where = [];
    $params = [];

    if ($listSearch !== '') {
        $where[] = '(title LIKE ? OR location LIKE ? OR slug LIKE ?)';
        $params[] = '%' . $listSearch . '%';
        $params[] = '%' . $listSearch . '%';
        $params[] = '%' . $listSearch . '%';
    }

    if ($listStatus !== '') {
        $where[] = 'status = ?';
        $params[] = $listStatus;
    }

    $orderBy = match ($listSort) {
        'oldest' => 'created_at ASC',
        'price_asc' => 'price ASC',
        'price_desc' => 'price DESC',
        'title_asc' => 'title ASC',
        default => 'created_at DESC'
    };

    $sql = 'SELECT id,title,slug,location,type,offer_type,price,status,featured,main_image,images,created_at,updated_at FROM properties';
    if (!empty($where)) {
        $sql .= ' WHERE ' . implode(' AND ', $where);
    }
    $sql .= ' ORDER BY ' . $orderBy;

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $properties = $stmt->fetchAll();
}

// ── Template ─────────────────────────────────────────────────
$pageTitle = $action === 'list' ? 'Immobilien' : ($action === 'new' ? 'Neue Immobilie' : 'Immobilie bearbeiten');
?>
<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($pageTitle) ?> – Admin</title>
    <meta name="robots" content="noindex, nofollow">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" crossorigin="anonymous">
    <style>
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .form-row.three {
            grid-template-columns: 1fr 1fr 1fr;
        }

        .check-wrap {
            display: flex;
            align-items: center;
            gap: .5rem;
        }

        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
            gap: .5rem;
        }

        .top-bar h1 {
            margin: 0;
            font-size: 1.35rem;
            color: var(--navy, #1a3a5c);
        }

        .btn-success {
            background: #27ae60;
            color: #fff;
        }

        .btn-success:hover {
            background: #219a52;
        }

        .badge.available {
            background: #e8f8e8;
            color: #27ae60;
        }

        .badge.rented {
            background: #fef0e7;
            color: #e67e22;
        }

        .badge.sold {
            background: #fee;
            color: #c0392b;
        }

        .badge.inactive {
            background: #e2e8f0;
            color: #334155;
        }

        .thumb-sm {
            width: 52px;
            height: 40px;
            object-fit: cover;
            border-radius: 6px;
            border: 1px solid #e2e8f0;
            background: #f8fafc;
        }

        .table-tools {
            display: flex;
            gap: .65rem;
            flex-wrap: wrap;
            align-items: center;
            margin-bottom: .9rem;
        }

        .table-tools input,
        .table-tools select {
            padding: .55rem .7rem;
            border: 1px solid #d6dbe1;
            border-radius: 8px;
            min-width: 150px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: .88rem;
        }

        th {
            text-align: left;
            padding: .65rem 1rem;
            border-bottom: 1px solid #e8ecf0;
            color: #64748b;
            font-size: .75rem;
            font-weight: 700;
            text-transform: uppercase;
            background: #f8fafc;
        }

        td {
            padding: .75rem 1rem;
            border-bottom: 1px solid #f0f0f0;
            vertical-align: middle;
        }

        tr:hover td {
            background: #fafbff;
        }

        .card-inner {
            padding: 1.5rem;
        }
    </style>
</head>

<body>
    <?php
    $activePage  = 'properties';
    $newContacts = (int)$pdo->query("SELECT COUNT(*) FROM contact_inquiries WHERE status='new'")->fetchColumn();
    require __DIR__ . '/_sidebar.php';
    ?>
    <div class="main">
        <header class="topbar">
            <div class="topbar-title">
                <?= $action === 'new' ? 'Neue Immobilie' : ($action === 'edit' ? 'Immobilie bearbeiten' : 'Immobilien') ?>
            </div>
            <div class="topbar-meta">
                <div class="topbar-user">
                    <div class="avatar"><?= strtoupper(substr(htmlspecialchars($admin['username']), 0, 1)) ?></div>
                    <span><?= htmlspecialchars($admin['username']) ?></span>
                </div>
            </div>
        </header>
        <div class="content">

            <?php if ($action === 'list'): ?>
                <div class="top-bar">
                    <h1>Immobilien (<?= count($properties) ?>)</h1>
                    <a href="?action=new" class="btn btn-primary">+ Neue Immobilie</a>
                </div>
                <?php if (isset($_GET['deleted'])): ?><div class="alert alert-success">Immobilie gelöscht.</div><?php endif; ?>
                <?php if (isset($_GET['toggled'])): ?><div class="alert alert-success">Sichtbarkeit aktualisiert.</div><?php endif; ?>
                <div class="card">
                    <form method="get" class="table-tools">
                        <input type="hidden" name="action" value="list">
                        <input type="text" name="q" value="<?= htmlspecialchars($listSearch) ?>" placeholder="Suche: Titel, Ort, Slug">
                        <select name="status">
                            <option value="">Alle Status</option>
                            <option value="available" <?= $listStatus === 'available' ? 'selected' : '' ?>>Verfügbar</option>
                            <option value="inactive" <?= $listStatus === 'inactive' ? 'selected' : '' ?>>Inaktiv</option>
                            <option value="rented" <?= $listStatus === 'rented' ? 'selected' : '' ?>>Vermietet</option>
                            <option value="sold" <?= $listStatus === 'sold' ? 'selected' : '' ?>>Verkauft</option>
                        </select>
                        <select name="sort">
                            <option value="newest" <?= $listSort === 'newest' ? 'selected' : '' ?>>Neueste zuerst</option>
                            <option value="oldest" <?= $listSort === 'oldest' ? 'selected' : '' ?>>Älteste zuerst</option>
                            <option value="price_asc" <?= $listSort === 'price_asc' ? 'selected' : '' ?>>Preis aufsteigend</option>
                            <option value="price_desc" <?= $listSort === 'price_desc' ? 'selected' : '' ?>>Preis absteigend</option>
                            <option value="title_asc" <?= $listSort === 'title_asc' ? 'selected' : '' ?>>Titel A-Z</option>
                        </select>
                        <button class="btn btn-secondary" type="submit">Filtern</button>
                        <a class="btn btn-outline" href="?action=list">Zurücksetzen</a>
                    </form>
                    <table>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Hauptbild</th>
                                <th>Titel</th>
                                <th>Bilder</th>
                                <th>Ort</th>
                                <th>Typ</th>
                                <th>Preis</th>
                                <th>Status</th>
                                <th>Sichtbar</th>
                                <th>Veröffentlicht</th>
                                <th>Aktionen</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($properties as $p): ?>
                                <?php
                                $images = json_decode((string)($p['images'] ?? '[]'), true);
                                $imageCount = is_array($images) ? count($images) : 0;
                                $isVisible = ($p['status'] === 'available');
                                ?>
                                <tr>
                                    <td><?= $p['id'] ?></td>
                                    <td>
                                        <?php if (!empty($p['main_image'])): ?>
                                            <img class="thumb-sm" src="<?= htmlspecialchars($p['main_image']) ?>" alt="">
                                        <?php else: ?>
                                            <span style="color:#94a3b8;">—</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= htmlspecialchars($p['title']) ?> <?= $p['featured'] ? '⭐' : '' ?></td>
                                    <td><?= $imageCount ?></td>
                                    <td><?= htmlspecialchars($p['location']) ?></td>
                                    <td><?= $p['type'] ?> / <?= $p['offer_type'] ?></td>
                                    <td><?= number_format($p['price'], 0, ',', '.') ?> €</td>
                                    <td>
                                        <span class="badge <?= htmlspecialchars($p['status']) ?>"><?=
                                                                                                    match ($p['status']) {
                                                                                                        'available' => 'Verfügbar',
                                                                                                        'inactive' => 'Inaktiv',
                                                                                                        'rented' => 'Vermietet',
                                                                                                        'sold' => 'Verkauft',
                                                                                                        default => ucfirst((string)$p['status'])
                                                                                                    }
                                                                                                    ?></span>
                                    </td>
                                    <td><?= $isVisible ? 'Ja' : 'Nein' ?></td>
                                    <td><?= date('d.m.Y', strtotime((string)$p['created_at'])) ?></td>
                                    <td>
                                        <a href="?action=edit&id=<?= $p['id'] ?>" class="btn btn-sm btn-secondary">Bearbeiten</a>
                                        <a href="?action=toggle_visibility&id=<?= $p['id'] ?>&token=<?= urlencode(CSRF_TOKEN) ?>" class="btn btn-sm btn-outline"><?= $isVisible ? 'Unsichtbar' : 'Sichtbar' ?></a>
                                        <a href="?action=delete&id=<?= $p['id'] ?>&token=<?= urlencode(CSRF_TOKEN) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Wirklich löschen?')">Löschen</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (empty($properties)): ?>
                                <tr>
                                    <td colspan="11" style="text-align:center;color:#999;padding:2rem;">Keine Immobilien vorhanden.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

            <?php else: // new or edit 
            ?>
                <div class="top-bar">
                    <h1><?= $action === 'new' ? 'Neue Immobilie anlegen' : 'Immobilie bearbeiten' ?></h1>
                    <a href="?action=list" class="btn btn-secondary">← Zurück</a>
                </div>
                <?php if (isset($_GET['saved'])): ?><div class="alert alert-success">Gespeichert!</div><?php endif; ?>
                <?php if ($msg && strpos($msg, 'error:') === 0): ?><div class="alert alert-error"><?= htmlspecialchars(substr($msg, 6)) ?></div><?php endif; ?>

                <form method="post" id="adminPropertyForm">
                    <?= csrf_field() ?>
                    <div class="card">
                        <h2 style="margin:0 0 1rem;font-size:1rem;color:#555;">Grunddaten</h2>
                        <div class="form-group">
                            <label>Titel *</label>
                            <input type="text" name="title" value="<?= htmlspecialchars($property['title'] ?? '') ?>" required>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Typ</label>
                                <select name="type">
                                    <?php foreach (['Wohnung', 'Haus', 'Gewerbe', 'Grundstück'] as $t): ?>
                                        <option value="<?= $t ?>" <?= ($property['type'] ?? 'Wohnung') === $t ? 'selected' : '' ?>><?= $t ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Angebotsart</label>
                                <select name="offer_type">
                                    <option value="Miete" <?= ($property['offer_type'] ?? 'Miete') === 'Miete' ? 'selected' : '' ?>>Miete</option>
                                    <option value="Kauf" <?= ($property['offer_type'] ?? '') === 'Kauf' ? 'selected' : '' ?>>Kauf</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Kurzbeschreibung</label>
                            <input type="text" name="short_description" value="<?= htmlspecialchars($property['short_description'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label>Beschreibung</label>
                            <textarea name="description" style="min-height:180px;"><?= htmlspecialchars($property['description'] ?? '') ?></textarea>
                        </div>
                    </div>

                    <div class="card">
                        <h2 style="margin:0 0 1rem;font-size:1rem;color:#555;">Kosten</h2>
                        <div class="form-row three">
                            <div class="form-group"><label>Kaltmiete / Kaufpreis (€) *</label><input type="number" id="price_input" name="price" value="<?= $property['price'] ?? '' ?>" step="0.01" required></div>
                            <div class="form-group"><label>Nebenkosten (€)</label><input type="number" id="extra_costs_input" name="extra_costs" value="<?= $property['extra_costs'] ?? 0 ?>" step="0.01"></div>
                            <div class="form-group"><label>Heizkosten (€)</label><input type="number" id="heating_costs_input" name="heating_costs" value="<?= $property['heating_costs'] ?? 0 ?>" step="0.01"></div>
                        </div>
                        <div class="form-row">
                            <div class="form-group"><label>Warmmiete (€)</label><input type="number" id="warm_rent_input" name="warm_rent" value="<?= $property['warm_rent'] ?? 0 ?>" step="0.01"></div>
                            <div class="form-group"><label>Kaution (€)</label><input type="number" name="deposit" value="<?= $property['deposit'] ?? 0 ?>" step="0.01"></div>
                        </div>
                        <p style="margin:.25rem 0 0;color:#64748b;font-size:.82rem;">Warmmiete wird automatisch aus Kaltmiete + Nebenkosten + Heizkosten berechnet, bis Sie den Wert manuell ändern.</p>
                    </div>

                    <div class="card">
                        <h2 style="margin:0 0 1rem;font-size:1rem;color:#555;">Fläche &amp; Zimmer</h2>
                        <div class="form-row three">
                            <div class="form-group"><label>Wohnfläche (m²)</label><input type="number" name="area" value="<?= $property['area'] ?? '' ?>" step="0.01"></div>
                            <div class="form-group"><label>Zimmer</label><input type="number" name="rooms" value="<?= $property['rooms'] ?? '' ?>" step="0.5"></div>
                            <div class="form-group"><label>Badezimmer</label><input type="number" name="bathrooms" value="<?= $property['bathrooms'] ?? 1 ?>"></div>
                        </div>
                    </div>

                    <div class="card">
                        <h2 style="margin:0 0 1rem;font-size:1rem;color:#555;">Energieausweis (Pflicht für verfügbare Inserate)</h2>
                        <div class="form-row three">
                            <div class="form-group">
                                <label>Energieausweistyp</label>
                                <select name="energy_certificate_type">
                                    <option value="" <?= (($extraData['energy_certificate_type'] ?? '') === '' ? 'selected' : '') ?>>Bitte wählen</option>
                                    <option value="bedarfsausweis" <?= (($extraData['energy_certificate_type'] ?? '') === 'bedarfsausweis' ? 'selected' : '') ?>>Bedarfsausweis</option>
                                    <option value="verbrauchsausweis" <?= (($extraData['energy_certificate_type'] ?? '') === 'verbrauchsausweis' ? 'selected' : '') ?>>Verbrauchsausweis</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Art Kennwert</label>
                                <select name="energy_value_kind">
                                    <option value="" <?= (($extraData['energy_value_kind'] ?? '') === '' ? 'selected' : '') ?>>Bitte wählen</option>
                                    <option value="bedarf" <?= (($extraData['energy_value_kind'] ?? '') === 'bedarf' ? 'selected' : '') ?>>Endenergiebedarf</option>
                                    <option value="verbrauch" <?= (($extraData['energy_value_kind'] ?? '') === 'verbrauch' ? 'selected' : '') ?>>Endenergieverbrauch</option>
                                </select>
                            </div>
                            <div class="form-group"><label>Energiekennwert (kWh/(m²·a))</label><input type="number" name="energy_value" step="0.1" value="<?= htmlspecialchars((string)($extraData['energy_value'] ?? '')) ?>"></div>
                        </div>
                        <div class="form-row three">
                            <div class="form-group">
                                <label>Energieeffizienzklasse</label>
                                <select name="energy_efficiency_class">
                                    <option value="" <?= (($extraData['energy_efficiency_class'] ?? '') === '' ? 'selected' : '') ?>>Bitte wählen</option>
                                    <?php foreach (['A+', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H'] as $clazz): ?>
                                        <option value="<?= $clazz ?>" <?= (($extraData['energy_efficiency_class'] ?? '') === $clazz ? 'selected' : '') ?>><?= $clazz ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Wesentlicher Energieträger</label>
                                <select name="energy_source">
                                    <option value="" <?= (($extraData['energy_source'] ?? '') === '' ? 'selected' : '') ?>>Bitte wählen</option>
                                    <?php foreach (['Gas', 'Öl', 'Fernwärme', 'Wärmepumpe', 'Strom', 'Pellets', 'Sonstige'] as $source): ?>
                                        <option value="<?= $source ?>" <?= (($extraData['energy_source'] ?? '') === $source ? 'selected' : '') ?>><?= $source ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group"><label>Baujahr Gebäude</label><input type="number" name="building_year" value="<?= htmlspecialchars((string)($extraData['building_year'] ?? '')) ?>" min="1800" max="2100"></div>
                        </div>
                    </div>

                    <div class="card">
                        <h2 style="margin:0 0 1rem;font-size:1rem;color:#555;">Erweiterte Objektdaten</h2>
                        <div class="form-row three">
                            <div class="form-group"><label>Etage</label><input type="text" name="floor" value="<?= htmlspecialchars((string)($extraData['floor'] ?? '')) ?>" placeholder="z.B. 3. OG"></div>
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <div class="check-wrap" style="margin-top:.6rem;">
                                    <input type="checkbox" name="has_elevator" id="has_elevator" value="1" <?= !empty($extraData['has_elevator']) ? 'checked' : '' ?>>
                                    <label for="has_elevator" style="margin:0;font-weight:normal;">Aufzug vorhanden</label>
                                </div>
                            </div>
                            <div class="form-group"><label>Letzte Modernisierung (Jahr)</label><input type="number" name="last_modernization_year" value="<?= htmlspecialchars((string)($extraData['last_modernization_year'] ?? '')) ?>" min="1800" max="2100"></div>
                        </div>
                        <div class="form-row three">
                            <div class="form-group"><label>Hausgeld (€/Monat, Kauf)</label><input type="number" name="house_fee" value="<?= htmlspecialchars((string)($extraData['house_fee'] ?? '')) ?>" step="0.01"></div>
                            <div class="form-group"><label>Stellplatz/Garage Kosten</label><input type="text" name="parking_info" value="<?= htmlspecialchars((string)($extraData['parking_info'] ?? '')) ?>" placeholder="z.B. Garage 60 €/Monat"></div>
                            <div class="form-group"></div>
                        </div>
                        <div class="form-row three">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <div class="check-wrap" style="margin-top:.6rem;">
                                    <input type="checkbox" name="commission_required" id="commission_required" value="1" <?= !empty($extraData['commission_required']) ? 'checked' : '' ?>>
                                    <label for="commission_required" style="margin:0;font-weight:normal;">Provision/Courtage fällt an</label>
                                </div>
                            </div>
                            <div class="form-group"><label>Courtage-Höhe</label><input type="number" name="commission_amount" value="<?= htmlspecialchars((string)($extraData['commission_amount'] ?? '')) ?>" step="0.01"></div>
                            <div class="form-group">
                                <label>Einheit</label>
                                <select name="commission_unit">
                                    <option value="percent" <?= (($extraData['commission_unit'] ?? 'percent') === 'percent' ? 'selected' : '') ?>>%</option>
                                    <option value="eur" <?= (($extraData['commission_unit'] ?? '') === 'eur' ? 'selected' : '') ?>>EUR</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group"><label>Wer zahlt die Courtage?</label><input type="text" name="commission_payer" value="<?= htmlspecialchars((string)($extraData['commission_payer'] ?? '')) ?>" placeholder="z.B. Käufer, Verkäufer, Mieter"></div>
                            <div></div>
                        </div>
                    </div>

                    <div class="card">
                        <h2 style="margin:0 0 1rem;font-size:1rem;color:#555;">Lage</h2>
                        <div class="form-row">
                            <div class="form-group"><label>Ort *</label><input type="text" name="location" value="<?= htmlspecialchars($property['location'] ?? '') ?>" required></div>
                            <div class="form-group"><label>PLZ</label><input type="text" name="zip" value="<?= htmlspecialchars($property['zip'] ?? '') ?>"></div>
                        </div>
                        <div class="form-row">
                            <div class="form-group"><label>Adresse</label><input type="text" name="address" value="<?= htmlspecialchars($property['address'] ?? '') ?>"></div>
                            <div class="form-group"><label>Verfügbar ab</label><input type="text" name="available_from" value="<?= htmlspecialchars($property['available_from'] ?? '') ?>" placeholder="z.B. 01.04.2026 oder Sofort"></div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <div class="check-wrap" style="margin-top:.35rem;">
                                    <input type="checkbox" name="hide_exact_address" id="hide_exact_address" value="1" <?= !empty($extraData['hide_exact_address']) ? 'checked' : '' ?>>
                                    <label for="hide_exact_address" style="margin:0;font-weight:normal;">Genaue Adresse im Inserat verbergen</label>
                                </div>
                            </div>
                            <div class="form-group"><label>Öffentliche Ortsangabe (optional)</label><input type="text" name="address_public_note" value="<?= htmlspecialchars((string)($extraData['address_public_note'] ?? '')) ?>" placeholder="z.B. Frankfurt-Bornheim"></div>
                        </div>
                    </div>

                    <div class="card" id="upload-card">
                        <h2 style="margin:0 0 1rem;font-size:1rem;color:#555;">Bilder &amp; Video</h2>

                        <?php
                        // Prepare existing images for JS
                        $existingImagesRaw = json_decode($property['images'] ?? '[]', true) ?: [];
                        $existingImages = array_map(function ($img) {
                            if (is_string($img)) return ['url' => $img, 'label' => ''];
                            return ['url' => $img['url'] ?? '', 'label' => $img['label'] ?? ''];
                        }, $existingImagesRaw);
                        ?>

                        <!-- Hidden inputs for form submission -->
                        <input type="hidden" name="images_json" id="images_json_input" value="<?= htmlspecialchars(json_encode($existingImages, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), ENT_QUOTES) ?>">
                        <input type="hidden" name="main_image" id="main_image_input" value="<?= htmlspecialchars($property['main_image'] ?? '') ?>">
                        <input type="hidden" name="floorplans_json" id="floorplans_json_input" value="<?= htmlspecialchars(json_encode($existingFloorplans, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), ENT_QUOTES) ?>">
                        <input type="hidden" name="documents_json" id="documents_json_input" value="<?= htmlspecialchars(json_encode($existingDocuments, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), ENT_QUOTES) ?>">

                        <!-- Drag & Drop Upload Zone -->
                        <div class="upload-zone" id="upload-zone">
                            <div class="upload-zone-inner">
                                <i class="fas fa-cloud-upload-alt" style="font-size:2rem;color:#6b7280;margin-bottom:.5rem;"></i>
                                <p style="margin:0;color:#374151;font-weight:600;">Bilder hierher ziehen</p>
                                <p style="margin:.25rem 0 .75rem;color:#6b7280;font-size:.85rem;">oder</p>
                                <button type="button" class="btn btn-secondary" id="upload-browse-btn">Dateien auswählen</button>
                                <p style="margin:.5rem 0 0;color:#9ca3af;font-size:.8rem;">JPG, PNG, WebP · max. 10 MB pro Bild</p>
                            </div>
                            <input type="file" id="upload-file-input" accept="image/jpeg,image/png,image/webp,image/gif" multiple style="display:none">
                        </div>

                        <!-- Upload Progress -->
                        <div id="upload-progress" style="display:none;margin:.75rem 0;">
                            <div style="height:4px;background:#e5e7eb;border-radius:2px;overflow:hidden;">
                                <div id="upload-progress-bar" style="height:100%;background:#1a56a4;width:0%;transition:width .3s;"></div>
                            </div>
                            <p id="upload-progress-text" style="font-size:.8rem;color:#6b7280;margin:.25rem 0 0;"></p>
                        </div>

                        <!-- Image Previews (sortable) -->
                        <div class="upload-previews" id="upload-previews">
                            <!-- Populated by JS from existing images and new uploads -->
                        </div>

                        <hr style="border:none;border-top:1px solid #e5e7eb;margin:1.25rem 0;">

                        <h3 style="margin:0 0 .5rem;font-size:.95rem;color:#475569;">Grundrisse</h3>
                        <div class="upload-zone" id="floorplan-zone" style="padding:1rem;">
                            <div>
                                <p style="margin:0;color:#374151;font-weight:600;">Grundriss hochladen</p>
                                <p style="margin:.3rem 0 0;color:#9ca3af;font-size:.8rem;">PNG, JPG, WebP oder PDF</p>
                            </div>
                            <input type="file" id="floorplan-file-input" accept="image/jpeg,image/png,image/webp,application/pdf" multiple style="display:none">
                        </div>
                        <div class="file-list" id="floorplan-list" style="margin-top:.6rem;"></div>

                        <h3 style="margin:1.1rem 0 .5rem;font-size:.95rem;color:#475569;">Dokumente</h3>
                        <div class="upload-zone" id="document-zone" style="padding:1rem;">
                            <div>
                                <p style="margin:0;color:#374151;font-weight:600;">Dokumente hochladen</p>
                                <p style="margin:.3rem 0 0;color:#9ca3af;font-size:.8rem;">PDF, DOC, DOCX (max. 15 MB)</p>
                            </div>
                            <input type="file" id="document-file-input" accept="application/pdf,.pdf,.doc,.docx,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document" multiple style="display:none">
                        </div>
                        <div class="file-list" id="document-list" style="margin-top:.6rem;"></div>

                        <!-- Video URL -->
                        <div class="form-group" style="margin-top:1rem;">
                            <label>Video-URL (optional)</label>
                            <input type="url" name="video_url" value="<?= htmlspecialchars($property['video_url'] ?? '') ?>" placeholder="https://youtube.com/...">
                        </div>
                    </div>

                    <style>
                        .upload-zone {
                            border: 2px dashed #d1d5db;
                            border-radius: 8px;
                            padding: 2rem;
                            text-align: center;
                            transition: border-color .2s, background .2s;
                            cursor: pointer;
                        }

                        .upload-zone.drag-over {
                            border-color: #1a56a4;
                            background: #eff6ff;
                        }

                        .upload-zone-inner {
                            pointer-events: none;
                        }

                        .upload-previews {
                            display: flex;
                            flex-wrap: wrap;
                            gap: .75rem;
                            margin-top: 1rem;
                        }

                        .upload-preview-item {
                            position: relative;
                            width: 140px;
                            flex-shrink: 0;
                            border: 1px solid #e5e7eb;
                            border-radius: 6px;
                            overflow: hidden;
                            background: #f9fafb;
                        }

                        .upload-preview-item.is-main {
                            border-color: #1a56a4;
                            box-shadow: 0 0 0 2px #1a56a4;
                        }

                        .upload-preview-item img {
                            width: 100%;
                            height: 90px;
                            object-fit: cover;
                            display: block;
                        }

                        .upload-preview-item .preview-label {
                            padding: 4px 6px;
                        }

                        .upload-preview-item .preview-label input {
                            width: 100%;
                            border: 1px solid #e5e7eb;
                            border-radius: 4px;
                            padding: 2px 6px;
                            font-size: .75rem;
                        }

                        .upload-preview-item .preview-actions {
                            display: flex;
                            gap: 4px;
                            padding: 4px 6px;
                        }

                        .upload-preview-item .preview-actions button {
                            flex: 1;
                            font-size: .7rem;
                            padding: 2px 4px;
                            border: 1px solid #e5e7eb;
                            border-radius: 3px;
                            cursor: pointer;
                            background: #fff;
                        }

                        .upload-preview-item .preview-actions .del-btn {
                            background: #fee2e2;
                            border-color: #fca5a5;
                            color: #991b1b;
                        }

                        .upload-preview-item .main-badge {
                            position: absolute;
                            top: 4px;
                            left: 4px;
                            background: #1a56a4;
                            color: #fff;
                            font-size: .65rem;
                            padding: 1px 5px;
                            border-radius: 3px;
                        }

                        .uploading-overlay {
                            position: absolute;
                            inset: 0;
                            background: rgba(255, 255, 255, .8);
                            display: flex;
                            align-items: center;
                            justify-content: center;
                        }

                        .file-list {
                            display: grid;
                            gap: .45rem;
                        }

                        .file-row {
                            display: flex;
                            align-items: center;
                            justify-content: space-between;
                            border: 1px solid #e5e7eb;
                            border-radius: 6px;
                            padding: .45rem .6rem;
                            background: #fff;
                            font-size: .84rem;
                        }

                        .file-row-name {
                            color: #1f2937;
                            overflow: hidden;
                            text-overflow: ellipsis;
                            white-space: nowrap;
                            padding-right: .5rem;
                        }

                        .file-row button {
                            border: 1px solid #fca5a5;
                            background: #fee2e2;
                            color: #991b1b;
                            border-radius: 4px;
                            font-size: .75rem;
                            padding: 2px 6px;
                            cursor: pointer;
                        }
                    </style>

                    <script>
                        (function() {
                            var CSRF_TOKEN = '<?= htmlspecialchars(generate_csrf_token(), ENT_QUOTES) ?>';
                            var zone = document.getElementById('upload-zone');
                            var fileInput = document.getElementById('upload-file-input');
                            var previews = document.getElementById('upload-previews');
                            var jsonInput = document.getElementById('images_json_input');
                            var mainInput = document.getElementById('main_image_input');
                            var floorplanZone = document.getElementById('floorplan-zone');
                            var floorplanInput = document.getElementById('floorplan-file-input');
                            var floorplanJsonInput = document.getElementById('floorplans_json_input');
                            var floorplanList = document.getElementById('floorplan-list');
                            var documentZone = document.getElementById('document-zone');
                            var documentInput = document.getElementById('document-file-input');
                            var documentJsonInput = document.getElementById('documents_json_input');
                            var documentList = document.getElementById('document-list');
                            var progressWrap = document.getElementById('upload-progress');
                            var progressBar = document.getElementById('upload-progress-bar');
                            var progressText = document.getElementById('upload-progress-text');
                            var priceInput = document.getElementById('price_input');
                            var extraInput = document.getElementById('extra_costs_input');
                            var heatingInput = document.getElementById('heating_costs_input');
                            var warmInput = document.getElementById('warm_rent_input');
                            var warmRentManuallyTouched = false;

                            // Image data array: [{url, label}]
                            var images = <?= json_encode($existingImages, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
                            var floorplans = <?= json_encode($existingFloorplans, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
                            var documents = <?= json_encode($existingDocuments, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;

                            function renderPreviews() {
                                previews.innerHTML = '';
                                images.forEach(function(img, i) {
                                    var item = document.createElement('div');
                                    item.className = 'upload-preview-item' + (i === 0 ? ' is-main' : '');
                                    item.innerHTML =
                                        (i === 0 ? '<div class="main-badge">Hauptbild</div>' : '') +
                                        '<img src="' + escHtml(img.url) + '" alt="" loading="lazy">' +
                                        '<div class="preview-label"><input type="text" placeholder="Raum (z.B. Küche)" value="' + escHtml(img.label || '') + '" data-idx="' + i + '"></div>' +
                                        '<div class="preview-actions">' +
                                        (i > 0 ? '<button type="button" data-action="main" data-idx="' + i + '">★ Haupt</button>' : '<span style="flex:1"></span>') +
                                        '<button type="button" data-action="del" data-idx="' + i + '" class="del-btn">✕</button>' +
                                        '</div>';
                                    previews.appendChild(item);
                                });
                                syncInputs();
                            }

                            function syncInputs() {
                                jsonInput.value = JSON.stringify(images);
                                mainInput.value = images.length > 0 ? images[0].url : '';
                                floorplanJsonInput.value = JSON.stringify(floorplans);
                                documentJsonInput.value = JSON.stringify(documents);
                            }

                            function escHtml(s) {
                                return String(s).replace(/&/g, '&amp;').replace(/"/g, '&quot;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
                            }

                            function renderSimpleFileList(targetEl, data, type) {
                                targetEl.innerHTML = '';
                                if (!data.length) {
                                    targetEl.innerHTML = '<div style="font-size:.8rem;color:#9ca3af;">Noch keine Dateien hochgeladen.</div>';
                                    return;
                                }

                                data.forEach(function(item, idx) {
                                    var row = document.createElement('div');
                                    row.className = 'file-row';
                                    row.innerHTML =
                                        '<a class="file-row-name" href="' + escHtml(item.url) + '" target="_blank" rel="noopener">' + escHtml(item.name || ('Datei ' + (idx + 1))) + '</a>' +
                                        '<button type="button" data-file-type="' + type + '" data-file-idx="' + idx + '">Entfernen</button>';
                                    targetEl.appendChild(row);
                                });
                            }

                            function renderAllFiles() {
                                renderSimpleFileList(floorplanList, floorplans, 'floorplan');
                                renderSimpleFileList(documentList, documents, 'document');
                                syncInputs();
                            }

                            previews.addEventListener('click', function(e) {
                                var btn = e.target.closest('[data-action]');
                                if (!btn) return;
                                var idx = parseInt(btn.dataset.idx);
                                if (btn.dataset.action === 'del') {
                                    images.splice(idx, 1);
                                } else if (btn.dataset.action === 'main') {
                                    var item = images.splice(idx, 1)[0];
                                    images.unshift(item);
                                }
                                renderPreviews();
                            });

                            previews.addEventListener('input', function(e) {
                                if (e.target.dataset.idx !== undefined) {
                                    images[parseInt(e.target.dataset.idx)].label = e.target.value;
                                    syncInputs();
                                }
                            });

                            document.addEventListener('click', function(e) {
                                var btn = e.target.closest('button[data-file-type]');
                                if (!btn) return;
                                var idx = parseInt(btn.getAttribute('data-file-idx'), 10);
                                if (btn.getAttribute('data-file-type') === 'floorplan') {
                                    floorplans.splice(idx, 1);
                                } else {
                                    documents.splice(idx, 1);
                                }
                                renderAllFiles();
                            });

                            // Drag & drop
                            zone.addEventListener('click', function() {
                                fileInput.click();
                            });
                            document.getElementById('upload-browse-btn').addEventListener('click', function(e) {
                                e.stopPropagation();
                                fileInput.click();
                            });
                            fileInput.addEventListener('change', function() {
                                handleFiles(this.files);
                                this.value = '';
                            });
                            zone.addEventListener('dragover', function(e) {
                                e.preventDefault();
                                zone.classList.add('drag-over');
                            });
                            zone.addEventListener('dragleave', function() {
                                zone.classList.remove('drag-over');
                            });
                            zone.addEventListener('drop', function(e) {
                                e.preventDefault();
                                zone.classList.remove('drag-over');
                                handleFiles(e.dataTransfer.files);
                            });

                            function handleFiles(files) {
                                var arr = Array.from(files).filter(f => /^image\/(jpeg|png|webp|gif)$/.test(f.type));
                                if (arr.length === 0) return;
                                uploadQueue(arr, 0);
                            }

                            function bindDropZone(zoneEl, inputEl, handler) {
                                zoneEl.addEventListener('click', function() {
                                    inputEl.click();
                                });
                                inputEl.addEventListener('change', function() {
                                    handler(Array.from(this.files || []));
                                    this.value = '';
                                });
                                zoneEl.addEventListener('dragover', function(e) {
                                    e.preventDefault();
                                    zoneEl.classList.add('drag-over');
                                });
                                zoneEl.addEventListener('dragleave', function() {
                                    zoneEl.classList.remove('drag-over');
                                });
                                zoneEl.addEventListener('drop', function(e) {
                                    e.preventDefault();
                                    zoneEl.classList.remove('drag-over');
                                    handler(Array.from((e.dataTransfer && e.dataTransfer.files) || []));
                                });
                            }

                            function uploadQueue(arr, i) {
                                if (i >= arr.length) {
                                    progressWrap.style.display = 'none';
                                    return;
                                }
                                progressWrap.style.display = 'block';
                                progressBar.style.width = Math.round((i / arr.length) * 100) + '%';
                                progressText.textContent = 'Hochladen ' + (i + 1) + ' von ' + arr.length + ': ' + arr[i].name;
                                uploadFile(arr[i], function() {
                                    uploadQueue(arr, i + 1);
                                });
                            }

                            function uploadFile(file, done) {
                                var fd = new FormData();
                                fd.append('csrf_token', CSRF_TOKEN);
                                fd.append('image', file);
                                fd.append('kind', 'image');
                                fetch('<?= BASE ?>/api/upload.php', {
                                        method: 'POST',
                                        body: fd,
                                        credentials: 'same-origin'
                                    })
                                    .then(function(r) {
                                        return r.text().then(function(raw) {
                                            var cleaned = (raw || '').replace(/^\uFEFF+/, '').trim();
                                            var json = {};
                                            try {
                                                json = cleaned ? JSON.parse(cleaned) : {};
                                            } catch (e) {
                                                json = {
                                                    error: cleaned || ('HTTP ' + r.status)
                                                };
                                            }
                                            json._httpOk = r.ok;
                                            json._status = r.status;
                                            return json;
                                        });
                                    })
                                    .then(function(json) {
                                        if (json.success && json._httpOk) {
                                            images.push({
                                                url: json.url,
                                                label: ''
                                            });
                                            renderPreviews();
                                        } else {
                                            alert('Upload-Fehler: ' + (json.error || ('HTTP ' + (json._status || '?'))));
                                        }
                                        done();
                                    })
                                    .catch(function() {
                                        alert('Verbindungsfehler beim Upload.');
                                        done();
                                    });
                            }

                            function uploadAsset(file, kind, done) {
                                var fd = new FormData();
                                fd.append('csrf_token', CSRF_TOKEN);
                                fd.append('asset', file);
                                fd.append('kind', kind);

                                fetch('<?= BASE ?>/api/upload.php', {
                                        method: 'POST',
                                        body: fd,
                                        credentials: 'same-origin'
                                    })
                                    .then(function(r) {
                                        return r.text().then(function(raw) {
                                            var cleaned = (raw || '').replace(/^\uFEFF+/, '').trim();
                                            var json = {};
                                            try {
                                                json = cleaned ? JSON.parse(cleaned) : {};
                                            } catch (e) {
                                                json = {
                                                    error: cleaned || ('HTTP ' + r.status)
                                                };
                                            }
                                            json._httpOk = r.ok;
                                            json._status = r.status;
                                            return json;
                                        });
                                    })
                                    .then(function(json) {
                                        if (json.success && json._httpOk) {
                                            var item = {
                                                url: json.url,
                                                name: json.name || file.name,
                                                mime: json.mime || file.type
                                            };
                                            if (kind === 'floorplan') {
                                                floorplans.push(item);
                                            } else {
                                                documents.push(item);
                                            }
                                            renderAllFiles();
                                        } else {
                                            alert('Upload-Fehler: ' + (json.error || ('HTTP ' + (json._status || '?'))));
                                        }
                                        done();
                                    })
                                    .catch(function() {
                                        alert('Verbindungsfehler beim Upload.');
                                        done();
                                    });
                            }

                            function uploadAssetQueue(files, kind, idx) {
                                if (idx >= files.length) {
                                    progressWrap.style.display = 'none';
                                    return;
                                }
                                progressWrap.style.display = 'block';
                                progressBar.style.width = Math.round((idx / files.length) * 100) + '%';
                                progressText.textContent = 'Hochladen ' + (idx + 1) + ' von ' + files.length + ': ' + files[idx].name;
                                uploadAsset(files[idx], kind, function() {
                                    uploadAssetQueue(files, kind, idx + 1);
                                });
                            }

                            function computeWarmRent() {
                                if (warmRentManuallyTouched) return;
                                var kalt = parseFloat(priceInput.value || '0');
                                var nk = parseFloat(extraInput.value || '0');
                                var hk = parseFloat(heatingInput.value || '0');
                                var warm = (isNaN(kalt) ? 0 : kalt) + (isNaN(nk) ? 0 : nk) + (isNaN(hk) ? 0 : hk);
                                warmInput.value = warm.toFixed(2);
                            }

                            if (warmInput) {
                                warmInput.addEventListener('input', function() {
                                    warmRentManuallyTouched = true;
                                });
                            }
                            [priceInput, extraInput, heatingInput].forEach(function(el) {
                                if (!el) return;
                                el.addEventListener('input', computeWarmRent);
                            });
                            computeWarmRent();

                            bindDropZone(floorplanZone, floorplanInput, function(files) {
                                var valid = files.filter(function(f) {
                                    return /^image\/(jpeg|png|webp)$/.test(f.type) || f.type === 'application/pdf' || /\.pdf$/i.test(f.name);
                                });
                                if (!valid.length) return;
                                uploadAssetQueue(valid, 'floorplan', 0);
                            });

                            bindDropZone(documentZone, documentInput, function(files) {
                                var valid = files.filter(function(f) {
                                    return f.type === 'application/pdf' ||
                                        f.type === 'application/msword' ||
                                        f.type === 'application/vnd.openxmlformats-officedocument.wordprocessingml.document' ||
                                        /\.(pdf|doc|docx)$/i.test(f.name);
                                });
                                if (!valid.length) return;
                                uploadAssetQueue(valid, 'document', 0);
                            });

                            renderPreviews();
                            renderAllFiles();
                        })();
                    </script>

                    <div class="card">
                        <h2 style="margin:0 0 1rem;font-size:1rem;color:#555;">Ausstattung &amp; Status</h2>
                        <div class="form-group">
                            <label>Ausstattungsmerkmale (eine pro Zeile)</label>
                            <textarea name="features_text"><?= htmlspecialchars(implode("\n", json_decode($property['features'] ?? '[]', true) ?: [])) ?></textarea>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Status</label>
                                <select name="status">
                                    <option value="available" <?= ($property['status'] ?? '') === 'available' ? 'selected' : '' ?>>Verfügbar</option>
                                    <option value="rented" <?= ($property['status'] ?? '') === 'rented'    ? 'selected' : '' ?>>Vermietet</option>
                                    <option value="sold" <?= ($property['status'] ?? '') === 'sold'      ? 'selected' : '' ?>>Verkauft</option>
                                    <option value="inactive" <?= ($property['status'] ?? '') === 'inactive'  ? 'selected' : '' ?>>Inaktiv</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <div class="check-wrap" style="margin-top:.6rem;">
                                    <input type="checkbox" name="featured" id="featured" value="1" <?= !empty($property['featured']) ? 'checked' : '' ?>>
                                    <label for="featured" style="margin:0;font-weight:normal;">Auf der Startseite anzeigen</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php
                    $subConfirmed = 0;
                    $alreadyNotified = 0;
                    try {
                        subscribers_ensure_tables($pdo);
                        $subConfirmed = (int)$pdo->query("SELECT COUNT(*) FROM subscribers WHERE status='confirmed'")->fetchColumn();
                        if (!empty($property['id'])) {
                            $nStmt = $pdo->prepare('SELECT COUNT(*) FROM property_notifications WHERE property_id = ?');
                            $nStmt->execute([(int)$property['id']]);
                            $alreadyNotified = (int)$nStmt->fetchColumn();
                        }
                    } catch (Throwable $e) {
                        error_log('Abonnenten-Zähler: ' . $e->getMessage());
                    }
                    ?>
                    <div class="card">
                        <h2 style="margin:0 0 .6rem;font-size:1rem;color:#555;">📣 Abonnenten benachrichtigen</h2>
                        <div class="check-wrap">
                            <input type="checkbox" name="notify_subscribers" id="notify_subscribers" value="1" <?= ($action === 'new' && $subConfirmed > 0) ? 'checked' : '' ?>>
                            <label for="notify_subscribers" style="margin:0;font-weight:normal;">
                                Beim Speichern E-Mail an <strong><?= $subConfirmed ?></strong> bestätigte Abonnenten senden
                            </label>
                        </div>
                        <p style="margin:.5rem 0 0;font-size:.82rem;color:#64748b;">
                            Nur bei Status „Verfügbar“. Jeder Abonnent erhält pro Immobilie nur eine Mail
                            <?php if ($alreadyNotified > 0): ?>(bereits an <?= $alreadyNotified ?> gesendet – es werden nur neue Abonnenten angeschrieben)<?php endif; ?>.
                            Abonnenten verwalten: <a href="<?= BASE ?>/admin/subscribers.php">Abonnentenliste</a>.
                        </p>
                    </div>

                    <div style="display:flex;gap:1rem;justify-content:space-between;align-items:center;flex-wrap:wrap;">
                        <div style="font-size:.82rem;color:#64748b;" id="draftStatus">Entwurf: wird automatisch lokal gespeichert</div>
                        <button type="button" class="btn btn-secondary" id="clearDraftBtn" style="padding:.45rem .75rem;font-size:.82rem;">Lokalen Entwurf löschen</button>
                    </div>

                    <div style="display:flex;gap:1rem;justify-content:flex-end;margin-top:.7rem;">
                        <a href="?action=list" class="btn btn-secondary">Abbrechen</a>
                        <button type="submit" class="btn btn-success">💾 Speichern</button>
                    </div>
                </form>

                <script>
                    (function() {
                        var form = document.getElementById('adminPropertyForm');
                        if (!form) return;

                        var draftStatus = document.getElementById('draftStatus');
                        var clearBtn = document.getElementById('clearDraftBtn');
                        var draftKey = 'admin_property_draft_<?= $action ?>_<?= (int)($id ?? 0) ?>';

                        function setStatus(msg) {
                            if (draftStatus) draftStatus.textContent = msg;
                        }

                        function collectDraft() {
                            var payload = {
                                ts: Date.now(),
                                fields: {}
                            };
                            form.querySelectorAll('input[name], textarea[name], select[name]').forEach(function(el) {
                                if (el.name === 'csrf_token') return;
                                if (el.type === 'file') return;
                                if (el.type === 'checkbox') {
                                    payload.fields[el.name] = el.checked ? '1' : '';
                                } else {
                                    payload.fields[el.name] = el.value;
                                }
                            });
                            return payload;
                        }

                        function saveDraft() {
                            try {
                                localStorage.setItem(draftKey, JSON.stringify(collectDraft()));
                                setStatus('Entwurf lokal gespeichert: ' + new Date().toLocaleTimeString('de-DE'));
                            } catch (e) {
                                setStatus('Entwurf konnte lokal nicht gespeichert werden.');
                            }
                        }

                        function applyDraft(data) {
                            if (!data || !data.fields) return;
                            Object.keys(data.fields).forEach(function(name) {
                                form.querySelectorAll('[name="' + name.replace(/"/g, '\\"') + '"]').forEach(function(el) {
                                    if (el.type === 'checkbox') {
                                        el.checked = data.fields[name] === '1';
                                    } else {
                                        el.value = data.fields[name];
                                    }
                                });
                            });
                        }

                        try {
                            var raw = localStorage.getItem(draftKey);
                            if (raw) {
                                var draft = JSON.parse(raw);
                                var draftTime = draft.ts ? new Date(draft.ts).toLocaleString('de-DE') : 'unbekannt';
                                if (confirm('Ein lokaler Entwurf wurde gefunden (' + draftTime + '). Wiederherstellen?')) {
                                    applyDraft(draft);
                                    setStatus('Entwurf wiederhergestellt: ' + draftTime);
                                } else {
                                    setStatus('Lokaler Entwurf vorhanden (nicht geladen).');
                                }
                            }
                        } catch (e) {
                            setStatus('Entwurf konnte nicht gelesen werden.');
                        }

                        var saveTimer = null;
                        form.addEventListener('input', function() {
                            clearTimeout(saveTimer);
                            saveTimer = setTimeout(saveDraft, 450);
                        });
                        form.addEventListener('change', function() {
                            clearTimeout(saveTimer);
                            saveTimer = setTimeout(saveDraft, 200);
                        });

                        form.addEventListener('submit', function() {
                            try {
                                localStorage.removeItem(draftKey);
                            } catch (e) {}
                        });

                        if (clearBtn) {
                            clearBtn.addEventListener('click', function() {
                                try {
                                    localStorage.removeItem(draftKey);
                                    setStatus('Lokaler Entwurf gelöscht.');
                                } catch (e) {
                                    setStatus('Entwurf konnte nicht gelöscht werden.');
                                }
                            });
                        }
                    })();
                </script>
            <?php endif; ?>

        </div><!-- /content -->
    </div><!-- /main -->
</body>

</html>