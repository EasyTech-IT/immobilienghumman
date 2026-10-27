<?php
$pageTitle       = 'Immobilienfinanzierung Bad Vilbel & Frankfurt | Immobilien Ghumman';
$pageDescription = 'Professionelle Baufinanzierung, Anschlussfinanzierung und KfW-Foerderung im Rhein-Main-Gebiet. Kostenloser Finanzierungsrechner und unabhaengige Beratung.';
$pageKeywords    = 'Immobilienfinanzierung, Baufinanzierung Bad Vilbel, KfW-Foerderung, Finanzierungsrechner Frankfurt';
$activeNav       = 'finanzierung';
require_once __DIR__ . '/includes/header.php';
?>
<!-- Page Hero -->
<section class="page-hero">
    <div class="page-hero-inner">
        <nav class="page-hero-breadcrumb" aria-label="Breadcrumb">
            <a href="<?= BASE ?>/">Startseite</a><span>/</span>
            <span>Finanzierung</span>
        </nav>
        <div class="page-hero-badge"><i class="fas fa-landmark"></i> Baufinanzierung</div>
        <h1>Immobilien<span class="accent">finanzierung</span></h1>
        <p>Baufinanzierung, Anschlussfinanzierung und KfW-Foerderung aus einer Hand &ndash; unabhaengig, transparent und auf Ihre Situation zugeschnitten.</p>
        <div class="page-hero-btns">
            <a href="#rechner" class="btn-hero-primary"><i class="fas fa-calculator"></i> Finanzierungsrechner</a>
            <a href="tel:+4916098787878" class="btn-hero-secondary"><i class="fas fa-phone"></i> Kostenlose Beratung</a>
        </div>
    </div>
</section>

<!-- Stats Strip -->
<section class="hero-stats-strip">
    <div class="container">
        <div class="stats-grid-inline">
            <?php foreach (
                [
                    ['fas fa-star',        '4,9',  'Google-Rating'],
                    ['fab fa-google',      '20+',  'Verifizierte Bewertungen'],
                    ['fas fa-handshake',   '500+', 'Erfolgreiche Abschluesse'],
                    ['fas fa-award',       '10+',  'Jahre Erfahrung'],
                ] as $s
            ): ?>
                <div class="hero-stat">
                    <i class="<?= $s[0] ?>"></i>
                    <div><span class="hero-stat-number"><?= $s[1] ?></span><span class="hero-stat-label"><?= $s[2] ?></span></div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Leistungen -->
<section style="padding:var(--section-gap) 0;background:var(--premium-offwhite)">
    <div class="container">
        <div class="section-header-center reveal">
            <span class="section-tag">Unsere Leistungen</span>
            <h2 class="section-heading">Finanzierung fuer <span class="accent">jeden Bedarf</span></h2>
            <div class="divider-gold centered"></div>
        </div>
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:1.5rem;margin-top:2.5rem">
            <?php foreach (
                [
                    ['fas fa-home', '#2d6cdf', 'Baufinanzierung', 'Der Klassiker: Wir finden fuer Sie die optimale Finanzierung aus unserem Netzwerk von ueber 500 Banken und Sparkassen.'],
                    ['fas fa-rotate', '#059669', 'Anschlussfinanzierung', 'Zinsbindung laeuft bald aus? Wir sichern Ihnen guenstige Konditionen – auch als Forward-Darlehen bis 5 Jahre im Voraus.'],
                    ['fas fa-hand-holding-dollar', '#c9a84c', 'KfW &amp; Foerderungen', 'Wir kennen alle aktuellen Foerderprogramme: KfW, Wohn-Riester, Laenderfoerderungen. Kein Zuschuss wird verpasst.'],
                    ['fas fa-chart-pie', '#7c3aed', 'Finanzierungsoptimierung', 'Analyse Ihrer bestehenden Finanzierung: Sondertilgungen, Umschuldung, Konditionsverbesserung.'],
                ] as $s
            ): ?>
                <div class="service-card reveal">
                    <div class="service-card-icon" style="background:<?= $s[1] ?>18;color:<?= $s[1] ?>"><i class="<?= $s[0] ?>"></i></div>
                    <h3><?= $s[2] ?></h3>
                    <p><?= $s[3] ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Finanzierungsrechner -->
