<?php

define('BASE', '');
define('SITE_PHONE', '01234');
define('SITE_EMAIL', 'test@example.test');
define('SITE_URL', 'https://example.test');
define('AUTH_SECRET_CONFIGURED', true);
define('AUTH_SECRET', 'test-secret');
require_once __DIR__ . '/../includes/expose-pdf.php';
require_once __DIR__ . '/../includes/mailer.php';

function check(bool $condition, string $message): void
{
    if (!$condition) throw new RuntimeException($message);
    echo "OK: $message\n";
}

$property = [
    'title' => 'Wohnung <Muster>',
    'location' => 'Büdingen',
    'zip' => '63654',
    'address' => 'Musterweg 1',
    'type' => 'Wohnung',
    'offer_type' => 'Kauf',
    'price' => 250000,
    'area' => 100,
    'rooms' => 3,
    'extra_costs' => '0.00',
    'heating_costs' => '25.00',
    'warm_rent' => '1000.00',
    'deposit' => '500.00',
    'short_description' => 'Helle Wohnung mit Balkon.',
    'video_url' => 'https://example.test/objektvideo',
    'description' => "Heller Wohnbereich.\n\nZwei Ebenen.\n\nHeller Wohnbereich.",
    'features' => json_encode(['Balkon']),
    'extra_data' => json_encode([
        'commission_required' => true,
        'commission_amount' => 3.57,
        'commission_payer' => 'Käufer',
        'location_description' => 'Nah am Bahnhof',
        'plot_area' => 1234,
        'residential_units' => 4,
        'house_fee' => 225,
        'private_expose' => [
            'current_annual_rent' => 18000,
            'burdens_notes' => 'Nur im privaten Exposé',
            'unit_overview' => [['unit' => 'A', 'floor' => 'EG', 'area' => '80', 'rooms' => '3', 'status' => 'vermietet', 'rent' => '1200']],
        ],
    ]),
];
$recipient = ['name' => 'Anna <Test>', 'email' => 'anna@example.test', 'phone' => '12345'];
$html = expose_pdf_html($property, $recipient);
check(strpos($html, 'Die Immobilie auf einen Blick') !== false && strpos($html, 'data:image/svg+xml;base64,') !== false,
    'Faktenübersicht mit Icons wird erzeugt');
check(strpos($html, 'Anna &lt;Test&gt;') !== false && strpos($html, '<Test>') === false, 'Empfängerdaten werden personalisiert und maskiert');
check(strpos($html, 'Musterweg 1') !== false && strpos($html, '3,57 %') !== false, 'Adresse und Provision stehen im Exposé');
check(strpos($html, '<h2>Lage</h2>') !== false && strpos($html, 'Nah am Bahnhof') !== false, 'Eingetragene Lagebeschreibung erscheint im Exposé');
check(strpos($html, 'Helle Wohnung mit Balkon.') !== false && strpos($html, 'https://example.test/objektvideo') !== false, 'Kurzbeschreibung und gültiger Videolink erscheinen');
check(strpos($html, '<h2>Energieinformationen</h2>') === false, 'Leere Energieinformationen bleiben verborgen');
$withEnergy = $property;
$energyExtra = json_decode($withEnergy['extra_data'], true);
$energyExtra += [
    'energy_certificate_type' => 'bedarfsausweis', 'energy_value' => '145.2',
    'energy_value_kind' => 'bedarf', 'energy_source' => 'Gas',
    'energy_building_year' => 1995, 'energy_efficiency_class' => 'E',
    'energy_value_electricity' => '0',
];
$withEnergy['extra_data'] = json_encode($energyExtra);
$energyHtml = expose_pdf_html($withEnergy, $recipient);
foreach (['Bedarfsausweis', '145,2 kWh/(m²·a)', 'Endenergiebedarf', 'Gas', '1995', 'Effizienzklasse', '0 kWh/(m²·a)'] as $value) {
    check(strpos($energyHtml, $value) !== false, 'Vorhandene Energieangabe bleibt sichtbar: ' . $value);
}
check(strpos($html, 'Nebenkosten') === false && strpos($html, 'Warmmiete') === false && strpos($html, 'Kaution') === false && strpos($html, 'Heizkosten') === false, 'Mietkosten erscheinen nicht im Kaufexposé');
check(strpos($html, 'Grundstücksfläche') !== false && strpos($html, '1.234,00 m²') !== false && strpos($html, 'Wohneinheiten') !== false, 'Eingetragene Grundstücksfläche und Wohneinheiten erscheinen');
check(strpos($html, 'Nur im privaten Exposé') !== false && strpos($html, 'Übersicht Wohneinheiten') !== false, 'Private Exposé-Angaben und Einheitenübersicht erscheinen');
check(substr_count($html, 'Heller Wohnbereich.') === 1, 'Identische Beschreibungsabsätze werden nicht wiederholt');
check(strpos($html, 'gesetzliche Haftungsansprüche bleiben unberührt') !== false && strpos($html, '<h2>Ihr Kontakt</h2>') !== false, 'Kontakt und Hinweis zu den Angaben erscheinen');
$rental = $property;
$rental['offer_type'] = 'Miete';
check(strpos(expose_pdf_html($rental, $recipient), 'Warmmiete') !== false, 'Mietkosten bleiben bei Mietobjekten sichtbar');
check(strpos(expose_pdf_html($rental, $recipient), '0,00 €') !== false, 'Auch ausdrücklich angegebene Mietkosten von null werden angezeigt');
$rentalHtml = expose_pdf_html($rental, $recipient);
check(strpos($rentalHtml, 'Hausgeld') === false && strpos($rentalHtml, 'Jahresnettokaltmiete') === false
    && strpos($rentalHtml, 'Bruttomietrendite') === false && strpos($rentalHtml, 'Übersicht Wohneinheiten') === false,
    'Kauf- und Anlegerdaten erscheinen nicht im Mietexposé');
