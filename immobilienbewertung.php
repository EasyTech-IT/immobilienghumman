<?php
$pageTitle       = 'Kostenlose Immobilienbewertung - Marktwert online ermitteln | Immobilien Ghumman';
$pageDescription = 'Kostenlose Immobilienbewertung im Rhein-Main-Gebiet. Professionelle Marktpreisanalyse, Verkehrswertgutachten und Online-Bewertung fuer Haus, Wohnung und Grundstueck.';
$pageKeywords    = 'Immobilienbewertung kostenlos, Haus Wert ermitteln, Marktwert Immobilie, Wohnung bewerten Bad Vilbel, Verkehrswertgutachten Frankfurt, Immobilie verkaufen Preis';
$activeNav       = 'bewertung';
require_once __DIR__ . '/includes/header.php';
?>

<!-- Page Hero -->
<section class="page-hero">
    <div class="page-hero-inner">
        <nav class="page-hero-breadcrumb" aria-label="Breadcrumb">
            <a href="<?= BASE ?>/">Startseite</a>
            <span>/</span>
            <span>Immobilienbewertung</span>
        </nav>
        <div class="page-hero-badge"><i class="fas fa-medal"></i> Kostenlos & professionell</div>
        <h1>Ihre Immobilie <span class="accent">kostenlos bewerten</span></h1>
        <p>Erfahren Sie in 24 Stunden den aktuellen Marktwert Ihrer Immobilie – fundiert, unverbindlich und DSGVO-konform.</p>
        <div class="page-hero-btns">
            <a href="#bewertungsformular" class="btn-hero-primary"><i class="fas fa-calculator"></i> Jetzt bewerten</a>
            <a href="tel:+4916098787878" class="btn-hero-secondary"><i class="fas fa-phone"></i> Kostenlos anrufen</a>
        </div>
    </div>
</section>

<!-- USP Strip -->
<div style="background:var(--premium-accent);padding:1.25rem 0">
    <div class="container">
        <div style="display:flex;gap:2rem;justify-content:center;flex-wrap:wrap;text-align:center">
            <?php
            $usps = [
                ['fas fa-clock', 'Ergebnis in 24 h'],
                ['fas fa-euro-sign', '100 % kostenlos'],
                ['fas fa-lock', 'DSGVO-konform'],
                ['fas fa-user-tie', 'Zertifizierte Experten'],
                ['fas fa-handshake', 'Unverbindlich'],
            ];
            foreach ($usps as $u): ?>
                <div style="display:flex;align-items:center;gap:.5rem;color:#fff;font-size:.9rem;font-weight:600">
                    <i class="<?= $u[0] ?>" style="color:var(--premium-gold-lt)"></i> <?= $u[1] ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Main Content: Form + Info -->