<section id="rechner" style="padding:var(--section-gap) 0;background:#fff">
    <div class="container">
        <div class="section-header-center reveal">
            <span class="section-tag">Kostenlos &amp; unverbindlich</span>
            <h2 class="section-heading">Ihr persoenlicher <span class="accent">Finanzierungsrechner</span></h2>
            <div class="divider-gold centered"></div>
        </div>
        <div class="two-col reveal" style="margin-top:2.5rem;align-items:start">
            <div class="calculator-card">
                <h3 style="color:#fff;margin:0 0 1.5rem">Monatsrate berechnen</h3>
                <div style="display:grid;gap:1.25rem">
                    <div>
                        <label>Kaufpreis / Darlehensbetrag</label>
                        <div style="display:flex;justify-content:space-between;margin-bottom:.3rem">
                            <span style="font-size:.8rem;opacity:.6">100.000 EUR</span>
                            <span id="lbVal" style="color:var(--premium-gold-lt);font-weight:700">300.000 EUR</span>
                            <span style="font-size:.8rem;opacity:.6">900.000 EUR</span>
                        </div>
                        <input type="range" id="lbRange" min="100000" max="900000" step="5000" value="300000">
                    </div>
                    <div>
                        <label>Zinssatz p.a.</label>
                        <div style="display:flex;justify-content:space-between;margin-bottom:.3rem">
                            <span style="font-size:.8rem;opacity:.6">1,5 %</span>
                            <span id="zinsenVal" style="color:var(--premium-gold-lt);font-weight:700">3,8 %</span>
                            <span style="font-size:.8rem;opacity:.6">7,0 %</span>
                        </div>
                        <input type="range" id="zinsenRange" min="1.5" max="7.0" step="0.1" value="3.8">
                    </div>
                    <div>
                        <label>Anfangstilgung p.a.</label>
                        <div style="display:flex;justify-content:space-between;margin-bottom:.3rem">
                            <span style="font-size:.8rem;opacity:.6">1,0 %</span>
                            <span id="tilgungVal" style="color:var(--premium-gold-lt);font-weight:700">2,5 %</span>
                            <span style="font-size:.8rem;opacity:.6">5,0 %</span>
                        </div>
                        <input type="range" id="tilgungRange" min="1.0" max="5.0" step="0.1" value="2.5">
                    </div>
                </div>
                <div class="calc-result" style="margin-top:2rem">
                    <div style="font-size:.8rem;color:rgba(255,255,255,0.6);margin-bottom:.3rem">Geschaetzte Monatsrate</div>
                    <div id="calcResult" style="font-size:2.2rem;font-weight:800;color:var(--premium-gold-lt)">1.575 EUR</div>
                    <div style="font-size:.75rem;color:rgba(255,255,255,0.5);margin-top:.5rem">Zinsen + Tilgung &middot; Nur zur Orientierung</div>
                </div>
            </div>
            <div>
                <div style="display:grid;gap:1.25rem">
                    <div class="info-card reveal">
                        <div class="info-card-icon" style="background:rgba(45,108,223,0.1);color:var(--premium-accent)"><i class="fas fa-shield-halved"></i></div>
                        <div>
                            <h4>Unabhaengige Beratung</h4>
                            <p>Wir arbeiten nicht fuer eine Bank &ndash; wir arbeiten fuer Sie. Vergleich aus ueber 500 Finanzierungspartnern.</p>
                        </div>
                    </div>
                    <div class="info-card reveal">
                        <div class="info-card-icon" style="background:rgba(37,99,235,0.1);color:var(--accent)"><i class="fas fa-tags"></i></div>
                        <div>
                            <h4>Kostenlos fuer Sie</h4>
                            <p>Unsere Finanzierungsberatung und -vermittlung ist fuer Sie als Kaeufer vollstaendig kostenlos.</p>
                        </div>
                    </div>
                    <div class="info-card reveal">
                        <div class="info-card-icon" style="background:rgba(5,150,105,0.1);color:#059669"><i class="fas fa-clock"></i></div>
                        <div>
                            <h4>Schnelle Entscheidung</h4>
                            <p>Erstangebot innerhalb von 24 Stunden &ndash; damit Sie bei Ihrem Wunschobjekt handlungsfaehig sind.</p>
                        </div>
                    </div>
                    <a href="tel:+4916098787878" class="btn-premium" style="text-align:center"><i class="fas fa-phone"></i> Jetzt persoenlich beraten lassen</a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- KfW Foerderungen -->
