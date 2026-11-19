<?php
define('BASE', '');
define('SITE_LOGO', '/logo.png');
define('SITE_EMAIL', 'info@example.test');
define('SITE_URL', 'https://example.test');
define('AUTH_SECRET', 'test-secret-with-more-than-thirty-two-characters');
define('AUTH_SECRET_CONFIGURED', true);

$property = [
    'id' => 42,
    'slug' => 'testwohnung',
    'title' => 'Wohnung <Test>',
    'type' => 'Wohnung',
    'offer_type' => 'Miete',
    'price' => 1250,
    'area' => 90.5,
    'address' => 'Geheime Straße 1',
    'location' => 'Bad Vilbel',
    'description' => 'Eine Wohnung.',
    'features' => '["Balkon"]',
    'images' => '[{"url":"/bild.jpg","label":"Wohnzimmer"}]',
    'extra_data' => json_encode([
        'hide_exact_address' => false,
        'address_public_note' => 'Geheime Adresse',
        'commission_required' => true,
        'commission_amount' => 2.975,
        'commission_unit' => 'percent',
        'floorplans' => [
            ['url' => '/grundriss.pdf', 'name' => 'Grundriss'],
            ['url' => 'javascript:alert(1)', 'name' => 'Unsicher'],
        ],
        'private_expose' => [
            'documents' => [['url' => 'private:0123456789abcdef0123456789abcdef', 'name' => 'Mieterliste']],
            'current_annual_rent' => 18000,
        ],
    ]),
];

$_GET['print'] = '1';
ob_start();
require __DIR__ . '/../includes/expose-template.php';
$html = ob_get_clean();
$checks = [
    'Titel wird maskiert' => strpos($html, 'Wohnung &lt;Test&gt;') !== false,
    'Genaue Adresse bleibt unabhängig von der Alt-Option verborgen' => strpos($html, 'Geheime Straße') === false && strpos($html, 'Geheime Adresse') === false,
    'Mietpreis wird formatiert' => strpos($html, '1.250,00 €') !== false && strpos($html, 'Kaltmiete') !== false,
    'Provision und Ausstattung erscheinen' => strpos($html, '2,975 %') !== false && strpos($html, 'Balkon') !== false,
    'Objektnummer wird nicht öffentlich angezeigt' => strpos($html, 'Objekt 0042') === false,
    'Bilder, öffentliche Grundrisse und private Anlagen sind verknüpft' => strpos($html, 'Wohnzimmer') !== false && strpos($html, '/grundriss.pdf') !== false && strpos($html, 'private-expose-document.php') !== false,
    'Druckansicht verwendet dieselbe Exposé-Vorlage' => strpos($html, 'window.print()') !== false,
    'Unsichere URL wird verworfen' => strpos($html, 'javascript:') === false,
];
foreach ($checks as $name => $passed) {
    echo ($passed ? 'OK: ' : 'FEHLER: ') . $name . PHP_EOL;
}
exit(in_array(false, $checks, true) ? 1 : 0);
