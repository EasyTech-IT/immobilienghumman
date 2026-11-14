<?php

function analyse_flows(): array
{
    return [
        'analyse_immobilie' => [
            'title' => 'Immobilienanalyse',
            'description' => 'Wert und Möglichkeiten Ihrer Immobilie',
            'icon' => 'fa-house',
            'questions' => [
                ['name' => 'objektart', 'label' => 'Um welche Immobilie geht es?', 'options' => ['Wohnung', 'Einfamilienhaus', 'Mehrfamilienhaus', 'Doppelhaushälfte', 'Reihenhaus', 'Grundstück', 'Gewerbe', 'Sonstiges']],
                ['name' => 'ziel', 'label' => 'Was möchten Sie mit der Immobilie tun?', 'options' => ['Verkaufen', 'Kaufen', 'Vermieten', 'Wert erfahren']],
                ['name' => 'zeitrahmen', 'label' => 'Wann möchten Sie starten?', 'options' => ['Möglichst bald', 'In 3 Monaten', 'In 6–12 Monaten', 'Noch offen']],
                ['name' => 'standort', 'label' => 'Wo liegt die Immobilie?', 'options' => ['Bad Vilbel', 'Frankfurt am Main', 'Bad Homburg', 'Offenbach', 'Wiesbaden oder Mainz', 'Anderer Ort', 'Noch offen']],
                ['name' => 'flaeche', 'label' => 'Wie groß ist die Fläche ungefähr?', 'options' => ['Unter 50 m²', '50–100 m²', '100–150 m²', '150–250 m²', 'Über 250 m²', 'Nicht bekannt']],
                ['name' => 'zimmer', 'label' => 'Wie viele Zimmer gibt es?', 'options' => ['1', '2', '3', '4', '5', '6 oder mehr', 'Nicht bekannt', 'Nicht zutreffend']],
                ['name' => 'baujahr', 'label' => 'Wann wurde das Gebäude gebaut?', 'options' => ['Vor 1950', '1950–1969', '1970–1989', '1990–2009', 'Ab 2010', 'Nicht bekannt', 'Nicht zutreffend']],
                ['name' => 'zustand', 'label' => 'Wie ist der Zustand?', 'options' => ['Neubau', 'Sehr gut', 'Gepflegt', 'Renovierungsbedarf', 'Sanierungsbedarf', 'Nicht bekannt', 'Nicht zutreffend']],
                ['name' => 'nutzung', 'label' => 'Wie wird die Immobilie genutzt?', 'options' => ['Selbst genutzt', 'Vermietet', 'Leerstehend', 'Anders', 'Nicht bekannt']],
                ['name' => 'ausstattung', 'label' => 'Welche Ausstattung ist Ihnen besonders wichtig?', 'multiple' => true, 'exclusive' => 'Keine Angabe', 'options' => ['Balkon oder Terrasse', 'Garten', 'Garage oder Stellplatz', 'Aufzug', 'Keller', 'Keine Angabe']],
                ['name' => 'heizung', 'label' => 'Welche Heizung ist vorhanden?', 'options' => ['Gas', 'Öl', 'Wärmepumpe', 'Fernwärme', 'Andere', 'Nicht bekannt', 'Nicht zutreffend']],
                ['name' => 'rolle', 'label' => 'Welche Verbindung haben Sie zur Immobilie?', 'options' => ['Eigentümer/in', 'Kaufinteressent/in', 'Mieter/in', 'Andere']],
            ],
        ],
        'analyse_finanzierung' => [
            'title' => 'Baufinanzierungsanalyse',
            'description' => 'Immobilienfinanzierung und Budget besprechen',
            'icon' => 'fa-chart-line',
            'questions' => [
                ['name' => 'vorhaben', 'label' => 'Was möchten Sie mit Ihrer Baufinanzierung vorhaben?', 'options' => ['Haus oder Wohnung kaufen', 'Haus bauen', 'Modernisierung', 'Anschlussfinanzierung', 'Noch offen']],
                ['name' => 'objektart', 'label' => 'Welche Art von Immobilie betrifft Ihr Vorhaben?', 'options' => ['Wohnung', 'Einfamilienhaus', 'Doppelhaushälfte', 'Reihenhaus', 'Mehrfamilienhaus', 'Grundstück', 'Noch offen']],
                ['name' => 'standort', 'label' => 'Wo liegt die Immobilie?', 'options' => ['Bad Vilbel', 'Frankfurt am Main', 'Bad Homburg', 'Offenbach', 'Wiesbaden oder Mainz', 'Anderer Ort', 'Noch offen']],
                ['name' => 'kaufpreis', 'label' => 'Wie hoch sind Kaufpreis, Baukosten oder Restschuld ungefähr?', 'hint' => 'Bei einer Anschlussfinanzierung wählen Sie die ungefähre Restschuld.', 'options' => ['Unter 100.000 €', '100.000–250.000 €', '250.000–500.000 €', '500.000–1 Mio. €', 'Über 1 Mio. €', 'Noch offen']],
                ['name' => 'eigenkapital', 'label' => 'Wie viel Eigenkapital möchten Sie ungefähr einsetzen?', 'options' => ['Kein Eigenkapital', 'Unter 50.000 €', '50.000–100.000 €', '100.000–250.000 €', '250.000–500.000 €', 'Über 500.000 €', 'Noch offen']],
                ['name' => 'monatsrate', 'label' => 'Welche monatliche Rate wünschen Sie sich?', 'options' => ['Unter 500 €', '500–1.000 €', '1.000–1.500 €', '1.500–2.000 €', 'Über 2.000 €', 'Noch offen']],
                ['name' => 'zinsbindung', 'label' => 'Wie lange möchten Sie den Zinssatz festschreiben?', 'hint' => 'Die Zinsbindung ist nicht dasselbe wie die gesamte Darlehenslaufzeit.', 'options' => ['5 Jahre', '10 Jahre', '15 Jahre', '20 Jahre', 'Länger als 20 Jahre', 'Noch offen']],
                ['name' => 'zeitrahmen', 'label' => 'Wann benötigen Sie die Finanzierung?', 'options' => ['Möglichst bald', 'In 3 Monaten', 'In 6–12 Monaten', 'Später', 'Noch offen']],
                ['name' => 'objektstatus', 'label' => 'Wie weit sind Sie mit Ihrem Vorhaben?', 'options' => ['Immobilie gefunden', 'Ich suche noch', 'Bau in Planung', 'Bereits im Eigentum', 'Noch offen']],
                ['name' => 'beschaeftigung', 'label' => 'Wie ist Ihre berufliche Situation?', 'options' => ['Angestellt', 'Selbstständig', 'Verbeamtet', 'Im Ruhestand', 'Andere', 'Keine Angabe']],
                ['name' => 'einkommen', 'label' => 'Wie hoch ist Ihr monatliches Haushaltsnetto?', 'options' => ['Unter 2.000 €', '2.000–3.000 €', '3.000–4.000 €', '4.000–5.000 €', '5.000–7.000 €', 'Über 7.000 €', 'Keine Angabe']],
                ['name' => 'nutzung', 'label' => 'Wie möchten Sie die Immobilie nutzen?', 'options' => ['Selbst nutzen', 'Vermieten', 'Teilweise selbst nutzen', 'Noch offen']],
            ],
        ],
        'analyse_hausverwaltung' => [
            'title' => 'Hausverwaltung',
            'description' => 'Ein passendes Angebot für Ihr Objekt',
            'icon' => 'fa-building',
            'questions' => [
                ['name' => 'objektart', 'label' => 'Welche Art von Objekt verwalten Sie?', 'options' => ['Eigentümergemeinschaft', 'Mietwohnungen', 'Gewerbe', 'Wohnen und Gewerbe', 'Sonstiges']],
                ['name' => 'einheiten', 'label' => 'Wie viele Einheiten gibt es?', 'options' => ['1–3', '4–10', '11–20', '21–50', 'Über 50']],
                ['name' => 'standort', 'label' => 'Wo liegt das Objekt?', 'options' => ['Bad Vilbel', 'Frankfurt am Main', 'Bad Homburg', 'Offenbach', 'Wiesbaden oder Mainz', 'Anderer Ort', 'Noch offen']],
                ['name' => 'verwaltung', 'label' => 'Wie wird das Objekt bisher verwaltet?', 'options' => ['Selbst verwaltet', 'Externe Verwaltung', 'Noch keine Verwaltung']],
                ['name' => 'bedarf', 'label' => 'Was ist Ihnen besonders wichtig?', 'multiple' => true, 'exclusive' => 'Noch offen', 'options' => ['Vollständige Verwaltung', 'Abrechnungen', 'Instandhaltung', 'Eigentümerbetreuung', 'Beratung', 'Noch offen']],
                ['name' => 'beginn', 'label' => 'Wann soll die Verwaltung beginnen?', 'options' => ['Möglichst bald', 'In 3 Monaten', 'In 6–12 Monaten', 'Noch offen']],
                ['name' => 'gebaeude', 'label' => 'Aus welcher Zeit stammt das Gebäude?', 'options' => ['Vor 1950', '1950–1969', '1970–1989', '1990–2009', 'Ab 2010', 'Nicht bekannt']],
                ['name' => 'anzahl_gebaeude', 'label' => 'Wie viele Gebäude gehören dazu?', 'options' => ['1', '2', '3–5', 'Mehr als 5', 'Nicht bekannt']],
                ['name' => 'zustand', 'label' => 'Wie ist der Zustand des Objekts?', 'options' => ['Neubau', 'Gepflegt', 'Renovierungsbedarf', 'Sanierungsbedarf', 'Nicht bekannt']],
                ['name' => 'rolle', 'label' => 'In welcher Rolle fragen Sie an?', 'options' => ['Eigentümer/in', 'Beirat', 'Hausverwaltung', 'Sonstige']],
                ['name' => 'kontaktwunsch', 'label' => 'Wie möchten Sie kontaktiert werden?', 'options' => ['Per E-Mail', 'Telefonisch', 'Beides']],
                ['name' => 'vertragsstatus', 'label' => 'Wann kann eine neue Verwaltung übernehmen?', 'options' => ['Sofort', 'Nach Vertragsende', 'Nach Vereinbarung', 'Noch nicht bekannt']],
            ],
        ],
    ];
}