<section id="foerderungen" style="padding:var(--section-gap) 0;background:var(--premium-offwhite)">
    <div class="container">
        <div class="section-header-center reveal">
            <span class="section-tag">Staatliche Foerderung</span>
            <h2 class="section-heading">KfW-Programme &amp; <span class="accent">Zuschuesse 2026</span></h2>
            <div class="divider-gold centered"></div>
        </div>
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:1.5rem;margin-top:2.5rem">
            <?php foreach (
                [
                    ['KfW 300', 'Wohneigentum fuer Familien', 'Fuer Familien mit Kindern beim Kauf selbstgenutzten Wohneigentums', 'Kredit bis 270.000 EUR', '#2d6cdf', 'fas fa-house-chimney'],
                    ['KfW 261', 'Bundesfoerderung Effizientes Gebaeude', 'Kauf oder Bau energieeffizienter Gebaeude (mind. EH 55)', 'Kredit + Tilgungszuschuss', '#059669', 'fas fa-leaf'],
                    ['KfW 124', 'Wohnraum Modernisieren', 'Finanzierung energetischer Sanierungsmassnahmen', 'Kredit bis 50.000 EUR', '#c9a84c', 'fas fa-screwdriver-wrench'],
                    ['KfW 159', 'Altersgerecht Umbauen', 'Barrierefreier Umbau fuer rollstuhlgerechtes Wohnen', 'Kredit bis 50.000 EUR', '#7c3aed', 'fas fa-universal-access'],
                ] as $k
            ): ?>
                <div class="service-card reveal">
                    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1rem">
                        <span style="background:<?= $k[3] ?>18;color:<?= $k[3] ?>;border-radius:var(--radius-md);padding:.3rem .85rem;font-size:.8rem;font-weight:700"><?= $k[0] ?></span>
                        <i class="<?= $k[5] ?>" style="font-size:1.5rem;color:<?= $k[3] ?>"></i>
                    </div>
                    <h3><?= $k[1] ?></h3>
                    <p><?= $k[2] ?></p>
                    <div style="font-weight:700;color:<?= $k[3] ?>;font-size:.85rem;margin-top:auto"><i class="fas fa-tag"></i> <?= $k[4] ?></div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Prozess -->
