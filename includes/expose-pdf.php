<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/private-expose.php';

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
    $private = expose_pdf_list($e['private_expose'] ?? []);
    $money = static fn($v): string => number_format((float)$v, 2, ',', '.') . ' €';
    $rent = strtolower((string)($property['offer_type'] ?? '')) === 'miete';
    $facts = [
        'Ort' => trim(($property['zip'] ?? '') . ' ' . ($property['location'] ?? '')),
        'Adresse' => $property['address'] ?? '',
        'Objektart' => $property['type'] ?? '',
        $rent ? 'Kaltmiete' : 'Kaufpreis' => !empty($property['price']) ? $money($property['price']) : '',
        'Nebenkosten' => $rent && !empty($property['extra_costs']) ? $money($property['extra_costs']) : '',
        'Heizkosten' => $rent && !empty($property['heating_costs']) ? $money($property['heating_costs']) : '',
        'Warmmiete' => $rent && !empty($property['warm_rent']) ? $money($property['warm_rent']) : '',
        'Kaution' => $rent && !empty($property['deposit']) ? $money($property['deposit']) : '',
        'Wohnfläche' => !empty($property['area']) ? number_format((float)$property['area'], 2, ',', '.') . ' m²' : '',
        'Grundstücksfläche' => !empty($e['plot_area']) ? number_format((float)$e['plot_area'], 2, ',', '.') . ' m²' : '',
        'Wohneinheiten' => $e['residential_units'] ?? '',
        'Zimmer' => $property['rooms'] ?? '',
        'Badezimmer' => $property['bathrooms'] ?? '',
        'Berechnungsgrundlage Fläche' => $e['area_calculation_basis'] ?? '',
        'Gäste-WCs' => $e['guest_toilets'] ?? '',
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
        'Vermietungsstand' => ['vacant' => 'Leerstehend', 'leased' => 'Vollständig vermietet', 'partially_leased' => 'Teilweise vermietet'][$private['rental_status'] ?? ''] ?? '',
        'Jahresnettokaltmiete aktuell' => isset($private['current_annual_rent']) && $private['current_annual_rent'] !== '' ? $money($private['current_annual_rent']) : '',
        'Jahresnettokaltmiete Soll' => isset($private['target_annual_rent']) && $private['target_annual_rent'] !== '' ? $money($private['target_annual_rent']) : '',
        'Erbbaurecht' => ['yes' => 'Ja', 'no' => 'Nein', 'unknown' => 'Ungeklärt'][$private['leasehold_status'] ?? ''] ?? '',
        'Erbbauzins jährlich' => isset($private['leasehold_annual_fee']) && $private['leasehold_annual_fee'] !== '' ? $money($private['leasehold_annual_fee']) : '',
        'Restlaufzeit Erbbaurecht' => isset($private['leasehold_remaining_years']) ? $private['leasehold_remaining_years'] . ' Jahre' : '',
        'Denkmalschutz' => ['yes' => 'Ja', 'no' => 'Nein', 'unknown' => 'Ungeklärt'][$private['monument_status'] ?? ''] ?? '',
        'Baulasten / Rechte Dritter' => ['yes' => 'Ja', 'no' => 'Nein', 'unknown' => 'Ungeklärt'][$private['burdens_status'] ?? ''] ?? '',
        'Erschließungsstand' => ['developed' => 'Erschlossen', 'partial' => 'Teilerschlossen', 'undeveloped' => 'Unerschlossen', 'unknown' => 'Ungeklärt'][$private['development_status'] ?? ''] ?? '',
        'Ausrichtung / Garten' => $private['garden_orientation'] ?? '',
    ];
    if (!empty($private['current_annual_rent']) && (float)($property['price'] ?? 0) > 0) {
        $facts['Bruttomietrendite (Ist)'] = number_format((float)$private['current_annual_rent'] / (float)$property['price'] * 100, 2, ',', '.') . ' %';
    }
    if (!empty($e['commission_required']) && isset($e['commission_amount']) && $e['commission_amount'] !== '') {
        $facts['Provision'] = ($e['commission_unit'] ?? 'percent') === 'eur'
            ? $money($e['commission_amount']) : str_replace('.', ',', (string)$e['commission_amount']) . ' %';
        if (!empty($e['commission_payer'])) $facts['Provisionspflichtig'] = $e['commission_payer'];
        if (!empty($e['commission_note'])) $facts['Provisionshinweis'] = $e['commission_note'];
    }
    $energy = [
        'Energieausweis' => ['verbrauchsausweis' => 'Verbrauchsausweis', 'bedarfsausweis' => 'Bedarfsausweis'][$e['energy_certificate_type'] ?? ''] ?? ($e['energy_certificate_type'] ?? ''),
        'Energiekennwert' => isset($e['energy_value']) && $e['energy_value'] !== '' ? $e['energy_value'] . ' kWh/(m²·a)' : '',
        'Energiekennwert Strom' => isset($e['energy_value_electricity']) && $e['energy_value_electricity'] !== '' ? $e['energy_value_electricity'] . ' kWh/(m²·a)' : '',
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
    foreach ($images as $image) {
        $url = is_string($image) ? $image : (string)($image['url'] ?? $image['image_url'] ?? '');
        if ($url !== '') $imageUrls[$url] = is_array($image) ? $image : [];
    }
    if (!empty($property['main_image'])) {
        $main = (string)$property['main_image'];
        $imageUrls = [$main => $imageUrls[$main] ?? []] + $imageUrls;
    }
    $imageHtml = [];
    foreach (array_slice($imageUrls, 0, 8, true) as $url => $image) {
        $label = trim((string)($image['label'] ?? ''));
        if (!empty($image['virtual_staging'])) $label = 'Visualisierung / Einrichtungsbeispiel' . ($label !== '' ? ' · ' . $label : '');
        $imageHtml[] = '<div class="picture"><img src="' . expose_pdf_image($url) . '" alt="">'
            . ($label !== '' ? '<p class="picture-label">' . expose_pdf_escape($label) . '</p>' : '') . '</div>';
    }
    $cover = array_shift($imageHtml) ?: '';
    $plans = '';
    foreach (expose_pdf_list($e['floorplans'] ?? []) as $plan) {
        $url = is_string($plan) ? $plan : (string)($plan['url'] ?? '');
        if ($url !== '' && preg_match('/\.(jpe?g|png|gif|webp)(?:[?#]|$)/i', $url)) {
            $label = is_array($plan) ? trim((string)($plan['label'] ?? '')) : '';
            $plans .= '<div class="picture">' . ($label !== '' ? '<h3>' . expose_pdf_escape($label) . '</h3>' : '')
                . '<img src="' . expose_pdf_image($url) . '" alt="Grundriss"></div>';
        }
    }
    $documents = '';
    foreach (['floorplans' => 'Grundriss'] as $key => $label) {
        foreach (expose_pdf_list($e[$key] ?? []) as $item) {
            $url = is_string($item) ? $item : (string)($item['url'] ?? '');
            if ($url === '' || ($key === 'floorplans' && preg_match('/\.(jpe?g|png|gif|webp)(?:[?#]|$)/i', $url))) continue;
            if (strpos($url, '/') === 0 && strpos($url, '//') !== 0) $url = rtrim(SITE_URL, '/') . $url;
            if (!preg_match('~^https?://~i', $url)) continue;
            $name = is_array($item) ? (string)($item['label'] ?? $item['name'] ?? $label) : $label;
            $documents .= '<li><a href="' . expose_pdf_escape($url) . '">' . expose_pdf_escape($name ?: $label) . '</a></li>';
        }
    }
    foreach (expose_pdf_list($private['documents'] ?? []) as $item) {
        $url = is_array($item) ? (string)($item['url'] ?? '') : '';
        if (!preg_match('/^private:([a-f0-9]{32})$/', $url, $matches)) continue;
        $name = trim((string)($item['label'] ?? $item['name'] ?? 'Dokument'));
        $link = private_expose_signed_url($matches[1], time() + 7 * 86400);
        $documents .= '<li><a href="' . expose_pdf_escape($link) . '">' . expose_pdf_escape($name ?: 'Dokument') . '</a></li>';
    }
    $features = expose_pdf_list($property['features'] ?? []);
    $featureHtml = '';
    foreach ($features as $feature) {
        if (is_string($feature) && trim($feature) !== '') $featureHtml .= '<li>' . expose_pdf_escape($feature) . '</li>';
    }
    $description = trim((string)($property['description'] ?? ''));
    $descriptionHtml = '';
    $seenParagraphs = [];
    foreach (preg_split('/\R{2,}/u', $description) ?: [] as $paragraph) {
        $paragraph = trim($paragraph);
        $key = mb_strtolower((string)preg_replace('/\s+/u', ' ', $paragraph), 'UTF-8');
        if ($paragraph !== '' && !isset($seenParagraphs[$key])) {
            $descriptionHtml .= '<p>' . nl2br(expose_pdf_escape($paragraph)) . '</p>';
            $seenParagraphs[$key] = true;
        }
    }
    $locationHtml = '';
    foreach (preg_split('/\R{2,}/u', trim((string)($e['location_description'] ?? ''))) ?: [] as $paragraph) {
        if (trim($paragraph) !== '') $locationHtml .= '<p>' . nl2br(expose_pdf_escape($paragraph)) . '</p>';
    }
    $privateSections = '';
    foreach ([
        'Modernisierungs- und Sanierungshistorie' => $private['modernization_history'] ?? '',
        'Raum- und Nutzungsdetails' => $private['room_details'] ?? '',
        'Baulasten / Rechte Dritter – Hinweise' => $private['burdens_notes'] ?? '',
    ] as $heading => $text) {
        $text = trim((string)$text);
        if ($text !== '') $privateSections .= '<h2>' . expose_pdf_escape($heading) . '</h2><p>' . nl2br(expose_pdf_escape($text)) . '</p>';
    }
    $unitRows = expose_pdf_list($private['unit_overview'] ?? []);
    $unitHtml = '';
    if ($unitRows) {
        $unitHtml = '<h2>Übersicht Wohneinheiten</h2><table class="facts"><thead><tr><th>Einheit</th><th>Etage</th><th>Fläche</th><th>Zimmer</th><th>Status</th><th>Kaltmiete</th></tr></thead><tbody>';
        foreach ($unitRows as $unit) {
            if (!is_array($unit)) continue;
            $unitHtml .= '<tr>' . implode('', array_map(
                static fn($key): string => '<td>' . expose_pdf_escape($unit[$key] ?? '') . '</td>',
                ['unit', 'floor', 'area', 'rooms', 'status', 'rent']
            )) . '</tr>';
        }
        $unitHtml .= '</tbody></table>';
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
        .picture-label { font-size: 9pt; color: #546778; margin: 4pt 0; }
        .facts { border-collapse: collapse; width: 100%; }
        .facts th, .facts td { border-bottom: 1px solid #dce5eb; padding: 6pt; text-align: left; vertical-align: top; }
        .facts th { width: 40%; font-weight: normal; color: #546778; }
        li { margin-bottom: 4pt; } p { margin: 0 0 10pt; }
        .footer { page-break-before: always; border-top: 2px solid #3070a0; padding-top: 16pt; font-size: 10pt; }
        </style></head><body><div class="brand">IMMOBILIEN GHUMMAN</div><div class="eyebrow">Immobilien-Exposé</div><h1>'
        . expose_pdf_escape($property['title'] ?? '') . '</h1><p>' . expose_pdf_escape(trim(($property['zip'] ?? '') . ' ' . ($property['location'] ?? ''))) . '</p>'
        . $person . $cover . '<h2>Fakten und Konditionen</h2>' . $table($facts)
        . ($descriptionHtml !== '' ? '<h2>Objektbeschreibung</h2>' . $descriptionHtml : '')
        . ($featureHtml !== '' ? '<h2>Ausstattung</h2><ul>' . $featureHtml . '</ul>' : '')
        . ($locationHtml !== '' ? '<h2>Lage</h2>' . $locationHtml : '')
        . $unitHtml . $privateSections
        . $energyTable . ($imageHtml ? '<h2>Impressionen</h2>' . implode('', $imageHtml) : '')
        . (count($imageUrls) > 8 ? '<p>Weitere Bilder erhalten Sie auf Anfrage.</p>' : '')
        . ($plans !== '' ? '<h2>Grundrisse</h2>' . $plans : '')
        . ($documents !== '' ? '<h2>Weitere Unterlagen</h2><ul>' . $documents . '</ul>' : '')
        . '<div class="footer"><h2>Ihr Kontakt</h2><p><strong>Immobilien Ghumman</strong><br>Telefon: '
        . expose_pdf_escape(SITE_PHONE) . '<br>E-Mail: ' . expose_pdf_escape(SITE_EMAIL) . '</p>'
        . (isset($facts['Provision']) ? '<h3>Provision</h3><p>' . expose_pdf_escape($facts['Provision'])
            . (!empty($facts['Provisionspflichtig']) ? ' · ' . expose_pdf_escape($facts['Provisionspflichtig']) : '')
            . (!empty($facts['Provisionshinweis']) ? ' · ' . expose_pdf_escape($facts['Provisionshinweis']) : '') . '</p>' : '')
        . '<h3>Hinweis zu den Angaben</h3><p>Die Angaben beruhen auf Informationen der Eigentümerseite. Für Vollständigkeit und Richtigkeit kann keine Gewähr übernommen werden; gesetzliche Haftungsansprüche bleiben unberührt.</p></div></body></html>';
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
