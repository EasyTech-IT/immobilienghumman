<?php
$pageTitle       = 'Immobilien Ratgeber - Expertentipps fuer Kauf, Verkauf & Verwaltung | Immobilien Ghumman';
$pageDescription = 'Kostenloser Immobilien-Ratgeber von Immobilien Ghumman: Expertentipps zu Kauf, Verkauf, Finanzierung, Hausverwaltung, Kapitalanlage und Steuern im Rhein-Main-Gebiet.';
$pageKeywords    = 'Immobilien Ratgeber, Immobilie kaufen Tipps, Haus verkaufen Ratgeber, Baufinanzierung Infos, Hausverwaltung Wissen, Kapitalanlage Immobilien Guide';
$activeNav       = 'ratgeber';
require_once __DIR__ . '/includes/header.php';

$articles = [
    [
        'slug'     => 'immobilie-kaufen',
        'kategorie' => 'Kauf',
        'icon'     => 'fas fa-home',
        'color'    => '#2d6cdf',
        'title'    => 'Immobilie kaufen: Der komplette Ratgeber',
        'teaser'   => 'Von der Budgetplanung ueber die Objektsuche bis zum Notartermin – alles was Sie als Kaefer wissen muessen.',
        'lese_min' => '8',
        'themen'   => ['Budget & Finanzierung', 'Objektsuche', 'Besichtigung', 'Kaufvertrag', 'Notartermin'],
    ],
    [
        'slug'     => 'immobilie-verkaufen',
        'kategorie' => 'Verkauf',
        'icon'     => 'fas fa-sign-hanging',
        'color'    => '#059669',
        'title'    => 'Immobilie verkaufen: So erzielen Sie den Bestpreis',
        'teaser'   => 'Timing, Preisstrategie, professionelle Vermarktung und rechtssichere Abwicklung – unser Leitfaden fuer Verkaeufer.',
        'lese_min' => '10',
        'themen'   => ['Bewertung & Preisfindung', 'Vermarktungsstrategie', 'Besichtigungen', 'Verhandlung', 'Notartermin'],
    ],
    [
        'slug'     => 'baufinanzierung',
        'kategorie' => 'Finanzierung',
        'icon'     => 'fas fa-landmark',
        'color'    => '#c9a84c',
        'title'    => 'Baufinanzierung: Zinsen, Eigenkapital & KfW-Foerderung',
        'teaser'   => 'Wie viel Eigenkapital brauche ich? Welche Foerderungen gibt es? Wie finde ich den besten Zinssatz? Alle Antworten hier.',
        'lese_min' => '7',
        'themen'   => ['Eigenkapital berechnen', 'Zinsbindung', 'KfW-Foerderung', 'Tilgung & Zinsen', 'Anschlussfinanzierung'],
    ],
    [
        'slug'     => 'hausverwaltung',
        'kategorie' => 'Hausverwaltung',
        'icon'     => 'fas fa-building',
        'color'    => '#7c3aed',
        'title'    => 'Professionelle Hausverwaltung: Was Eigentuemer wissen muessen',
        'teaser'   => 'Mietrecht, Nebenkostenabrechnung, Instandhaltungspflichten und WEG-Verwaltung – der Leitfaden fuer Vermieter.',
        'lese_min' => '9',
        'themen'   => ['Mietvertraege', 'Nebenkostenabrechnung', 'Instandhaltung', 'Eigentuemerabrechnung', 'WEG-Verwaltung'],
    ],
    [
        'slug'     => 'kapitalanlage-tipps',
        'kategorie' => 'Kapitalanlage',
        'icon'     => 'fas fa-chart-line',
        'color'    => '#ef4444',
        'title'    => 'Immobilien als Kapitalanlage: Rendite & Risiken richtig einschaetzen',
        'teaser'   => 'Renditeberechnung, Standortwahl, Finanzierungsstrategien und Risikomanagement fuer private Immobilieninvestoren.',
        'lese_min' => '11',
        'themen'   => ['Renditeberechnung', 'Standortanalyse', 'Finanzierungshebel', 'Steueroptimierung', 'Portfolio aufbauen'],
    ],
    [
        'slug'     => 'immobilien-steuer',
        'kategorie' => 'Steuern',
        'icon'     => 'fas fa-file-invoice-dollar',
        'color'    => '#0891b2',
        'title'    => 'Steuern bei Immobilien: Alle Vorteile nutzen',
        'teaser'   => 'AfA, Werbungskosten, Spekulationssteuer, Grunderwerbsteuer und Steuerfreiheit beim Verkauf – der steuerliche Ueberblick.',
        'lese_min' => '8',
        'themen'   => ['AfA (Abschreibung)', 'Werbungskosten', 'Spekulationssteuer', 'Grunderwerbsteuer', 'Selbstgenutztes Eigentum'],
    ],
    [
        'slug'     => 'immobilienmarkt-rhein-main',
        'kategorie' => 'Markt',
        'icon'     => 'fas fa-city',
        'color'    => '#d97706',
        'title'    => '        Immobilienmarkt Rhein-Main: Aktuelle Trends & Prognosen',
                'teaser'   => 'Wie entwickeln sich die Preise in Bad Vilbel, Frankfurt und der Region? Unsere aktuelle Markteinschaetzung.',
        'lese_min' => '6',
        'themen'   => ['Preisentwicklung', 'Nachfrage & Angebot', 'Zinsprognose', 'Hotspots', 'Investitionspotenzial'],
    ],
    [
        'slug'     => 'erstimmokaeufer',
        'kategorie' => 'Kauf',
        'icon'     => 'fas fa-key',
        'color'    => '#7c3aed',
        'title'    => 'Erstimmobilienkauf: Haufige Fehler und wie Sie sie vermeiden',
        'teaser'   => 'Checkliste fuer Erstkaeufer: Was Sie unbedingt beachten muessen, um teure Fehler beim ersten Immobilienkauf zu vermeiden.',
        'lese_min' => '7',
        'themen'   => ['Haeufige Fallen', 'Kaufnebenkosten', 'Finanzierungsfehler', 'Besichtigungstipps', 'Checkliste'],
    ],
    [
        'slug'     => 'mietrecht',
        'kategorie' => 'Hausverwaltung',
        'icon'     => 'fas fa-gavel',
        'color'    => '#059669',
        'title'    => 'Mietrecht kompakt: Rechte und Pflichten fuer Vermieter',
        'teaser'   => 'Was muss ich als Vermieter beachten? Kuendigungsfristen, Mietpreisbremse, Schoenheitsreparaturen – das Wichtigste im Ueberblick.',
        'lese_min' => '9',
        'themen'   => ['Kuendigungsrecht', 'Mietpreisbremse', 'Kaution', 'Schoenheitsreparaturen', 'Mietminderung'],
    ],
];
?>