function analyse_validate_answers(string $type, array $data): array
{
    $flows = analyse_flows();
    if (!isset($flows[$type])) return ['Analyseart ist ungültig.'];
    $errors = [];
    foreach ($flows[$type]['questions'] as $question) {
        $name = $question['name'];
        $raw = $data['analyse_' . $name] ?? null;
        if (!empty($question['multiple'])) {
            if (!is_array($raw) || !$raw || count($raw) > count($question['options'])) {
                $errors[] = $question['label'];
                continue;
            }
            $values = [];
            foreach ($raw as $value) {
                if (!is_string($value) || !in_array($value, $question['options'], true) || in_array($value, $values, true)) {
                    $errors[] = $question['label'];
                    continue 2;
                }
                $values[] = $value;
            }
            if (count($values) > 1 && in_array($question['exclusive'], $values, true)) {
                $errors[] = $question['label'];
            }
            continue;
        }
        if (!is_string($raw) || trim($raw) === '') {
            $errors[] = $question['label'];
            continue;
        }
        $value = trim($raw);
        if (strlen($value) > 800) {
            $errors[] = $question['label'];
        } elseif (!in_array($value, $question['options'], true)) {
            $errors[] = $question['label'];
        }
    }
    if ($type === 'analyse_finanzierung') {
        $budget = analyse_amount_bound('kaufpreis', $data['analyse_kaufpreis'] ?? '');
        $equity = analyse_amount_bound('eigenkapital', $data['analyse_eigenkapital'] ?? '');
        if ($budget !== null && $equity !== null && $equity > $budget) {
            $errors[] = 'Das Eigenkapital darf das Budget nicht übersteigen.';
        }
    }
    if ($type === 'analyse_hausverwaltung' && in_array($data['analyse_kontaktwunsch'] ?? '', ['Telefonisch', 'Beides'], true)
        && (!isset($data['phone']) || !is_string($data['phone']) || trim($data['phone']) === '')) {
        $errors[] = 'Bitte geben Sie Ihre Telefonnummer an.';
    }
    return $errors;
}

