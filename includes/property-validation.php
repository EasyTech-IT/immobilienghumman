<?php

function property_listing_validation_errors(array $data, array $images): array
{
    $errors = [];
    $type = trim((string)($data['type'] ?? ''));
    $offerType = trim((string)($data['offer_type'] ?? ''));
    $isRent = $offerType === 'Miete';
    $isFlat = $type === 'Wohnung';
    $isHouse = in_array($type, ['Haus', 'Einfamilienhaus', 'Mehrfamilienhaus'], true);
    $isPlot = $type === 'Grundstück';
    $isCommercial = $type === 'Gewerbe';

    foreach ([
        'title' => 'Titel',
        'short_description' => 'Kurzbeschreibung',
        'description' => 'Objektbeschreibung',
        'location' => 'Ort',
        'zip' => 'PLZ',
        'address' => 'Straße und Hausnummer',
        'available_from' => 'Verfügbarkeit',
        'location_description' => 'Lagebeschreibung',
        'condition' => 'Objektzustand',
        'energy_certificate_status' => 'Status Energieausweis',
    ] as $field => $label) {
        if (trim((string)($data[$field] ?? '')) === '') {
            $errors[] = $label . ' fehlt.';
        }
    }

    if (!in_array($type, ['Wohnung', 'Haus', 'Einfamilienhaus', 'Mehrfamilienhaus', 'Gewerbe', 'Grundstück'], true)) {
        $errors[] = 'Bitte eine gültige Objektart auswählen.';
    }
    if (!in_array($offerType, ['Kauf', 'Miete'], true)) {
        $errors[] = 'Bitte Kauf oder Miete auswählen.';
    }
    if (!in_array($data['energy_certificate_status'] ?? '', ['', 'available', 'ordered', 'not_required', 'missing'], true)) {
        $errors[] = 'Bitte einen gültigen Energieausweisstatus auswählen.';
    }
    if (in_array($data['energy_certificate_status'] ?? '', ['ordered', 'missing'], true)) {
        $errors[] = 'Ein Energieausweis fehlt oder ist noch beantragt. Veröffentlichung erst nach Vorlage oder nach Prüfung einer gesetzlichen Ausnahme.';
    }
    if (mb_strlen(trim((string)($data['short_description'] ?? '')), 'UTF-8') > 160) {
        $errors[] = 'Die Kurzbeschreibung darf höchstens 160 Zeichen haben.';
    }
    if (!preg_match('/^\d{5}$/', trim((string)($data['zip'] ?? '')))) {
        $errors[] = 'Die PLZ muss aus fünf Ziffern bestehen.';
    }
    if (!is_numeric($data['price'] ?? null) || (float)$data['price'] <= 0) {
        $errors[] = $isRent ? 'Die Kaltmiete muss größer als 0 sein.' : 'Der Kaufpreis muss größer als 0 sein.';
    }

    if (!$isPlot) {
        if (!is_numeric($data['area'] ?? null) || (float)$data['area'] <= 0) {
            $errors[] = 'Die Wohnfläche muss größer als 0 sein.';
        }
    }
    if (!$isPlot && !$isCommercial) {
        if (!is_numeric($data['rooms'] ?? null) || (float)$data['rooms'] <= 0) {
            $errors[] = 'Die Zimmerzahl muss größer als 0 sein.';
        }
    }
    if (($isFlat || $isHouse) && (!is_numeric($data['bathrooms'] ?? null) || (int)$data['bathrooms'] < 1)) {
        $errors[] = 'Bitte mindestens ein Badezimmer angeben.';
    }
    if (($isHouse || $isPlot) && (!is_numeric($data['plot_area'] ?? null) || (float)$data['plot_area'] <= 0)) {
        $errors[] = 'Für Haus und Grundstück ist eine Grundstücksfläche erforderlich.';
    }
    if ($type === 'Mehrfamilienhaus' && (!is_numeric($data['residential_units'] ?? null) || (int)$data['residential_units'] < 1)) {
        $errors[] = 'Für ein Mehrfamilienhaus ist die Anzahl der Wohneinheiten erforderlich.';
    }
    if ($isRent) {
        foreach (['extra_costs' => 'Nebenkosten', 'deposit' => 'Kaution'] as $field => $label) {
            if (!isset($data[$field]) || $data[$field] === '' || !is_numeric($data[$field]) || (float)$data[$field] < 0) {
                $errors[] = $label . ' muss angegeben werden (0 ist möglich).';
            }
        }
        if (empty($data['heating_costs_included'])
            && (!isset($data['heating_costs']) || $data['heating_costs'] === '' || !is_numeric($data['heating_costs']) || (float)$data['heating_costs'] < 0)) {
            $errors[] = 'Heizkosten angeben oder bestätigen, dass sie in den Nebenkosten enthalten sind.';
        }
    }
    if (!$isRent && $isFlat && (!isset($data['house_fee']) || $data['house_fee'] === '' || !is_numeric($data['house_fee']) || (float)$data['house_fee'] < 0)) {
        $errors[] = 'Für eine Eigentumswohnung bitte das monatliche Hausgeld angeben (0 ist möglich).';
    }
    if (!$isRent && !empty($data['commission_required'])) {
        foreach ([
            'commission_amount' => 'Courtage-Höhe',
            'commission_payer' => 'zahlende Partei',
            'commission_note' => 'Provisionshinweis',
        ] as $field => $label) {
            if (trim((string)($data[$field] ?? '')) === '') {
                $errors[] = 'Bei provisionspflichtigem Verkauf fehlt: ' . $label . '.';
            }
        }
        if (!is_numeric($data['commission_amount'] ?? null) || (float)$data['commission_amount'] <= 0) {
            $errors[] = 'Die Courtage-Höhe muss größer als 0 sein.';
        }
    }
    if (($data['energy_certificate_status'] ?? '') === 'available') {
        foreach ([
            'energy_certificate_type' => 'Energieausweistyp',
            'energy_value_kind' => 'Art des Energiekennwerts',
            'energy_value' => 'Energiekennwert',
            'energy_source' => 'wesentlicher Energieträger',
        ] as $field => $label) {
            if (trim((string)($data[$field] ?? '')) === '') {
                $errors[] = 'Beim vorhandenen Energieausweis fehlt: ' . $label . '.';
            }
        }
        if (!is_numeric($data['energy_value'] ?? null) || (float)$data['energy_value'] < 0) {
            $errors[] = 'Der Energiekennwert muss eine Zahl ab 0 sein.';
        }
        if (!in_array($data['energy_certificate_type'] ?? '', ['bedarfsausweis', 'verbrauchsausweis'], true)
            || !in_array($data['energy_value_kind'] ?? '', ['bedarf', 'verbrauch'], true)) {
            $errors[] = 'Energieausweistyp und Kennwertart müssen gültig ausgewählt werden.';
        }
        if (!in_array($data['energy_source'] ?? '', ['Gas', 'Öl', 'Fernwärme', 'Wärmepumpe', 'Strom', 'Pellets', 'Sonstige'], true)) {
            $errors[] = 'Bitte einen gültigen wesentlichen Energieträger auswählen.';
        }
        if ($isCommercial) {
            if (!is_numeric($data['energy_value_electricity'] ?? null) || (float)$data['energy_value_electricity'] < 0) {
                $errors[] = 'Beim Nichtwohngebäude ist auch der Energiekennwert für Strom erforderlich.';
            }
        } else {
            foreach ([
                'energy_efficiency_class' => 'Energieeffizienzklasse',
                'energy_building_year' => 'Baujahr laut Energieausweis',
            ] as $field => $label) {
                if (trim((string)($data[$field] ?? '')) === '') {
                    $errors[] = 'Beim vorhandenen Energieausweis fehlt: ' . $label . '.';
                }
            }
            if (!in_array($data['energy_efficiency_class'] ?? '', ['A+', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H'], true)) {
                $errors[] = 'Bitte eine gültige Energieeffizienzklasse auswählen.';
            }
        }
    }
    if (count($images) < 1) {
        $errors[] = 'Für die Veröffentlichung ist mindestens ein Objektfoto erforderlich.';
    }

    return array_values(array_unique($errors));
}