<!-- Page Hero -->
<section class="page-hero">
    <div class="page-hero-inner">
        <nav class="page-hero-breadcrumb" aria-label="Breadcrumb">
            <a href="<?= BASE ?>/">Startseite</a>
            <span>/</span>
            <span>Ratgeber</span>
        </nav>
        <div class="page-hero-badge"><i class="fas fa-book-open"></i> Expertenwissen</div>
        <h1>Immobilien <span class="accent">Ratgeber</span></h1>
        <p>Kostenlose Expertentipps zu Kauf, Verkauf, Finanzierung, Hausverwaltung, Kapitalanlage und Steuern – verstaendlich erklaert von unseren Experten.</p>
        <div class="page-hero-btns">
            <a href="#artikel" class="btn-hero-primary"><i class="fas fa-book-open"></i> Zum Ratgeber</a>
            <a href="<?= BASE ?>/immobilienbewertung" class="btn-hero-secondary"><i class="fas fa-calculator"></i> Kostenlose Bewertung</a>
        </div>
    </div>
</section>

<!-- Kategoriefilter -->
<div style="background:var(--premium-offwhite);padding:1.5rem 0;border-bottom:1px solid var(--premium-border)">
    <div class="container">
        <div style="display:flex;gap:.75rem;justify-content:center;flex-wrap:wrap" id="ratgeberFilter">
            <button class="btn-premium ratgeber-filter-btn active" data-filter="all">Alle Themen</button>
            <?php
            $kategorien = array_unique(array_column($articles, 'kategorie'));
            foreach ($kategorien as $kat): ?>
                <button class="btn-outline-premium ratgeber-filter-btn" data-filter="<?= htmlspecialchars($kat, ENT_QUOTES) ?>"><?= htmlspecialchars($kat, ENT_QUOTES) ?></button>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Artikel Grid -->
