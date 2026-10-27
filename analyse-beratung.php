<?php
$pageTitle       = 'Kostenlose Immobilienanalyse & Beratung | Immobilien Ghumman';
$pageDescription = 'Starten Sie Ihre kostenlose Immobilienanalyse: Immobilienwert ermitteln, Finanzierung berechnen oder Hausverwaltungsangebot anfragen – in wenigen Minuten online.';
$pageKeywords    = 'Immobilienanalyse kostenlos, Finanzierungsberatung, Hausverwaltung Angebot, Immobilienbewertung Rhein-Main';
$activeNav       = 'analyse';
require_once __DIR__ . '/includes/header.php';
?>

<!-- PAGE HERO -->
<section class="page-hero">
    <div class="page-hero-inner">
        <nav class="page-hero-breadcrumb" aria-label="Breadcrumb">
            <a href="<?= BASE ?>/">Startseite</a><span>/</span><span>Analyse & Beratung</span>
        </nav>
        <div class="page-hero-badge"><i class="fas fa-magnifying-glass-chart"></i> Kostenlos & Unverbindlich</div>
        <h1>Ihre kostenlose <span class="accent">Analyse</span></h1>
        <p>Immobilienwert ermitteln, Finanzierung berechnen oder Hausverwaltungsangebot anfragen – alles in wenigen Minuten.</p>
        <div class="page-hero-btns">
            <a href="#analyser" class="btn-hero-primary"><i class="fas fa-play-circle"></i> Analyse starten</a>
            <a href="tel:+4916098787878" class="btn-hero-secondary"><i class="fas fa-phone"></i> Direkt anrufen</a>
        </div>
    </div>
</section>

<!-- TRUST STRIP -->
<div class="analyser-trust-strip">
    <div class="container">
        <div class="analyser-trust-items">
            <div class="analyser-trust-item"><i class="fas fa-shield-alt"></i> 100% kostenlos &amp; unverbindlich</div>
            <div class="analyser-trust-item"><i class="fas fa-lock"></i> DSGVO-konform &amp; sicher</div>
            <div class="analyser-trust-item"><i class="fas fa-clock"></i> Antwort in 24 Stunden</div>
            <div class="analyser-trust-item"><i class="fas fa-star"></i> 20+ Google-Rezensionen</div>
        </div>
    </div>
</div>

