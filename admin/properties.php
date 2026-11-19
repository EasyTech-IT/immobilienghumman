<?php

/**
 * admin/properties.php – Immobilien verwalten
 */
require_once __DIR__ . '/_auth_check.php';
require_once __DIR__ . '/../includes/property-url.php';
require_once __DIR__ . '/../includes/property-validation.php';
require_once __DIR__ . '/../includes/private-expose.php';

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
$postedFormData = [];
$postedExtraData = [];
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
            'label' => trim((string)($item['label'] ?? '')),
            'legacy_source' => trim((string)($item['legacy_source'] ?? '')),
        ];
    }

    return array_values($items);
}

$hasExtraDataColumn = ensure_extra_data_column($pdo);
if ($hasExtraDataColumn) {
    property_migrate_slugs($pdo);
}
$existingSlug = '';
$existingExtraData = [];
$existingStatus = '';
if ($action === 'edit' && $id) {
    $existingStmt = $pdo->prepare($hasExtraDataColumn
        ? 'SELECT slug, extra_data, status FROM properties WHERE id = ?'
        : 'SELECT slug, status FROM properties WHERE id = ?');
    $existingStmt->execute([$id]);
    $existingProperty = $existingStmt->fetch();
    if ($existingProperty) {
        $existingSlug = (string)$existingProperty['slug'];
        $existingStatus = (string)($existingProperty['status'] ?? '');
        $decodedExtraData = json_decode((string)($existingProperty['extra_data'] ?? '{}'), true);
        $existingExtraData = is_array($decodedExtraData) ? $decodedExtraData : [];
    }
}

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
            if ($newStatus === 'available') {
                $stmt = $pdo->prepare('SELECT * FROM properties WHERE id = ? LIMIT 1');
                $stmt->execute([$id]);
                $propertyRow = $stmt->fetch();
                $listingExtra = json_decode((string)($propertyRow['extra_data'] ?? '{}'), true);
                $listingExtra = is_array($listingExtra) ? $listingExtra : [];
                $listingData = array_merge($propertyRow ?: [], $listingExtra);
                $listingImages = json_decode((string)($propertyRow['images'] ?? '[]'), true);
                $errors = property_listing_validation_errors($listingData, is_array($listingImages) ? $listingImages : []);
                if ($errors) {
                    $msg = 'error:' . implode(' ', $errors);
                }
            }
            if ($msg === '') {
                $pdo->prepare('UPDATE properties SET status = ? WHERE id = ?')->execute([$newStatus, $id]);
                header('Location: ' . BASE . '/admin/properties.php?toggled=1');
                exit;
            }
        }
    }
}
if ($action === 'toggle_visibility') {
    $action = 'list';
}

