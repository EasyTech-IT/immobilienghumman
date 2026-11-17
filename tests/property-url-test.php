<?php
require_once __DIR__ . '/../includes/property-url.php';

if (property_legacy_number_id('0013') !== 13 || property_legacy_number_id('1123') !== 13
    || property_legacy_number_id('0000') !== null || property_legacy_number_id('13x') !== null
    || property_legacy_number_id('99999999999999999999999999') !== null) {
    throw new RuntimeException('Alte Objektnummern werden nicht korrekt aufgelöst.');
}

$pdo = new PDO('sqlite::memory:');
$pdo->exec('CREATE TABLE properties (id INTEGER PRIMARY KEY, slug TEXT UNIQUE, title TEXT, extra_data TEXT)');
$insert = $pdo->prepare('INSERT INTO properties (id, slug, title, extra_data) VALUES (?, ?, ?, ?)');
$insert->execute([1, 'alte-wohnungs-url', 'Helle Wohnung in Bad Vilbel', '{}']);
$insert->execute([2, 'andere-alte-url', 'Helle Wohnung in Bad Vilbel', '{}']);

property_migrate_slugs($pdo);
$rows = $pdo->query('SELECT id, slug, extra_data FROM properties ORDER BY id')->fetchAll(PDO::FETCH_ASSOC);
$firstExtra = json_decode($rows[0]['extra_data'], true);
$secondExtra = json_decode($rows[1]['extra_data'], true);
property_migrate_slugs($pdo);
$repeatedRows = $pdo->query('SELECT id, slug, extra_data FROM properties ORDER BY id')->fetchAll(PDO::FETCH_ASSOC);
$invalid = new PDO('sqlite::memory:');
$invalid->exec('CREATE TABLE properties (id INTEGER PRIMARY KEY, slug TEXT UNIQUE, title TEXT, extra_data TEXT)');
$invalid->exec("INSERT INTO properties VALUES (1, 'alt', 'Neuer Titel', '{}'), (2, 'alt-zwei', 'Noch ein Titel', 'null')");
$rolledBack = false;
try {
    property_migrate_slugs($invalid);
} catch (RuntimeException $error) {
    $rolledBack = $invalid->query('SELECT slug FROM properties WHERE id = 1')->fetchColumn() === 'alt';
}

$checks = [
    'deutsche Umlaute werden lesbar transliteriert' => property_slugify('München') === 'muenchen',
    'Titel wird als Slug verwendet' => $rows[0]['slug'] === 'helle-wohnung-in-bad-vilbel',
    'gleiche Titel erhalten einen Buchstaben statt einer Nummer' => $rows[1]['slug'] === 'helle-wohnung-in-bad-vilbel-a',
    'alte Slugs bleiben als Alias erhalten' => in_array('alte-wohnungs-url', $firstExtra['legacy_slugs'], true)
        && in_array('andere-alte-url', $secondExtra['legacy_slugs'], true),
    'alte Slugs anderer Objekte bleiben reserviert' => property_unique_slug($pdo, 'Alte Wohnungs URL') === 'alte-wohnungs-url-a',
    'unveränderter Titel behält seinen Slug' => property_unique_slug($pdo, 'Helle Wohnung in Bad Vilbel', 1) === 'helle-wohnung-in-bad-vilbel',
    'erneute Migration verändert nichts' => $rows === $repeatedRows,
    'lange Titel bleiben innerhalb der Datenbankgrenze' => strlen(property_unique_slug($pdo, str_repeat('a', 300))) === 255,
    'ungültige Zusatzdaten rollen die gesamte Migration zurück' => $rolledBack,
];

foreach ($checks as $name => $passed) {
    echo ($passed ? 'OK: ' : 'FEHLER: ') . $name . PHP_EOL;
}
exit(in_array(false, $checks, true) ? 1 : 0);