<!-- 3-TAB MULTI-STEP ANALYSER -->
<section id="analyser" class="analyser-section">
    <div class="container">

        <!-- Tab Navigation -->
        <div class="analyser-tabs" role="tablist">
            <button class="analyser-tab active" role="tab" aria-selected="true" data-panel="tab-immo">
                <i class="fas fa-home"></i>
                <span>Immobilienanalyse</span>
                <small>Wert & Markt</small>
            </button>
            <button class="analyser-tab" role="tab" aria-selected="false" data-panel="tab-finanz">
                <i class="fas fa-chart-line"></i>
                <span>Finanzierungsanalyse</span>
                <small>Rate & Budget</small>
            </button>
            <button class="analyser-tab" role="tab" aria-selected="false" data-panel="tab-hausverwaltung">
                <i class="fas fa-building"></i>
                <span>Hausverwaltung</span>
                <small>Angebot anfragen</small>
            </button>
        </div>

        <!-- ═══ TAB 1: IMMOBILIENANALYSE ═══ -->
        <div class="analyser-panel active" id="tab-immo" role="tabpanel">
            <div class="analyser-card">

                <!-- Progress Bar -->
                <div class="step-progress" data-steps="3">
                    <div class="sp-step active" data-step="1"><span>1</span><small>Objekt</small></div>
                    <div class="sp-line"></div>
                    <div class="sp-step" data-step="2"><span>2</span><small>Details</small></div>
                    <div class="sp-line"></div>
                    <div class="sp-step" data-step="3"><span>3</span><small>Kontakt</small></div>
                </div>

                <form id="form-immo" novalidate>
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(generate_csrf_token(), ENT_QUOTES) ?>">
                    <input type="hidden" name="form_type" value="immobilienanalyse">

                    <!-- STEP 1: Immobilientyp -->
                    <div class="analyser-step active" data-step="1">
                        <h3 class="analyser-step-title">Welchen Immobilientyp möchten Sie analysieren?</h3>
                        <div class="type-card-grid">
                            <?php
                            $types = [
                                ['wohnung',    'fas fa-building',        'Wohnung',     'Eigentumswohnung oder Mietwohnung'],
                                ['haus',       'fas fa-house',           'Haus',        'Einfamilienhaus, DHH oder Reihenhaus'],
                                ['mehrfamilien', 'fas fa-city',           'Mehrfamilienhaus', 'Renditeobjekt mit mehreren Einheiten'],
                                ['grundstueck', 'fas fa-map',             'Grundstück',  'Bau- oder Investitionsgrundstück'],
                                ['gewerbe',    'fas fa-store',           'Gewerbe',     'Büro, Einzelhandel oder Industrie'],
                                ['sonstiges',  'fas fa-ellipsis-h',      'Sonstiges',   'Andere Immobilienart'],
                            ];
                            foreach ($types as $t): ?>
                                <label class="type-card">
                                    <input type="radio" name="immo_type" value="<?= $t[0] ?>" required>
                                    <div class="type-card-inner">
                                        <i class="<?= $t[1] ?>"></i>
                                        <strong><?= $t[2] ?></strong>
                                        <small><?= $t[3] ?></small>
                                    </div>
                                </label>
                            <?php endforeach; ?>
                        </div>
                        <div class="analyser-step-nav">
                            <div></div>
                            <button type="button" class="btn-premium step-next" data-validate="immo_type">
                                Weiter <i class="fas fa-arrow-right"></i>
                            </button>
                        </div>
                    </div>

                    <!-- STEP 2: Details -->
                    <div class="analyser-step" data-step="2">
                        <h3 class="analyser-step-title">Details zu Ihrer Immobilie</h3>
                        <div class="analyser-form-grid">
                            <div class="analyser-field">
                                <label for="immo_zip">PLZ / Ort <span class="required">*</span></label>
                                <input type="text" id="immo_zip" name="zip" placeholder="z.B. 61118 Bad Vilbel" required>
                            </div>
                            <div class="analyser-field">
                                <label for="immo_area">Wohnfläche (m²) <span class="required">*</span></label>
                                <input type="number" id="immo_area" name="area" placeholder="z.B. 95" min="1" required>
                            </div>
                            <div class="analyser-field">
                                <label for="immo_rooms">Zimmer</label>
                                <select id="immo_rooms" name="rooms">
                                    <option value="">Bitte wählen</option>
                                    <?php foreach (['1', '2', '3', '4', '5', '6+'] as $r): ?>
                                        <option value="<?= $r ?>"><?= $r ?> Zimmer</option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="analyser-field">
                                <label for="immo_baujahr">Baujahr (ca.)</label>
                                <input type="number" id="immo_baujahr" name="baujahr" placeholder="z.B. 2005" min="1850" max="2026">
                            </div>
                            <div class="analyser-field full">
                                <label>Ziel der Analyse <span class="required">*</span></label>
                                <div class="radio-pill-group">
                                    <?php foreach (['Verkauf' => 'Ich möchte verkaufen', 'Kauf' => 'Ich möchte kaufen', 'Vermietung' => 'Ich möchte vermieten', 'Bewertung' => 'Nur Wertermittlung'] as $v => $l): ?>
                                        <label class="radio-pill">
                                            <input type="radio" name="ziel" value="<?= $v ?>" required>
                                            <span><?= $l ?></span>
                                        </label>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                        <div class="analyser-step-nav">
                            <button type="button" class="btn-outline-premium step-back">
                                <i class="fas fa-arrow-left"></i> Zurück
                            </button>
                            <button type="button" class="btn-premium step-next" data-validate="zip,area,ziel">
                                Weiter <i class="fas fa-arrow-right"></i>
                            </button>
                        </div>
                    </div>

                    <!-- STEP 3: Kontakt -->
                    <div class="analyser-step" data-step="3">
                        <h3 class="analyser-step-title">Wohin senden wir Ihre Analyse?</h3>
                        <div class="analyser-form-grid">
                            <div class="analyser-field">
                                <label for="immo_name">Ihr Name <span class="required">*</span></label>
                                <input type="text" id="immo_name" name="name" placeholder="Vorname Nachname" required>
                            </div>
                            <div class="analyser-field">
                                <label for="immo_email">E-Mail-Adresse <span class="required">*</span></label>
                                <input type="email" id="immo_email" name="email" placeholder="ihre@email.de" required>
                            </div>
                            <div class="analyser-field">
                                <label for="immo_phone">Telefon (optional)</label>
                                <input type="tel" id="immo_phone" name="phone" placeholder="+49 160 1234567">
                            </div>
                            <div class="analyser-field">
                                <label for="immo_termin">Wunschtermin (optional)</label>
                                <input type="date" id="immo_termin" name="termin" min="<?= date('Y-m-d', strtotime('+1 day')) ?>">
                            </div>
                            <div class="analyser-field full">
                                <label for="immo_nachricht">Zusätzliche Informationen</label>
                                <textarea id="immo_nachricht" name="nachricht" rows="3" placeholder="Was ist besonders wichtig für Ihre Analyse?"></textarea>
                            </div>
                            <div class="analyser-field full">
                                <label class="dsgvo-check">
                                    <input type="checkbox" name="datenschutz" required>
                                    <span>Ich stimme der Verarbeitung meiner Daten gemäß der <a href="<?= BASE ?>/datenschutz">Datenschutzerklärung</a> zu. *</span>
                                </label>
                            </div>
                        </div>
                        <div class="analyser-step-nav">
                            <button type="button" class="btn-outline-premium step-back">
                                <i class="fas fa-arrow-left"></i> Zurück
                            </button>
                            <button type="submit" class="btn-premium btn-submit">
                                <i class="fas fa-paper-plane"></i> Analyse kostenlos anfragen
                            </button>
                        </div>
                    </div>

                    <!-- RESULT -->
                    <div class="analyser-result" style="display:none">
                        <div class="result-icon"><i class="fas fa-check-circle"></i></div>
                        <h3>Anfrage erfolgreich gesendet!</h3>
                        <p>Wir melden uns innerhalb von <strong>24 Stunden</strong> mit Ihrer persönlichen Immobilienanalyse. Prüfen Sie auch Ihren Spam-Ordner.</p>
                        <div class="result-actions">
                            <a href="tel:+4916098787878" class="btn-premium"><i class="fas fa-phone"></i> Sofort anrufen</a>
                            <a href="https://wa.me/4916098787878" class="btn-outline-premium" target="_blank" rel="noopener noreferrer"><i class="fab fa-whatsapp"></i> WhatsApp</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- ═══ TAB 2: FINANZIERUNGSANALYSE ═══ -->
        <div class="analyser-panel" id="tab-finanz" role="tabpanel">
            <div class="analyser-card">

                <!-- Live Kalkulator -->
                <div class="finanz-live-calc">
                    <h3 class="analyser-step-title">Schnell-Kalkulation</h3>
                    <div class="finanz-slider-grid">
                        <div class="finanz-slider-item">
                            <div class="finanz-slider-label">
                                <span>Kaufpreis</span>
                                <strong id="fCalcPrice">350.000 €</strong>
                            </div>
                            <input type="range" id="fPrice" min="50000" max="2000000" step="10000" value="350000" class="finanz-range">
                        </div>
                        <div class="finanz-slider-item">
                            <div class="finanz-slider-label">
                                <span>Eigenkapital</span>
                                <strong id="fCalcEquity">70.000 €</strong>
                            </div>
                            <input type="range" id="fEquity" min="0" max="800000" step="5000" value="70000" class="finanz-range">
                        </div>
                        <div class="finanz-slider-item">
                            <div class="finanz-slider-label">
                                <span>Zinssatz (p.a.)</span>
                                <strong id="fCalcRate">3,8 %</strong>
                            </div>
                            <input type="range" id="fRate" min="0.5" max="8" step="0.1" value="3.8" class="finanz-range">
                        </div>
                        <div class="finanz-slider-item">
                            <div class="finanz-slider-label">
                                <span>Laufzeit</span>
                                <strong id="fCalcTerm">20 Jahre</strong>
                            </div>
                            <input type="range" id="fTerm" min="5" max="35" step="1" value="20" class="finanz-range">
                        </div>
                    </div>
                    <div class="finanz-result-grid">
                        <div class="finanz-result-item">
                            <span>Darlehensbetrag</span>
                            <strong id="fResLoan">280.000 €</strong>
                        </div>
                        <div class="finanz-result-item highlight">
                            <span>Monatliche Rate</span>
                            <strong id="fResRate">ca. 1.690 €</strong>
                        </div>
                        <div class="finanz-result-item">
                            <span>Gesamtzinsen</span>
                            <strong id="fResInterest">ca. 125.600 €</strong>
                        </div>
                        <div class="finanz-result-item">
                            <span>Eigenkapitalquote</span>
                            <strong id="fResEquityPct">20,0 %</strong>
                        </div>
                    </div>
                    <p class="finanz-disclaimer">* Vereinfachte Berechnung. Unverbindlich. Für individuelle Konditionen anfragen.</p>
                </div>

                <!-- Kontaktformular -->
                <div class="finanz-form-wrap">
                    <h3 class="analyser-step-title">Vollständige Finanzierungsberatung anfragen</h3>
                    <form id="form-finanz" novalidate>
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(generate_csrf_token(), ENT_QUOTES) ?>">
                        <input type="hidden" name="form_type" value="finanzierungsanalyse">
                        <input type="hidden" name="finanz_data" id="finanz_data_hidden">
                        <div class="analyser-form-grid">
                            <div class="analyser-field">
                                <label for="fn_name">Name <span class="required">*</span></label>
                                <input type="text" id="fn_name" name="name" placeholder="Vor- und Nachname" required>
                            </div>
                            <div class="analyser-field">
                                <label for="fn_email">E-Mail <span class="required">*</span></label>
                                <input type="email" id="fn_email" name="email" placeholder="ihre@email.de" required>
                            </div>
                            <div class="analyser-field">
                                <label for="fn_phone">Telefon</label>
                                <input type="tel" id="fn_phone" name="phone" placeholder="+49 ...">
                            </div>
                            <div class="analyser-field">
                                <label for="fn_einkommen">Monatliches Netto-Einkommen</label>
                                <select id="fn_einkommen" name="einkommen">
                                    <option value="">Bitte wählen</option>
                                    <option value="bis 2000">bis 2.000 €</option>
                                    <option value="2000-3000">2.000 – 3.000 €</option>
                                    <option value="3000-4000">3.000 – 4.000 €</option>
                                    <option value="4000-5000">4.000 – 5.000 €</option>
                                    <option value="5000-7000">5.000 – 7.000 €</option>
                                    <option value="über 7000">über 7.000 €</option>
                                </select>
                            </div>
                            <div class="analyser-field full">
                                <label class="dsgvo-check">
                                    <input type="checkbox" name="datenschutz" required>
                                    <span>Ich stimme der Verarbeitung meiner Daten gemäß der <a href="<?= BASE ?>/datenschutz">Datenschutzerklärung</a> zu. *</span>
                                </label>
                            </div>
                        </div>
                        <button type="submit" class="btn-premium btn-submit" style="width:100%;justify-content:center">
                            <i class="fas fa-paper-plane"></i> Beratung anfragen
                        </button>
                        <div class="analyser-result" style="display:none">
                            <div class="result-icon"><i class="fas fa-check-circle"></i></div>
                            <h3>Anfrage gesendet!</h3>
                            <p>Wir melden uns innerhalb von 24 Stunden mit Ihrem persönlichen Finanzierungsangebot.</p>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- ═══ TAB 3: HAUSVERWALTUNG ═══ -->
        <div class="analyser-panel" id="tab-hausverwaltung" role="tabpanel">
            <div class="analyser-card">
                <div class="step-progress" data-steps="2">
                    <div class="sp-step active" data-step="1"><span>1</span><small>Objekt</small></div>
                    <div class="sp-line"></div>
                    <div class="sp-step" data-step="2"><span>2</span><small>Kontakt</small></div>
                </div>

                <form id="form-hausverwaltung" novalidate>
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(generate_csrf_token(), ENT_QUOTES) ?>">
                    <input type="hidden" name="form_type" value="hausverwaltung">

                    <!-- STEP 1 -->
                    <div class="analyser-step active" data-step="1">
                        <h3 class="analyser-step-title">Ihr Objekt</h3>
                        <div class="analyser-form-grid">
                            <div class="analyser-field full">
                                <label>Objektart <span class="required">*</span></label>
                                <div class="radio-pill-group">
                                    <?php foreach (['WEG (Eigentümergemeinschaft)' => 'WEG', 'Mietwohnungen' => 'Miete', 'Gewerbeimmobilie' => 'Gewerbe', 'Gemischt (Wohnen + Gewerbe)' => 'Gemischt'] as $l => $v): ?>
                                        <label class="radio-pill">
                                            <input type="radio" name="objekt_art" value="<?= $v ?>" required>
                                            <span><?= $l ?></span>
                                        </label>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <div class="analyser-field">
                                <label for="hv_einheiten">Anzahl Einheiten <span class="required">*</span></label>
                                <select id="hv_einheiten" name="einheiten" required>
                                    <option value="">Bitte wählen</option>
                                    <option value="1-3">1 – 3 Einheiten</option>
                                    <option value="4-10">4 – 10 Einheiten</option>
                                    <option value="11-20">11 – 20 Einheiten</option>
                                    <option value="21-50">21 – 50 Einheiten</option>
                                    <option value="50+">Über 50 Einheiten</option>
                                </select>
                            </div>
                            <div class="analyser-field">
                                <label for="hv_standort">Standort (PLZ / Stadt) <span class="required">*</span></label>
                                <input type="text" id="hv_standort" name="standort" placeholder="z.B. 61118 Bad Vilbel" required>
                            </div>
                            <div class="analyser-field full">
                                <label>Aktueller Verwaltungsstatus</label>
                                <div class="radio-pill-group">
                                    <?php foreach (['Keine Verwaltung' => 'keine', 'Selbstverwaltet' => 'selbst', 'Andere Verwaltung' => 'andere'] as $l => $v): ?>
                                        <label class="radio-pill">
                                            <input type="radio" name="verwaltung_status" value="<?= $v ?>">
                                            <span><?= $l ?></span>
                                        </label>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                        <div class="analyser-step-nav">
                            <div></div>
                            <button type="button" class="btn-premium step-next" data-validate="objekt_art,einheiten,standort">
                                Weiter <i class="fas fa-arrow-right"></i>
                            </button>
                        </div>
                    </div>

                    <!-- STEP 2: Kontakt -->
                    <div class="analyser-step" data-step="2">
                        <h3 class="analyser-step-title">Ihr kostenloses Angebot anfordern</h3>
                        <div class="analyser-form-grid">
                            <div class="analyser-field">
                                <label for="hv_name">Name <span class="required">*</span></label>
                                <input type="text" id="hv_name" name="name" placeholder="Vor- und Nachname" required>
                            </div>
                            <div class="analyser-field">
                                <label for="hv_email">E-Mail <span class="required">*</span></label>
                                <input type="email" id="hv_email" name="email" placeholder="ihre@email.de" required>
                            </div>
                            <div class="analyser-field">
                                <label for="hv_phone">Telefon</label>
                                <input type="tel" id="hv_phone" name="phone" placeholder="+49 ...">
                            </div>
                            <div class="analyser-field">
                                <label for="hv_nachricht">Anmerkungen</label>
                                <textarea id="hv_nachricht" name="nachricht" rows="3" placeholder="Besonderheiten, Wünsche ..."></textarea>
                            </div>
                            <div class="analyser-field full">
                                <label class="dsgvo-check">
                                    <input type="checkbox" name="datenschutz" required>
                                    <span>Ich stimme der Verarbeitung meiner Daten gemäß der <a href="<?= BASE ?>/datenschutz">Datenschutzerklärung</a> zu. *</span>
                                </label>
                            </div>
                        </div>
                        <div class="analyser-step-nav">
                            <button type="button" class="btn-outline-premium step-back">
                                <i class="fas fa-arrow-left"></i> Zurück
                            </button>
                            <button type="submit" class="btn-premium btn-submit">
                                <i class="fas fa-paper-plane"></i> Kostenloses Angebot anfragen
                            </button>
                        </div>
                    </div>

                    <!-- RESULT -->
                    <div class="analyser-result" style="display:none">
                        <div class="result-icon"><i class="fas fa-check-circle"></i></div>
                        <h3>Anfrage gesendet!</h3>
                        <p>Wir erstellen Ihnen ein <strong>individuelles Hausverwaltungsangebot</strong> und melden uns innerhalb von 24 Stunden.</p>
                        <div class="result-actions">
                            <a href="<?= BASE ?>/hausverwaltung" class="btn-outline-premium"><i class="fas fa-building"></i> Zur Hausverwaltung</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

    </div><!-- /container -->