<section class="onboarding-section">
    <div class="container">
        <div class="section-header-center reveal">
            <span class="section-tag">Ihr Weg zur Finanzierung</span>
            <h2 class="section-heading">In <span class="accent">4&nbsp;Schritten</span> zur optimalen Finanzierung</h2>
            <p class="section-subline">Schnell, transparent und vollstaendig digital &ndash; wir begleiten Sie von der ersten Anfrage bis zum Vertragsabschluss.</p>
        </div>
        <div class="onboarding-steps-grid">
            <?php
            $finSteps = [
                // [icon, color, title, description, badge]
                ['fas fa-comments',      '#3070a0', 'Kostenloses Erstgespraech', 'Wir analysieren Ihre finanzielle Situation, Ziele und Wuensche &ndash; unverbindlich und vertraulich.',                      'Kostenlos'],
                ['fas fa-chart-bar',     '#059669', 'Angebotsvergleich',        'Wir vergleichen Angebote aus ueber 500 Banken und Sparkassen und waehlen die besten Konditionen fuer Sie.',                   '500+ Banken'],
                ['fas fa-file-signature', '#c9a84c', 'Antragsstellung',          'Wir bereiten alle Unterlagen vor und stellen den Kreditantrag bei der optimalen Bank &ndash; komplett papierlos.',            'Papierlos'],
                ['fas fa-key',           '#059669', 'Finanzierung steht',       'Genehmigung erhalten, Vertrag unterschreiben, Kaufpreis zahlen &ndash; Ihre Immobilie gehoert Ihnen!',                         'Glueckwunsch'],
            ];
            foreach ($finSteps as $i => $st): ?>
                <div class="ob-step-card reveal reveal-delay-<?= $i + 1 ?>" style="--step-color:<?= $st[1] ?>">
                    <div class="ob-step-num-badge">0<?= $i + 1 ?></div>
                    <div class="ob-step-icon-wrap"><i class="<?= $st[0] ?>"></i></div>
                    <div class="ob-step-meta" style="background:<?= $st[1] ?>1a;color:<?= $st[1] ?>"><?= $st[4] ?></div>
                    <h3><?= $st[2] ?></h3>
                    <p><?= $st[3] ?></p>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="reveal" style="text-align:center;margin-top:2.75rem">
            <a href="#rechner" class="btn-premium" style="display:inline-flex">
                <i class="fas fa-calculator"></i>&nbsp;Zum Finanzierungsrechner
            </a>
            <p style="margin-top:.75rem;font-size:.78rem;color:var(--premium-text-lt)">
                Kostenlose Erstberatung &ndash; Ihr persoenlicher Berater meldet sich innerhalb von 24&nbsp;Stunden.
            </p>
        </div>
    </div>
</section>

<!-- Beratungsformular -->
<section style="padding:var(--section-gap) 0;background:var(--premium-offwhite)">
    <div class="container" style="max-width:700px">
        <div class="section-header-center reveal">
            <span class="section-tag">Kostenlos &amp; unverbindlich</span>
            <h2 class="section-heading">Finanzierungsberatung <span class="accent">anfragen</span></h2>
            <div class="divider-gold centered"></div>
        </div>
        <form class="form-premium reveal" style="margin-top:2.5rem" id="finanzierungsForm" novalidate>
            <input type="hidden" name="form_type" value="finanzierung">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(generate_csrf_token(), ENT_QUOTES) ?>">
            <div class="form-row">
                <div class="form-group"><label>Vorname *</label><input type="text" name="vorname" placeholder="Max" required></div>
                <div class="form-group"><label>Nachname *</label><input type="text" name="nachname" placeholder="Mustermann" required></div>
            </div>
            <div class="form-row">
                <div class="form-group"><label>E-Mail *</label><input type="email" name="email" placeholder="max@beispiel.de" required></div>
                <div class="form-group"><label>Telefon *</label><input type="tel" name="phone" placeholder="+49 160 1234567" required></div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Finanzierungsanlass</label>
                    <select name="anlass">
                        <option value="">Bitte waehlen</option>
                        <option>Immobilienkauf</option>
                        <option>Neubau</option>
                        <option>Anschlussfinanzierung</option>
                        <option>Modernisierung</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Benoedigter Kreditbetrag (ca.)</label>
                    <select name="kreditbetrag">
                        <option value="">Bitte waehlen</option>
                        <option>bis 200.000 EUR</option>
                        <option>200.000 - 350.000 EUR</option>
                        <option>350.000 - 500.000 EUR</option>
                        <option>500.000 - 750.000 EUR</option>
                        <option>ueber 750.000 EUR</option>
                    </select>
                </div>
            </div>
            <div class="form-group"><label>Nachricht (optional)</label><textarea name="message" rows="3" placeholder="Ihre Fragen oder besonderen Wuensche..."></textarea></div>
            <div class="form-group" style="display:flex;align-items:flex-start;gap:.75rem">
                <input type="checkbox" id="datenschutzF" name="datenschutz" required style="margin-top:3px;flex-shrink:0">
                <label for="datenschutzF" style="font-size:.82rem;font-weight:400;margin:0">Ich stimme der <a href="<?= BASE ?>/datenschutz" target="_blank" style="color:var(--premium-accent)">Datenschutzerklaerung</a> zu.</label>
            </div>
            <button type="submit" class="btn-premium" style="width:100%"><i class="fas fa-paper-plane"></i> Jetzt Finanzierungsberatung anfragen</button>
            <div id="finanzierungsMeldung" style="display:none;margin-top:1rem;padding:1rem;border-radius:var(--radius-md);text-align:center"></div>
        </form>
    </div>