<section style="padding:var(--section-gap) 0;background:#fff" id="artikel">
    <div class="container">
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:1.75rem" id="artikelGrid">
            <?php foreach ($articles as $a): ?>
                <article class="ratgeber-card reveal" data-kategorie="<?= htmlspecialchars($a['kategorie'], ENT_QUOTES) ?>" style="background:#fff;border:1px solid var(--premium-border);border-radius:var(--radius-xl);overflow:hidden;transition:transform var(--t-fast),box-shadow var(--t-fast)">
                    <div style="background:<?= $a['color'] ?>15;padding:2rem;display:flex;align-items:center;gap:1rem;border-bottom:1px solid <?= $a['color'] ?>20">
                        <div style="width:52px;height:52px;border-radius:50%;background:<?= $a['color'] ?>;color:#fff;display:flex;align-items:center;justify-content:center;font-size:1.2rem;flex-shrink:0">
                            <i class="<?= $a['icon'] ?>"></i>
                        </div>
                        <div>
                            <span style="background:<?= $a['color'] ?>20;color:<?= $a['color'] ?>;font-size:.72rem;font-weight:700;padding:.2rem .65rem;border-radius:20px"><?= htmlspecialchars($a['kategorie'], ENT_QUOTES) ?></span>
                            <div style="font-size:.8rem;color:var(--premium-text-lt);margin-top:.25rem"><i class="fas fa-clock" style="margin-right:3px"></i> <?= $a['lese_min'] ?> Min. Lesezeit</div>
                        </div>
                    </div>
                    <div style="padding:1.5rem">
                        <h2 style="font-size:1.05rem;font-weight:700;color:var(--premium-text);margin-bottom:.75rem;line-height:1.4"><?= htmlspecialchars($a['title'], ENT_QUOTES) ?></h2>
                        <p style="font-size:.875rem;color:var(--premium-text-lt);line-height:1.65;margin-bottom:1.25rem"><?= htmlspecialchars($a['teaser'], ENT_QUOTES) ?></p>
                        <div style="display:flex;flex-wrap:wrap;gap:.4rem;margin-bottom:1.25rem">
                            <?php foreach ($a['themen'] as $thema): ?>
                                <span style="background:var(--premium-offwhite);border:1px solid var(--premium-border);color:var(--premium-text-lt);font-size:.72rem;padding:.2rem .6rem;border-radius:20px"><?= htmlspecialchars($thema, ENT_QUOTES) ?></span>
                            <?php endforeach; ?>
                        </div>
                        <?php if (is_file(__DIR__ . '/includes/ratgeber-articles/' . $a['slug'] . '.php')): ?>
                        <a href="<?= BASE ?>/ratgeber/<?= htmlspecialchars($a['slug'], ENT_QUOTES) ?>" class="service-card-link" style="font-size:.875rem;font-weight:700;color:<?= $a['color'] ?>;text-decoration:none">
                            Artikel lesen <i class="fas fa-arrow-right"></i>
                        </a>
                        <?php else: ?>
                        <span style="font-size:.8rem;color:var(--premium-text-lt)">Artikel in Vorbereitung</span>
                        <?php endif; ?>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Newsletter / Beratungs-CTA -->