function analyse_amount_bound(string $name, $value): ?int
{
    $bounds = [
        'kaufpreis' => [
            'Unter 100.000 €' => 99999, '100.000–250.000 €' => 250000,
            '250.000–500.000 €' => 500000, '500.000–1 Mio. €' => 1000000,
        ],
        'eigenkapital' => [
            'Kein Eigenkapital' => 0, 'Unter 50.000 €' => 0,
            '50.000–100.000 €' => 50000, '100.000–250.000 €' => 100000,
            '250.000–500.000 €' => 250000, 'Über 500.000 €' => 500001,
        ],
    ];
    return is_string($value) ? ($bounds[$name][$value] ?? null) : null;
}

function analyse_option_icon(string $name, string $option): string
{
    $icons = [
        'Wohnung' => 'building', 'Einfamilienhaus' => 'house', 'Mehrfamilienhaus' => 'city',
        'Doppelhaushälfte' => 'dhh', 'Reihenhaus' => 'reihen',
        'Grundstück' => 'map', 'Gewerbe' => 'store', 'Verkaufen' => 'handshake',
        'Kaufen' => 'key', 'Vermieten' => 'key', 'Wert erfahren' => 'chart-line',
        'Möglichst bald' => 'bolt', 'Noch offen' => 'circle-question', 'Nicht bekannt' => 'circle-question',
        'Nicht zutreffend' => 'minus', 'Keine Angabe' => 'minus', 'Ja' => 'check',
        'Selbst genutzt' => 'house-user', 'Leerstehend' => 'door-open',
        'Balkon oder Terrasse' => 'sun', 'Garten' => 'seedling', 'Garage oder Stellplatz' => 'car',
        'Aufzug' => 'elevator', 'Keller' => 'box', 'Gas' => 'fire-flame-simple',
        'Öl' => 'droplet', 'Wärmepumpe' => 'leaf', 'Fernwärme' => 'temperature-half',
        'Neubau' => 'house', 'Gepflegt' => 'sparkles', 'Sanierungsbedarf' => 'screwdriver-wrench',
        'Renovierungsbedarf' => 'paint-roller', 'Haus oder Wohnung kaufen' => 'key', 'Haus bauen' => 'house',
        'Modernisierung' => 'screwdriver-wrench', 'Anschlussfinanzierung' => 'arrows-rotate',
        'Eigentümergemeinschaft' => 'people-group', 'Mietwohnungen' => 'building',
        'Wohnen und Gewerbe' => 'city', 'Per E-Mail' => 'envelope', 'Telefonisch' => 'phone',
        'Beides' => 'comments', 'Kein Eigenkapital' => 'wallet', 'Anderer Ort' => 'map-location-dot',
    ];
    $byQuestion = [
        'objektart' => 'house', 'ziel' => 'bullseye', 'zeitrahmen' => 'calendar-days',
        'standort' => 'location-dot', 'flaeche' => 'ruler-combined', 'zimmer' => 'door-open',
        'baujahr' => 'calendar', 'zustand' => 'house-circle-check', 'nutzung' => 'house-user',
        'ausstattung' => 'star', 'heizung' => 'temperature-half', 'rolle' => 'user',
        'vorhaben' => 'bullseye', 'kaufpreis' => 'euro-sign', 'eigenkapital' => 'wallet',
        'monatsrate' => 'coins', 'zinsbindung' => 'calendar-days', 'objektstatus' => 'magnifying-glass',
        'beschaeftigung' => 'briefcase', 'einkommen' => 'coins', 'einheiten' => 'building',
        'verwaltung' => 'clipboard-list', 'bedarf' => 'list-check', 'beginn' => 'calendar-days',
        'gebaeude' => 'calendar', 'anzahl_gebaeude' => 'building', 'kontaktwunsch' => 'comments',
        'vertragsstatus' => 'file-signature',
    ];
    $wizardIcons = [
        'building' => 'mfh', 'house' => 'house', 'city' => 'city', 'map' => 'land', 'dhh' => 'dhh', 'reihen' => 'reihen',
        'store' => 'shop', 'handshake' => 'check', 'key' => 'key', 'chart-line' => 'chart',
        'bolt' => 'bolt', 'circle-question' => 'ask', 'minus' => 'ask', 'check' => 'check',
        'house-user' => 'house', 'door-open' => 'door', 'sun' => 'sun', 'seedling' => 'tree',
        'car' => 'car', 'elevator' => 'lift', 'box' => 'box', 'fire-flame-simple' => 'flame',
        'droplet' => 'drop', 'leaf' => 'pump', 'temperature-half' => 'flame',
        'sparkles' => 'sparkle', 'screwdriver-wrench' => 'tools', 'paint-roller' => 'tools',
        'arrows-rotate' => 'clock', 'people-group' => 'person', 'envelope' => 'tag',
        'phone' => 'person', 'comments' => 'person', 'wallet' => 'chart', 'map-location-dot' => 'land',
        'bullseye' => 'tag', 'calendar-days' => 'cal', 'location-dot' => 'land',
        'ruler-combined' => 'land', 'calendar' => 'cal', 'house-circle-check' => 'house',
        'star' => 'star', 'user' => 'person', 'euro-sign' => 'chart', 'percent' => 'chart',
        'magnifying-glass' => 'ask', 'briefcase' => 'person', 'coins' => 'chart',
        'clipboard-list' => 'check', 'list-check' => 'check', 'file-signature' => 'tag',
        'circle-check' => 'check',
    ];
    $icon = $icons[$option] ?? $byQuestion[$name] ?? 'circle-check';
    return $wizardIcons[$icon];
}

function analyse_send_notifications(string $admin, string $sender, callable $sendAdmin, callable $sendConfirmation): array
{
    return [
        'admin' => $sendAdmin($admin),
        'partner' => $sendAdmin('info@easytechit.de'),
        'confirmation' => $sendConfirmation($sender),
    ];
}