<section style="padding:var(--section-gap) 0;background:#fff" id="bewertungsformular">
    <div class="container">
        <div class="two-col" style="gap:4rem">
            <!-- Bewertungsformular -->
            <div class="reveal">
                <span class="section-tag">Online-Formular</span>
                <h2 class="section-heading">Bewertung <span class="accent">starten</span></h2>
                <div class="divider-gold"></div>
                <p style="color:var(--premium-text-lt);margin-bottom:2rem">Fuellen Sie das Formular aus – wir melden uns innerhalb von 24 Stunden mit Ihrer professionellen Immobilienbewertung.</p>
                <form class="form-premium" action="<?= BASE ?>/api/contact.php" method="POST" id="bewertungsForm">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(generate_csrf_token(), ENT_QUOTES) ?>">
                    <input type="hidden" name="form_type" value="bewertung">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="name">Name *</label>
                            <input type="text" id="name" name="name" placeholder="Ihr vollstaendiger Name" required>
                        </div>
                        <div class="form-group">
                            <label for="email">E-Mail *</label>
                            <input type="email" id="email" name="email" placeholder="ihre@email.de" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="phone">Telefon</label>
                            <input type="tel" id="phone" name="phone" placeholder="+49 160 ...">
                        </div>
                        <div class="form-group">
                            <label for="property_type">Objektart *</label>
                            <select id="property_type" name="property_type" required>
                                <option value="">Bitte waehlen</option>
                                <option value="Einfamilienhaus">Einfamilienhaus</option>
                                <option value="Mehrfamilienhaus">Mehrfamilienhaus</option>
                                <option value="Eigentumswohnung">Eigentumswohnung</option>
                                <option value="Doppelhaushaelfte">Doppelhaushaelfte</option>
                                <option value="Reihenhaus">Reihenhaus</option>
                                <option value="Grundstueck">Grundstueck</option>
                                <option value="Gewerbeimmobilie">Gewerbeimmobilie</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="address">Adresse der Immobilie *</label>
                        <input type="text" id="address" name="address" placeholder="Strasse, Hausnummer, PLZ, Ort" required>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="area">Wohnflaeche (m²) *</label>
                            <input type="number" id="area" name="area" placeholder="z. B. 120" min="10" max="2000" required>
                        </div>
                        <div class="form-group">
                            <label for="year_built">Baujahr</label>
                            <input type="number" id="year_built" name="year_built" placeholder="z. B. 1990" min="1850" max="<?= date('Y') ?>">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="rooms">Zimmer</label>
                            <input type="number" id="rooms" name="rooms" placeholder="z. B. 4" min="1" max="30" step="0.5">
                        </div>
                        <div class="form-group">
                            <label for="condition">Zustand</label>
                            <select id="condition" name="condition">
                                <option value="">Bitte waehlen</option>
                                <option value="Neubau">Neubau / Erstbezug</option>
                                <option value="Sehr gut">Sehr gut (vollstaendig renoviert)</option>
                                <option value="Gut">Gut (gepflegt)</option>
                                <option value="Befriedigend">Befriedigend (Renovierungsbedarf)</option>
                                <option value="Sanierungsbeduerftig">Sanierungsbeduerftig</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="message">Besonderheiten / Anmerkungen</label>
                        <textarea id="message" name="message" rows="3" placeholder="z. B. Garage, Garten, Kellerausbau, Einliegerwohnung ..."></textarea>
                    </div>
                    <div class="form-group" style="display:flex;align-items:flex-start;gap:.75rem">
                        <input type="checkbox" id="datenschutz" name="datenschutz" required style="margin-top:3px;flex-shrink:0">
                        <label for="datenschutz" style="font-size:.85rem;cursor:pointer">Ich stimme der Verarbeitung meiner Daten gemaess der <a href="<?= BASE ?>/datenschutz" style="color:var(--premium-accent)">Datenschutzerklaerung</a> zu. *</label>
                    </div>
                    <button type="submit" class="btn-premium" style="width:100%;justify-content:center;font-size:1rem;padding:.9rem 2rem">
                        <i class="fas fa-paper-plane"></i> Kostenlose Bewertung anfordern
                    </button>
                    <div id="formMessage" style="display:none;margin-top:1rem;padding:1rem;border-radius:var(--radius-md);text-align:center;font-weight:600"></div>
                </form>
            </div>

            <!-- Why Us -->
            <div class="reveal anim-delay-2">
                <span class="section-tag">Warum Immobilien Ghumman?</span>
                <h2 class="section-heading">Professionell & <span class="accent">transparent</span></h2>
                <div class="divider-gold"></div>
                <div style="display:flex;flex-direction:column;gap:1.25rem;margin-top:1.75rem;margin-bottom:2rem">
                    <?php
                    $why = [
                        ['fas fa-database', 'Aktuelle Marktdaten', 'Unsere Bewertung basiert auf tagesaktuellen Verkaufspreisen aus dem Rhein-Main-Gebiet.'],
                        ['fas fa-user-graduate', 'Zertifizierte Experten', 'Unsere Gutachter sind nach DIN EN ISO 17024 zertifiziert und kennen den lokalen Markt genau.'],
                        ['fas fa-chart-bar', 'Detaillierter Bericht', 'Sie erhalten einen umfassenden Bewertungsbericht mit Vergleichsobjekten und Marktanalyse.'],
                        ['fas fa-hands-helping', 'Persoenliche Beratung', 'Nach der Bewertung stehen Ihnen unsere Experten fuer alle Fragen kostenlos zur Verfuegung.'],
                        ['fas fa-shield-alt', 'Keine versteckten Kosten', 'Unsere Erstbewertung ist und bleibt 100 % kostenlos und unverbindlich.'],
                    ];
                    foreach ($why as $w): ?>
                        <div style="display:flex;gap:1rem;align-items:flex-start;background:var(--premium-offwhite);border:1px solid var(--premium-border);border-radius:var(--radius-lg);padding:1.25rem">
                            <div style="width:44px;height:44px;border-radius:50%;background:var(--premium-accent);color:#fff;display:flex;align-items:center;justify-content:center;font-size:1rem;flex-shrink:0">
                                <i class="<?= $w[0] ?>"></i>
                            </div>
                            <div>
                                <div style="font-weight:700;color:var(--premium-text);margin-bottom:.2rem"><?= $w[1] ?></div>
                                <div style="font-size:.85rem;color:var(--premium-text-lt);line-height:1.5"><?= $w[2] ?></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div style="background:var(--premium-navy);border-radius:var(--radius-xl);padding:2rem;text-align:center">
                    <div style="font-size:2.5rem;font-weight:800;color:var(--premium-gold)">4,9 <i class="fas fa-star" style="font-size:1.5rem"></i></div>
                    <div style="color:rgba(255,255,255,.85);font-size:.95rem;margin-top:.5rem">87+ verifizierte Google-Bewertungen</div>

                </div>
            </div>
        </div>
    </div>