</section>

<!-- CTA -->
<section class="cta-banner">
    <div class="container cta-banner-inner">
        <div class="cta-banner-text">
            <h2>Jetzt Finanzierung sichern</h2>
            <p>Erstgespraech kostenlos &ndash; individuelle Finanzierungsangebote innerhalb von 24 Stunden.</p>
        </div>
        <div class="cta-banner-actions">
            <a href="tel:+4916098787878" class="btn-premium"><i class="fas fa-phone"></i> 0160 9878 7878</a>
            <a href="<?= BASE ?>/#kontakt" class="btn-outline-premium"><i class="fas fa-envelope"></i> Kontakt aufnehmen</a>
        </div>
    </div>
</section>

<script>
    (function() {
        const lb = document.getElementById('lbRange'),
            z = document.getElementById('zinsenRange'),
            t = document.getElementById('tilgungRange');

        function fmt(n) {
            return new Intl.NumberFormat('de-DE').format(Math.round(n));
        }

        function calc() {
            const P = +lb.value,
                r = +z.value / 100 / 12,
                ti = +t.value / 100 / 12;
            document.getElementById('lbVal').textContent = fmt(P) + ' EUR';
            document.getElementById('zinsenVal').textContent = (+z.value).toFixed(1).replace('.', ',') + ' %';
            document.getElementById('tilgungVal').textContent = (+t.value).toFixed(1).replace('.', ',') + ' %';
            document.getElementById('calcResult').textContent = fmt(P * (r + ti)) + ' EUR';
        }
        [lb, z, t].forEach(function(el) {
            el.addEventListener('input', calc);
        });
        calc();
        document.getElementById('finanzierungsForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            var btn = this.querySelector('button[type=submit]'),
                msg = document.getElementById('finanzierungsMeldung');
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Wird gesendet...';
            try {
                var res = await fetch('<?= BASE ?>/api/contact.php?action=general', {
                    method: 'POST',
                    body: new FormData(this)
                });
                var d = await res.json();
                if (d.success) {
                    msg.style.cssText = 'display:block;background:rgba(5,150,105,0.1);color:#059669;border:1px solid #059669;padding:1rem;border-radius:8px;text-align:center;margin-top:1rem';
                    msg.textContent = 'Vielen Dank! Wir melden uns innerhalb von 24 Stunden.';
                    this.reset();
                } else {
                    throw new Error(d.message || 'Fehler');
                }
            } catch (err) {
                msg.style.cssText = 'display:block;background:rgba(239,68,68,0.1);color:#ef4444;border:1px solid #ef4444;padding:1rem;border-radius:8px;text-align:center;margin-top:1rem';
                msg.textContent = 'Leider ist ein Fehler aufgetreten. Bitte rufen Sie uns an.';
            }
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-paper-plane"></i> Jetzt Finanzierungsberatung anfragen';
        });
    })();
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
