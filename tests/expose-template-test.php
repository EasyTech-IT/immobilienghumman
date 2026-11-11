<?php
define('BASE', '');
define('SITE_LOGO', '/logo.png');
define('SITE_EMAIL', 'info@example.test');

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
        'hide_exact_address' => true,
        'address_public_note' => 'Bad Vilbel',
        'commission_required' => true,
        'commission_amount' => 2.975,
        'commission_unit' => 'percent',
        'documents' => [
            ['url' => '/grundriss.pdf', 'name' => 'Grundriss'],
            ['url' => 'javascript:alert(1)', 'name' => 'Unsicher'],
        ],
    ]),
];

ob_start();
require __DIR__ . '/../includes/expose-template.php';
$html = ob_get_clean();
$checks = [
    'Titel wird maskiert' => strpos($html, 'Wohnung &lt;Test&gt;') !== false,
    'Verborgene Adresse bleibt verborgen' => strpos($html, 'Geheime Straße') === false,
    'Mietpreis wird formatiert' => strpos($html, '1.250,00 €') !== false && strpos($html, 'Kaltmiete') !== false,
    'Provision und Ausstattung erscheinen' => strpos($html, '2,975 %') !== false && strpos($html, 'Balkon') !== false,
    'Bilder und Dokumente sind verknüpft' => strpos($html, 'Wohnzimmer') !== false && strpos($html, '/grundriss.pdf') !== false,
    'Unsichere URL wird verworfen' => strpos($html, 'javascript:') === false,
];
foreach ($checks as $name => $passed) {
    echo ($passed ? 'OK: ' : 'FEHLER: ') . $name . PHP_EOL;
}
exit(in_array(false, $checks, true) ? 1 : 0);
