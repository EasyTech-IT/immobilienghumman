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

function expose_pdf_icon(string $kind): string
{
    $paths = [
        'price' => '<circle cx="12" cy="12" r="9"/><path d="M15.5 8a4.5 4.5 0 1 0 0 8M6.5 10h7M6.5 14h7"/>',
        'area' => '<path d="M4 20V4h16v16zM4 9h3M4 14h3M9 4v3M14 4v3"/>',
        'rooms' => '<path d="M4 20V4h16v16zM12 4v16M4 12h16"/>',
        'home' => '<path d="M3 11 12 4l9 7M5 10v10h14V10M10 20v-6h4v6"/>',
        'location' => '<path d="M19 10c0 5-7 10-7 10S5 15 5 10a7 7 0 1 1 14 0z"/><circle cx="12" cy="10" r="2"/>',
        'calendar' => '<rect x="4" y="6" width="16" height="14" rx="1"/><path d="M8 3v6M16 3v6M4 11h16"/>',
        'key' => '<circle cx="8" cy="9" r="4"/><path d="m11 12 8 8m-3-3 2-2m-5-1 2-2"/>',
        'building' => '<path d="M5 21V4h14v17M3 21h18M9 8h1m4 0h1M9 12h1m4 0h1M9 16h1m4 0h1"/>',
        'bath' => '<path d="M4 10V6a2 2 0 0 1 4 0M3 13h18v2a4 4 0 0 1-4 4H7a4 4 0 0 1-4-4zM6 19l-1 2m13-2 1 2"/>',
    ];
    $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#286b9a" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">'
        . ($paths[$kind] ?? $paths['home']) . '</svg>';
    return 'data:image/svg+xml;base64,' . base64_encode($svg);
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
        'Nebenkosten' => $rent && isset($property['extra_costs']) && $property['extra_costs'] !== '' ? $money($property['extra_costs']) : '',
        'Heizkosten' => $rent && isset($property['heating_costs']) && $property['heating_costs'] !== '' ? $money($property['heating_costs']) : '',
        'Heizkosten enthalten' => $rent && !empty($e['heating_costs_included']) ? 'In den Nebenkosten enthalten' : '',
        'Warmmiete' => $rent && isset($property['warm_rent']) && $property['warm_rent'] !== '' ? $money($property['warm_rent']) : '',
        'Kaution' => $rent && isset($property['deposit']) && $property['deposit'] !== '' ? $money($property['deposit']) : '',
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
        'Hausgeld' => !$rent && !empty($e['house_fee']) ? $money($e['house_fee']) : '',
        'Stellplatz' => $e['parking_info'] ?? '',
        'Anzahl Stellplätze' => $e['parking_spaces'] ?? '',
        'Stellplatztyp' => $e['parking_type'] ?? '',
        'Vermietungsstand' => ['vacant' => 'Leerstehend', 'leased' => 'Vollständig vermietet', 'partially_leased' => 'Teilweise vermietet'][$private['rental_status'] ?? ''] ?? '',
        'Jahresnettokaltmiete aktuell' => !$rent && isset($private['current_annual_rent']) && $private['current_annual_rent'] !== '' ? $money($private['current_annual_rent']) : '',
        'Jahresnettokaltmiete Soll' => !$rent && isset($private['target_annual_rent']) && $private['target_annual_rent'] !== '' ? $money($private['target_annual_rent']) : '',
        'Erbbaurecht' => ['yes' => 'Ja', 'no' => 'Nein', 'unknown' => 'Ungeklärt'][$private['leasehold_status'] ?? ''] ?? '',
        'Erbbauzins jährlich' => isset($private['leasehold_annual_fee']) && $private['leasehold_annual_fee'] !== '' ? $money($private['leasehold_annual_fee']) : '',
        'Restlaufzeit Erbbaurecht' => isset($private['leasehold_remaining_years']) ? $private['leasehold_remaining_years'] . ' Jahre' : '',
        'Denkmalschutz' => ['yes' => 'Ja', 'no' => 'Nein', 'unknown' => 'Ungeklärt'][$private['monument_status'] ?? ''] ?? '',
        'Baulasten / Rechte Dritter' => ['yes' => 'Ja', 'no' => 'Nein', 'unknown' => 'Ungeklärt'][$private['burdens_status'] ?? ''] ?? '',
        'Erschließungsstand' => ['developed' => 'Erschlossen', 'partial' => 'Teilerschlossen', 'undeveloped' => 'Unerschlossen', 'unknown' => 'Ungeklärt'][$private['development_status'] ?? ''] ?? '',
        'Ausrichtung / Garten' => $private['garden_orientation'] ?? '',
    ];
    if (!$rent && !empty($private['current_annual_rent']) && (float)($property['price'] ?? 0) > 0) {
        $facts['Bruttomietrendite (Ist)'] = number_format((float)$private['current_annual_rent'] / (float)$property['price'] * 100, 2, ',', '.') . ' %';
    }
    if (!empty($e['commission_required']) && isset($e['commission_amount']) && $e['commission_amount'] !== '') {
        $facts['Provision'] = ($e['commission_unit'] ?? 'percent') === 'eur'
            ? $money($e['commission_amount']) : str_replace('.', ',', (string)$e['commission_amount']) . ' %';
        if (!empty($e['commission_payer'])) $facts['Provisionspflichtig'] = $e['commission_payer'];
        if (!empty($e['commission_note'])) $facts['Provisionshinweis'] = $e['commission_note'];
    }
    $validUntil = (string)($e['energy_valid_until'] ?? '');
    if (preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $validUntil, $date)) $validUntil = $date[3] . '.' . $date[2] . '.' . $date[1];
    $energy = [
        'Status' => ($e['energy_certificate_status'] ?? '') === 'not_required' ? 'Nicht erforderlich (Ausnahme geprüft)' : '',
        'Energieausweis' => ['verbrauchsausweis' => 'Verbrauchsausweis', 'bedarfsausweis' => 'Bedarfsausweis'][$e['energy_certificate_type'] ?? ''] ?? ($e['energy_certificate_type'] ?? ''),
        'Energiekennwert' => isset($e['energy_value']) && $e['energy_value'] !== '' ? str_replace('.', ',', (string)$e['energy_value']) . ' kWh/(m²·a)' : '',
        'Energiekennwert Strom' => isset($e['energy_value_electricity']) && $e['energy_value_electricity'] !== '' ? str_replace('.', ',', (string)$e['energy_value_electricity']) . ' kWh/(m²·a)' : '',
        'Kennwertart' => ['bedarf' => 'Endenergiebedarf', 'verbrauch' => 'Endenergieverbrauch'][$e['energy_value_kind'] ?? ''] ?? '',
        'Energieträger' => $e['energy_source'] ?? '',
        'Effizienzklasse' => $e['energy_efficiency_class'] ?? '',
        'Baujahr laut Energieausweis' => $e['energy_building_year'] ?? '',
        'Heizung' => $e['heating_type'] ?? '',
        'Baujahr Heizung' => $e['heating_year'] ?? '',
        'Gültig bis' => $validUntil,
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
    foreach ($imageUrls as $url => $image) {
        $label = trim((string)($image['label'] ?? ''));
        if (!empty($image['virtual_staging'])) $label = 'Visualisierung / Einrichtungsbeispiel' . ($label !== '' ? ' · ' . $label : '');
        $imageHtml[] = '<div class="picture"><img src="' . expose_pdf_image($url) . '" alt="">'
            . ($label !== '' ? '<p class="picture-label">' . expose_pdf_escape($label) . '</p>' : '') . '</div>';
    }
    $cover = array_shift($imageHtml) ?: '';
    $gallery = '';
    foreach (array_chunk($imageHtml, 2) as $page) {
        $gallery .= '<div class="gallery-page"><h2>Impressionen</h2>' . implode('', $page) . '</div>';
    }
    $plans = '';
    $hasPdfPlans = false;
    foreach (expose_pdf_list($e['floorplans'] ?? []) as $plan) {
        $url = is_string($plan) ? $plan : (string)($plan['url'] ?? '');
        if (preg_match('/\.pdf(?:[?#]|$)/i', $url)) $hasPdfPlans = true;
        if ($url !== '' && preg_match('/\.(jpe?g|png|gif|webp)(?:[?#]|$)/i', $url)) {
            $label = is_array($plan) ? trim((string)($plan['label'] ?? '')) : '';
            $plans .= '<div class="plan"><h2>Grundriss</h2>' . ($label !== '' ? '<h3>' . expose_pdf_escape($label) . '</h3>' : '')
                . '<img src="' . expose_pdf_image($url) . '" alt="Grundriss"></div>';
        }
    }
    $documents = '';
    foreach (['floorplans' => 'Grundriss'] as $key => $label) {
        foreach (expose_pdf_list($e[$key] ?? []) as $item) {
            $url = is_string($item) ? $item : (string)($item['url'] ?? '');
            if ($url === '' || ($key === 'floorplans' && preg_match('/\.(jpe?g|png|gif|webp|pdf)(?:[?#]|$)/i', $url))) continue;
            if (strpos($url, '/') === 0 && strpos($url, '//') !== 0) $url = rtrim(SITE_URL, '/') . $url;
            if (!preg_match('~^https?://~i', $url)) continue;
            $name = is_array($item) ? (string)($item['label'] ?? $item['name'] ?? $label) : $label;
            $documents .= '<li><a href="' . expose_pdf_escape($url) . '">' . expose_pdf_escape($name ?: $label) . '</a></li>';
        }
    }
    foreach (expose_pdf_list($private['documents'] ?? []) as $item) {
        if (!is_array($item) || ($item['expose_approved'] ?? false) !== true) continue;
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
    $shortDescription = trim((string)($property['short_description'] ?? ''));
    $video = trim((string)($property['video_url'] ?? ''));
    if ($video !== '' && (!filter_var($video, FILTER_VALIDATE_URL) || !in_array(strtolower((string)parse_url($video, PHP_URL_SCHEME)), ['http', 'https'], true))) {
        throw new RuntimeException('Die Video-URL im Exposé ist ungültig.');
    }
    $locationDescription = trim((string)($e['location_description'] ?? ''));
    foreach (preg_split('/\R{2,}/u', $locationDescription) ?: [] as $locationParagraph) {
        $locationParagraph = trim($locationParagraph);
        if ($locationParagraph !== '') $description = str_replace($locationParagraph, '', $description);
    }
    $descriptionHtml = '';
    $seenParagraphs = [];
    foreach (preg_split('/\R{2,}/u', $description) ?: [] as $paragraph) {
        $paragraph = trim($paragraph);
        $key = mb_strtolower((string)preg_replace('/\s+/u', ' ', $paragraph), 'UTF-8');
        if ($paragraph !== '' && !isset($seenParagraphs[$key])) {
            $lines = preg_split('/\R/u', $paragraph) ?: [];
            $text = [];
            foreach ($lines as $line) {
                if (preg_match('/^#{1,6}\s+(.+)$/u', $line, $heading)) {
                    if ($text) $descriptionHtml .= '<p>' . nl2br(expose_pdf_escape(implode("\n", $text))) . '</p>';
                    $text = [];
                    $descriptionHtml .= '<h3>' . expose_pdf_escape($heading[1]) . '</h3>';
                } else {
                    $text[] = $line;
                }
            }
            if ($text) $descriptionHtml .= '<p>' . nl2br(expose_pdf_escape(implode("\n", $text))) . '</p>';
            $seenParagraphs[$key] = true;
        }
    }
    $locationHtml = '';
    foreach (preg_split('/\R{2,}/u', $locationDescription) ?: [] as $paragraph) {
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
    if (!$rent && $unitRows) {
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
        $person = '<p class="recipient">Guten Tag ' . expose_pdf_escape($recipient['name'] ?? '') . ',<br>wir freuen uns, Ihnen diese Immobilie persönlich vorzustellen.</p>';
    }
    $energyTable = count(array_filter($energy, static fn($v) => $v !== '' && $v !== null)) ? '<div class="energy"><h2>Energieinformationen</h2>' . $table($energy) . '</div>' : '';
    $logoPath = __DIR__ . '/../assets/expose-logo.png';
    if (!is_readable($logoPath)) throw new RuntimeException('Exposé-Logo ist nicht verfügbar.');
    $icons = [
        'Kaufpreis' => 'price', 'Kaltmiete' => 'price', 'Wohnfläche' => 'area',
        'Grundstücksfläche' => 'area', 'Zimmer' => 'rooms', 'Ort' => 'location',
        'Objektart' => 'home', 'Baujahr' => 'calendar', 'Bezugsfrei ab' => 'key',
        'Badezimmer' => 'bath', 'Etage' => 'building', 'Wohneinheiten' => 'building',
    ];
    $tile = static function (string $label, $value) use ($icons): string {
        return '<td><div class="tile"><img src="' . expose_pdf_icon($icons[$label] ?? 'home') . '" alt="">'
            . '<div class="tile-label">' . expose_pdf_escape($label) . '</div><div class="tile-value">'
            . expose_pdf_escape($value) . '</div></div></td>';
    };
    $summaryKeys = array_flip([$rent ? 'Kaltmiete' : 'Kaufpreis', 'Wohnfläche', 'Zimmer', 'Grundstücksfläche', 'Baujahr']);
    $summary = array_filter(array_intersect_key($facts, $summaryKeys), static fn($v) => $v !== '' && $v !== null);
    $glanceKeys = array_flip(['Ort', 'Objektart', 'Bezugsfrei ab', 'Badezimmer', 'Etage', 'Wohneinheiten']);
    $glance = array_filter(array_intersect_key($facts, $glanceKeys), static fn($v) => $v !== '' && $v !== null);
    $details = array_diff_key($facts, $summaryKeys, $glanceKeys);
    $glanceColumns = count($glance) === 4 ? 2 : 3;
    $glanceHtml = '';
    foreach (array_chunk($glance, $glanceColumns, true) as $row) {
        $glanceHtml .= '<tr>';
        foreach ($row as $label => $value) $glanceHtml .= $tile($label, $value);
        $glanceHtml .= str_repeat('<td></td>', $glanceColumns - count($row)) . '</tr>';
    }
    $summaryHtml = '';
    foreach (array_chunk($summary, 5, true) as $row) {
        $summaryHtml .= '<tr>';
        foreach ($row as $label => $value) $summaryHtml .= $tile($label, $value);
        $summaryHtml .= str_repeat('<td></td>', 5 - count($row)) . '</tr>';
    }
    $titleLines = (int)ceil(mb_strlen((string)($property['title'] ?? ''), 'UTF-8') / 30);
    $coverImageHeight = max(80, 330 - max(0, $titleLines - 2) * 28);
    return '<!doctype html><html lang="de"><head><meta charset="utf-8"><style>
        @page { margin: 25mm 18mm 18mm; }
        body { font-family: DejaVu Sans, sans-serif; color: #1a2d3f; font-size: 10pt; line-height: 1.55; }
        h1 { font-size: 21pt; line-height: 1.18; margin: 0 0 8pt; color: #ffffff; word-wrap: break-word; }
        h2 { font-size: 15pt; color: #1a3a5c; margin: 22pt 0 9pt; page-break-after: avoid; }
        h3 { font-size: 10pt; color: #3070a0; margin: 12pt 0 5pt; page-break-after: avoid; }
        .recipient { color: #546778; margin: 12pt 0; }
        .cover { page-break-after: always; }
        .cover .picture { margin: 0 0 12pt; }
        .cover .picture img { max-height: ' . $coverImageHeight . 'pt; }
        .cover-title { background: #193955; color: #ffffff; padding: 20pt 22pt; }
        .cover-title p { margin: 0; color: #e4f0f7; }
        .picture { margin: 10pt 0; page-break-inside: avoid; text-align: center; }
        .picture img { max-width: 100%; max-height: 330pt; }
        .picture-label { font-size: 9pt; color: #546778; margin: 4pt 0; }
        .gallery-page { page-break-before: always; }
        .gallery-page .picture { margin: 12pt 0 20pt; }
        .gallery-page .picture img { max-height: 275pt; }
        .location { page-break-before: always; }
        .plan { page-break-before: always; page-break-inside: avoid; text-align: center; }
        .plan h2, .plan h3 { text-align: left; }
        .plan img { max-width: 100%; max-height: 610pt; }
        .facts { border-collapse: collapse; width: 100%; }
        .facts th, .facts td { border-bottom: 1px solid #dce5eb; padding: 4pt 6pt; text-align: left; vertical-align: top; }
        .facts th { width: 40%; font-weight: normal; color: #546778; }
        .facts tr { page-break-inside: avoid; }
        .summary, .glance { width: 100%; table-layout: fixed; border-collapse: separate; border-spacing: 3pt; }
        .summary td, .glance td { vertical-align: top; }
        .summary td { width: 20%; }
        .glance td { width: ' . (100 / $glanceColumns) . '%; }
        .tile { background: #edf4f8; padding: 9pt; min-height: 52pt; }
        .tile img { width: 17pt; height: 17pt; margin-bottom: 3pt; }
        .tile-label { color: #506a7c; font-size: 8pt; line-height: 1.2; }
        .tile-value { color: #1a3a5c; font-size: 10pt; font-weight: bold; overflow-wrap: break-word; }
        .energy { page-break-inside: avoid; }
        li { margin-bottom: 3pt; } p { margin: 0 0 9pt; }
        .footer { background: #193955; color: #ffffff; margin-top: 18pt; padding: 22pt; font-size: 10pt; page-break-inside: avoid; }
        .footer h2 { color: #ffffff; font-size: 19pt; margin-top: 0; }
        .footer h3 { color: #c7e1f1; }
        </style></head><body><div class="cover">' . $cover . '<div class="cover-title"><h1>'
        . expose_pdf_escape($property['title'] ?? '') . '</h1><p>' . expose_pdf_escape(trim(($property['zip'] ?? '') . ' ' . ($property['location'] ?? ''))) . '</p></div>'
        . $person . '</div><h2>Die Immobilie auf einen Blick</h2>'
        . ($summaryHtml !== '' ? '<table class="summary">' . $summaryHtml . '</table>' : '')
        . ($glanceHtml !== '' ? '<table class="glance">' . $glanceHtml . '</table>' : '')
        . (count(array_filter($details, static fn($v) => $v !== '' && $v !== null)) ? '<h2>Weitere Fakten und Konditionen</h2>' . $table($details) : '')
        . ($shortDescription !== '' && $shortDescription !== $description ? '<h2>Kurzbeschreibung</h2><p>' . nl2br(expose_pdf_escape($shortDescription)) . '</p>' : '')
        . ($descriptionHtml !== '' ? '<h2>Objektbeschreibung</h2>' . $descriptionHtml : '')
        . ($featureHtml !== '' ? '<h2>Ausstattung</h2><ul>' . $featureHtml . '</ul>' : '')
        . $unitHtml . $privateSections
        . $energyTable
        . ($locationHtml !== '' ? '<div class="location"><h2>Lage</h2>' . $locationHtml . '</div>' : '')
        . ($video !== '' ? '<p><a href="' . expose_pdf_escape($video) . '">Video zur Immobilie ansehen</a></p>' : '')
        . $gallery
        . $plans
        . ($documents !== '' ? '<h2>Weitere Unterlagen</h2><ul>' . $documents . '</ul>' : '')
        . '<div class="footer"><h2>Ihr Kontakt</h2><p>Sie möchten mehr über diese Immobilie erfahren oder einen Besichtigungstermin vereinbaren? Wir sind gerne persönlich für Sie da.</p><p><strong>Immobilien Ghumman · Nasir Ghumman</strong><br>Homburger Str. 100 · 61118 Bad Vilbel<br>Telefon: '
        . expose_pdf_escape(SITE_PHONE) . '<br>E-Mail: ' . expose_pdf_escape(SITE_EMAIL) . '<br>Web: ' . expose_pdf_escape(SITE_URL) . '</p>'
        . '<h3>Hinweis zu den Angaben</h3><p>Die Angaben beruhen auf Informationen der Eigentümerseite. Für Vollständigkeit und Richtigkeit kann keine Gewähr übernommen werden; gesetzliche Haftungsansprüche bleiben unberührt.</p></div>'
        . ($hasPdfPlans ? '<p>Die als PDF hochgeladenen Grundrisse finden Sie auf den folgenden Seiten.</p>' : '') . '</body></html>';
}

function expose_pdf_render(array $property, array $recipient = []): string
{
    $options = new Dompdf\Options();
    $options->set('isRemoteEnabled', false);
    $dompdf = new Dompdf\Dompdf($options);
    $dompdf->loadHtml(expose_pdf_html($property, $recipient), 'UTF-8');
    $dompdf->setPaper('A4');
    $dompdf->render();
    $font = $dompdf->getFontMetrics()->getFont('DejaVu Sans', 'normal');
    $canvas = $dompdf->getCanvas();
    $logoPath = __DIR__ . '/../assets/expose-logo.png';
    $canvas->page_script(static function ($page, $count, $canvas) use ($logoPath, $font): void {
        $canvas->image($logoPath, 51, 19, 60, 38);
        $canvas->line(51, 68, 544, 68, [0.83, 0.89, 0.93], 0.6);
        $canvas->text(453, 37, 'IMMOBILIEN-EXPOSÉ', $font, 8, [0.33, 0.42, 0.49]);
    });
    $canvas->page_text(51, 815, 'IMMOBILIEN GHUMMAN  |  ' . SITE_URL, $font, 7, [0.30, 0.39, 0.47]);
    $canvas->page_text(510, 815, 'Seite {PAGE_NUM}', $font, 7, [0.30, 0.39, 0.47]);
    $pdf = $dompdf->output();
    if (strncmp($pdf, '%PDF-', 5) !== 0) throw new RuntimeException('Exposé-PDF konnte nicht erzeugt werden.');
    $extra = expose_pdf_list($property['extra_data'] ?? []);
    $pdfPlans = [];
    foreach (expose_pdf_list($extra['floorplans'] ?? []) as $plan) {
        $url = is_string($plan) ? $plan : (string)($plan['url'] ?? '');
        if (!preg_match('/\.pdf(?:[?#]|$)/i', $url)) continue;
        $parts = parse_url($url);
        $host = parse_url(SITE_URL, PHP_URL_HOST);
        $prefix = (defined('BASE') ? BASE : '') . '/uploads/';
        if ($parts === false || (isset($parts['host']) && (strtolower($parts['host']) !== strtolower((string)$host)
            || ($parts['scheme'] ?? '') !== 'https' || isset($parts['user']) || isset($parts['port'])))
            || strpos((string)($parts['path'] ?? ''), $prefix) !== 0) {
            throw new RuntimeException('PDF-Grundriss muss lokal gespeichert sein.');
        }
        $root = realpath(__DIR__ . '/../uploads');
        $path = realpath(__DIR__ . '/../uploads/' . substr($parts['path'], strlen($prefix)));
        if ($root === false || $path === false || strpos($path, $root . DIRECTORY_SEPARATOR) !== 0
            || !is_file($path) || filesize($path) > 10 * 1024 * 1024
            || (new finfo(FILEINFO_MIME_TYPE))->file($path) !== 'application/pdf') {
            throw new RuntimeException('PDF-Grundriss ist nicht verfügbar oder ungültig.');
        }
        $pdfPlans[] = $path;
    }
    if ($pdfPlans) {
        $merged = new \setasign\Fpdi\Fpdi();
        $sources = array_merge([\setasign\Fpdi\PdfParser\StreamReader::createByString($pdf)], $pdfPlans);
        foreach ($sources as $source) {
            $count = $merged->setSourceFile($source);
            for ($page = 1; $page <= $count; $page++) {
                $template = $merged->importPage($page, '/CropBox', true, true);
                $size = $merged->getTemplateSize($template);
                $merged->AddPage($size['orientation'], [$size['width'], $size['height']]);
                $merged->useTemplate($template);
            }
        }
        $pdf = $merged->Output('S');
    }
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

function expose_request_store(PDO $pdo, array $fields, string $pdf = ''): int
{
    if ($pdf !== '' && strncmp($pdf, '%PDF-', 5) !== 0) throw new InvalidArgumentException('Ungültiges Exposé-PDF.');
    $pdo->beginTransaction();
    try {
        $stmt = $pdo->prepare('INSERT INTO contact_inquiries (name, email, phone, message, property_id, ip_address, created_at) VALUES (?, ?, ?, ?, ?, ?, CURRENT_TIMESTAMP)');
        $stmt->execute($fields);
        $id = (int)$pdo->lastInsertId();
        $stmt = $pdo->prepare("INSERT INTO expose_requests (inquiry_id, pdf, delivery_status) VALUES (?, ?, 'new')");
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

function expose_request_snapshot(PDO $pdo, int $id, ?array $approvedProperty = null): string
{
    $stmt = $pdo->prepare('SELECT er.pdf, er.delivery_status, i.name, i.email, i.phone, i.property_id FROM expose_requests er JOIN contact_inquiries i ON i.id = er.inquiry_id WHERE er.inquiry_id = ?');
    $stmt->execute([$id]);
    $request = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$request) throw new RuntimeException('Exposé-Anfrage nicht gefunden.');
    $pdf = is_resource($request['pdf']) ? stream_get_contents($request['pdf']) : $request['pdf'];
    if (is_string($pdf) && strncmp($pdf, '%PDF-', 5) === 0) return $pdf;
    if ($pdf !== '' || $request['delivery_status'] !== 'new') throw new RuntimeException('Gespeichertes Exposé ist ungültig.');
    if ($approvedProperty !== null && (int)($approvedProperty['id'] ?? 0) === (int)$request['property_id']) {
        $property = $approvedProperty;
    } else {
        $stmt = $pdo->prepare('SELECT * FROM properties WHERE id = ?');
        $stmt->execute([$request['property_id']]);
        $property = $stmt->fetch(PDO::FETCH_ASSOC);
    }
    if (!$property) throw new RuntimeException('Immobilie für die Exposé-Anfrage nicht gefunden.');
    $pdf = expose_pdf_render($property, ['name' => $request['name'], 'email' => $request['email'], 'phone' => $request['phone']]);
    $stmt = $pdo->prepare("UPDATE expose_requests SET pdf = ? WHERE inquiry_id = ? AND delivery_status = 'new' AND length(pdf) = 0");
    $stmt->bindValue(1, $pdf, PDO::PARAM_LOB);
    $stmt->bindValue(2, $id, PDO::PARAM_INT);
    $stmt->execute();
    if ($stmt->rowCount() === 1) return $pdf;
    $stmt = $pdo->prepare('SELECT pdf FROM expose_requests WHERE inquiry_id = ?');
    $stmt->execute([$id]);
    $saved = $stmt->fetchColumn();
    if (is_resource($saved)) $saved = stream_get_contents($saved);
    if (!is_string($saved) || strncmp($saved, '%PDF-', 5) !== 0) throw new RuntimeException('Exposé konnte nicht gespeichert werden.');
    return $saved;
}

function expose_request_deliver(PDO $pdo, int $id, string $pdf, callable $send): bool
{
    if (strncmp($pdf, '%PDF-', 5) !== 0) throw new InvalidArgumentException('Ungültiges Exposé-PDF.');
    $stmt = $pdo->prepare("UPDATE expose_requests SET delivery_status = 'sending' WHERE inquiry_id = ? AND delivery_status IN ('new', 'prepared', 'unknown') AND pdf = ?");
    $stmt->bindValue(1, $id, PDO::PARAM_INT);
    $stmt->bindValue(2, $pdf, PDO::PARAM_LOB);
    $stmt->execute();
    if ($stmt->rowCount() !== 1) throw new RuntimeException('Exposé wurde verändert, bereits versendet oder wird gerade versendet.');
    $sent = $send($pdf);
    $stmt = $pdo->prepare('UPDATE expose_requests SET delivery_status = ? WHERE inquiry_id = ?');
    $stmt->execute([$sent ? 'sent' : 'unknown', $id]);
    if ($stmt->rowCount() !== 1) throw new RuntimeException('Exposé-Versandstatus konnte nicht zugeordnet werden.');
    return $sent;
}