$formatted = $property;
$formatted['description'] = "## Architektur\n\n### Wohnen\nHeller Wohnbereich.\n\nNah am Bahnhof";
$formattedHtml = expose_pdf_html($formatted, $recipient);
check(strpos($formattedHtml, '###') === false && strpos($formattedHtml, '<h3>Wohnen</h3>') !== false, 'Markdown-Überschriften erscheinen formatiert');
check(substr_count($formattedHtml, 'Nah am Bahnhof') === 1, 'Lagebeschreibung erscheint nicht doppelt');

$imageFile = tempnam(__DIR__ . '/../uploads', 'expose-test-');
if ($imageFile === false) throw new RuntimeException('Testbild konnte nicht angelegt werden.');
$pngFile = $imageFile . '.png';
if (file_exists($pngFile) || !rename($imageFile, $pngFile)) {
    unlink($imageFile);
    throw new RuntimeException('Testbild konnte nicht vorbereitet werden.');
}
$imageFile = $pngFile;
try {
    file_put_contents($imageFile, base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+/l2cAAAAASUVORK5CYII='));
    $url = '/uploads/' . basename($imageFile);
    $pictured = $property;
    $pictured['main_image'] = $url;
    $pictured['images'] = json_encode([
        ['url' => $url, 'label' => 'Dachstudio', 'virtual_staging' => true],
        ['url' => $url . '?view=2', 'label' => 'Wohnbereich'],
    ]);
    $extra = json_decode($pictured['extra_data'], true);
    $extra['floorplans'] = [['url' => $url, 'label' => 'Dachgeschoss W4']];
    $pictured['extra_data'] = json_encode($extra);
    $pictureHtml = expose_pdf_html($pictured, $recipient);
    check(strpos($pictureHtml, 'Visualisierung / Einrichtungsbeispiel · Dachstudio') !== false, 'Gekennzeichnete Visualisierungen sind im Exposé erkennbar');
    check(strpos($pictureHtml, '<h3>Dachgeschoss W4</h3>') !== false, 'Grundrisse tragen ihre eingetragene Beschriftung');
    check(strpos($pictureHtml, '<h2>Objektbeschreibung</h2>') < strpos($pictureHtml, '<h2>Lage</h2>')
        && strpos($pictureHtml, '<h2>Lage</h2>') < strpos($pictureHtml, '<h2>Impressionen</h2>'), 'Bilder folgen auf Beschreibung und Lage');
    $manyImages = $property;
    $manyImages['images'] = json_encode(array_map(static fn($n) => ['url' => $url . '?image=' . $n], range(1, 10)));
    check(substr_count(expose_pdf_html($manyImages, $recipient), '<div class="picture">') === 10, 'Auch mehr als acht hochgeladene Bilder erscheinen im Exposé');
} finally {
    unlink($imageFile);
}
$badVideo = $property;
$badVideo['video_url'] = 'javascript:alert(1)';
try {
    expose_pdf_html($badVideo, $recipient);
    throw new RuntimeException('Unsichere Video-URL wurde akzeptiert');
} catch (RuntimeException $e) {
    check(strpos($e->getMessage(), 'Video-URL') !== false, 'Ungültige Video-URLs werden ausdrücklich zurückgewiesen');
}
$pdf = expose_pdf_render($property, $recipient);
check(strncmp($pdf, '%PDF-', 5) === 0 && strlen($pdf) > 1000, 'Ein echtes PDF wird erzeugt');
check(strpos($pdf, '/Subtype /Image') !== false, 'Das bereitgestellte Logo wird in das PDF eingebettet');
$withDocuments = $property;
$extra = json_decode($withDocuments['extra_data'], true);
$extra['private_expose']['documents'] = [
    ['url' => 'private:' . str_repeat('a', 32), 'name' => 'Freigegeben', 'expose_approved' => true],
    ['url' => 'private:' . str_repeat('b', 32), 'name' => 'Vertraulich'],
];
$withDocuments['extra_data'] = json_encode($extra);
$documentHtml = expose_pdf_html($withDocuments, $recipient);
check(strpos($documentHtml, 'Freigegeben') !== false && strpos($documentHtml, 'Vertraulich') === false, 'Nur ausdrücklich freigegebene private Dokumente werden verlinkt');
$floorplanBase = tempnam(__DIR__ . '/../uploads', 'floorplan-test-');
if ($floorplanBase === false) throw new RuntimeException('Testgrundriss konnte nicht erstellt werden.');
$floorplanFile = $floorplanBase . '.pdf';
if (file_exists($floorplanFile) || !rename($floorplanBase, $floorplanFile)) {
    unlink($floorplanBase);
    throw new RuntimeException('Testgrundriss konnte nicht vorbereitet werden.');
}
try {
    file_put_contents($floorplanFile, $pdf);
    $extra['floorplans'] = [['url' => '/uploads/' . basename($floorplanFile), 'label' => 'Grundriss']];
    $withDocuments['extra_data'] = json_encode($extra);
    $withFloorplan = expose_pdf_render($withDocuments, $recipient);
    check(strpos($withFloorplan, str_repeat('a', 32)) !== false, 'Downloadlinks bleiben beim Zusammenführen anklickbar');
    $reader = new \setasign\Fpdi\Fpdi();
    $count = $reader->setSourceFile(\setasign\Fpdi\PdfParser\StreamReader::createByString($withFloorplan));
    $originalCount = $reader->setSourceFile(\setasign\Fpdi\PdfParser\StreamReader::createByString($pdf));
    check($count > $originalCount, 'PDF-Grundrisse werden als Seiten in das Exposé übernommen');
} finally {
    unlink($floorplanFile);
}
try {
    expose_pdf_image('https://example.test/privat.jpg');
    throw new RuntimeException('Fremder Bildhost wurde nicht blockiert');
} catch (RuntimeException $e) {
    check(strpos($e->getMessage(), 'nicht freigegeben') !== false, 'Nicht freigegebene Bildhosts werden blockiert');
}

$pdo = new PDO('sqlite::memory:', null, null, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
$pdo->exec('PRAGMA foreign_keys=ON');
$pdo->exec('CREATE TABLE contact_inquiries (id INTEGER PRIMARY KEY AUTOINCREMENT, name TEXT, email TEXT, phone TEXT, message TEXT, property_id INTEGER, ip_address TEXT, created_at TEXT)');
$pdo->exec('CREATE TABLE expose_requests (inquiry_id INTEGER PRIMARY KEY REFERENCES contact_inquiries(id) ON DELETE CASCADE, pdf BLOB NOT NULL, delivery_status TEXT NOT NULL)');
$fields = ['Anna Test', 'anna@example.test', '12345', 'Exposé-Anfrage', 1, '127.0.0.1'];
$pendingId = expose_request_store($pdo, $fields);
$pending = $pdo->query("SELECT pdf, delivery_status FROM expose_requests WHERE inquiry_id = $pendingId")->fetch(PDO::FETCH_ASSOC);
$pendingPdf = is_resource($pending['pdf']) ? stream_get_contents($pending['pdf']) : $pending['pdf'];
check($pendingPdf === '' && $pending['delivery_status'] === 'new', 'Neue Anfrage wird ohne PDF und ohne Versand gespeichert');
$id = expose_request_store($pdo, $fields, $pdf);
try {
    expose_request_deliver($pdo, $id, $pdf . 'different', static function (): bool {
        throw new RuntimeException('Veraltete Vorschau wurde versendet');
    });
    throw new RuntimeException('Veraltete Vorschau wurde nicht erkannt');
} catch (RuntimeException $e) {
    check(strpos($e->getMessage(), 'verändert') !== false, 'Eine veränderte Vorschau kann nicht versendet werden');
}
$attached = '';
$sent = expose_request_deliver($pdo, $id, $pdf, static function ($bytes) use (&$attached): bool {
    $attached = $bytes;
    return true;
});
$row = $pdo->query("SELECT pdf, delivery_status FROM expose_requests WHERE inquiry_id = $id")->fetch(PDO::FETCH_ASSOC);
$stored = is_resource($row['pdf']) ? stream_get_contents($row['pdf']) : $row['pdf'];
check($sent && $row['delivery_status'] === 'sent' && $attached === $stored, 'Versand und Dashboard-Kopie enthalten identische PDF-Bytes');
$failedId = expose_request_store($pdo, $fields, $pdf);
$sent = expose_request_deliver($pdo, $failedId, $pdf, static fn() => false);
check(!$sent && $pdo->query("SELECT delivery_status FROM expose_requests WHERE inquiry_id = $failedId")->fetchColumn() === 'unknown', 'SMTP-Fehler wird nicht als Erfolg gespeichert');
$legacyId = expose_request_store($pdo, $fields, $pdf);
$pdo->exec("UPDATE expose_requests SET delivery_status = 'prepared' WHERE inquiry_id = $legacyId");
check(expose_request_deliver($pdo, $legacyId, $pdf, static fn() => true), 'Frühere vorbereitete Exposés bleiben versendbar');
$pdo->exec("CREATE TRIGGER reject_snapshot BEFORE INSERT ON expose_requests BEGIN SELECT RAISE(ABORT, 'snapshot rejected'); END");
$count = (int)$pdo->query('SELECT COUNT(*) FROM contact_inquiries')->fetchColumn();
try {
    expose_request_store($pdo, $fields, $pdf);
    throw new RuntimeException('Speicherfehler wurde nicht erkannt');
} catch (PDOException $e) {
    check((int)$pdo->query('SELECT COUNT(*) FROM contact_inquiries')->fetchColumn() === $count, 'Fehler beim Speichern rollt auch die Anfrage zurück');
}
$pdo->exec("DELETE FROM contact_inquiries WHERE id = $id");
check((int)$pdo->query("SELECT COUNT(*) FROM expose_requests WHERE inquiry_id = $id")->fetchColumn() === 0, 'Beim Löschen der Anfrage wird die Kopie mitgelöscht');

$mailer = new SimpleSocketMailer('localhost');
$method = new ReflectionMethod(SimpleSocketMailer::class, 'buildMessage');
$method->setAccessible(true);
$mime = $method->invoke($mailer, 'sender@example.test', 'Test', 'anna@example.test', 'Exposé', 'Ihr Exposé', '', '', ['content' => $pdf]);
preg_match('/Content-Disposition: attachment; filename="expose\.pdf"\r\nContent-Transfer-Encoding: base64\r\n\r\n([A-Za-z0-9+\/=\r\n]+)\r\n--=_ghumman_mixed_/', $mime, $matches);
check(count($matches) === 2, 'E-Mail enthält einen PDF-Anhang');
check(base64_decode($matches[1], true) === $pdf, 'E-Mail-Anhang ist bytegleich zur gespeicherten PDF-Kopie');
