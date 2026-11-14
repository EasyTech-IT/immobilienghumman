<?php
/* Klick-Wizard für die Immobilienbewertung. Sendet an denselben Endpunkt wie zuvor. */
$wzIcons = [
    'house' => '<path class="t" d="M8 22 24 9l16 13v18H8z"/><path d="M8 22 24 9l16 13v18H8zM20 40V28h8v12"/>',
    'mfh' => '<rect class="t" x="10" y="8" width="28" height="34" rx="2"/><path d="M10 8h28v34H10zM17 15h4M27 15h4M17 23h4M27 23h4M20 42v-8h8v8"/>',
    'etw' => '<rect class="t" x="12" y="6" width="24" height="36" rx="2"/><path d="M12 6h24v36H12zM18 14h12M18 21h12M21 42v-9h6v9"/>',
    'dhh' => '<path class="t" d="M6 26 18 14l12 12v14H6z"/><path d="M6 26 18 14l12 12v14H6zM30 40V26l6-6 6 6v14zM14 40v-8h8v8"/>',
    'reihen' => '<path class="t" d="M16 24l6-6 6 6v16H16z"/><path d="M4 24l6-6 6 6v16H4zM16 24l6-6 6 6v16H16zM28 24l6-6 6 6v16H28z"/>',
    'land' => '<path class="t" d="M4 36l9-16h22l9 16z"/><path d="M4 36l9-16h22l9 16zM24 20v16M10 28h28"/>',
    'shop' => '<rect class="t" x="8" y="18" width="32" height="22"/><path d="M6 18l4-10h28l4 10zM8 18v22h32V18M19 40V29h10v11"/>',
    'tag' => '<path class="t" d="M6 24 24 6h16v16L22 42z"/><path d="M6 24 24 6h16v16L22 42zM33 15h.5"/>',
    'key' => '<circle class="t" cx="16" cy="24" r="9"/><path d="M25 24h19M37 24v7M43 24v5"/><circle cx="16" cy="24" r="9"/>',
    'chart' => '<path class="t" d="M14 28h6v12h-6zM25 20h6v20h-6zM36 12h6v28h-6z"/><path d="M8 6v34h36M14 28h6v12h-6zM25 20h6v20h-6zM36 12h6v28h-6z"/>',
    'bolt' => '<path class="t" d="M27 4 10 28h12l-2 16 18-26H26z"/><path d="M27 4 10 28h12l-2 16 18-26H26z"/>',
    'clock' => '<circle class="t" cx="24" cy="24" r="17"/><circle cx="24" cy="24" r="17"/><path d="M24 14v10l7 5"/>',
    'cal' => '<rect class="t" x="7" y="10" width="34" height="31" rx="4"/><path d="M7 10h34v31H7zM7 20h34M16 5v9M32 5v9M16 28h4M28 28h4M16 34h4"/>',
    'ask' => '<circle class="t" cx="24" cy="24" r="18"/><circle cx="24" cy="24" r="18"/><path d="M18 19a6.5 6.5 0 1 1 9.5 5.7c-2 1.2-3.5 2.3-3.5 4.6M24 35v.5"/>',
    'sparkle' => '<path class="t" d="M22 6l4 12 12 4-12 4-4 12-4-12-12-4 12-4z"/><path d="M22 6l4 12 12 4-12 4-4 12-4-12-12-4 12-4zM38 6v8M34 10h8"/>',
    'star' => '<path class="t" d="M24 5l5.7 11.6 12.8 1.9-9.2 9 2.2 12.7L24 34.200 12.500 40.200l2.200-12.700-9.200-9 12.800-1.900z"/><path d="M24 5l5.7 11.6 12.8 1.9-9.200 9 2.200 12.700L24 34.200 12.500 40.200l2.200-12.700-9.200-9 12.800-1.900z"/>',
    'check' => '<circle class="t" cx="24" cy="24" r="18"/><circle cx="24" cy="24" r="18"/><path d="M15 24.500l6 6L33 18"/>',
    'tools' => '<path class="t" d="M31 7a10 10 0 0 0-9 13L7 35l6 6 15-15a10 10 0 0 0 13-9l-6 5-5-2-2-5z"/><path d="M31 7a10 10 0 0 0-9 13L7 35l6 6 15-15a10 10 0 0 0 13-9l-6 5-5-2-2-5z"/>',
    'crack' => '<path class="t" d="M8 22 24 9l16 13v18H8z"/><path d="M8 22 24 9l16 13v18H8zM25 14l-5 9 6 5-5 12"/>',
    'balkon' => '<rect class="t" x="10" y="8" width="28" height="18"/><path d="M10 8h28v18H10zM5 26h38M11 26v14M19 26v14M29 26v14M37 26v14"/>',
    'tree' => '<circle class="t" cx="24" cy="18" r="12"/><circle cx="24" cy="18" r="12"/><path d="M24 30v13M16 43h16"/>',
    'car' => '<path class="t" d="M5 31l4-11h30l4 11v8H5z"/><path d="M5 31l4-11h30l4 11v8H5zM5 31h38M12 39v-4M36 39v-4M15 26h18"/>',
    'box' => '<path class="t" d="M7 15 24 7l17 8v19l-17 8-17-8z"/><path d="M7 15 24 7l17 8v19l-17 8-17-8zM7 15l17 8 17-8M24 23v19"/>',
    'lift' => '<rect class="t" x="10" y="5" width="28" height="38" rx="2"/><path d="M10 5h28v38H10zM24 5v38M15 21l3-4 3 4M27 27l3 4 3-4"/>',
    'stove' => '<rect class="t" x="8" y="7" width="32" height="36" rx="2"/><path d="M8 7h32v36H8zM8 19h32M15 13h2M23 13h2M31 13h2M15 27h18v10H15z"/>',
    'sun' => '<circle class="t" cx="24" cy="24" r="8"/><circle cx="24" cy="24" r="8"/><path d="M24 5v6M24 37v6M5 24h6M37 24h6M10.500 10.500l4 4M33.500 33.500l4 4M37.500 10.500l-4 4M14.500 33.500l-4 4"/>',
    'flame' => '<path class="t" d="M24 4c2 9 12 12 12 24a12 12 0 0 1-24 0c0-6 4-9 6-15 3 2 4 4 4 7 2-4 2-10 2-16z"/><path d="M24 4c2 9 12 12 12 24a12 12 0 0 1-24 0c0-6 4-9 6-15 3 2 4 4 4 7 2-4 2-10 2-16z"/>',
    'pump' => '<rect class="t" x="7" y="8" width="34" height="32" rx="6"/><rect x="7" y="8" width="34" height="32" rx="6"/><path d="M14 20c3-3 6 3 10 0s7 3 10 0M14 30c3-3 6 3 10 0s7 3 10 0"/>',
    'drop' => '<path class="t" d="M24 5c8 10 13 16 13 23a13 13 0 0 1-26 0c0-7 5-13 13-23z"/><path d="M24 5c8 10 13 16 13 23a13 13 0 0 1-26 0c0-7 5-13 13-23z"/>',
    'city' => '<path class="t" d="M22 42V8h16v34z"/><path d="M6 42V22h16M22 42V8h16v34M4 42h40M28 15h4M28 22h4M28 29h4M12 28h4M12 35h4"/>',
    'person' => '<circle class="t" cx="24" cy="16" r="9"/><circle cx="24" cy="16" r="9"/><path d="M7 43c0-9 7-15 17-15s17 6 17 15"/>',
    'door' => '<path class="t" d="M12 5h24v38H12z"/><path d="M12 5h24v38H12zM29 24.500v.5M5 43h38"/>',
];
function wz_icon($n) {
    global $wzIcons;
    return '<svg class="wz-ico" viewBox="0 0 48 48" aria-hidden="true" focusable="false">' . ($wzIcons[$n] ?? '') . '</svg>';
}
function wz_options($name, $options, $type = 'radio', $wide = false) {
    $out = '<div class="wz-grid' . ($wide ? ' wz-grid--wide' : '') . '">';
    foreach ($options as $o) {
        $value = htmlspecialchars($o[0], ENT_QUOTES);
        $label = htmlspecialchars($o[1], ENT_QUOTES);
        $sub = isset($o[3]) ? '<small>' . htmlspecialchars($o[3]) . '</small>' : '';
        $out .= '<label class="wz-card"><input type="' . $type . '" name="' . $name . '" value="' . $value . '">'
            . '<span class="wz-card-in">' . wz_icon($o[2]) . '<span class="wz-card-t">' . $label . '</span>' . $sub . '</span>'
            . '<span class="wz-tick" aria-hidden="true"><svg viewBox="0 0 16 16"><path d="M3.500 8.500l3 3 6-7" fill="none" stroke="#fff" stroke-width="2.200" stroke-linecap="round" stroke-linejoin="round"/></svg></span></label>';
    }
    return $out . '</div>';
}
if (!empty($wzIconsOnly)) return;
?>
<link rel="stylesheet" href="<?= BASE ?>/bewertung-wizard.css">
<form class="form-premium wz" action="<?= BASE ?>/api/contact.php" method="POST" id="bewertungsForm" novalidate>
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(generate_csrf_token(), ENT_QUOTES) ?>">
    <input type="hidden" name="form_type" value="bewertung">
    <input type="hidden" name="message" id="wzMessage" value="">

    <div class="wz-top" aria-live="polite">
        <div class="wz-top-row"><span class="wz-count" id="wzCount">Frage 1 von 12</span><span class="wz-eta">Nur Klicks · ca. 2 Min.</span></div>
        <div class="wz-bar" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="8" aria-label="Fortschritt"><span id="wzBar"></span></div>
    </div>

    <div class="wz-steps">
        <fieldset class="wz-step" data-need="radio" data-auto>
            <legend class="wz-q" tabindex="-1">Um welche Immobilie geht es?</legend>
            <?= wz_options('property_type', [
                ['Einfamilienhaus', 'Einfamilienhaus', 'house'],
                ['Eigentumswohnung', 'Eigentumswohnung', 'etw'],
                ['Mehrfamilienhaus', 'Mehrfamilienhaus', 'mfh'],
                ['Doppelhaushaelfte', 'Doppelhaushälfte', 'dhh'],
                ['Reihenhaus', 'Reihenhaus', 'reihen'],
                ['Grundstueck', 'Grundstück', 'land'],
                ['Gewerbeimmobilie', 'Gewerbe', 'shop'],
            ]) ?>
        </fieldset>

        <fieldset class="wz-step" data-need="radio" data-auto hidden>
            <legend class="wz-q" tabindex="-1">Was haben Sie vor?</legend>
            <?= wz_options('goal', [
                ['Verkaufen', 'Verkaufen', 'tag'],
                ['Vermieten', 'Vermieten', 'key'],
                ['Nur den Wert wissen', 'Nur den Wert wissen', 'chart'],
            ]) ?>
        </fieldset>

        <fieldset class="wz-step" data-need="radio" data-auto hidden>
            <legend class="wz-q" tabindex="-1">Wie eilig ist es?</legend>
            <?= wz_options('timeframe', [
                ['So schnell wie möglich', 'So schnell wie möglich', 'bolt'],
                ['In 3 Monaten', 'In ca. 3 Monaten', 'clock'],
                ['In 6–12 Monaten', 'In 6–12 Monaten', 'cal'],
                ['Noch unklar', 'Noch unklar', 'ask'],
            ]) ?>
        </fieldset>

        <fieldset class="wz-step" data-need="none" hidden>
            <legend class="wz-q" tabindex="-1" id="wzAreaQ">Wie groß ist die Wohnfläche?</legend>
            <p class="wz-hint">Ein Richtwert genügt. Ziehen Sie den Regler oder nutzen Sie + und −.</p>
            <div class="wz-range">
                <button type="button" class="wz-step-btn" data-d="-5" aria-label="5 Quadratmeter weniger">−</button>
                <div class="wz-range-mid">
                    <output class="wz-out" id="wzAreaOut" for="wzArea">120 m²</output>
                    <input type="range" id="wzArea" name="area" min="20" max="600" step="5" value="120" aria-label="Fläche in Quadratmetern">
                </div>
                <button type="button" class="wz-step-btn" data-d="5" aria-label="5 Quadratmeter mehr">+</button>
            </div>
        </fieldset>

        <fieldset class="wz-step" data-need="radio" data-auto data-skip hidden>
            <legend class="wz-q" tabindex="-1">Wie viele Zimmer hat die Immobilie?</legend>
            <div class="wz-chips">
                <?php foreach (['1', '2', '3', '4', '5', '6+'] as $r): ?>
                    <label class="wz-chip"><input type="radio" name="rooms" value="<?= $r ?>"><span><?= $r ?></span></label>
                <?php endforeach; ?>
            </div>
        </fieldset>

        <fieldset class="wz-step" data-need="radio" data-auto data-skip hidden>
            <legend class="wz-q" tabindex="-1">Aus welcher Zeit stammt das Gebäude?</legend>
            <div class="wz-chips wz-chips--lg">
                <?php foreach (['bis 1949', '1950–1969', '1970–1989', '1990–2009', '2010 bis heute'] as $y): ?>
                    <label class="wz-chip"><input type="radio" name="year_built" value="<?= $y ?>"><span><?= $y ?></span></label>
                <?php endforeach; ?>
            </div>
        </fieldset>

        <fieldset class="wz-step" data-need="radio" data-auto data-skip hidden>
            <legend class="wz-q" tabindex="-1">In welchem Zustand ist sie?</legend>
            <?= wz_options('condition', [
                ['Neubau', 'Neubau', 'sparkle', 'Erstbezug'],
                ['Sehr gut', 'Sehr gut', 'star', 'Komplett renoviert'],
                ['Gut', 'Gut', 'check', 'Gepflegt'],
                ['Befriedigend', 'Befriedigend', 'tools', 'Etwas Renovierung'],
                ['Sanierungsbeduerftig', 'Sanierungsbedürftig', 'crack', 'Viel zu tun'],
            ]) ?>
        </fieldset>

        <fieldset class="wz-step" data-need="none" hidden>
            <legend class="wz-q" tabindex="-1">Was gehört dazu?</legend>
            <p class="wz-hint">Mehrfachauswahl möglich, oder einfach weiter.</p>
            <?= wz_options('amenity', [
                ['Balkon/Terrasse', 'Balkon / Terrasse', 'balkon'],
                ['Garten', 'Garten', 'tree'],
                ['Garage/Stellplatz', 'Garage / Stellplatz', 'car'],
                ['Keller', 'Keller', 'box'],
                ['Aufzug', 'Aufzug', 'lift'],
                ['Einbauküche', 'Einbauküche', 'stove'],
                ['Photovoltaik', 'Solaranlage', 'sun'],
            ], 'checkbox') ?>
        </fieldset>

        <fieldset class="wz-step" data-need="radio" data-auto data-skip hidden>
            <legend class="wz-q" tabindex="-1">Womit wird geheizt?</legend>
            <?= wz_options('heating', [
                ['Gas', 'Gas', 'flame'],
                ['Wärmepumpe', 'Wärmepumpe', 'pump'],
                ['Öl', 'Öl', 'drop'],
                ['Fernwärme', 'Fernwärme', 'city'],
                ['Weiß ich nicht', 'Weiß ich nicht', 'ask'],
            ]) ?>
        </fieldset>

        <fieldset class="wz-step" data-need="radio" data-auto hidden>
            <legend class="wz-q" tabindex="-1">Wer wohnt aktuell dort?</legend>
            <?= wz_options('usage', [
                ['Selbstgenutzt', 'Ich selbst', 'person'],
                ['Vermietet', 'Mieter', 'key'],
                ['Leerstand', 'Steht leer', 'door'],
            ]) ?>
        </fieldset>

        <fieldset class="wz-step" data-need="address" hidden>
            <legend class="wz-q" tabindex="-1">Wo liegt die Immobilie?</legend>
            <p class="wz-hint">Straße und Ort genügen. Die genaue Adresse bleibt vertraulich.</p>
            <div class="form-group">
                <label for="address" class="wz-sr">Adresse der Immobilie</label>
                <input type="text" id="address" name="address" class="wz-input" autocomplete="street-address" placeholder="Straße, Hausnummer, PLZ, Ort">
            </div>
        </fieldset>

        <fieldset class="wz-step wz-step--last" data-need="contact" hidden>
            <legend class="wz-q" tabindex="-1">Wohin dürfen wir Ihre Bewertung senden?</legend>
            <div class="form-row">
                <div class="form-group">
                    <label for="name">Name *</label>
                    <input type="text" id="name" name="name" class="wz-input" autocomplete="name" placeholder="Ihr vollständiger Name">
                </div>
                <div class="form-group">
                    <label for="email">E-Mail *</label>
                    <input type="email" id="email" name="email" class="wz-input" autocomplete="email" placeholder="ihre@email.de">
                </div>
            </div>
            <div class="form-group">
                <label for="phone">Telefon (optional, für schnelle Rückfragen)</label>
                <input type="tel" id="phone" name="phone" class="wz-input" autocomplete="tel" placeholder="+49 160 ...">
            </div>
            <div class="form-group wz-consent">
                <input type="checkbox" id="datenschutz" name="datenschutz">
                <label for="datenschutz">Ich stimme der Verarbeitung meiner Daten gemäß der <a href="<?= BASE ?>/datenschutz">Datenschutzerklärung</a> zu. *</label>
            </div>
            <button type="submit" class="btn-premium wz-submit">
                <i class="fas fa-paper-plane"></i> Kostenlose Bewertung anfordern
            </button>
            <p class="wz-assure"><i class="fas fa-lock" aria-hidden="true"></i> Kostenlos · unverbindlich · Antwort innerhalb von 24 Stunden</p>
        </fieldset>
    </div>

    <p class="wz-error" id="wzError" role="alert" hidden></p>
    <div class="wz-nav">
        <button type="button" class="wz-back" id="wzBack"><span aria-hidden="true">←</span> Zurück</button>
        <button type="button" class="wz-next" id="wzNext">Weiter <span aria-hidden="true">→</span></button>
    </div>

    <div id="formMessage" style="display:none;margin-top:1rem;padding:1rem;border-radius:var(--radius-md);text-align:center;font-weight:600"></div>

    <div class="wz-success" id="wzSuccess" hidden tabindex="-1">
        <svg class="wz-success-ico" viewBox="0 0 64 64" aria-hidden="true"><circle cx="32" cy="32" r="29"/><path d="M19 33l9 9 17-19"/></svg>
        <h3>Geschafft, vielen Dank!</h3>
        <p>Wir prüfen Ihre Angaben und melden uns innerhalb von 24 Stunden mit Ihrer Bewertung.</p>
        <ol class="wz-next-steps">
            <li><strong>1.</strong> Wir sichten Ihre Angaben</li>
            <li><strong>2.</strong> Wir vergleichen mit aktuellen Marktdaten</li>
            <li><strong>3.</strong> Sie erhalten Ihren Wert per Anruf oder E-Mail</li>
        </ol>
        <button type="button" class="wz-again" id="wzAgain">Weitere Immobilie bewerten</button>
    </div>
</form>
<script src="<?= BASE ?>/js/bewertung-wizard.js"></script>