</section>

<!-- USPs SECTION -->
<section style="padding:var(--section-gap) 0;background:var(--premium-offwhite)">
    <div class="container">
        <div class="section-header-center reveal">
            <span class="section-tag">Warum wir?</span>
            <h2 class="section-heading">Daten statt <span class="accent">Bauchgefühl</span></h2>
            <div class="divider-gold centered"></div>
        </div>
        <div class="services-grid" style="margin-top:2.5rem">
            <?php
            $usps = [
                ['fas fa-chart-bar', '#2d6cdf', 'Marktanalyse', 'Aktuelle Preistrends und Angebots-/Nachfragedaten für Ihren Zielstandort.'],
                ['fas fa-map-marked-alt', '#059669', 'Standortbewertung', 'Infrastruktur, Verkehrsanbindung, Sozialstruktur und Zukunftspotenzial.'],
                ['fas fa-chart-line', '#c9a84c', 'Renditeanalyse', 'Brutto- und Nettomietrendite, Cashflow und Amortisationszeitraum.'],
                ['fas fa-scale-balanced', '#ef4444', 'Kaufpreisprüfung', 'Ist der Preis marktgerecht? Wir prüfen und verhandeln für Sie.'],
                ['fas fa-home-user', '#7c3aed', 'Mietmarktanalyse', 'Realistischer Mietpreisspiegel und Mietprognose für Ihr Objekt.'],
                ['fas fa-lightbulb', '#0891b2', 'Strategieberatung', 'Langfristige Strategie für Eigennutzung, Vermietung oder Portfolio.'],
            ];
            foreach ($usps as $u): ?>
                <div class="service-card reveal">
                    <div class="service-card-icon" style="background:<?= $u[1] ?>18;color:<?= $u[1] ?>"><i class="<?= $u[0] ?>"></i></div>
                    <h3><?= $u[2] ?></h3>
                    <p><?= $u[3] ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- CTA BANNER -->
