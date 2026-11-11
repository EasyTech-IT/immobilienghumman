<?php
function expose_escape($value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function expose_list($value): array
{
    if (is_array($value)) return $value;
    $decoded = json_decode((string)$value, true);
    return is_array($decoded) ? $decoded : [];
}

function expose_url($url): string
{
    $url = trim((string)$url);
    if (preg_match('~^/(?!/)~', $url) || preg_match('~^https?://~i', $url)) return $url;
    return '';
}

function expose_money($value): string
{
    return number_format((float)$value, 2, ',', '.') . ' €';
}

$extra = expose_list($property['extra_data'] ?? []);
$images = expose_list($property['images'] ?? []);
if (!$images && !empty($property['main_image'])) $images = [$property['main_image']];
$features = expose_list($property['features'] ?? []);
$facts = [];
if ($property) {
    $offerType = strtolower((string)($property['offer_type'] ?? ''));
    $priceLabel = $offerType === 'kauf' ? 'Kaufpreis' : ($offerType === 'miete' ? 'Kaltmiete' : 'Preis');
    $address = !empty($extra['hide_exact_address'])
        ? (($extra['address_public_note'] ?? '') ?: ($property['location'] ?? ''))
        : trim(implode(', ', array_filter([$property['address'] ?? '', trim(($property['zip'] ?? '') . ' ' . ($property['location'] ?? ''))])));
    $facts = [
        'Objektnummer' => $property['id'] ?? '',
        'Objektadresse' => $address,
        'Objektart' => $property['type'] ?? '',
        'Bezug' => $property['available_from'] ?? '',
        $priceLabel => !empty($property['price']) ? expose_money($property['price']) : '',
        'Nebenkosten' => !empty($property['extra_costs']) ? expose_money($property['extra_costs']) : '',
        'Heizkosten' => !empty($property['heating_costs']) ? expose_money($property['heating_costs']) : '',
        'Warmmiete' => !empty($property['warm_rent']) ? expose_money($property['warm_rent']) : '',
        'Kaution' => !empty($property['deposit']) ? expose_money($property['deposit']) : '',
        'Wohnfläche' => !empty($property['area']) ? number_format((float)$property['area'], 2, ',', '.') . ' m²' : '',
        'Zimmer' => !empty($property['rooms']) ? number_format((float)$property['rooms'], 1, ',', '.') : '',
        'Badezimmer' => !empty($property['bathrooms']) ? $property['bathrooms'] : '',
        'Baujahr' => $extra['building_year'] ?? '',
        'Modernisierung' => $extra['last_modernization_year'] ?? '',
        'Etage' => $extra['floor'] ?? '',
        'Aufzug' => !empty($extra['has_elevator']) ? 'Ja' : '',
        'Energieausweis' => isset($extra['energy_certificate_type']) ? ucfirst($extra['energy_certificate_type']) : '',
        'Energiekennwert' => isset($extra['energy_value']) && $extra['energy_value'] !== '' ? $extra['energy_value'] . ' kWh/(m²·a)' : '',
        'Art des Energiekennwerts' => ['bedarf' => 'Endenergiebedarf', 'verbrauch' => 'Endenergieverbrauch'][$extra['energy_value_kind'] ?? ''] ?? '',
        'Energieeffizienzklasse' => $extra['energy_efficiency_class'] ?? '',
        'Energieträger' => $extra['energy_source'] ?? '',
        'Hausgeld' => !empty($extra['house_fee']) ? expose_money($extra['house_fee']) : '',
        'Stellplatz' => $extra['parking_info'] ?? '',
    ];
    if (!empty($extra['commission_required']) && isset($extra['commission_amount']) && $extra['commission_amount'] !== '') {
        $facts['Provision'] = ($extra['commission_unit'] ?? 'percent') === 'eur'
            ? expose_money($extra['commission_amount'])
            : str_replace('.', ',', (string)$extra['commission_amount']) . ' %';
        if (!empty($extra['commission_payer'])) $facts['Provisionspflichtig'] = $extra['commission_payer'];
    }
}
$attachments = [];
foreach (['floorplans' => 'Grundriss', 'documents' => 'Dokument'] as $key => $label) {
    foreach (expose_list($extra[$key] ?? []) as $file) {
        $url = expose_url(is_string($file) ? $file : ($file['url'] ?? ''));
        if ($url !== '') $attachments[] = ['url' => $url, 'label' => is_array($file) ? (($file['label'] ?? '') ?: (($file['name'] ?? '') ?: $label)) : $label];
    }
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title><?= expose_escape($property ? 'Exposé – ' . $property['title'] : 'Exposé nicht verfügbar') ?> | Immobilien Ghumman</title>
    <link rel="stylesheet" href="<?= BASE ?>/expose.css?v=<?= (int)filemtime(__DIR__ . '/../expose.css') ?>">
</head>
<body>
<nav class="expose-toolbar" aria-label="Exposé Aktionen">
    <a href="<?= BASE ?>/immobilien-angebote">Alle Immobilien</a>
    <?php if ($property): ?>
    <a href="<?= BASE ?>/property-detail.php?<?= !empty($property['slug']) ? 'slug=' . rawurlencode($property['slug']) : 'id=' . (int)$property['id'] ?>">Zur Immobilie</a>
    <button type="button" onclick="window.print()">Drucken / als PDF speichern</button>
    <?php endif; ?>
</nav>
<?php if (!$property): ?>
<main class="expose-document"><p role="alert"><?= expose_escape($error) ?></p></main>
<?php else: ?>
<footer class="expose-footer">
    Immobilien Ghumman | Nasir Ghumman | Homburger Straße 100, 61118 Bad Vilbel<br>
    Tel. 0160 9878 7878 | immobilienghumman.de | info@immobilienghumman.de
</footer>
<main class="expose-document">
    <header class="expose-header">
        <div class="expose-sender">Immobilien Ghumman · Homburger Straße 100 · 61118 Bad Vilbel</div>
        <div class="expose-brand">
            <img src="<?= expose_escape(SITE_LOGO) ?>" alt="Immobilien Ghumman">
            <span>Homburger Straße 100<br>61118 Bad Vilbel</span>
        </div>
    </header>
    <div class="expose-date">Bad Vilbel, <?= date('d.m.Y') ?></div>
    <div class="expose-reference">Exposé · Objekt <?= expose_escape($property['id']) ?></div>
    <h1><?= expose_escape($property['title']) ?></h1>
    <p>Vielen Dank für Ihr Interesse an unserem Immobilienangebot.</p>
    <dl class="expose-facts">
        <?php foreach ($facts as $label => $value): if ($value === '' || $value === null) continue; ?>
        <div><dt><?= expose_escape($label) ?></dt><dd><?= expose_escape($value) ?></dd></div>
        <?php endforeach; ?>
    </dl>
    <?php if (!empty($property['description'])): ?>
    <section class="expose-text">
        <h2>Objekt</h2>
        <div><?php foreach (preg_split('/\R{2,}/u', trim($property['description'])) as $paragraph): ?>
            <p><?= nl2br(expose_escape($paragraph)) ?></p>
        <?php endforeach; ?></div>
    </section>
    <?php endif; ?>
    <?php if ($features): ?>
    <section class="expose-text">
        <h2>Ausstattung</h2>
        <ul><?php foreach ($features as $feature): if (!is_string($feature) || trim($feature) === '') continue; ?>
            <li><?= expose_escape($feature) ?></li>
        <?php endforeach; ?></ul>
    </section>
    <?php endif; ?>
    <?php if ($attachments): ?>
    <section class="expose-text expose-attachments">
        <h2>Anlagen</h2>
        <div>
            <ul><?php foreach ($attachments as $attachment): ?>
                <li><a href="<?= expose_escape($attachment['url']) ?>" target="_blank" rel="noopener noreferrer"><?= expose_escape($attachment['label']) ?></a></li>
            <?php endforeach; ?></ul>
            <p class="expose-attachment-note">Anlagen bitte separat öffnen und bei Bedarf drucken.</p>
        </div>
    </section>
    <?php endif; ?>
    <section class="expose-contact">
        <p>Für einen Besichtigungs- oder Beratungstermin stehen wir Ihnen gerne zur Verfügung.</p>
        <p>Mit freundlichen Grüßen<br><strong>Immobilien Ghumman</strong></p>
        <a href="tel:+4916098787878">0160 9878 7878</a> · <a href="mailto:<?= expose_escape(SITE_EMAIL) ?>"><?= expose_escape(SITE_EMAIL) ?></a>
    </section>
    <?php if ($images): ?>
    <section class="expose-gallery" aria-label="Bilder der Immobilie">
        <h2>Impressionen</h2>
        <?php foreach ($images as $image):
            $url = expose_url(is_string($image) ? $image : ($image['url'] ?? $image['image_url'] ?? ''));
            if ($url === '') continue;
            $label = is_array($image) ? ($image['label'] ?? '') : '';
        ?>
        <figure>
            <a href="<?= expose_escape($url) ?>" target="_blank" rel="noopener noreferrer"><img src="<?= expose_escape($url) ?>" alt="<?= expose_escape($label ?: $property['title']) ?>"></a>
            <?php if ($label !== ''): ?><figcaption><?= expose_escape($label) ?></figcaption><?php endif; ?>
        </figure>
        <?php endforeach; ?>
    </section>
    <?php endif; ?>
</main>
<?php endif; ?>
</body>
</html>