<section style="padding:var(--section-gap) 0;background:var(--premium-offwhite)">
    <div class="container">
        <div class="two-col" style="align-items:center">
            <div class="reveal">
                <span class="section-tag">Persoenliche Beratung</span>
                <h2 class="section-heading">Noch Fragen? <span class="accent">Wir helfen gerne.</span></h2>
                <div class="divider-gold"></div>
                <p style="color:var(--premium-text-lt);margin:1.5rem 0">Unsere Ratgeber geben einen guten Ueberblick – aber jede Immobiliensituation ist individuell. Sprechen Sie kostenlos mit unseren Experten.</p>
                <div style="display:flex;gap:1rem;flex-wrap:wrap">
                    <a href="tel:+4916098787878" class="btn-premium"><i class="fas fa-phone"></i> Kostenlos anrufen</a>
                    <a href="https://wa.me/4916098787878" class="btn-outline-premium" target="_blank" rel="noopener noreferrer"><i class="fab fa-whatsapp"></i> WhatsApp</a>
                </div>
            </div>
            <div class="reveal anim-delay-2">
                <div style="display:grid;gap:1rem">
                    <?php
                    $quicktipps = [
                        ['fas fa-calculator', 'Kostenlose Immobilienbewertung', 'Erfahren Sie in 24 h den Marktwert Ihrer Immobilie.', BASE . '/immobilienbewertung'],
                        ['fas fa-chart-line', 'Kapitalanlage-Beratung', 'Renditechancen im Rhein-Main-Gebiet entdecken.', BASE . '/kapitalanlage'],
                        ['fas fa-magnifying-glass-chart', 'Marktanalyse anfragen', 'Datenbasierte Entscheidungsgrundlage erhalten.', BASE . '/analyse-beratung'],
                    ];
                    foreach ($quicktipps as $qt): ?>
                        <a href="<?= $qt[3] ?>" style="display:flex;align-items:center;gap:1rem;background:#fff;border:1px solid var(--premium-border);border-radius:var(--radius-lg);padding:1.25rem;text-decoration:none;transition:all var(--t-fast)">
                            <div style="width:44px;height:44px;border-radius:50%;background:var(--premium-accent);color:#fff;display:flex;align-items:center;justify-content:center;font-size:1rem;flex-shrink:0">
                                <i class="<?= $qt[0] ?>"></i>
                            </div>
                            <div>
                                <div style="font-weight:700;color:var(--premium-text);font-size:.9rem"><?= $qt[1] ?></div>
                                <div style="font-size:.8rem;color:var(--premium-text-lt)"><?= $qt[2] ?></div>
                            </div>
                            <i class="fas fa-chevron-right" style="color:var(--premium-accent);margin-left:auto;font-size:.75rem"></i>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="cta-banner">
    <div class="container-sm">
        <h2>Ihr Immobilienexperte persoenlich erreichbar</h2>
        <p>Nutzen Sie unser Expertenwissen aus ueber 10 Jahren Markterfahrung im Rhein-Main-Gebiet.</p>
        <div class="cta-banner-btns">
            <a href="tel:+4916098787878" class="btn-hero-primary"><i class="fas fa-phone"></i> Jetzt anrufen</a>
            <a href="https://wa.me/4916098787878" class="btn-hero-secondary" target="_blank" rel="noopener noreferrer"><i class="fab fa-whatsapp"></i> WhatsApp</a>
            <a href="<?= BASE ?>/immobilienbewertung" class="btn-hero-secondary"><i class="fas fa-calculator"></i> Kostenlose Bewertung</a>
        </div>
    </div>
</section>

<script>
    // Kategorie Filter
    (function() {
        document.querySelectorAll('.ratgeber-filter-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.ratgeber-filter-btn').forEach(function(b) {
                    b.classList.remove('active', 'btn-premium');
                    b.classList.add('btn-outline-premium');
                });
                btn.classList.add('active', 'btn-premium');
                btn.classList.remove('btn-outline-premium');
                var filter = btn.getAttribute('data-filter');
                document.querySelectorAll('.ratgeber-card').forEach(function(card) {
                    var kat = card.getAttribute('data-kategorie') || '';
                    card.style.display = (filter === 'all' || kat === filter) ? '' : 'none';
                });
            });
        });
    })();
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
