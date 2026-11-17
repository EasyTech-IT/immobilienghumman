<?php

require_once __DIR__ . '/../vendor/autoload.php';

function expose_pdf_list($value): array
{
    if (is_array($value)) return $value;
    $decoded = json_decode((string)$value, true);
    return is_array($decoded) ? $decoded : [];
}

function expose_pdf_escape($value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function expose_pdf_image(string $url): string
{
    $parts = parse_url($url);
    if ($parts === false) throw new RuntimeException('Ungültige Bildadresse im Exposé.');
    $siteHost = defined('SITE_URL') ? parse_url(SITE_URL, PHP_URL_HOST) : null;
    $local = !isset($parts['host']) || (
        is_string($siteHost) && strtolower($parts['host']) === strtolower($siteHost)
        && ($parts['scheme'] ?? '') === 'https' && !isset($parts['user']) && !isset($parts['port'])
    );
    if (!$local) {
        if (strtolower($parts['host']) !== 'res.cloudinary.com' || ($parts['scheme'] ?? '') !== 'https'
            || isset($parts['user']) || isset($parts['port'])) {
            throw new RuntimeException('Bildhost für das Exposé nicht freigegeben.');
        }
        $bytes = '';
        $curl = curl_init($url);
        curl_setopt_array($curl, [
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_TIMEOUT => 15,
            CURLOPT_WRITEFUNCTION => static function ($handle, string $chunk) use (&$bytes): int {
                if (strlen($bytes) + strlen($chunk) > 8 * 1024 * 1024) return 0;
                $bytes .= $chunk;
                return strlen($chunk);
            },
        ]);
        $ok = curl_exec($curl);
        $status = curl_getinfo($curl, CURLINFO_RESPONSE_CODE);
        $error = curl_error($curl);
        curl_close($curl);
        if ($ok === false || $status !== 200) throw new RuntimeException('Exposé-Bild konnte nicht geladen werden: ' . $error);
    } else {
        $prefix = (defined('BASE') ? BASE : '') . '/uploads/';
        $path = (string)($parts['path'] ?? '');
        if (strpos($path, $prefix) !== 0) throw new RuntimeException('Bildpfad für das Exposé nicht freigegeben.');
        $root = realpath(__DIR__ . '/../uploads');
        $file = realpath(__DIR__ . '/../uploads/' . substr($path, strlen($prefix)));
        if ($root === false || $file === false || strpos($file, $root . DIRECTORY_SEPARATOR) !== 0
            || !is_file($file) || filesize($file) > 8 * 1024 * 1024) {
            throw new RuntimeException('Exposé-Bild ist nicht verfügbar oder zu groß.');
        }
        $bytes = file_get_contents($file);
        if ($bytes === false) throw new RuntimeException('Exposé-Bild konnte nicht gelesen werden.');
    }
    $type = (new finfo(FILEINFO_MIME_TYPE))->buffer($bytes);
    if (!in_array($type, ['image/jpeg', 'image/png', 'image/gif', 'image/webp'], true)) {
        throw new RuntimeException('Nicht unterstütztes Bildformat im Exposé.');
    }
    return 'data:' . $type . ';base64,' . base64_encode($bytes);
}

function expose_pdf_html(array $property, array $recipient): string
{
    $e = expose_pdf_list($property['extra_data'] ?? []);
    $money = static fn($v): string => number_format((float)$v, 2, ',', '.') . ' €';
    $rent = strtolower((string)($property['offer_type'] ?? '')) === 'miete';
    $facts = [
        'Ort' => trim(($property['zip'] ?? '') . ' ' . ($property['location'] ?? '')),
        'Adresse' => $property['address'] ?? '',
        'Objektart' => $property['type'] ?? '',
        $rent ? 'Kaltmiete' : 'Kaufpreis' => !empty($property['price']) ? $money($property['price']) : '',
        'Nebenkosten' => !empty($property['extra_costs']) ? $money($property['extra_costs']) : '',
        'Heizkosten' => !empty($property['heating_costs']) ? $money($property['heating_costs']) : '',
        'Warmmiete' => !empty($property['warm_rent']) ? $money($property['warm_rent']) : '',
        'Kaution' => !empty($property['deposit']) ? $money($property['deposit']) : '',
        'Wohnfläche' => !empty($property['area']) ? number_format((float)$property['area'], 2, ',', '.') . ' m²' : '',
        'Zimmer' => $property['rooms'] ?? '',
        'Badezimmer' => $property['bathrooms'] ?? '',
        'Schlafzimmer' => $e['bedrooms'] ?? '',
        'Etage' => $e['floor'] ?? '',
        'Etagen' => $e['floors_total'] ?? '',
        'Balkon-/Terrassenfläche' => !empty($e['balcony_area']) ? $e['balcony_area'] . ' m²' : '',
        'Balkon/Terrasse' => !empty($e['has_balcony_terrace']) ? 'Ja' : '',
        'Einbauküche' => !empty($e['has_builtin_kitchen']) ? 'Ja' : '',
        'Keller' => !empty($e['has_basement']) ? 'Ja' : '',
        'Aufzug' => !empty($e['has_elevator']) ? 'Ja' : '',
        'Bad' => $e['bath_features'] ?? '',
        'Bodenbelag' => $e['flooring'] ?? '',
        'Zustand' => $e['condition'] ?? '',
        'Baujahr' => $e['building_year'] ?? '',
        'Modernisierung' => $e['last_modernization_year'] ?? '',
        'Bezugsfrei ab' => $property['available_from'] ?? '',
        'Hausgeld' => !empty($e['house_fee']) ? $money($e['house_fee']) : '',
        'Stellplatz' => $e['parking_info'] ?? '',
        'Anzahl Stellplätze' => $e['parking_spaces'] ?? '',
        'Stellplatztyp' => $e['parking_type'] ?? '',
    ];
    if (!empty($e['commission_required']) && isset($e['commission_amount']) && $e['commission_amount'] !== '') {
        $facts['Provision'] = ($e['commission_unit'] ?? 'percent') === 'eur'
            ? $money($e['commission_amount']) : str_replace('.', ',', (string)$e['commission_amount']) . ' %';
        if (!empty($e['commission_payer'])) $facts['Provisionspflichtig'] = $e['commission_payer'];
    }
    $energy = [
        'Energieausweis' => ['verbrauchsausweis' => 'Verbrauchsausweis', 'bedarfsausweis' => 'Bedarfsausweis'][$e['energy_certificate_type'] ?? ''] ?? ($e['energy_certificate_type'] ?? ''),
        'Energiekennwert' => isset($e['energy_value']) && $e['energy_value'] !== '' ? $e['energy_value'] . ' kWh/(m²·a)' : '',
        'Kennwertart' => ['bedarf' => 'Endenergiebedarf', 'verbrauch' => 'Endenergieverbrauch'][$e['energy_value_kind'] ?? ''] ?? '',
        'Energieträger' => $e['energy_source'] ?? '',
        'Effizienzklasse' => $e['energy_efficiency_class'] ?? '',
        'Baujahr laut Energieausweis' => $e['energy_building_year'] ?? '',
        'Heizung' => $e['heating_type'] ?? '',
        'Baujahr Heizung' => $e['heating_year'] ?? '',
        'Gültig bis' => $e['energy_valid_until'] ?? '',
    ];
    $table = static function (array $rows): string {
        $html = '<table class="facts">';
        foreach ($rows as $label => $value) {
            if ($value === null || $value === '') continue;
            $html .= '<tr><th>' . expose_pdf_escape($label) . '</th><td>' . expose_pdf_escape($value) . '</td></tr>';
        }
        return $html . '</table>';
    };
    $images = expose_pdf_list($property['images'] ?? []);
    $imageUrls = [];
    if (!empty($property['main_image'])) $imageUrls[] = (string)$property['main_image'];
    foreach ($images as $image) {
        $url = is_string($image) ? $image : (string)($image['url'] ?? $image['image_url'] ?? '');
        if ($url !== '') $imageUrls[] = $url;
    }
    $imageUrls = array_values(array_unique($imageUrls));
    $imageHtml = [];
    foreach (array_slice($imageUrls, 0, 8) as $url) {
        $imageHtml[] = '<div class="picture"><img src="' . expose_pdf_image($url) . '" alt=""></div>';
    }
    $cover = array_shift($imageHtml) ?: '';
    $plans = '';
    foreach (expose_pdf_list($e['floorplans'] ?? []) as $plan) {
        $url = is_string($plan) ? $plan : (string)($plan['url'] ?? '');
        if ($url !== '' && preg_match('/\.(jpe?g|png|gif|webp)(?:[?#]|$)/i', $url)) {
            $plans .= '<div class="picture"><img src="' . expose_pdf_image($url) . '" alt="Grundriss"></div>';
        }
    }
    $documents = '';
    foreach (['floorplans' => 'Grundriss', 'documents' => 'Dokument'] as $key => $label) {
        foreach (expose_pdf_list($e[$key] ?? []) as $item) {
            $url = is_string($item) ? $item : (string)($item['url'] ?? '');
            if ($url === '' || ($key === 'floorplans' && preg_match('/\.(jpe?g|png|gif|webp)(?:[?#]|$)/i', $url))) continue;
            if (strpos($url, '/') === 0 && strpos($url, '//') !== 0) $url = rtrim(SITE_URL, '/') . $url;
            if (!preg_match('~^https?://~i', $url)) continue;
            $name = is_array($item) ? (string)($item['label'] ?? $item['name'] ?? $label) : $label;
            $documents .= '<li><a href="' . expose_pdf_escape($url) . '">' . expose_pdf_escape($name ?: $label) . '</a></li>';
        }
    }
    $features = expose_pdf_list($property['features'] ?? []);
    $featureHtml = '';
    foreach ($features as $feature) {
        if (is_string($feature) && trim($feature) !== '') $featureHtml .= '<li>' . expose_pdf_escape($feature) . '</li>';
    }
    $description = trim((string)($property['description'] ?? ''));
    $descriptionHtml = '';
    foreach (preg_split('/\R{2,}/u', $description) ?: [] as $paragraph) {
        if (trim($paragraph) !== '') $descriptionHtml .= '<p>' . nl2br(expose_pdf_escape($paragraph)) . '</p>';
    }
    $locationHtml = '';
    foreach (preg_split('/\R{2,}/u', trim((string)($e['location_description'] ?? ''))) ?: [] as $paragraph) {
        if (trim($paragraph) !== '') $locationHtml .= '<p>' . nl2br(expose_pdf_escape($paragraph)) . '</p>';
    }
    $person = '';
    if ($recipient) {
        $person = '<div class="recipient">Persönlich erstellt für<br><strong>' . expose_pdf_escape($recipient['name'] ?? '') . '</strong><br>'
            . expose_pdf_escape($recipient['email'] ?? '') . '<br>' . expose_pdf_escape($recipient['phone'] ?? '') . '</div>';
    }
    $energyTable = count(array_filter($energy, static fn($v) => $v !== '' && $v !== null)) ? '<h2>Energieinformationen</h2>' . $table($energy) : '';
    return '<!doctype html><html lang="de"><head><meta charset="utf-8"><style>
        @page { margin: 20mm 18mm; }
        body { font-family: DejaVu Sans, sans-serif; color: #1a2d3f; font-size: 10pt; line-height: 1.5; }
        .brand { color: #3070a0; font-size: 11pt; font-weight: bold; border-bottom: 2px solid #3070a0; padding-bottom: 8pt; }
        .eyebrow { color: #3070a0; font-size: 9pt; margin-top: 20pt; text-transform: uppercase; }
        h1 { font-size: 22pt; line-height: 1.2; margin: 6pt 0 10pt; }
        h2 { font-size: 15pt; color: #3070a0; margin: 20pt 0 8pt; page-break-after: avoid; }
        .recipient { background: #edf4f9; padding: 10pt; margin: 12pt 0; }
        .picture { margin: 12pt 0; page-break-inside: avoid; text-align: center; }
        .picture img { max-width: 100%; max-height: 350pt; }
        .facts { border-collapse: collapse; width: 100%; }
        .facts th, .facts td { border-bottom: 1px solid #dce5eb; padding: 6pt; text-align: left; vertical-align: top; }
        .facts th { width: 40%; font-weight: normal; color: #546778; }
        li { margin-bottom: 4pt; } p { margin: 0 0 10pt; }
        .footer { margin-top: 20pt; border-top: 1px solid #dce5eb; padding-top: 10pt; font-size: 9pt; }
        </style></head><body><div class="brand">IMMOBILIEN GHUMMAN</div><div class="eyebrow">Immobilien-Exposé</div><h1>'
        . expose_pdf_escape($property['title'] ?? '') . '</h1><p>' . expose_pdf_escape(trim(($property['zip'] ?? '') . ' ' . ($property['location'] ?? ''))) . '</p>'
        . $person . $cover . '<h2>Fakten und Konditionen</h2>' . $table($facts)
        . ($descriptionHtml !== '' ? '<h2>Objektbeschreibung</h2>' . $descriptionHtml : '')
        . ($featureHtml !== '' ? '<h2>Ausstattung</h2><ul>' . $featureHtml . '</ul>' : '')
        . ($locationHtml !== '' ? '<h2>Lage</h2>' . $locationHtml : '')
        . $energyTable . ($imageHtml ? '<h2>Impressionen</h2>' . implode('', $imageHtml) : '')
        . (count($imageUrls) > 8 ? '<p>Weitere Bilder erhalten Sie auf Anfrage.</p>' : '')
        . ($plans !== '' ? '<h2>Grundrisse</h2>' . $plans : '')
        . ($documents !== '' ? '<h2>Weitere Unterlagen</h2><ul>' . $documents . '</ul>' : '')
        . '<div class="footer">Immobilien Ghumman · ' . expose_pdf_escape(SITE_PHONE) . ' · ' . expose_pdf_escape(SITE_EMAIL) . '</div></body></html>';
}

function expose_pdf_render(array $property, array $recipient = []): string
{
    $options = new Dompdf\Options();
    $options->set('isRemoteEnabled', false);
    $dompdf = new Dompdf\Dompdf($options);
    $dompdf->loadHtml(expose_pdf_html($property, $recipient), 'UTF-8');
    $dompdf->setPaper('A4');
    $dompdf->render();
    $pdf = $dompdf->output();
    if (strncmp($pdf, '%PDF-', 5) !== 0) throw new RuntimeException('Exposé-PDF konnte nicht erzeugt werden.');
    return $pdf;
}

function expose_requests_ensure_table(PDO $pdo): void
{
    $pdo->exec("CREATE TABLE IF NOT EXISTS expose_requests (
        inquiry_id INT UNSIGNED PRIMARY KEY,
        pdf LONGBLOB NOT NULL,
        delivery_status VARCHAR(20) NOT NULL,
        FOREIGN KEY (inquiry_id) REFERENCES contact_inquiries(id) ON DELETE CASCADE
    ) ENGINE=InnoDB");
}

function expose_request_store(PDO $pdo, array $fields, string $pdf): int
{
    if (strncmp($pdf, '%PDF-', 5) !== 0) throw new InvalidArgumentException('Ungültiges Exposé-PDF.');
    $pdo->beginTransaction();
    try {
        $stmt = $pdo->prepare('INSERT INTO contact_inquiries (name, email, phone, message, property_id, ip_address, created_at) VALUES (?, ?, ?, ?, ?, ?, CURRENT_TIMESTAMP)');
        $stmt->execute($fields);
        $id = (int)$pdo->lastInsertId();
        $stmt = $pdo->prepare("INSERT INTO expose_requests (inquiry_id, pdf, delivery_status) VALUES (?, ?, 'prepared')");
        $stmt->bindValue(1, $id, PDO::PARAM_INT);
        $stmt->bindValue(2, $pdf, PDO::PARAM_LOB);
        $stmt->execute();
        $pdo->commit();
        return $id;
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        throw $e;
    }
}

function expose_request_deliver(PDO $pdo, int $id, string $pdf, callable $send): bool
{
    $sent = $send($pdf);
    $stmt = $pdo->prepare('UPDATE expose_requests SET delivery_status = ? WHERE inquiry_id = ?');
    $stmt->execute([$sent ? 'sent' : 'unknown', $id]);
    if ($stmt->rowCount() !== 1) throw new RuntimeException('Exposé-Versandstatus konnte nicht zugeordnet werden.');
    return $sent;
}
