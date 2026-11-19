<?php

require_once __DIR__ . '/../includes/property-validation.php';
require_once __DIR__ . '/../includes/property-data.php';

function assert_property_check(bool $condition, string $message): void
{
    if (!$condition) throw new RuntimeException($message);
    echo "OK: $message\n";
}

$rental = [
    'title' => 'Helle Wohnung',
    'short_description' => 'Ruhige Lage mit Balkon',
    'description' => 'Gepflegte Wohnung mit Balkon.',
    'type' => 'Wohnung',
    'offer_type' => 'Miete',
    'price' => 1200,
    'extra_costs' => 200,
    'deposit' => 3600,
    'heating_costs_included' => true,
    'area' => 90,
    'rooms' => 3,
    'bathrooms' => 1,
    'location' => 'Bad Vilbel',
    'zip' => '61118',
    'address' => 'Musterstraße 1',
    'available_from' => 'Sofort',
    'location_description' => 'Gute Anbindung an den ÖPNV.',
    'condition' => 'Gepflegt',
    'energy_certificate_status' => 'not_required',
];

assert_property_check(property_listing_validation_errors($rental, [['url' => '/bild.jpg']]) === [], 'Vollständiges Mietangebot wird zur Veröffentlichung akzeptiert');
$withoutAreaBasis = $rental;
assert_property_check(property_listing_validation_errors($withoutAreaBasis, array_fill(0, 26, ['url' => '/bild.jpg'])) === [], 'Flächenmethode bleibt optional und mehr als 25 Fotos sind zulässig');
$missingCertificate = $rental;
$missingCertificate['energy_certificate_status'] = 'missing';
assert_property_check(count(property_listing_validation_errors($missingCertificate, [['url' => '/bild.jpg']])) > 0, 'Fehlender Energieausweis blockiert die Veröffentlichung');
$orderedCertificate = $rental;
$orderedCertificate['energy_certificate_status'] = 'ordered';
assert_property_check(count(property_listing_validation_errors($orderedCertificate, [['url' => '/bild.jpg']])) > 0, 'Beantragter Energieausweis blockiert die Veröffentlichung bis zur Vorlage');
$withoutCosts = $rental;
unset($withoutCosts['extra_costs']);
assert_property_check(in_array('Nebenkosten muss angegeben werden (0 ist möglich).', property_listing_validation_errors($withoutCosts, [['url' => '/bild.jpg']]), true), 'Mietangebot verlangt ausdrücklich Nebenkosten');
$withoutEnergy = $rental;
unset($withoutEnergy['energy_certificate_status']);
assert_property_check(count(property_listing_validation_errors($withoutEnergy, [['url' => '/bild.jpg']])) > 0, 'Energieausweisstatus ist verpflichtend');
assert_property_check(count(property_listing_validation_errors($rental, [])) > 0, 'Mindestens ein Objektfoto wird verlangt');

$commercial = $rental;
$commercial['type'] = 'Gewerbe';
$commercial['energy_certificate_status'] = 'available';
$commercial['energy_certificate_type'] = 'verbrauchsausweis';
$commercial['energy_value_kind'] = 'verbrauch';
$commercial['energy_value'] = 0;
$commercial['energy_value_electricity'] = 0;
$commercial['energy_source'] = 'Gas';
$futureExpiry = (new DateTimeImmutable('+1 year', new DateTimeZone('Europe/Berlin')))->format('Y-m-d');
$commercial['energy_valid_until'] = $futureExpiry;
assert_property_check(property_listing_validation_errors($commercial, [['url' => '/bild.jpg']]) === [], 'Nichtwohngebäude akzeptiert getrennte Wärme- und Stromkennwerte einschließlich 0');
$expiredCertificate = $commercial;
$expiredCertificate['energy_valid_until'] = '2020-01-01';
assert_property_check(in_array('Der Energieausweis ist abgelaufen. Vor der Veröffentlichung einen gültigen Ausweis hinterlegen.', property_listing_validation_errors($expiredCertificate, [['url' => '/bild.jpg']]), true), 'Abgelaufener Energieausweis blockiert die Veröffentlichung');
unset($commercial['energy_value_electricity']);
assert_property_check(count(property_listing_validation_errors($commercial, [['url' => '/bild.jpg']])) > 0, 'Nichtwohngebäude benötigt den separaten Stromkennwert');

$residentialEnergy = $rental;
$residentialEnergy['energy_certificate_status'] = 'available';
$residentialEnergy['energy_certificate_type'] = 'verbrauchsausweis';
$residentialEnergy['energy_value_kind'] = 'verbrauch';
$residentialEnergy['energy_value'] = 85;
$residentialEnergy['energy_source'] = 'Gas';
$residentialEnergy['energy_efficiency_class'] = 'C';
$residentialEnergy['energy_building_year'] = 1980;
$residentialEnergy['energy_valid_until'] = $futureExpiry;
assert_property_check(property_listing_validation_errors($residentialEnergy, [['url' => '/bild.jpg']]) === [], 'Vollständige Energieangaben für ein Wohngebäude werden akzeptiert');
$oldCertificateWithoutClass = $residentialEnergy;
unset($oldCertificateWithoutClass['energy_efficiency_class']);
$oldCertificateWithoutClass['energy_efficiency_class_not_in_certificate'] = true;
$oldCertificateWithoutClass['energy_issue_date'] = '2014-04-30';
assert_property_check(property_listing_validation_errors($oldCertificateWithoutClass, [['url' => '/bild.jpg']]) === [], 'Fehlende Effizienzklasse ist bei geprüftem Alt-Ausweis zulässig');
$newCertificateWithoutClass = $oldCertificateWithoutClass;
$newCertificateWithoutClass['energy_issue_date'] = '2014-05-01';
assert_property_check(count(property_listing_validation_errors($newCertificateWithoutClass, [['url' => '/bild.jpg']])) > 0, 'Ausnahme für fehlende Effizienzklasse gilt nicht für spätere Ausweise');

$purchase = $rental;
$purchase['offer_type'] = 'Kauf';
$purchase['house_fee'] = 0;
unset($purchase['extra_costs'], $purchase['deposit'], $purchase['heating_costs_included']);
assert_property_check(property_listing_validation_errors($purchase, [['url' => '/bild.jpg']]) === [], 'Kaufangebot verlangt keine Mietkosten');

$public = property_public_extra_data([
    'floorplans' => [['url' => '/uploads/plan.pdf']],
    'documents' => [['url' => '/uploads/legacy.pdf']],
    'area_calculation_basis' => 'WoFlV',
    'energy_value_electricity' => 32,
    'energy_issue_date' => '2013-04-01',
    'energy_efficiency_class_not_in_certificate' => true,
    'private_expose' => ['burdens_notes' => 'Nur intern'],
    'legacy_slugs' => ['alte-adresse'],
]);
assert_property_check(isset($public['floorplans']) && !isset($public['documents']) && !isset($public['private_expose']), 'Private Dokumente und Exposé-Angaben fehlen in öffentlichen API-Daten');
assert_property_check(($public['area_calculation_basis'] ?? '') === 'WoFlV' && ($public['energy_value_electricity'] ?? null) === 32, 'Flächenmethode und Stromkennwert bleiben für öffentliche Details verfügbar');
assert_property_check(!isset($public['energy_issue_date']) && !isset($public['energy_efficiency_class_not_in_certificate']), 'Interne Alt-Ausweis-Prüffelder werden nicht öffentlich ausgegeben');