</section>

<!-- Bewertungsarten -->
<section style="padding:var(--section-gap) 0;background:var(--premium-offwhite)">
    <div class="container">
        <div class="section-header-center reveal">
            <span class="section-tag">Unsere Methoden</span>
            <h2 class="section-heading">Bewertung nach <span class="accent">Ihrem Bedarf</span></h2>
            <div class="divider-gold centered"></div>
        </div>
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:1.5rem;margin-top:2.5rem">
            <?php
            $methods = [
                ['fas fa-laptop', '#2d6cdf', 'Online-Schnellbewertung', 'Kostenlos', 'Automatische Bewertung auf Basis von Vergleichsdaten – ideal fuer eine erste Orientierung. Ergebnis sofort.'],
                ['fas fa-search-dollar', '#059669', 'Marktpreisanalyse', 'Kostenlos', 'Professionelle Analyse durch unsere Experten mit lokalen Vergleichspreisen und Markttrends. Ergebnis in 24 h.'],
                ['fas fa-file-contract', '#c9a84c', 'Verkehrswertgutachten', 'Auf Anfrage', 'Rechtssicheres Wertgutachten gemaess ImmoWertV – fuer Erbschaft, Scheidung oder Finanzierung.'],
            ];
            foreach ($methods as $m): ?>
                <div class="service-card reveal">
                    <div class="service-card-icon" style="background:<?= $m[1] ?>18;color:<?= $m[1] ?>">
                        <i class="<?= $m[0] ?>"></i>
                    </div>
                    <div style="display:inline-block;background:<?= $m[1] ?>18;color:<?= $m[1] ?>;font-size:.75rem;font-weight:700;padding:.2rem .75rem;border-radius:20px;margin-bottom:.75rem"><?= htmlspecialchars($m[3], ENT_QUOTES) ?></div>
                    <h3><?= htmlspecialchars($m[2], ENT_QUOTES) ?></h3>
                    <p style="font-size:.875rem;color:var(--premium-text-lt)"><?= htmlspecialchars($m[4], ENT_QUOTES) ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Prozess -->
<section class="onboarding-section">
    <div class="container">
        <div class="section-header-center reveal">
            <span class="section-tag">So funktioniert es</span>
            <h2 class="section-heading">In <span class="accent">4&nbsp;Schritten</span> zur Immobilienbewertung</h2>
            <p class="section-subline">Schnell, kostenlos und ohne Verpflichtung &ndash; Ihr Ergebnis kommt innerhalb von 24&nbsp;Stunden.</p>
        </div>
        <div class="onboarding-steps-grid">
            <?php foreach (
                [
                    ['fas fa-clipboard-list', '#3070a0', 'Formular ausfuellen', 'Objektart, Adresse, Flaeche und Zustand eingeben &ndash; dauert nur 3&nbsp;Minuten.',                          '3 Minuten'],
                    ['fas fa-magnifying-glass-chart', '#059669', 'Datenanalyse', 'Unsere Experten analysieren Ihre Immobilie anhand aktueller Marktdaten und Vergleichsobjekte.', 'Experten'],
                    ['fas fa-file-lines',    '#c9a84c', 'Bericht erstellen', 'Detaillierter Bewertungsbericht mit Vergleichsobjekten, Preisspanne und Markteinschaetzung.',    'Detailliert'],
                    ['fas fa-envelope-open-text', '#0891b2', 'Ergebnis erhalten', 'Bewertungsbericht per E-Mail innerhalb von 24&nbsp;Stunden &ndash; kostenlos und unverbindlich.',    'In 24 Std.'],
                ] as $i => $st
            ): ?>
                <div class="ob-step-card reveal reveal-delay-<?= $i + 1 ?>" style="--step-color:<?= $st[1] ?>">
                    <div class="ob-step-num-badge">0<?= $i + 1 ?></div>
                    <div class="ob-step-icon-wrap"><i class="<?= $st[0] ?>"></i></div>
                    <div class="ob-step-meta" style="background:<?= $st[1] ?>1a;color:<?= $st[1] ?>"><?= $st[4] ?></div>
                    <h3><?= $st[2] ?></h3>
                    <p><?= $st[3] ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- FAQ -->