// ── Speichern (neu / bearbeiten) ────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && in_array($action, ['new', 'edit'])) {
    if (!csrf_verify($_POST['csrf_token'] ?? '')) {
        $msg = 'error:Ungültiges Sicherheitstoken.';
    } else {
        $d = $_POST;
        $validationErrors = [];
        $postedFormData = $d;
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
        $privateDocuments = normalize_upload_list($d['private_documents_json'] ?? '[]');
        foreach ($privateDocuments as $privateDocument) {
            $documentUrl = (string)($privateDocument['url'] ?? '');
            if (!preg_match('/^private:([a-f0-9]{32})$/', $documentUrl, $matches)
                || private_expose_file($matches[1]) === null) {
                $validationErrors[] = 'Eine private Exposé-Anlage ist ungültig oder nicht mehr verfügbar. Bitte erneut hochladen.';
            }
        }
        foreach (normalize_upload_list(json_encode($existingExtraData['documents'] ?? [])) as $legacyDocument) {
            $legacyPath = parse_url((string)($legacyDocument['url'] ?? ''), PHP_URL_PATH);
            $legacyName = is_string($legacyPath) ? basename($legacyPath) : '';
            if ($legacyName !== '' && in_array($legacyName, array_column($privateDocuments, 'legacy_source'), true)) {
                continue;
            }
            try {
                $migrated = private_expose_import_legacy_document($legacyDocument);
                if ($migrated === null) {
                    $validationErrors[] = 'Ein bisheriges Dokument konnte nicht automatisch geschützt werden. Bitte prüfen und erneut hochladen.';
                } else {
                    $privateDocuments[] = $migrated;
                }
            } catch (Throwable $e) {
                error_log('Legacy Exposé-Dokument konnte nicht geschützt werden: ' . $e->getMessage());
                $validationErrors[] = 'Ein bisheriges Dokument konnte nicht geschützt werden. Bitte erneut hochladen.';
            }
        }
        $unitOverview = [];
        foreach (preg_split('/\R/u', trim((string)($d['unit_overview_text'] ?? ''))) ?: [] as $line) {
            if (trim($line) === '') continue;
            $parts = array_map('trim', explode('|', $line));
            if (count($parts) !== 6) {
                $validationErrors[] = 'Jede Wohneinheit benötigt genau sechs durch | getrennte Angaben.';
                continue;
            }
            $unitOverview[] = [
                'unit' => $parts[0],
                'floor' => $parts[1],
                'area' => $parts[2],
                'rooms' => $parts[3],
                'status' => $parts[4],
                'rent' => $parts[5],
            ];
        }

        $energyCertificateType = trim((string)($d['energy_certificate_type'] ?? ''));
        $energyValueKind = trim((string)($d['energy_value_kind'] ?? ''));
        $energyValueRaw = trim((string)($d['energy_value'] ?? ''));
        $energyValue = ($energyValueRaw === '' ? null : (float)$energyValueRaw);
        $energyClass = trim((string)($d['energy_efficiency_class'] ?? ''));
        $energySource = trim((string)($d['energy_source'] ?? ''));
        $buildingYearRaw = trim((string)($d['building_year'] ?? ''));
        $buildingYear = ($buildingYearRaw === '' ? null : (int)$buildingYearRaw);

        $extraData = [
            'energy_certificate_type' => $energyCertificateType,
            'energy_value_kind' => $energyValueKind,
            'energy_value' => $energyValue,
            'energy_value_electricity' => (($d['energy_value_electricity'] ?? '') !== '' ? (float)$d['energy_value_electricity'] : null),
            'energy_efficiency_class' => $energyClass,
            'energy_efficiency_class_not_in_certificate' => !empty($d['energy_efficiency_class_not_in_certificate']),
            'energy_issue_date' => trim((string)($d['energy_issue_date'] ?? '')),
            'energy_source' => $energySource,
            'energy_certificate_status' => trim((string)($d['energy_certificate_status'] ?? '')),
            'energy_valid_until' => trim((string)($d['energy_valid_until'] ?? '')),
            'energy_building_year' => (($d['energy_building_year'] ?? '') !== '' ? (int)$d['energy_building_year'] : null),
            'heating_type' => trim((string)($d['heating_type'] ?? '')),
            'heating_year' => (($d['heating_year'] ?? '') !== '' ? (int)$d['heating_year'] : null),
            'building_year' => $buildingYear,
            'floor' => trim((string)($d['floor'] ?? '')),
            'floors_total' => (($d['floors_total'] ?? '') !== '' ? (int)$d['floors_total'] : null),
            'bedrooms' => (($d['bedrooms'] ?? '') !== '' ? (int)$d['bedrooms'] : null),
            'plot_area' => (($d['plot_area'] ?? '') !== '' ? (float)$d['plot_area'] : null),
            'area_calculation_basis' => trim((string)($d['area_calculation_basis'] ?? '')),
            'heating_costs_included' => !empty($d['heating_costs_included']),
            'residential_units' => (($d['residential_units'] ?? '') !== '' ? (int)$d['residential_units'] : null),
            'guest_toilets' => (($d['guest_toilets'] ?? '') !== '' ? (int)$d['guest_toilets'] : null),
            'balcony_area' => (($d['balcony_area'] ?? '') !== '' ? (float)$d['balcony_area'] : null),
            'has_balcony_terrace' => !empty($d['has_balcony_terrace']),
            'has_builtin_kitchen' => !empty($d['has_builtin_kitchen']),
            'has_basement' => !empty($d['has_basement']),
            'bath_features' => trim((string)($d['bath_features'] ?? '')),
            'flooring' => trim((string)($d['flooring'] ?? '')),
            'condition' => trim((string)($d['condition'] ?? '')),
            'has_elevator' => !empty($d['has_elevator']),
            'last_modernization_year' => (($d['last_modernization_year'] ?? '') !== '' ? (int)$d['last_modernization_year'] : null),
            'house_fee' => (($d['house_fee'] ?? '') !== '' ? (float)$d['house_fee'] : null),
            'parking_spaces' => (($d['parking_spaces'] ?? '') !== '' ? (int)$d['parking_spaces'] : null),
            'parking_type' => trim((string)($d['parking_type'] ?? '')),
            'parking_info' => trim((string)($d['parking_info'] ?? '')),
            'location_description' => trim((string)($d['location_description'] ?? '')),
            'commission_required' => !empty($d['commission_required']),
            'commission_amount' => (($d['commission_amount'] ?? '') !== '' ? (float)$d['commission_amount'] : null),
            'commission_unit' => trim((string)($d['commission_unit'] ?? 'percent')),
            'commission_payer' => trim((string)($d['commission_payer'] ?? '')),
            'commission_note' => trim((string)($d['commission_note'] ?? '')),
            'hide_exact_address' => true,
            'floorplans' => $floorplans,
            'private_expose' => [
                'rental_status' => trim((string)($d['rental_status'] ?? '')),
                'current_annual_rent' => (($d['current_annual_rent'] ?? '') !== '' ? (float)$d['current_annual_rent'] : null),
                'target_annual_rent' => (($d['target_annual_rent'] ?? '') !== '' ? (float)$d['target_annual_rent'] : null),
                'unit_overview' => $unitOverview,
                'leasehold_status' => trim((string)($d['leasehold_status'] ?? '')),
                'leasehold_annual_fee' => (($d['leasehold_annual_fee'] ?? '') !== '' ? (float)$d['leasehold_annual_fee'] : null),
                'leasehold_remaining_years' => (($d['leasehold_remaining_years'] ?? '') !== '' ? (int)$d['leasehold_remaining_years'] : null),
                'monument_status' => trim((string)($d['monument_status'] ?? '')),
                'burdens_status' => trim((string)($d['burdens_status'] ?? '')),
                'burdens_notes' => trim((string)($d['burdens_notes'] ?? '')),
                'development_status' => trim((string)($d['development_status'] ?? '')),
                'modernization_history' => trim((string)($d['modernization_history'] ?? '')),
                'room_details' => trim((string)($d['room_details'] ?? '')),
                'garden_orientation' => trim((string)($d['garden_orientation'] ?? '')),
                'documents' => $privateDocuments,
            ],
        ];
        unset($extraData['documents']);

        $slug = ($action === 'edit' && !$hasExtraDataColumn)
            ? $existingSlug
            : property_unique_slug($pdo, (string)($d['title'] ?? ''), $id, $hasExtraDataColumn);
        $legacySlugs = array_values(array_unique(array_filter(
            array_merge(
                is_array($existingExtraData['legacy_slugs'] ?? null) ? $existingExtraData['legacy_slugs'] : [],
                $existingSlug !== '' && $existingSlug !== $slug ? [$existingSlug] : []
            ),
            static fn($value): bool => is_string($value) && $value !== '' && $value !== $slug
        )));
        if ($hasExtraDataColumn) {
            $extraData['legacy_slugs'] = $legacySlugs;
        }

        $shouldValidatePublish = ($d['status'] ?? 'available') === 'available'
            && ($action === 'new' || $existingStatus !== 'available');
        if ($shouldValidatePublish) {
            $validationErrors = array_merge($validationErrors, property_listing_validation_errors($d, $images));
        }
        $postedExtraData = $extraData;

        if (!empty($validationErrors)) {
            $msg = 'error:' . implode(' ', array_unique($validationErrors));
        }

        $fields = [
            $d['title'] ?? '',
            $slug,
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
$existingDocuments = normalize_upload_list(json_encode($extraData['private_expose']['documents'] ?? [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
$existingPrivateExpose = is_array($extraData['private_expose'] ?? null) ? $extraData['private_expose'] : [];
$unitOverviewText = implode("\n", array_map(static function ($unit): string {
    return implode(' | ', array_map(static fn($field): string => (string)($unit[$field] ?? ''), ['unit', 'floor', 'area', 'rooms', 'status', 'rent']));
}, is_array($existingPrivateExpose['unit_overview'] ?? null) ? $existingPrivateExpose['unit_overview'] : []));
$existingImages = [];
if ($postedFormData) {
    if ($property) $property = array_merge($property, $postedFormData);
    $extraData = $postedExtraData;
    $existingPrivateExpose = is_array($extraData['private_expose'] ?? null) ? $extraData['private_expose'] : [];
    $postedImages = json_decode((string)($postedFormData['images_json'] ?? '[]'), true);
    $existingImages = is_array($postedImages) ? $postedImages : [];
    $existingFloorplans = normalize_upload_list($postedFormData['floorplans_json'] ?? '[]');
    $existingDocuments = normalize_upload_list($postedFormData['private_documents_json'] ?? '[]');
}
$unitOverviewText = implode("\n", array_map(static function ($unit): string {
    return implode(' | ', array_map(static fn($field): string => (string)($unit[$field] ?? ''), ['unit', 'floor', 'area', 'rooms', 'status', 'rent']));
}, is_array($existingPrivateExpose['unit_overview'] ?? null) ? $existingPrivateExpose['unit_overview'] : []));
$properties = [];
if ($action === 'list') {
    $where = [];
    $params = [];

    if ($listSearch !== '') {
        $where[] = '(title LIKE ? OR location LIKE ?)';
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
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: .75rem;
            margin-bottom: .75rem;
        }

        .form-row.three {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .content > .card { overflow: hidden; }
        .properties-table-wrap { overflow-x: auto; overscroll-behavior-x: contain; }
        .properties-table-wrap table { min-width: 980px; }
        .properties-table-wrap th, .properties-table-wrap td { padding: .55rem .7rem; }
        #adminPropertyForm > .card { padding: 1.1rem; margin-bottom: .9rem; }
        #adminPropertyForm .form-group { min-width: 0; margin-bottom: .55rem; }
        #adminPropertyForm .publish-required-mark { color: #b42318; font-weight: 700; }
        #adminPropertyForm [data-residential-energy-only][hidden] { display: none !important; }
        .table-actions { display: flex; flex-wrap: wrap; gap: .35rem; min-width: 190px; }
        .form-help { margin: .2rem 0 0; color: #64748b; font-size: .82rem; }
        @media (max-width: 900px) {
            .form-row.three { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }
        @media (max-width: 620px) {
            .form-row, .form-row.three { grid-template-columns: minmax(0, 1fr); gap: .35rem; }
            .table-tools > * { width: 100%; min-width: 0; }
            #adminPropertyForm > .card { padding: .85rem; }
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
                <?php if ($msg && strpos($msg, 'error:') === 0): ?><div class="alert alert-error"><?= htmlspecialchars(substr($msg, 6)) ?></div><?php endif; ?>
                <?php if (isset($_GET['deleted'])): ?><div class="alert alert-success">Immobilie gelöscht.</div><?php endif; ?>
                <?php if (isset($_GET['toggled'])): ?><div class="alert alert-success">Sichtbarkeit aktualisiert.</div><?php endif; ?>
                <div class="card">
                    <form method="get" class="table-tools">
                        <input type="hidden" name="action" value="list">
                        <input type="text" name="q" value="<?= htmlspecialchars($listSearch) ?>" placeholder="Suche: Titel oder Ort">
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
                    <div class="properties-table-wrap">
                    <table>
                        <thead>
                            <tr>
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
                                        <div class="table-actions">
                                            <a href="?action=edit&id=<?= $p['id'] ?>" class="btn btn-sm btn-secondary">Bearbeiten</a>
                                            <a href="<?= BASE ?>/immobilie/<?= rawurlencode((string)$p['slug']) ?>" class="btn btn-sm btn-outline" target="_blank" rel="noopener">Ansehen</a>
                                            <a href="<?= BASE ?>/admin/expose-download.php?property_id=<?= (int)$p['id'] ?>" class="btn btn-sm btn-outline">PDF herunterladen</a>
                                            <a href="?action=toggle_visibility&id=<?= $p['id'] ?>&token=<?= urlencode(CSRF_TOKEN) ?>" class="btn btn-sm btn-outline"><?= $isVisible ? 'Unsichtbar' : 'Sichtbar' ?></a>
                                            <a href="?action=delete&id=<?= $p['id'] ?>&token=<?= urlencode(CSRF_TOKEN) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Wirklich löschen?')">Löschen</a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (empty($properties)): ?>
                                <tr>
                                    <td colspan="10" style="text-align:center;color:#999;padding:2rem;">Keine Immobilien vorhanden.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                    </div>
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
                        <p class="form-help">Prüfen Sie vor der Veröffentlichung, ob Texte und Fotos persönliche Daten oder Rechte Dritter betreffen. Nur Angaben und Bilder verwenden, für deren Veröffentlichung eine Berechtigung vorliegt.</p>
                        <p class="form-help">Mit * markierte Felder sind Voraussetzungen dieses Systems zur Veröffentlichung und nicht automatisch gesetzliche Pflichtangaben. Einige Markierungen hängen von Objektart, Angebotsart oder Energieausweis ab.</p>
                        <p class="form-help">Orientierung: <a href="https://www.bodenrichtwerte-deutschland.de/immobilienwissen/immobilienanzeige-erstellen" target="_blank" rel="noopener noreferrer">Immobilienanzeige erstellen</a> und <a href="https://www.bodenrichtwerte-deutschland.de/immobilienwissen" target="_blank" rel="noopener noreferrer">Immobilienwissen</a>. Die Beiträge sind Ratgeber, keine Rechtsberatung.</p>
                        <p class="form-help"><a href="https://www.gesetze-im-internet.de/geg/__79.html" target="_blank" rel="noopener noreferrer">§ 79 GModG</a> sieht eine zehnjährige Gültigkeit für Energieausweise vor. <a href="https://www.gesetze-im-internet.de/geg/__80.html" target="_blank" rel="noopener noreferrer">§ 80 GModG</a> regelt, wann ein Energieausweis auszustellen und vorzulegen ist. Liegt bei einer kommerziellen Immobilienanzeige ein Ausweis vor, nennt <a href="https://www.gesetze-im-internet.de/geg/__87.html" target="_blank" rel="noopener noreferrer">§ 87 GModG</a> die Pflichtangaben: Ausweisart, Kennwert und Energieträger; bei Wohngebäuden außerdem Baujahr und Effizienzklasse, bei Nichtwohngebäuden Wärme und Strom getrennt.</p>
                        <div class="form-group">
                            <label>Titel <span class="publish-required-mark" aria-hidden="true">*</span></label>
                            <input type="text" name="title" value="<?= htmlspecialchars($property['title'] ?? '') ?>" required>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Typ <span class="publish-required-mark" aria-hidden="true">*</span></label>
                                <select name="type">
                                    <?php foreach (['Wohnung', 'Haus', 'Einfamilienhaus', 'Mehrfamilienhaus', 'Gewerbe', 'Grundstück'] as $t): ?>
                                        <option value="<?= $t ?>" <?= ($property['type'] ?? 'Wohnung') === $t ? 'selected' : '' ?>><?= $t ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Angebotsart <span class="publish-required-mark" aria-hidden="true">*</span></label>
                                <select name="offer_type" id="offer_type_input">
                                    <option value="Miete" <?= ($property['offer_type'] ?? 'Miete') === 'Miete' ? 'selected' : '' ?>>Miete</option>
                                    <option value="Kauf" <?= ($property['offer_type'] ?? '') === 'Kauf' ? 'selected' : '' ?>>Kauf</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Kurzbeschreibung <span class="publish-required-mark" aria-hidden="true">*</span></label>
                            <input type="text" name="short_description" maxlength="160" value="<?= htmlspecialchars($property['short_description'] ?? '') ?>">
                            <p class="form-help">Sachlich formulieren. Bei Auswahlkriterien das <a href="https://www.gesetze-im-internet.de/agg/__19.html" target="_blank" rel="noopener noreferrer">AGG</a> und dessen Anwendungsbereich sowie Ausnahmen prüfen.</p>
                        </div>
                        <div class="form-group">
                            <label>Beschreibung <span class="publish-required-mark" aria-hidden="true">*</span></label>
                            <textarea name="description" style="min-height:180px;"><?= htmlspecialchars($property['description'] ?? '') ?></textarea>
                            <p class="form-help">Wahrheitsgemäß und nachvollziehbar beschreiben; bekannte wesentliche Einschränkungen sachlich nennen. Der Ratgeber nennt etwa 300-500 Wörter als Orientierung, nicht als gesetzliche Pflicht.</p>
                        </div>
                    </div>

                    <div class="card">
                        <h2 style="margin:0 0 1rem;font-size:1rem;color:#555;">Kosten</h2>
                        <div class="form-row three">
                            <div class="form-group"><label id="price_label">Kaltmiete / Kaufpreis (€) *</label><input type="number" id="price_input" name="price" value="<?= $property['price'] ?? '' ?>" step="0.01" required></div>
                            <div class="form-group" data-rent-only><label>Nebenkosten / Monat (€) <span class="publish-required-mark" data-publish-required="extra_costs" aria-hidden="true" hidden>*</span></label><input type="number" id="extra_costs_input" name="extra_costs" value="<?= $property['extra_costs'] ?? 0 ?>" step="0.01"></div>
                            <div class="form-group" data-rent-only><label>Heizkosten / Monat (€) <span class="publish-required-mark" data-publish-required="heating_costs" aria-hidden="true" hidden>*</span></label><input type="number" id="heating_costs_input" name="heating_costs" value="<?= $property['heating_costs'] ?? 0 ?>" step="0.01"></div>
                        </div>
                        <div class="form-row">
                            <div class="form-group" data-rent-only><label>Warmmiete / Monat (€)</label><input type="number" id="warm_rent_input" name="warm_rent" value="<?= $property['warm_rent'] ?? 0 ?>" step="0.01"></div>
                            <div class="form-group" data-rent-only><label>Kaution (€) <span class="publish-required-mark" data-publish-required="deposit" aria-hidden="true" hidden>*</span></label><input type="number" name="deposit" value="<?= $property['deposit'] ?? 0 ?>" step="0.01"></div>
                        </div>
                        <div class="form-group" data-rent-only><label><input type="checkbox" name="heating_costs_included" value="1" <?= !empty($extraData['heating_costs_included']) ? 'checked' : '' ?>> Heizkosten sind in den Nebenkosten enthalten</label></div>
                        <p id="warm-rent-help" class="form-help">Warmmiete wird aus Kaltmiete + Nebenkosten + Heizkosten berechnet, bis Sie den Wert manuell ändern.</p>
                        <p class="form-help" data-rent-only>Bei Miete sind Nebenkosten und Kaution Pflichtfelder (0 ist möglich). Heizkosten eintragen oder bestätigen, dass sie in den Nebenkosten enthalten sind.</p>
                        <p class="form-help" data-rent-only>Bei Wohnraummiete örtliche Mietpreisbremse und Ausnahmen prüfen (<a href="https://www.gesetze-im-internet.de/bgb/__556d.html" target="_blank" rel="noopener noreferrer">§§ 556d ff. BGB</a>). Kostenbestandteile korrekt und nachvollziehbar ausweisen.</p>
                    </div>

                    <div class="card">
                        <h2 style="margin:0 0 1rem;font-size:1rem;color:#555;">Fläche &amp; Zimmer</h2>
                        <div class="form-row three">
                            <div class="form-group"><label>Wohnfläche (m²) <span class="publish-required-mark" data-publish-required="area" aria-hidden="true" hidden>*</span></label><input type="number" name="area" value="<?= $property['area'] ?? '' ?>" step="0.01"></div>
                            <div class="form-group"><label>Zimmer <span class="publish-required-mark" data-publish-required="rooms" aria-hidden="true" hidden>*</span></label><input type="number" name="rooms" value="<?= $property['rooms'] ?? '' ?>" step="0.5"></div>
                            <div class="form-group"><label>Badezimmer <span class="publish-required-mark" data-publish-required="bathrooms" aria-hidden="true" hidden>*</span></label><input type="number" name="bathrooms" value="<?= $property['bathrooms'] ?? 1 ?>"></div>
                        </div>
                        <div class="form-row">
                            <div class="form-group"><label>Grundstücksfläche (m²) <span class="publish-required-mark" data-publish-required="plot_area" aria-hidden="true" hidden>*</span></label><input type="number" name="plot_area" min="0" step="0.01" value="<?= htmlspecialchars((string)($extraData['plot_area'] ?? '')) ?>"></div>
                            <div class="form-group"><label>Wohneinheiten <span class="publish-required-mark" data-publish-required="residential_units" aria-hidden="true" hidden>*</span></label><input type="number" name="residential_units" min="0" step="1" value="<?= htmlspecialchars((string)($extraData['residential_units'] ?? '')) ?>"></div>
                            <div class="form-group"><label>Gäste-WCs</label><input type="number" name="guest_toilets" min="0" step="1" value="<?= htmlspecialchars((string)($extraData['guest_toilets'] ?? '')) ?>"></div>
                        </div>
                        <p class="form-help">Zimmer, Bäder und Wohneinheiten anhand der Unterlagen prüfen; keine Werte aus Grundrissen schätzen.</p>
                    </div>

                    <div class="card">
                        <h2 style="margin:0 0 1rem;font-size:1rem;color:#555;">Fakten für die Detailseite</h2>
                        <div class="form-row three">
                            <div class="form-group"><label>Schlafzimmer</label><input type="number" name="bedrooms" min="0" step="1" value="<?= htmlspecialchars((string)($extraData['bedrooms'] ?? '')) ?>"></div>
                            <div class="form-group"><label>Etagen im Gebäude</label><input type="number" name="floors_total" min="0" step="1" value="<?= htmlspecialchars((string)($extraData['floors_total'] ?? '')) ?>"></div>
                            <div class="form-group"><label>Balkon-/Terrassenfläche (m²)</label><input type="number" name="balcony_area" min="0" step="0.01" value="<?= htmlspecialchars((string)($extraData['balcony_area'] ?? '')) ?>"></div>
                        </div>
                        <div class="form-row three">
                            <div class="form-group"><label>Stellplätze</label><input type="number" name="parking_spaces" min="0" step="1" value="<?= htmlspecialchars((string)($extraData['parking_spaces'] ?? '')) ?>"></div>
                            <div class="form-group"><label>Stellplatztyp</label><input type="text" name="parking_type" value="<?= htmlspecialchars((string)($extraData['parking_type'] ?? '')) ?>" placeholder="z.B. Tiefgarage"></div>
                            <div class="form-group"><label>Zustand <span class="publish-required-mark" aria-hidden="true">*</span></label><input type="text" name="condition" value="<?= htmlspecialchars((string)($extraData['condition'] ?? '')) ?>" placeholder="z.B. gepflegt"></div>
                        </div>
                        <div class="form-row">
                            <div class="form-group"><label>Bad</label><input type="text" name="bath_features" value="<?= htmlspecialchars((string)($extraData['bath_features'] ?? '')) ?>" placeholder="z.B. Dusche, Wanne"></div>
                            <div class="form-group"><label>Bodenbelag</label><input type="text" name="flooring" value="<?= htmlspecialchars((string)($extraData['flooring'] ?? '')) ?>" placeholder="z.B. Parkett, Fliesen"></div>
                        </div>
                        <div class="form-row three">
                            <div class="form-group"><div class="check-wrap"><input type="checkbox" name="has_balcony_terrace" id="has_balcony_terrace" value="1" <?= !empty($extraData['has_balcony_terrace']) ? 'checked' : '' ?>><label for="has_balcony_terrace" style="margin:0;font-weight:normal;">Balkon/Terrasse vorhanden</label></div></div>
                            <div class="form-group"><div class="check-wrap"><input type="checkbox" name="has_builtin_kitchen" id="has_builtin_kitchen" value="1" <?= !empty($extraData['has_builtin_kitchen']) ? 'checked' : '' ?>><label for="has_builtin_kitchen" style="margin:0;font-weight:normal;">Einbauküche vorhanden</label></div></div>
                            <div class="form-group"><div class="check-wrap"><input type="checkbox" name="has_basement" id="has_basement" value="1" <?= !empty($extraData['has_basement']) ? 'checked' : '' ?>><label for="has_basement" style="margin:0;font-weight:normal;">Keller vorhanden</label></div></div>
                        </div>
                    </div>

                    <div class="card">
                        <h2 style="margin:0 0 1rem;font-size:1rem;color:#555;">Energieausweis</h2>
                        <div class="form-group">
                            <label>Status Energieausweis <span class="publish-required-mark" aria-hidden="true">*</span></label>
                            <select name="energy_certificate_status" required>
                                <?php foreach (['' => 'Bitte wählen', 'available' => 'Vorhanden', 'ordered' => 'Beantragt — noch nicht veröffentlichen', 'not_required' => 'Nicht erforderlich (Ausnahme geprüft)', 'missing' => 'Fehlt / ungeklärt — noch nicht veröffentlichen'] as $value => $label): ?>
                                    <option value="<?= htmlspecialchars($value) ?>" <?= (($extraData['energy_certificate_status'] ?? '') === $value ? 'selected' : '') ?>><?= htmlspecialchars($label) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <p class="form-help">„Nicht erforderlich“ nur wählen, wenn die Ausnahme für dieses Objekt geprüft wurde. Sonst vor der Veröffentlichung einen gültigen Ausweis beschaffen. Bei „Vorhanden“ die markierten Angaben aus dem Ausweis übernehmen; bei Nichtwohngebäuden Wärme- und Stromkennwert getrennt erfassen.</p>
                        <p class="form-help">Gültigkeitsende aus dem Energieausweis übernehmen; abgelaufene Ausweise blockieren die Veröffentlichung. Bei Wohngebäude-Ausweisen ohne Effizienzklasse kann diese nach <a href="https://www.gesetze-im-internet.de/geg/__112.html" target="_blank" rel="noopener noreferrer">§ 112 Abs. 3 GModG</a> nur für Ausweise vom 01.10.2007 bis 30.04.2014 entfallen. Ausstellungsdatum eintragen und die Ausnahme nur nach Prüfung markieren.</p>
                        <div class="form-row three">
                            <div class="form-group">
                                <label>Energieausweistyp <span class="publish-required-mark" data-publish-required="energy_certificate_type" aria-hidden="true" hidden>*</span></label>
                                <select name="energy_certificate_type">
                                    <option value="" <?= (($extraData['energy_certificate_type'] ?? '') === '' ? 'selected' : '') ?>>Bitte wählen</option>
                                    <option value="bedarfsausweis" <?= (($extraData['energy_certificate_type'] ?? '') === 'bedarfsausweis' ? 'selected' : '') ?>>Bedarfsausweis</option>
                                    <option value="verbrauchsausweis" <?= (($extraData['energy_certificate_type'] ?? '') === 'verbrauchsausweis' ? 'selected' : '') ?>>Verbrauchsausweis</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Art Kennwert <span class="publish-required-mark" data-publish-required="energy_value_kind" aria-hidden="true" hidden>*</span></label>
                                <select name="energy_value_kind">
                                    <option value="" <?= (($extraData['energy_value_kind'] ?? '') === '' ? 'selected' : '') ?>>Bitte wählen</option>
                                    <option value="bedarf" <?= (($extraData['energy_value_kind'] ?? '') === 'bedarf' ? 'selected' : '') ?>>Endenergiebedarf</option>
                                    <option value="verbrauch" <?= (($extraData['energy_value_kind'] ?? '') === 'verbrauch' ? 'selected' : '') ?>>Endenergieverbrauch</option>
                                </select>
                            </div>
                            <div class="form-group"><label>Energiekennwert (kWh/(m²·a)) <span class="publish-required-mark" data-publish-required="energy_value" aria-hidden="true" hidden>*</span></label><input type="number" name="energy_value" step="0.1" value="<?= htmlspecialchars((string)($extraData['energy_value'] ?? '')) ?>"></div>
                        </div>
                        <div class="form-row three">
                            <div class="form-group">
                                <label>Energieeffizienzklasse <span class="publish-required-mark" data-publish-required="energy_efficiency_class" aria-hidden="true" hidden>*</span></label>
                                <select name="energy_efficiency_class">
                                    <option value="" <?= (($extraData['energy_efficiency_class'] ?? '') === '' ? 'selected' : '') ?>>Bitte wählen</option>
                                    <?php foreach (['A+', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H'] as $clazz): ?>
                                        <option value="<?= $clazz ?>" <?= (($extraData['energy_efficiency_class'] ?? '') === $clazz ? 'selected' : '') ?>><?= $clazz ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="check-wrap" data-residential-energy-only style="margin-top:.45rem;">
                                    <input type="checkbox" name="energy_efficiency_class_not_in_certificate" id="energy_efficiency_class_not_in_certificate" value="1" <?= !empty($extraData['energy_efficiency_class_not_in_certificate']) ? 'checked' : '' ?>>
                                    <label for="energy_efficiency_class_not_in_certificate" style="margin:0;font-weight:normal;">Im geprüften Alt-Ausweis nicht angegeben</label>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Wesentlicher Energieträger <span class="publish-required-mark" data-publish-required="energy_source" aria-hidden="true" hidden>*</span></label>
                                <select name="energy_source">
                                    <option value="" <?= (($extraData['energy_source'] ?? '') === '' ? 'selected' : '') ?>>Bitte wählen</option>
                                    <?php foreach (['Gas', 'Öl', 'Fernwärme', 'Wärmepumpe', 'Strom', 'Pellets', 'Sonstige'] as $source): ?>
                                        <option value="<?= $source ?>" <?= (($extraData['energy_source'] ?? '') === $source ? 'selected' : '') ?>><?= $source ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group"><label>Baujahr Gebäude</label><input type="number" name="building_year" value="<?= htmlspecialchars((string)($extraData['building_year'] ?? '')) ?>" min="1800" max="2100"></div>
                        </div>
                        <div class="form-row three">
                            <div class="form-group"><label>Gültig bis <span class="publish-required-mark" data-publish-required="energy_valid_until" aria-hidden="true" hidden>*</span></label><input type="date" name="energy_valid_until" value="<?= htmlspecialchars((string)($extraData['energy_valid_until'] ?? '')) ?>"></div>
                            <div class="form-group"><label>Baujahr lt. Energieausweis <span class="publish-required-mark" data-publish-required="energy_building_year" aria-hidden="true" hidden>*</span></label><input type="number" name="energy_building_year" value="<?= htmlspecialchars((string)($extraData['energy_building_year'] ?? '')) ?>" min="1800" max="2100"></div>
                            <div class="form-group"><label>Heizungsart</label><input type="text" name="heating_type" value="<?= htmlspecialchars((string)($extraData['heating_type'] ?? '')) ?>" placeholder="z.B. Zentralheizung"></div>
                        </div>
                        <div class="form-row" data-residential-energy-only>
                            <div class="form-group"><label>Ausstellungsdatum Energieausweis <span class="publish-required-mark" data-publish-required="energy_issue_date" aria-hidden="true" hidden>*</span></label><input type="date" name="energy_issue_date" value="<?= htmlspecialchars((string)($extraData['energy_issue_date'] ?? '')) ?>"></div>
                        </div>
                        <div class="form-row">
                            <div class="form-group"><label>Baujahr Heizung</label><input type="number" name="heating_year" value="<?= htmlspecialchars((string)($extraData['heating_year'] ?? '')) ?>" min="1800" max="2100"></div>
                            <div class="form-group"><label>Energiekennwert Strom (Nichtwohngebäude) <span class="publish-required-mark" data-publish-required="energy_value_electricity" aria-hidden="true" hidden>*</span></label><input type="number" name="energy_value_electricity" min="0" step="0.1" value="<?= htmlspecialchars((string)($extraData['energy_value_electricity'] ?? '')) ?>"></div>
                            <div class="form-group"><label>Berechnungsgrundlage Fläche (optional)</label><input type="text" name="area_calculation_basis" value="<?= htmlspecialchars((string)($extraData['area_calculation_basis'] ?? '')) ?>" placeholder="z. B. WoFlV, DIN 277 oder Unterlage"></div>
                        </div>
                        <p class="form-help">Die Flächenmethode transparent anzugeben ist hilfreich, aber keine allgemeine gesetzliche Pflicht für jedes Inserat.</p>
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
                            <div class="form-group"><label>Hausgeld (€/Monat, Kauf) <span class="publish-required-mark" data-publish-required="house_fee" aria-hidden="true" hidden>*</span></label><input type="number" name="house_fee" value="<?= htmlspecialchars((string)($extraData['house_fee'] ?? '')) ?>" step="0.01"></div>
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
                            <div class="form-group"><label>Courtage-Höhe <span class="publish-required-mark" data-publish-required="commission_amount" aria-hidden="true" hidden>*</span></label><input type="number" name="commission_amount" value="<?= htmlspecialchars((string)($extraData['commission_amount'] ?? '')) ?>" step="0.01"></div>
                            <div class="form-group">
                                <label>Einheit</label>
                                <select name="commission_unit">
                                    <option value="percent" <?= (($extraData['commission_unit'] ?? 'percent') === 'percent' ? 'selected' : '') ?>>%</option>
                                    <option value="eur" <?= (($extraData['commission_unit'] ?? '') === 'eur' ? 'selected' : '') ?>>EUR</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group"><label>Wer zahlt die Courtage? <span class="publish-required-mark" data-publish-required="commission_payer" aria-hidden="true" hidden>*</span></label><input type="text" name="commission_payer" value="<?= htmlspecialchars((string)($extraData['commission_payer'] ?? '')) ?>" placeholder="z.B. Käufer, Verkäufer, Mieter"></div>
                            <div class="form-group"><label>Provisionshinweis <span class="publish-required-mark" data-publish-required="commission_note" aria-hidden="true" hidden>*</span></label><input type="text" name="commission_note" value="<?= htmlspecialchars((string)($extraData['commission_note'] ?? '')) ?>" placeholder="z.B. inkl. gesetzlicher Umsatzsteuer"></div>
                        </div>
                        <p class="form-help">Falls eine Provision anfällt, Höhe, Einheit und zahlende Partei vollständig eintragen und rechtlich prüfen. Bei Wohnraummiete gelten besondere Regeln nach dem <a href="https://www.gesetze-im-internet.de/wovermrg/__2.html" target="_blank" rel="noopener noreferrer">WoVermRG</a>; beim Verkauf von Wohnungen und Einfamilienhäusern können <a href="https://www.gesetze-im-internet.de/bgb/__656c.html" target="_blank" rel="noopener noreferrer">§ 656c BGB</a> und <a href="https://www.gesetze-im-internet.de/bgb/__656d.html" target="_blank" rel="noopener noreferrer">§ 656d BGB</a> gelten. Das Formular ersetzt keine rechtliche Prüfung.</p>
                    </div>

                    <div class="card">
                        <h2 style="margin:0 0 1rem;font-size:1rem;color:#555;">Lage</h2>
                        <div class="form-row">
                            <div class="form-group"><label>Ort <span class="publish-required-mark" aria-hidden="true">*</span></label><input type="text" name="location" value="<?= htmlspecialchars($property['location'] ?? '') ?>" required></div>
                            <div class="form-group"><label>PLZ <span class="publish-required-mark" aria-hidden="true">*</span></label><input type="text" name="zip" value="<?= htmlspecialchars($property['zip'] ?? '') ?>"></div>
                        </div>
                        <div class="form-row">
                            <div class="form-group"><label>Adresse <span class="publish-required-mark" aria-hidden="true">*</span></label><input type="text" name="address" value="<?= htmlspecialchars($property['address'] ?? '') ?>"></div>
                            <div class="form-group"><label>Verfügbar ab <span class="publish-required-mark" aria-hidden="true">*</span></label><input type="text" name="available_from" value="<?= htmlspecialchars($property['available_from'] ?? '') ?>" placeholder="z.B. sofort oder nach Absprache"></div>
                        </div>
                        <div class="form-group">
                            <label for="location_description">Lagebeschreibung <span class="publish-required-mark" aria-hidden="true">*</span></label>
                            <textarea id="location_description" name="location_description" rows="5" placeholder="Beschreiben Sie nur überprüfte Angaben zur Umgebung und Anbindung."><?= htmlspecialchars((string)($extraData['location_description'] ?? '')) ?></textarea>
                        </div>
                        <p class="form-hint">Im Inserat und auf der Karte erscheinen nur Ort und PLZ. Die genaue Adresse steht im persönlich angeforderten Exposé, sofern sie hier eingetragen ist. Die Lagebeschreibung ist öffentlich.</p>
                    </div>

                    <div class="card">
                        <h2 style="margin:0 0 1rem;font-size:1rem;color:#555;">Private Exposé-Daten</h2>
                        <p class="form-help">Diese Angaben werden nicht in der öffentlichen Immobilien-API ausgegeben. Private Dokumente liegen außerhalb des Webroots; Exposé-Downloadlinks sind signiert und sieben Tage gültig. Links können vom Empfänger weitergegeben werden, daher nur erforderliche Unterlagen aufnehmen.</p>
                        <div class="form-row three">
                            <div class="form-group">
                                <label>Vermietungsstand</label>
                                <select name="rental_status">
                                    <?php foreach (['' => 'Bitte wählen', 'vacant' => 'Leerstehend', 'leased' => 'Vollständig vermietet', 'partially_leased' => 'Teilweise vermietet'] as $value => $label): ?>
                                        <option value="<?= htmlspecialchars($value) ?>" <?= (($existingPrivateExpose['rental_status'] ?? '') === $value ? 'selected' : '') ?>><?= htmlspecialchars($label) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group"><label>Jahresnettokaltmiete aktuell (€)</label><input type="number" min="0" step="0.01" name="current_annual_rent" value="<?= htmlspecialchars((string)($existingPrivateExpose['current_annual_rent'] ?? '')) ?>"></div>
                            <div class="form-group"><label>Jahresnettokaltmiete Soll (€)</label><input type="number" min="0" step="0.01" name="target_annual_rent" value="<?= htmlspecialchars((string)($existingPrivateExpose['target_annual_rent'] ?? '')) ?>"></div>
                        </div>
                        <div class="form-group">
                            <label>Übersicht Wohneinheiten (eine pro Zeile)</label>
                            <textarea name="unit_overview_text" rows="4" placeholder="Einheit | Etage | Fläche m² | Zimmer | Status | Kaltmiete €/Monat"><?= htmlspecialchars($unitOverviewText) ?></textarea>
                            <p class="form-help">Format: Einheit | Etage | Fläche m² | Zimmer | Vermietungsstatus | Kaltmiete €/Monat</p>
                        </div>
                        <div class="form-row three">
                            <div class="form-group">
                                <label>Erbbaurecht</label>
                                <select name="leasehold_status">
                                    <?php foreach (['' => 'Bitte wählen', 'no' => 'Nein', 'yes' => 'Ja', 'unknown' => 'Ungeklärt'] as $value => $label): ?>
                                        <option value="<?= htmlspecialchars($value) ?>" <?= (($existingPrivateExpose['leasehold_status'] ?? '') === $value ? 'selected' : '') ?>><?= htmlspecialchars($label) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group"><label>Erbbauzins jährlich (€)</label><input type="number" min="0" step="0.01" name="leasehold_annual_fee" value="<?= htmlspecialchars((string)($existingPrivateExpose['leasehold_annual_fee'] ?? '')) ?>"></div>
                            <div class="form-group"><label>Restlaufzeit Erbbaurecht (Jahre)</label><input type="number" min="0" step="1" name="leasehold_remaining_years" value="<?= htmlspecialchars((string)($existingPrivateExpose['leasehold_remaining_years'] ?? '')) ?>"></div>
                        </div>
                        <div class="form-row three">
                            <?php foreach ([
                                'monument_status' => ['Denkmalschutz', ['' => 'Bitte wählen', 'no' => 'Nein', 'yes' => 'Ja', 'unknown' => 'Ungeklärt']],
                                'burdens_status' => ['Baulasten / Rechte Dritter', ['' => 'Bitte wählen', 'no' => 'Nein', 'yes' => 'Ja', 'unknown' => 'Ungeklärt']],
                                'development_status' => ['Erschließungsstand', ['' => 'Bitte wählen', 'developed' => 'Erschlossen', 'partial' => 'Teilerschlossen', 'undeveloped' => 'Unerschlossen', 'unknown' => 'Ungeklärt']],
                            ] as $field => [$label, $options]): ?>
                                <div class="form-group">
                                    <label><?= htmlspecialchars($label) ?></label>
                                    <select name="<?= htmlspecialchars($field) ?>">
                                        <?php foreach ($options as $value => $optionLabel): ?>
                                            <option value="<?= htmlspecialchars($value) ?>" <?= (($existingPrivateExpose[$field] ?? '') === $value ? 'selected' : '') ?>><?= htmlspecialchars($optionLabel) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="form-group"><label>Hinweise zu Baulasten / Rechten Dritter</label><textarea name="burdens_notes" rows="3"><?= htmlspecialchars((string)($existingPrivateExpose['burdens_notes'] ?? '')) ?></textarea></div>
                        <div class="form-group"><label>Modernisierungs- und Sanierungshistorie</label><textarea name="modernization_history" rows="4"><?= htmlspecialchars((string)($existingPrivateExpose['modernization_history'] ?? '')) ?></textarea></div>
                        <div class="form-group"><label>Raum- und Nutzungsdetails</label><textarea name="room_details" rows="4"><?= htmlspecialchars((string)($existingPrivateExpose['room_details'] ?? '')) ?></textarea></div>
                        <div class="form-group"><label>Ausrichtung / Garten</label><input type="text" name="garden_orientation" value="<?= htmlspecialchars((string)($existingPrivateExpose['garden_orientation'] ?? '')) ?>"></div>
                    </div>

                    <div class="card" id="upload-card">
                        <h2 style="margin:0 0 1rem;font-size:1rem;color:#555;">Bilder &amp; Video</h2>

                        <?php if (!$postedFormData):
                            $existingImagesRaw = json_decode($property['images'] ?? '[]', true) ?: [];
                            $existingImages = array_map(function ($img) {
                                if (is_string($img)) return ['url' => $img, 'label' => ''];
                                return ['url' => $img['url'] ?? '', 'label' => $img['label'] ?? ''];
                            }, $existingImagesRaw);
                        endif; ?>

                        <!-- Hidden inputs for form submission -->
                        <input type="hidden" name="images_json" id="images_json_input" value="<?= htmlspecialchars(json_encode($existingImages, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), ENT_QUOTES) ?>">
                        <input type="hidden" name="main_image" id="main_image_input" value="<?= htmlspecialchars($property['main_image'] ?? '') ?>">
                        <input type="hidden" name="floorplans_json" id="floorplans_json_input" value="<?= htmlspecialchars(json_encode($existingFloorplans, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), ENT_QUOTES) ?>">
                        <input type="hidden" name="private_documents_json" id="documents_json_input" value="<?= htmlspecialchars(json_encode($existingDocuments, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), ENT_QUOTES) ?>">

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
                        <p class="form-help">Das System verlangt mindestens ein Foto. Der Ratgeber empfiehlt 12–25 aussagekräftige Bilder; das ist eine Empfehlung, keine gesetzliche Höchstzahl. Die ersten acht Bilder werden im Exposé verwendet. Digital eingerichtete Bilder als Visualisierung kennzeichnen.</p>

                        <hr style="border:none;border-top:1px solid #e5e7eb;margin:1.25rem 0;">

                        <h3 style="margin:0 0 .5rem;font-size:.95rem;color:#475569;">Grundrisse</h3>
                        <p class="form-help">Grundrisse sind öffentlich abrufbar. Vertrauliche Unterlagen ausschließlich unter „Private Exposé-Daten“ hochladen.</p>
                        <div class="upload-zone" id="floorplan-zone" style="padding:1rem;">
                            <div>
                                <p style="margin:0;color:#374151;font-weight:600;">Grundriss hochladen</p>
                                <p style="margin:.3rem 0 0;color:#9ca3af;font-size:.8rem;">PNG, JPG, WebP oder PDF</p>
                            </div>
                            <input type="file" id="floorplan-file-input" accept="image/jpeg,image/png,image/webp,application/pdf" multiple style="display:none">
                        </div>
                        <div class="file-list" id="floorplan-list" style="margin-top:.6rem;"></div>

                        <h3 style="margin:1.1rem 0 .5rem;font-size:.95rem;color:#475569;">Private Exposé-Dokumente</h3>
                        <div class="upload-zone" id="document-zone" style="padding:1rem;">
                            <div>
                                <p style="margin:0;color:#374151;font-weight:600;">Vertrauliche Anlagen hochladen</p>
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
                            display: grid;
                            grid-template-columns: repeat(4, minmax(0, 1fr));
                            gap: .75rem;
                            margin-top: 1rem;
                        }
                        .form-hint { margin: 0; color: #64748b; font-size: .85rem; }

                        .upload-preview-item {
                            position: relative;
                            min-width: 0;
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
                            aspect-ratio: 4 / 3;
                            object-fit: cover;
                            display: block;
                        }

                        .upload-preview-item .preview-label {
                            padding: 4px 6px;
                        }

                        @media (max-width: 900px) {
                            .upload-previews { grid-template-columns: repeat(3, minmax(0, 1fr)); }
                        }
                        @media (max-width: 620px) {
                            .upload-previews { grid-template-columns: repeat(2, minmax(0, 1fr)); }
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
                            flex-wrap: wrap;
                            gap: 4px;
                            padding: 4px 6px;
                        }
                        .upload-preview-item .preview-staging {
                            display: flex; align-items: center; gap: .35rem; padding: 4px 6px; font-size: .75rem;
                        }
                        .upload-preview-item .preview-staging input { width: auto; margin: 0; }

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
                            flex-wrap: wrap;
                            gap: .5rem;
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
                            var offerTypeInput = document.getElementById('offer_type_input');
                            var priceLabel = document.getElementById('price_label');
                            var warmRentHelp = document.getElementById('warm-rent-help');
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
                                        '<label class="preview-staging"><input type="checkbox" data-staging="' + i + '"' + (img.virtual_staging ? ' checked' : '') + '> Visualisierung</label>' +
                                        '<div class="preview-actions">' +
                                        (i > 0 ? '<button type="button" data-action="main" data-idx="' + i + '">★ Haupt</button>' : '<span style="flex:1"></span>') +
                                        (i > 0 ? '<button type="button" data-action="up" data-idx="' + i + '" aria-label="Bild nach vorne">↑</button>' : '') +
                                        (i < images.length - 1 ? '<button type="button" data-action="down" data-idx="' + i + '" aria-label="Bild nach hinten">↓</button>' : '') +
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
                                    var name = escHtml(item.name || ('Datei ' + (idx + 1)));
                                    var fileLink = String(item.url || '').indexOf('private:') === 0
                                        ? '<span class="file-row-name">' + name + ' · privat gespeichert</span>'
                                        : '<a class="file-row-name" href="' + escHtml(item.url) + '" target="_blank" rel="noopener">' + name + '</a>';
                                    row.innerHTML =
                                        fileLink +
                                        (type === 'floorplan' ? '<input type="text" data-floorplan-label="' + idx + '" placeholder="Geschoss / Einheit" aria-label="Grundriss beschriften" value="' + escHtml(item.label || '') + '">' : '') +
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
                                } else if (btn.dataset.action === 'up' || btn.dataset.action === 'down') {
                                    var target = idx + (btn.dataset.action === 'up' ? -1 : 1);
                                    var moved = images.splice(idx, 1)[0];
                                    images.splice(target, 0, moved);
                                }
                                renderPreviews();
                            });

                            previews.addEventListener('input', function(e) {
                                if (e.target.dataset.idx !== undefined) {
                                    images[parseInt(e.target.dataset.idx)].label = e.target.value;
                                    syncInputs();
                                } else if (e.target.dataset.staging !== undefined) {
                                    images[parseInt(e.target.dataset.staging)].virtual_staging = e.target.checked;
                                    syncInputs();
                                }
                            });

                            floorplanList.addEventListener('input', function(e) {
                                if (e.target.dataset.floorplanLabel !== undefined) {
                                    floorplans[parseInt(e.target.dataset.floorplanLabel, 10)].label = e.target.value;
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
                                if (warmRentManuallyTouched || !offerTypeInput || offerTypeInput.value !== 'Miete') return;
                                var kalt = parseFloat(priceInput.value || '0');
                                var nk = parseFloat(extraInput.value || '0');
                                var hk = parseFloat(heatingInput.value || '0');
                                var warm = (isNaN(kalt) ? 0 : kalt) + (isNaN(nk) ? 0 : nk) + (isNaN(hk) ? 0 : hk);
                                warmInput.value = warm.toFixed(2);
                            }

                            if (warmInput) {
                                var initialWarmRent = parseFloat(warmInput.value || '0');
                                var initialCosts = [priceInput, extraInput, heatingInput].reduce(function(sum, el) {
                                    var value = el ? parseFloat(el.value || '0') : 0;
                                    return sum + (isNaN(value) ? 0 : value);
                                }, 0);
                                warmRentManuallyTouched = initialWarmRent > 0 && Math.abs(initialWarmRent - initialCosts) > 0.01;
                                warmInput.addEventListener('input', function() {
                                    warmRentManuallyTouched = true;
                                });
                            }
                            function updateOfferType() {
                                var isRent = offerTypeInput && offerTypeInput.value === 'Miete';
                                document.querySelectorAll('[data-rent-only]').forEach(function(el) {
                                    el.hidden = !isRent;
                                });
                                if (priceLabel) priceLabel.textContent = isRent ? 'Kaltmiete / Monat (€) *' : 'Kaufpreis (€) *';
                                if (warmRentHelp) warmRentHelp.hidden = !isRent;
                                if (isRent) computeWarmRent();
                            }
                            if (offerTypeInput) offerTypeInput.addEventListener('change', updateOfferType);
                            [priceInput, extraInput, heatingInput].forEach(function(el) {
                                if (!el) return;
                                el.addEventListener('input', computeWarmRent);
                            });
                            updateOfferType();

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
                                uploadAssetQueue(valid, 'private_document', 0);
                            });

                            renderPreviews();
                            renderAllFiles();
                        })();
                    </script>

                    <div class="card">
                        <h2 style="margin:0 0 1rem;font-size:1rem;color:#555;">Ausstattung &amp; Status</h2>
                        <div class="form-group">
                            <label>Ausstattungsmerkmale (eine pro Zeile)</label>
                            <textarea name="features_text" placeholder="z. B. Erstbezug, Einbauküche, Balkon"><?= htmlspecialchars((string)($postedFormData['features_text'] ?? implode("\n", json_decode((string)($property['features'] ?? '[]'), true) ?: []))) ?></textarea>
                            <p class="form-help">Passende Symbole werden auf der Detailseite automatisch zugeordnet.</p>
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
                        <p class="form-help">Status „Verfügbar“ veröffentlicht das Inserat. „Inaktiv“ bleibt verborgen; Abonnenten werden nur bei markierter Benachrichtigung kontaktiert.</p>
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

                        function updatePublishRequirements() {
                            var value = function(name) {
                                var field = form.querySelector('[name="' + name + '"]');
                                return field ? field.value : '';
                            };
                            var checked = function(name) {
                                var field = form.querySelector('[name="' + name + '"]');
                                return !!(field && field.checked);
                            };
                            var markRequired = function(name, required) {
                                var marker = form.querySelector('[data-publish-required="' + name + '"]');
                                if (marker) marker.hidden = !required;
                            };
                            var type = value('type');
                            var isRent = value('offer_type') === 'Miete';
                            var isHouse = ['Haus', 'Einfamilienhaus', 'Mehrfamilienhaus'].indexOf(type) !== -1;
                            var isPlot = type === 'Grundstück';
                            var isCommercial = type === 'Gewerbe';
                            var hasCertificate = value('energy_certificate_status') === 'available';
                            var classInput = form.querySelector('[name="energy_efficiency_class"]');
                            var mayOmitClass = checked('energy_efficiency_class_not_in_certificate')
                                && classInput && classInput.value === '';

                            markRequired('area', !isPlot);
                            markRequired('rooms', !isPlot && !isCommercial);
                            markRequired('bathrooms', type === 'Wohnung' || isHouse);
                            markRequired('plot_area', isHouse || isPlot);
                            markRequired('residential_units', type === 'Mehrfamilienhaus');
                            markRequired('extra_costs', isRent);
                            markRequired('deposit', isRent);
                            markRequired('heating_costs', isRent && !checked('heating_costs_included'));
                            markRequired('house_fee', !isRent && type === 'Wohnung');
                            markRequired('commission_amount', !isRent && checked('commission_required'));
                            markRequired('commission_payer', !isRent && checked('commission_required'));
                            markRequired('commission_note', !isRent && checked('commission_required'));
                            markRequired('energy_certificate_type', hasCertificate);
                            markRequired('energy_value_kind', hasCertificate);
                            markRequired('energy_value', hasCertificate);
                            markRequired('energy_source', hasCertificate);
                            markRequired('energy_valid_until', hasCertificate);
                            markRequired('energy_value_electricity', hasCertificate && isCommercial);
                            markRequired('energy_efficiency_class', hasCertificate && !isCommercial && !mayOmitClass);
                            markRequired('energy_building_year', hasCertificate && !isCommercial);
                            markRequired('energy_issue_date', hasCertificate && !isCommercial && mayOmitClass);
                            form.querySelectorAll('[data-residential-energy-only]').forEach(function(element) {
                                element.hidden = isCommercial;
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

                        updatePublishRequirements();
                        var saveTimer = null;
                        form.addEventListener('input', function() {
                            updatePublishRequirements();
                            clearTimeout(saveTimer);
                            saveTimer = setTimeout(saveDraft, 450);
                        });
                        form.addEventListener('change', function() {
                            updatePublishRequirements();
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