<section class="cta-banner">
    <div class="container-sm">
        <h2>Lieber persönlich beraten?</h2>
        <p>Unser Team ist täglich für Sie erreichbar – telefonisch, per WhatsApp oder vor Ort.</p>
        <div class="cta-banner-btns">
            <a href="tel:+4916098787878" class="btn-hero-primary"><i class="fas fa-phone"></i> Jetzt anrufen</a>
            <a href="https://wa.me/4916098787878" class="btn-hero-secondary" target="_blank" rel="noopener noreferrer"><i class="fab fa-whatsapp"></i> WhatsApp</a>
        </div>
    </div>
</section>

<script>
    /* ── Analyser Script ──────────────────────────────────────── */
    (function() {
        'use strict';

        function parseApiResponse(r) {
            return r.text().then(function(raw) {
                var cleaned = (raw || '').replace(/^\uFEFF+/, '').trim();
                var json = {};
                try {
                    json = cleaned ? JSON.parse(cleaned) : {};
                } catch (e) {
                    json = {
                        success: false,
                        message: cleaned || ('HTTP ' + r.status)
                    };
                }
                json._httpOk = r.ok;
                json._status = r.status;
                return json;
            });
        }

        /* Tab-Wechsel */
        document.querySelectorAll('.analyser-tab').forEach(function(tab) {
            tab.addEventListener('click', function() {
                document.querySelectorAll('.analyser-tab').forEach(t => {
                    t.classList.remove('active');
                    t.setAttribute('aria-selected', 'false');
                });
                document.querySelectorAll('.analyser-panel').forEach(p => p.classList.remove('active'));
                this.classList.add('active');
                this.setAttribute('aria-selected', 'true');
                var target = this.dataset.panel;
                var panel = document.getElementById(target);
                if (panel) {
                    panel.classList.add('active');
                    panel.scrollIntoView({
                        behavior: 'smooth',
                        block: 'nearest'
                    });
                }
            });
        });

        /* Multi-Step Navigation */
        function initSteps(formId) {
            var form = document.getElementById(formId);
            if (!form) return;
            var steps = form.querySelectorAll('.analyser-step');
            var progress = form.closest('.analyser-card').querySelector('.step-progress');

            function showStep(n) {
                steps.forEach(s => s.classList.remove('active'));
                var target = form.querySelector('[data-step="' + n + '"]');
                if (target) {
                    target.classList.add('active');
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'nearest'
                    });
                }
                if (progress) {
                    progress.querySelectorAll('.sp-step').forEach(function(sp) {
                        var sn = parseInt(sp.dataset.step);
                        sp.classList.toggle('active', sn === n);
                        sp.classList.toggle('done', sn < n);
                    });
                }
            }

            form.querySelectorAll('.step-next').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    var cur = form.querySelector('.analyser-step.active');
                    if (!cur) return;
                    var fields = (this.dataset.validate || '').split(',').filter(Boolean);
                    var valid = true;
                    fields.forEach(function(fname) {
                        var inputs = cur.querySelectorAll('[name="' + fname + '"]');
                        if (inputs.length === 0) return;
                        var isRadio = inputs[0].type === 'radio';
                        var filled = isRadio ?
                            Array.from(inputs).some(i => i.checked) :
                            inputs[0].value.trim() !== '';
                        inputs[0].closest('.analyser-field, .type-card-grid, .radio-pill-group').classList.toggle('field-error', !filled);
                        if (!filled) valid = false;
                    });
                    if (!valid) return;
                    showStep(parseInt(cur.dataset.step) + 1);
                });
            });

            form.querySelectorAll('.step-back').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    var cur = form.querySelector('.analyser-step.active');
                    if (cur) showStep(Math.max(1, parseInt(cur.dataset.step) - 1));
                });
            });

            /* Form Submit */
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                var submitBtn = this.querySelector('.btn-submit');
                var origText = submitBtn.innerHTML;
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Wird gesendet ...';

                // Finanzierungsdaten einsammeln
                var finanzHidden = document.getElementById('finanz_data_hidden');
                if (finanzHidden) {
                    finanzHidden.value = JSON.stringify({
                        kaufpreis: document.getElementById('fPrice')?.value,
                        eigenkapital: document.getElementById('fEquity')?.value,
                        zinssatz: document.getElementById('fRate')?.value,
                        laufzeit: document.getElementById('fTerm')?.value,
                    });
                }

                fetch('<?= BASE ?>/api/contact.php?action=general', {
                        method: 'POST',
                        body: new FormData(this),
                    })
                    .then(parseApiResponse)
                    .then(function(json) {
                        if (json.success && json._httpOk) {
                            form.querySelectorAll('.analyser-step').forEach(s => s.style.display = 'none');
                            if (progress) progress.style.display = 'none';
                            var result = form.querySelector('.analyser-result');
                            if (result) result.style.display = 'block';
                        } else {
                            alert(json.message || 'Fehler. Bitte rufen Sie uns direkt an.');
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = origText;
                        }
                    })
                    .catch(function() {
                        alert('Verbindungsfehler. Bitte rufen Sie uns direkt an: 0160 9878 7878');
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = origText;
                    });
            });
        }

        initSteps('form-immo');
        initSteps('form-hausverwaltung');

        /* Finanzierungsformular Submit */
        var finanzForm = document.getElementById('form-finanz');
        if (finanzForm) {
            finanzForm.addEventListener('submit', function(e) {
                e.preventDefault();
                if (!this.checkValidity()) {
                    this.reportValidity();
                    return;
                }
                var submitBtn = this.querySelector('.btn-submit');
                var origText = submitBtn.innerHTML;
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Wird gesendet ...';
                var finanzHidden = document.getElementById('finanz_data_hidden');
                if (finanzHidden) {
                    finanzHidden.value = JSON.stringify({
                        kaufpreis: document.getElementById('fPrice')?.value,
                        eigenkapital: document.getElementById('fEquity')?.value,
                        zinssatz: document.getElementById('fRate')?.value,
                        laufzeit: document.getElementById('fTerm')?.value,
                    });
                }
                fetch('<?= BASE ?>/api/contact.php?action=general', {
                        method: 'POST',
                        body: new FormData(this),
                    })
                    .then(parseApiResponse)
                    .then(function(json) {
                        if (json.success && json._httpOk) {
                            finanzForm.querySelector('.analyser-form-grid').style.display = 'none';
                            finanzForm.querySelector('button[type="submit"]').style.display = 'none';
                            var result = finanzForm.querySelector('.analyser-result');
                            if (result) result.style.display = 'block';
                        } else {
                            alert(json.message || 'Fehler. Bitte rufen Sie uns direkt an.');
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = origText;
                        }
                    })
                    .catch(function() {
                        alert('Verbindungsfehler. Bitte rufen Sie uns direkt an: 0160 9878 7878');
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = origText;
                    });
            });
        }

        /* Finanz-Live-Kalkulator */
        function calcFinanz() {
            var price = parseInt(document.getElementById('fPrice').value);
            var equity = parseInt(document.getElementById('fEquity').value);
            var rate = parseFloat(document.getElementById('fRate').value);
            var termYr = parseInt(document.getElementById('fTerm').value);
            equity = Math.min(equity, price);
            var loan = price - equity;
            var monthlyR = rate / 100 / 12;
            var n = termYr * 12;
            var monthly = loan > 0 && monthlyR > 0 ?
                Math.round(loan * monthlyR * Math.pow(1 + monthlyR, n) / (Math.pow(1 + monthlyR, n) - 1)) :
                Math.round(loan / n);
            var totalInterest = Math.max(0, monthly * n - loan);
            var equityPct = price > 0 ? (equity / price * 100).toFixed(1) : '0.0';

            var fmt = n => new Intl.NumberFormat('de-DE').format(n);
            document.getElementById('fCalcPrice').textContent = fmt(price) + ' €';
            document.getElementById('fCalcEquity').textContent = fmt(equity) + ' €';
            document.getElementById('fCalcRate').textContent = rate.toFixed(1).replace('.', ',') + ' %';
            document.getElementById('fCalcTerm').textContent = termYr + ' Jahre';
            document.getElementById('fResLoan').textContent = fmt(loan) + ' €';
            document.getElementById('fResRate').textContent = 'ca. ' + fmt(monthly) + ' €';
            document.getElementById('fResInterest').textContent = 'ca. ' + fmt(Math.round(totalInterest)) + ' €';
            document.getElementById('fResEquityPct').textContent = equityPct.replace('.', ',') + ' %';
        }

        ['fPrice', 'fEquity', 'fRate', 'fTerm'].forEach(function(id) {
            var el = document.getElementById(id);
            if (el) el.addEventListener('input', calcFinanz);
        });
        calcFinanz();

    })();
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