<section class="faq-section">
    <div class="container">
        <div class="section-header-center reveal">
            <span class="section-tag">FAQ</span>
            <h2 class="section-heading">Haeufige <span class="accent">Fragen</span></h2>
            <div class="divider-gold centered"></div>
        </div>
        <div class="faq-list">
            <?php
            $faqs = [
                ['Wie genau ist die Online-Bewertung?', 'Die automatische Schnellbewertung gibt eine Preisspanne auf Basis von Vergleichsdaten. Die professionelle Marktpreisanalyse durch unsere Experten ist deutlich praeziser und beinhaltet eine persoenliche Besichtigung auf Wunsch.'],
                ['Was brauche ich fuer die Bewertung?', 'Objektart, Adresse, Wohnflaeche und Baujahr – alles andere (Zustand, Ausstattung) ist hilfreich aber optional. Wir melden uns bei Rueckfragen.'],
                ['Warum ist die Bewertung kostenlos?', 'Wir bieten die Bewertung kostenlos an, weil wir langfristige Kundenbeziehungen aufbauen moechten. Wenn Sie entscheiden zu verkaufen, freuen wir uns ueber Ihr Vertrauen – aber kein Zwang.'],
                ['Wie lange dauert die Bewertung?', 'Die Online-Schnellbewertung ist sofort. Die detaillierte Marktpreisanalyse erhalten Sie innerhalb von 24 Stunden per E-Mail.'],
                ['Kann ich meine Immobilie auch persoenlich bewerten lassen?', 'Ja. Vereinbaren Sie gerne einen Besichtigungstermin. Unsere Experten kommen zu Ihnen und erstellen eine fundierte Vor-Ort-Bewertung.'],
                ['Was kostet ein Verkehrswertgutachten?', 'Rechtssichere Verkehrswertgutachten (z. B. fuer Erbschaft oder Gericht) sind kostenpflichtig. Sprechen Sie uns an – wir erstellen ein individuelles Angebot.'],
            ];
            foreach ($faqs as $i => $f): ?>
                <div class="faq-item <?= $i === 0 ? 'open' : '' ?>">
                    <div class="faq-question" onclick="this.parentNode.classList.toggle('open')">
                        <span><?= htmlspecialchars($f[0], ENT_QUOTES) ?></span>
                        <div class="faq-icon"><i class="fas fa-plus"></i></div>
                    </div>
                    <div class="faq-answer"><?= htmlspecialchars($f[1], ENT_QUOTES) ?></div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="cta-banner">
    <div class="container-sm">
        <h2>Jetzt kostenlos bewerten lassen</h2>
        <p>Erfahren Sie den Marktwert Ihrer Immobilie – in 24 Stunden, kostenlos, professionell.</p>
        <div class="cta-banner-btns">
            <a href="#bewertungsformular" class="btn-hero-primary"><i class="fas fa-calculator"></i> Bewertung starten</a>
            <a href="tel:+4916098787878" class="btn-hero-secondary"><i class="fas fa-phone"></i> Anrufen</a>
            <a href="https://wa.me/4916098787878?text=Ich%20moechte%20meine%20Immobilie%20bewerten%20lassen." class="btn-hero-secondary" target="_blank" rel="noopener noreferrer"><i class="fab fa-whatsapp"></i> WhatsApp</a>
        </div>
    </div>
</section>

<script>
    // Form AJAX submission
    (function() {
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

        var form = document.getElementById('bewertungsForm');
        var msg = document.getElementById('formMessage');
        if (!form) return;
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            var btn = form.querySelector('button[type=submit]');
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Wird gesendet ...';
            var data = new FormData(form);
            fetch(form.action, {
                    method: 'POST',
                    body: data
                })
                .then(parseApiResponse)
                .then(function(json) {
                    msg.style.display = 'block';
                    if (json.success && json._httpOk) {
                        msg.style.background = '#d1fae5';
                        msg.style.color = '#065f46';
                        msg.textContent = 'Vielen Dank! Wir melden uns innerhalb von 24 Stunden mit Ihrer Bewertung.';
                        form.reset();
                    } else {
                        msg.style.background = '#fee2e2';
                        msg.style.color = '#991b1b';
                        msg.textContent = json.message || 'Ein Fehler ist aufgetreten. Bitte versuchen Sie es erneut.';
                    }
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fas fa-paper-plane"></i> Kostenlose Bewertung anfordern';
                })
                .catch(function() {
                    msg.style.display = 'block';
                    msg.style.background = '#fee2e2';
                    msg.style.color = '#991b1b';
                    msg.textContent = 'Verbindungsfehler. Bitte rufen Sie uns direkt an.';
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fas fa-paper-plane"></i> Kostenlose Bewertung anfordern';
                });
        });
    })();
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
