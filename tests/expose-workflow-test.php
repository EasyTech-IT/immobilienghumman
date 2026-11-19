<?php

define('BASE', '');
define('SITE_PHONE', '01234');
define('SITE_EMAIL', 'test@example.test');
define('SITE_URL', 'https://example.test');
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
    'description' => "Heller Wohnbereich.\n\nZwei Ebenen.\n\nHeller Wohnbereich.",
    'features' => json_encode(['Balkon']),
    'extra_data' => json_encode([
        'commission_required' => true,
        'commission_amount' => 3.57,
        'commission_payer' => 'Käufer',
        'location_description' => 'Nah am Bahnhof',
        'plot_area' => 1234,
        'residential_units' => 4,
        'private_expose' => [
            'current_annual_rent' => 18000,
            'burdens_notes' => 'Nur im privaten Exposé',
            'unit_overview' => [['unit' => 'A', 'floor' => 'EG', 'area' => '80', 'rooms' => '3', 'status' => 'vermietet', 'rent' => '1200']],
        ],
    ]),
];
$recipient = ['name' => 'Anna <Test>', 'email' => 'anna@example.test', 'phone' => '12345'];
$html = expose_pdf_html($property, $recipient);
check(strpos($html, 'Anna &lt;Test&gt;') !== false && strpos($html, '<Test>') === false, 'Empfängerdaten werden personalisiert und maskiert');
check(strpos($html, 'Musterweg 1') !== false && strpos($html, '3,57 %') !== false, 'Adresse und Provision stehen im Exposé');
check(strpos($html, '<h2>Lage</h2>') !== false && strpos($html, 'Nah am Bahnhof') !== false, 'Eingetragene Lagebeschreibung erscheint im Exposé');
check(strpos($html, '<h2>Energieinformationen</h2>') === false, 'Leere Energieinformationen bleiben verborgen');
check(strpos($html, 'Nebenkosten') === false && strpos($html, 'Warmmiete') === false && strpos($html, 'Kaution') === false && strpos($html, 'Heizkosten') === false, 'Mietkosten erscheinen nicht im Kaufexposé');
check(strpos($html, 'Grundstücksfläche') !== false && strpos($html, '1.234,00 m²') !== false && strpos($html, 'Wohneinheiten') !== false, 'Eingetragene Grundstücksfläche und Wohneinheiten erscheinen');
check(strpos($html, 'Nur im privaten Exposé') !== false && strpos($html, 'Übersicht Wohneinheiten') !== false, 'Private Exposé-Angaben und Einheitenübersicht erscheinen');
check(substr_count($html, 'Heller Wohnbereich.') === 1, 'Identische Beschreibungsabsätze werden nicht wiederholt');
check(strpos($html, 'gesetzliche Haftungsansprüche bleiben unberührt') !== false && strpos($html, '<h2>Ihr Kontakt</h2>') !== false, 'Kontakt und Hinweis zu den Angaben erscheinen');
$rental = $property;
$rental['offer_type'] = 'Miete';
check(strpos(expose_pdf_html($rental, $recipient), 'Warmmiete') !== false, 'Mietkosten bleiben bei Mietobjekten sichtbar');

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
    $pictured['images'] = json_encode([['url' => $url, 'label' => 'Dachstudio', 'virtual_staging' => true]]);
    $extra = json_decode($pictured['extra_data'], true);
    $extra['floorplans'] = [['url' => $url, 'label' => 'Dachgeschoss W4']];
    $pictured['extra_data'] = json_encode($extra);
    $pictureHtml = expose_pdf_html($pictured, $recipient);
    check(strpos($pictureHtml, 'Visualisierung / Einrichtungsbeispiel · Dachstudio') !== false, 'Gekennzeichnete Visualisierungen sind im Exposé erkennbar');
    check(strpos($pictureHtml, '<h3>Dachgeschoss W4</h3>') !== false, 'Grundrisse tragen ihre eingetragene Beschriftung');
} finally {
    unlink($imageFile);
}
$pdf = expose_pdf_render($property, $recipient);
check(strncmp($pdf, '%PDF-', 5) === 0 && strlen($pdf) > 1000, 'Ein echtes PDF wird erzeugt');
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
$id = expose_request_store($pdo, $fields, $pdf);
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
