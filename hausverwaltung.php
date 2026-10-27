<?php
$pageTitle       = 'Hausverwaltung Bad Vilbel & Frankfurt | Immobilien Ghumman';
$pageDescription = 'Professionelle Hausverwaltung im Rhein-Main-Gebiet. Mietverwaltung, WEG-Verwaltung, Nebenkostenabrechnung und digitales Eigentuemerportal.';
$pageKeywords    = 'Hausverwaltung Bad Vilbel, WEG-Verwaltung Frankfurt, Mietverwaltung Rhein-Main';
$activeNav       = 'hausverwaltung';
$extraHead       = '<style>
    .hw-accordion{margin-top:2rem;display:grid;gap:1rem}
    .hw-acc-item{background:#fff;border:1px solid var(--premium-border);border-radius:var(--radius-md);overflow:hidden;box-shadow:0 10px 30px rgba(15,23,42,.05)}
    .hw-acc-trigger{width:100%;border:0;background:linear-gradient(180deg,#fff 0%,#f8fafc 100%);padding:1rem 1.1rem;display:flex;align-items:center;justify-content:space-between;gap:.75rem;cursor:pointer;text-align:left}
    .hw-acc-left{display:flex;align-items:center;gap:.85rem;min-width:0}
    .hw-acc-icon{width:42px;height:42px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:1rem;flex-shrink:0}
    .hw-acc-title{font-size:1rem;font-weight:700;color:var(--premium-dark)}
    .hw-acc-chev{transition:transform .2s ease;color:#64748b}
    .hw-acc-panel{max-height:0;overflow:hidden;transition:max-height .25s ease;background:#fff}
    .hw-acc-content{padding:0 1.1rem 1.1rem 4.2rem;color:var(--premium-text-muted);line-height:1.75}
    .hw-acc-content ul{margin:.75rem 0 0 1.1rem}
    .hw-acc-item.is-open .hw-acc-chev{transform:rotate(180deg)}
    @media (max-width:768px){
        .hw-acc-trigger{padding:.9rem .9rem}
        .hw-acc-content{padding:0 .9rem .95rem .9rem}
        .hw-acc-left{align-items:flex-start}
    }
</style>';
require_once __DIR__ . '/includes/header.php';
?>

<!-- Page Hero mit Cloudinary-Hintergrundbild -->
<section class="page-hero" style="background-image:url('https://res.cloudinary.com/dlpdbr0ey/image/upload/v1758552674/1758059612256_vousyd.png');background-size:cover;background-position:center">
    <div class="page-hero-inner">
        <nav class="page-hero-breadcrumb" aria-label="Breadcrumb">
            <a href="<?= BASE ?>/">Startseite</a><span>/</span>
            <span>Hausverwaltung</span>
        </nav>
        <div class="page-hero-badge"><i class="fas fa-building"></i> Professionelle Verwaltung</div>
        <h1>Haus<span class="accent">verwaltung</span></h1>
        <p>Wir verwalten Ihre Immobilie sorgfaeltig, transparent und digital &ndash; damit Sie sich um nichts kuemmern muessen.</p>
        <div class="page-hero-btns">
            <a href="#leistungen" class="btn-hero-primary"><i class="fas fa-list-check"></i> Unsere Leistungen</a>
            <a href="tel:+4916098787878" class="btn-hero-secondary"><i class="fas fa-phone"></i> Kostenlos anfragen</a>
        </div>
    </div>
</section>

<!-- Stats Strip -->
<section class="hero-stats-strip">
    <div class="container">
        <div class="stats-grid-inline">
            <?php foreach (
                [
                    ['fas fa-building', '150+', 'Verwaltete Einheiten'],
                    ['fas fa-users', '98%', 'Zufriedene Eigentuemer'],
                    ['fas fa-file-invoice', '100%', 'Digitale Abrechnung'],
                    ['fas fa-headset', '24/7', 'Notfallbereitschaft'],
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
<section id="leistungen" style="padding:var(--section-gap) 0;background:var(--premium-offwhite)">
    <div class="container">
        <div class="section-header-center reveal">
            <span class="section-tag">Was wir fuer Sie tun</span>
            <h2 class="section-heading">Unsere <span class="accent">Verwaltungsleistungen</span></h2>
            <div class="divider-gold centered"></div>
        </div>
        <div class="hw-accordion reveal" id="hwAccordion">
            <?php
            $hwServices = [
                [
                    'id' => 'mietverwaltung',
                    'icon' => 'fas fa-home',
                    'color' => '#2d6cdf',
                    'title' => 'Mietverwaltung',
                    'intro' => 'Wir uebernehmen den kompletten Vermietungsprozess von der Mieterauswahl bis zur laufenden Betreuung.',
                    'points' => ['Bonitaetspruefung und Vertragsmanagement', 'Mahnwesen und Forderungsmanagement', 'Regelmaessige Kommunikation mit Mietern']
                ],
                [
                    'id' => 'nebenkostenabrechnung',
                    'icon' => 'fas fa-file-invoice-dollar',
                    'color' => '#059669',
                    'title' => 'Nebenkostenabrechnung',
                    'intro' => 'Rechtssichere, transparente und fristgerechte Abrechnungen fuer Eigentuemer und Mieter.',
                    'points' => ['Prueffaehige Einzelabrechnungen', 'Saubere Umlageschluessel und Dokumentation', 'Rueckfragenservice fuer Eigentuemer und Mieter']
                ],
                [
                    'id' => 'instandhaltung',
                    'icon' => 'fas fa-screwdriver-wrench',
                    'color' => '#c9a84c',
                    'title' => 'Instandhaltung',
                    'intro' => 'Wir koordinieren alle Massnahmen rund um Werterhalt und technische Funktionsfaehigkeit.',
                    'points' => ['Handwerkersteuerung und Angebotsvergleich', 'Wartungsplanung und Schadensmanagement', 'Kosten- und Qualitaetskontrolle']
                ],
                [
                    'id' => 'eigentuemerversammlung',
                    'icon' => 'fas fa-users',
                    'color' => '#7c3aed',
                    'title' => 'Eigentuemmerversammlung',
                    'intro' => 'Professionelle Vorbereitung, Durchfuehrung und Nachbereitung gemaess WEG-Anforderungen.',
                    'points' => ['Einladung und Tagesordnung', 'Moderation und Beschlussmanagement', 'Protokoll, Umsetzung und Nachverfolgung']
                ],
                [
                    'id' => 'digital',
                    'icon' => 'fas fa-laptop',
                    'color' => '#0891b2',
                    'title' => 'Digitale Verwaltung',
                    'intro' => 'Alle Unterlagen zentral verfuegbar, transparente Kommunikation und schnellere Prozesse.',
                    'points' => ['Eigentuemerportal fuer Dokumente', 'Digitale Freigaben und Statusupdates', 'Schnelle Erreichbarkeit ohne Medienbrueche']
                ],
                [
                    'id' => 'rechtliches',
                    'icon' => 'fas fa-scale-balanced',
                    'color' => '#dc2626',
                    'title' => 'Rechtliches',
                    'intro' => 'Wir handeln innerhalb der geltenden Miet- und WEG-Vorgaben und reduzieren Ihr Haftungsrisiko.',
                    'points' => ['Mietrechtliche Vorgaben im Alltag', 'WEG-konforme Beschlussumsetzung', 'Saubere Fristen- und Dokumentationsfuehrung']
                ],
            ];
            foreach ($hwServices as $i => $s):
                $open = $i === 0;
            ?>
                <article class="hw-acc-item<?= $open ? ' is-open' : '' ?>" id="leistung-<?= htmlspecialchars($s['id'], ENT_QUOTES) ?>">
                    <button
                        class="hw-acc-trigger"
                        type="button"
                        aria-expanded="<?= $open ? 'true' : 'false' ?>"
                        aria-controls="hw-panel-<?= htmlspecialchars($s['id'], ENT_QUOTES) ?>">
                        <span class="hw-acc-left">
                            <span class="hw-acc-icon" style="background:<?= $s['color'] ?>18;color:<?= $s['color'] ?>"><i class="<?= $s['icon'] ?>"></i></span>
                            <span class="hw-acc-title"><?= $s['title'] ?></span>
                        </span>
                        <i class="fas fa-chevron-down hw-acc-chev" aria-hidden="true"></i>
                    </button>
                    <div
                        id="hw-panel-<?= htmlspecialchars($s['id'], ENT_QUOTES) ?>"
                        class="hw-acc-panel"
                        style="max-height:<?= $open ? '420px' : '0' ?>"
                        role="region">
                        <div class="hw-acc-content">
                            <p><?= $s['intro'] ?></p>
                            <ul>
                                <?php foreach ($s['points'] as $point): ?>
                                    <li><?= $point ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Vorteile Two-Col -->
<section style="padding:var(--section-gap) 0;background:#fff">
    <div class="container">
        <div class="two-col reveal" style="align-items:center">
            <div>
                <span class="section-tag">Warum Ghumman?</span>
                <h2 class="section-heading" style="margin-top:.5rem">Verwaltung mit <span class="accent">Herz &amp; Verstand</span></h2>
                <div class="divider-gold"></div>
                <p style="color:var(--premium-text-muted);margin:1.5rem 0">Immobilien Ghumman steht fuer persoenliche Betreuung, digitale Prozesse und volle Transparenz. Bei uns sind Ihre Eigentuemer keine Nummer &ndash; sondern Partner.</p>
                <div style="display:grid;gap:1rem;margin-top:1.5rem">
                    <?php foreach (
                        [
                            ['fas fa-check-circle', '#059669', 'Kein Callcenter &ndash; immer ein direkter Ansprechpartner'],
                            ['fas fa-check-circle', '#059669', 'Monatliche Berichterstattung per App oder E-Mail'],
                            ['fas fa-check-circle', '#059669', 'Transparente Kostenabrechnungen ohne versteckte Gebuehren'],
                            ['fas fa-check-circle', '#059669', 'Notfalldienst 24/7 fuer akute Schaeden'],
                            ['fas fa-check-circle', '#059669', 'Rechtssichere Vertragsgestaltung und Dokumentation'],
                        ] as $v
                    ): ?>
                        <div style="display:flex;align-items:center;gap:.75rem">
                            <i class="<?= $v[0] ?>" style="color:<?= $v[1] ?>;font-size:1.1rem;flex-shrink:0"></i>
                            <span style="color:var(--premium-dark)"><?= $v[2] ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
                <a href="tel:+4916098787878" class="btn-premium" style="margin-top:2rem;display:inline-flex"><i class="fas fa-phone"></i> Jetzt Angebot anfragen</a>
            </div>
            <div style="background:var(--premium-navy);border-radius:var(--radius-lg);padding:2.5rem;color:#fff">
                <h3 style="color:var(--premium-gold-lt);margin:0 0 1.5rem">Unsere Verwaltungsgebuehren</h3>
                <div style="display:grid;gap:1rem">
                    <?php foreach (
                        [
                            ['Mietverwaltung', '4&ndash;6 % der Jahresnettokaltmiete', '#059669'],
                            ['WEG-Verwaltung', 'ab 22 EUR / Einheit / Monat', '#c9a84c'],
                            ['Sondereigentuumsverwaltung', 'auf Anfrage', '#2d6cdf'],
                            ['Erstanfrage &amp; Beratung', 'kostenlos', '#7c3aed'],
                        ] as $g
                    ): ?>
                        <div style="display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid rgba(255,255,255,0.1);padding-bottom:1rem">
                            <span style="font-weight:500"><?= $g[0] ?></span>
                            <span style="font-weight:700;color:<?= $g[2] ?>"><?= $g[1] ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
                <p style="font-size:.75rem;color:rgba(255,255,255,0.5);margin-top:1.5rem">* Preise zzgl. MwSt. Individuelle Vereinbarungen moeglich.</p>
            </div>
        </div>
    </div>
</section>

<!-- Onboarding-Prozess -->
<section class="onboarding-section">
    <div class="container">
        <div class="section-header-center reveal">
            <span class="section-tag">Einfacher Start</span>
            <h2 class="section-heading">In <span class="accent">4&nbsp;Schritten</span> zur sorgenfreien Verwaltung</h2>
            <p class="section-subline">Von der ersten Anfrage bis zum laufenden Betrieb &ndash; transparent, schnell und vollstaendig digital.</p>
        </div>

        <div class="onboarding-steps-grid">
            <?php
            $obSteps = [
                // [icon, color, title, description, badge]
                ['fas fa-comments',      '#3070a0', 'Erstgespraech',     'Kostenlose Analyse Ihrer Immobilie und Ihrer Ziele &ndash; persoenlich, unverbindlich und ohne Risiko.',                       'Kostenlos'],
                ['fas fa-file-contract', '#059669', 'Vertragsabschluss', 'Individueller Verwaltungsvertrag, transparent und ohne versteckte Gebuehren &ndash; genau auf Sie zugeschnitten.',             'Transparent'],
                ['fas fa-box-open',      '#c9a84c', 'Uebergabe',         'Nahtlose Uebernahme aller Unterlagen, Schluessee und Kontakte von Ihrem bisherigen Verwalter.',                               'Sorgenfrei'],
                ['fas fa-mobile-screen', '#0891b2', 'Digitales Portal',  'Ihr persoenliches Eigentuemer-Portal mit Echtzeit-Zugang zu Dokumenten und Abrechnungen &ndash; rund um die Uhr.',           'Online 24/7'],
            ];
            foreach ($obSteps as $i => $st): ?>
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
            <a href="tel:+4916098787878" class="btn-premium" style="display:inline-flex">
                <i class="fas fa-phone"></i>&nbsp;Jetzt ersten Schritt machen
            </a>
            <p style="margin-top:.75rem;font-size:.78rem;color:var(--premium-text-lt)">
                Ihr persoenlicher Ansprechpartner meldet sich innerhalb eines Werktages.
            </p>
        </div>
    </div>
</section>

<!-- Kontaktformular -->
<section style="padding:var(--section-gap) 0;background:var(--premium-offwhite)">
    <div class="container" style="max-width:700px">
        <div class="section-header-center reveal">
            <span class="section-tag">Kostenlose Erstberatung</span>
            <h2 class="section-heading">Hausverwaltung <span class="accent">anfragen</span></h2>
            <div class="divider-gold centered"></div>
        </div>
        <form class="form-premium reveal" style="margin-top:2.5rem" id="hwForm" novalidate>
            <input type="hidden" name="form_type" value="hausverwaltung">
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
                    <label>Art der Verwaltung</label>
                    <select name="art">
                        <option value="">Bitte waehlen</option>
                        <option>Mietverwaltung (Einzelobjekt)</option>
                        <option>WEG-Verwaltung</option>
                        <option>Sondereigentuumsverwaltung</option>
                        <option>Gewerbeimmobilie</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Anzahl Einheiten</label>
                    <select name="einheiten">
                        <option value="">Bitte waehlen</option>
                        <option>1-3 Einheiten</option>
                        <option>4-10 Einheiten</option>
                        <option>11-30 Einheiten</option>
                        <option>ueber 30 Einheiten</option>
                    </select>
                </div>
            </div>
            <div class="form-group"><label>Adresse der Immobilie</label><input type="text" name="adresse" placeholder="Musterstrasse 1, 61118 Bad Vilbel"></div>
            <div class="form-group"><label>Nachricht (optional)</label><textarea name="message" rows="3" placeholder="Ihre Fragen oder besondere Anforderungen..."></textarea></div>
            <div class="form-group" style="display:flex;align-items:flex-start;gap:.75rem">
                <input type="checkbox" id="datenschutzHW" name="datenschutz" required style="margin-top:3px;flex-shrink:0">
                <label for="datenschutzHW" style="font-size:.82rem;font-weight:400;margin:0">Ich stimme der <a href="<?= BASE ?>/datenschutz" target="_blank" style="color:var(--premium-accent)">Datenschutzerklaerung</a> zu.</label>
            </div>
            <button type="submit" class="btn-premium" style="width:100%"><i class="fas fa-paper-plane"></i> Jetzt Hausverwaltung anfragen</button>
            <div id="hwMeldung" style="display:none;margin-top:1rem;padding:1rem;border-radius:var(--radius-md);text-align:center"></div>
        </form>
    </div>
</section>

<!-- CTA -->
<section class="cta-banner">
    <div class="container cta-banner-inner">
        <div class="cta-banner-text">
            <h2>Verwalter wechseln &ndash; ganz einfach</h2>
            <p>Wir uebernehmen den gesamten Wechselprozess fuer Sie. Kostenfrei und ohne Aufwand fuer Sie.</p>
        </div>
        <div class="cta-banner-actions">
            <a href="tel:+4916098787878" class="btn-premium"><i class="fas fa-phone"></i> 0160 9878 7878</a>
            <a href="<?= BASE ?>/#kontakt" class="btn-outline-premium"><i class="fas fa-envelope"></i> Kontakt aufnehmen</a>
        </div>
    </div>
</section>

<script>
    (function() {
        var acc = document.getElementById('hwAccordion');
        if (!acc) return;

        function openByHash() {
            if (!location.hash || !location.hash.startsWith('#leistung-')) return;
            var target = document.querySelector(location.hash + '.hw-acc-item');
            if (!target) return;
            var btn = target.querySelector('.hw-acc-trigger');
            if (btn) btn.click();
        }

        acc.querySelectorAll('.hw-acc-trigger').forEach(function(btn) {
            btn.addEventListener('click', function() {
                var item = btn.closest('.hw-acc-item');
                var panel = item.querySelector('.hw-acc-panel');
                var open = item.classList.contains('is-open');

                acc.querySelectorAll('.hw-acc-item').forEach(function(other) {
                    var ob = other.querySelector('.hw-acc-trigger');
                    var op = other.querySelector('.hw-acc-panel');
                    other.classList.remove('is-open');
                    ob.setAttribute('aria-expanded', 'false');
                    op.style.maxHeight = '0';
                });

                if (!open) {
                    item.classList.add('is-open');
                    btn.setAttribute('aria-expanded', 'true');
                    panel.style.maxHeight = panel.scrollHeight + 'px';
                }
            });
        });

        openByHash();
        window.addEventListener('hashchange', openByHash);
    })();

    document.getElementById('hwForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        var btn = this.querySelector('button[type=submit]'),
            msg = document.getElementById('hwMeldung');
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
                msg.textContent = 'Vielen Dank! Wir melden uns innerhalb eines Werktages.';
                this.reset();
            } else {
                throw new Error(d.message || 'Fehler');
            }
        } catch (err) {
            msg.style.cssText = 'display:block;background:rgba(239,68,68,0.1);color:#ef4444;border:1px solid #ef4444;padding:1rem;border-radius:8px;text-align:center;margin-top:1rem';
            msg.textContent = 'Leider ist ein Fehler aufgetreten. Bitte rufen Sie uns an.';
        }
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-paper-plane"></i> Jetzt Hausverwaltung anfragen';
    });
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>