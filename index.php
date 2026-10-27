<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';

$homepageProperties = [];
$homepageFallbackMode = 'none';

try {
    $pdo = db();

    $stmt = $pdo->prepare("SELECT * FROM properties WHERE status = 'available' AND featured = 1 ORDER BY created_at DESC LIMIT 6");
    $stmt->execute();
    $homepageProperties = $stmt->fetchAll();

    if (!$homepageProperties) {
        $stmt = $pdo->prepare("SELECT * FROM properties WHERE status = 'available' ORDER BY created_at DESC LIMIT 6");
        $stmt->execute();
        $homepageProperties = $stmt->fetchAll();
        if ($homepageProperties) {
            $homepageFallbackMode = 'recent_available';
        }
    }

    if (!$homepageProperties) {
        $stmt = $pdo->prepare("SELECT * FROM properties ORDER BY created_at DESC LIMIT 6");
        $stmt->execute();
        $homepageProperties = $stmt->fetchAll();
        if ($homepageProperties) {
            $homepageFallbackMode = 'recent_any_status';
        }
    }
} catch (Throwable $e) {
    $homepageProperties = [];
    $homepageFallbackMode = 'none';
}

$pageTitle       = 'Immobilien Ghumman - Immobilienmakler | Haus verkaufen, Wohnung mieten, Hausverwaltung';
$pageDescription = 'Immobilien Ghumman - Ihr erfahrener Immobilienmakler in Bad Vilbel. Wir verkaufen Häuser, vermieten Wohnungen, bieten Hausverwaltung und Immobilienfinanzierung. Kostenlose Immobilienbewertung und persönliche Beratung.';
$pageKeywords    = 'Immobilienmakler Bad Vilbel, Haus verkaufen Bad Vilbel, Wohnung mieten Bad Vilbel, Immobilienbewertung kostenlos, Hausverwaltung Bad Vilbel, Immobilienfinanzierung, Eigentumswohnung Bad Vilbel, Einfamilienhaus verkaufen, Mietverwaltung, Immobilien Frankfurt Umgebung, Makler Wetterau, Immobilien Hessen';
$activeNav       = 'home';
$ogUrl           = 'https://www.immobilienghumman.de';
$extraHead       = '<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "RealEstateAgent",
  "name": "Immobilien Ghumman",
  "description": "Ihr regionaler Immobilienmakler im Rhein-Main-Gebiet – Verkauf, Kauf, Hausverwaltung, Finanzierung, Bewertung",
    "url": "https://www.immobilienghumman.de",
  "telephone": "+49-160-98787878",
  "address": { "@type": "PostalAddress", "addressLocality": "Bad Vilbel", "addressRegion": "Hessen", "postalCode": "61118", "addressCountry": "DE" },
    "geo": { "@type": "GeoCoordinates", "latitude": 50.1782, "longitude": 8.7331 },
    "openingHours": "Mo-Fr 09:00-18:00",
    "areaServed": "Rhein-Main-Gebiet"
  }
</script>';
$extraScripts    = '<script src="' . BASE . '/js/api-client.js"></script>
<script src="' . BASE . '/js/error-tracker.js"></script>
<script src="' . BASE . '/property-manager.js"></script>';
require_once __DIR__ . '/includes/header.php';
?>

<!-- â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•
     HERO – Premium Fullscreen
     â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â• -->
<section class="hero-premium" id="home">
    <canvas id="heroParticles" aria-hidden="true"></canvas>
    <div class="hero-premium-inner animate-fadeInUp">

        <div class="hero-premium-badge">
            <i class="fas fa-map-marker-alt"></i> Immobilienmakler · Rhein-Main
        </div>

        <h1>
            Ihre Immobilie. <span style="color:#93c5fd">Unser Versprechen.</span>
        </h1>
        <!-- Search Box -->
        <form class="hero-search-box" action="<?= BASE ?>/immobilien-angebote" method="GET">
            <select name="offer_type" aria-label="Kaufen oder Mieten">
                <option value="">Kaufen & Mieten</option>
                <option value="Kauf">Kaufen</option>
                <option value="Miete">Mieten</option>
            </select>
            <select name="property_type" aria-label="Objektart">
                <option value="">Alle Objektarten</option>
                <option value="Haus">Haus</option>
                <option value="Wohnung">Wohnung</option>
                <option value="Gewerbe">Gewerbe</option>
                <option value="Grundstück">Grundstück</option>
            </select>
            <input type="text" name="location" placeholder="Stadt, PLZ oder Region ..." aria-label="Standort">
            <button type="submit" class="hero-search-btn">
                <i class="fas fa-search"></i> Suchen
            </button>
        </form>

        <!-- CTA Buttons -->
        <div class="hero-btn-group">
            <a href="<?= BASE ?>/immobilienbewertung" class="btn-hero-primary">
                <i class="fas fa-calculator"></i> Kostenlose Bewertung
            </a>
            <button class="btn-hero-secondary" id="heroPlayBtn" type="button">
                <i class="fas fa-play"></i> Video ansehen
            </button>
        </div>

        <!-- Trust Strip -->
        <div class="hero-stats-strip">
            <a class="hero-stat hero-stat-google" href="#bewertungen" aria-label="Google-Rezensionen ansehen">
                <span class="hero-stat-stars" aria-hidden="true">★★★★★</span>
                <span class="hero-stat-label"><strong>20+</strong> Google-Rezensionen</span>
            </a>
            <div class="hero-stat">
                <span class="hero-stat-num" data-count="500" data-suffix="+">500+</span>
                <span class="hero-stat-label">Objekte vermittelt</span>
            </div>
            <div class="hero-stat">
                <span class="hero-stat-num" data-count="10" data-suffix="+">10+</span>
                <span class="hero-stat-label">Jahre Erfahrung</span>
            </div>
        </div>

    </div>
</section>

<!-- Video Modal -->
<div class="video-modal" id="videoModal" role="dialog" aria-modal="true" aria-label="Erklärvideo">
    <div class="video-modal-backdrop" id="videoModalBackdrop"></div>
    <div class="video-modal-box">
        <button class="video-modal-close" id="videoModalClose" aria-label="Video schließen">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor">
                <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z" />
            </svg>
        </button>
        <video id="heroVideo" class="video-modal-player" controls playsinline>
            <source src="https://res.cloudinary.com/dlpdbr0ey/video/upload/v1773303467/Immobilien_Ghumman_erkl%C3%A4r_vodeo_2_fertig_ryx6vb.mp4" type="video/mp4">
        </video>
    </div>
</div>

<!-- ═══════════════════════════════════════════════════════
     KOSTENLOSE ANALYSE
     ═══════════════════════════════════════════════════════ -->
<section class="analyse-section" id="analyse">
    <div class="container">
        <div class="section-header-center reveal">
            <span class="section-tag">Kostenlos &amp; Unverbindlich</span>
            <h2 class="section-heading">Ihre kostenlose <span class="accent">Immobilienanalyse</span></h2>
            <div class="divider-gold centered"></div>
            <p class="section-subheading">Erfahren Sie in wenigen Minuten, was Ihre Immobilie wert ist – fundiert, diskret und ohne Verpflichtung.</p>
        </div>
        <div class="analyse-benefits">
            <div class="analyse-benefit reveal anim-delay-1">
                <div class="analyse-benefit-icon"><i class="fas fa-chart-area"></i></div>
                <h4>Marktanalyse</h4>
                <p>Aktuelle Vergleichsdaten aus Ihrer Region für eine präzise Werteinschätzung.</p>
            </div>
            <div class="analyse-benefit reveal anim-delay-2">
                <div class="analyse-benefit-icon"><i class="fas fa-magnifying-glass-dollar"></i></div>
                <h4>Wertermittlung</h4>
                <p>Professionelle Bewertung durch zertifizierte Immobilienexperten.</p>
            </div>
            <div class="analyse-benefit reveal anim-delay-3">
                <div class="analyse-benefit-icon"><i class="fas fa-handshake"></i></div>
                <h4>Persönliche Beratung</h4>
                <p>Individuelle Empfehlungen für Kauf, Verkauf oder Vermietung Ihrer Immobilie.</p>
            </div>
            <div class="analyse-benefit reveal anim-delay-4">
                <div class="analyse-benefit-icon"><i class="fas fa-shield-alt"></i></div>
                <h4>100 % Diskret</h4>
                <p>Ihre Daten sind sicher – DSGVO-konform und vollständig vertraulich behandelt.</p>
            </div>
        </div>
        <div style="text-align:center;margin-top:3rem" class="reveal">
            <a href="<?= BASE ?>/analyse-beratung" class="btn-premium">
                <i class="fas fa-magnifying-glass-chart"></i> Analyse jetzt starten
            </a>
            <a href="tel:+4916098787878" class="btn-outline-premium" style="margin-left:1rem">
                <i class="fas fa-phone"></i> Direkt anrufen
            </a>
        </div>
    </div>
</section>


<!-- â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•
     IMMOBILIENBEWERTUNG TEASER
     â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â• -->
<section style="padding:var(--section-gap) 0;background:#fff" id="bewertung">
    <div class="container">
        <div class="two-col">
            <div class="reveal">
                <span class="section-tag">Kostenlos & Online</span>
                <h2 class="section-heading">Was ist Ihre<br><span class="accent">Immobilie wert?</span></h2>
                <div class="divider-gold"></div>
                <p class="section-subheading" style="margin-bottom:1.75rem">Erhalten Sie in wenigen Schritten eine fundierte Marktbewertung Ihrer Immobilie – kostenlos, schnell und professionell auf Basis aktueller Vergleichsdaten.</p>
                <ul class="feature-list" style="margin-bottom:2rem">
                    <li>Marktpreisanalyse auf Basis aktueller Vergleichsdaten</li>
                    <li>Professionelles Gutachten durch zertifizierte Experten</li>
                    <li>Kostenlos &amp; unverbindlich – 100% DSGVO-konform</li>
                    <li>Ergebnis in 24 Stunden</li>
                </ul>
                <a href="<?= BASE ?>/immobilienbewertung" class="btn-premium">
                    <i class="fas fa-calculator"></i> Jetzt kostenlos bewerten
                </a>
            </div>
            <div class="reveal anim-delay-2">
                <div style="background:var(--premium-offwhite);border-radius:var(--radius-xl);padding:2.5rem;border:1px solid var(--premium-border)">
                    <h4 style="font-size:1.05rem;font-weight:700;color:var(--premium-text);margin-bottom:1.75rem;text-align:center">In 4 Schritten zum Ergebnis</h4>
                    <?php
                    $steps = [
                        ['fas fa-map-marker-alt', 'Adresse eingeben', 'Standort und PLZ Ihrer Immobilie'],
                        ['fas fa-home', 'Objektdaten', 'Baujahr, Fläche, Ausstattung'],
                        ['fas fa-chart-bar', 'Marktanalyse', 'Automatischer Vergleich mit aktuellen Verkaufspreisen'],
                        ['fas fa-envelope-open-text', 'Ergebnis erhalten', 'Professioneller Bewertungsbericht per E-Mail'],
                    ];
                    foreach ($steps as $i => $s): ?>
                        <div style="display:flex;gap:1rem;align-items:flex-start;margin-bottom:<?= $i < 3 ? '1.25rem' : '0' ?>">
                            <div style="width:44px;height:44px;border-radius:50%;background:var(--premium-accent);color:#fff;display:flex;align-items:center;justify-content:center;font-size:.95rem;flex-shrink:0">
                                <i class="<?= $s[0] ?>"></i>
                            </div>
                            <div>
                                <div style="font-weight:700;font-size:.95rem;color:var(--premium-text)"><?= $s[1] ?></div>
                                <div style="font-size:.85rem;color:var(--premium-text-lt)"><?= $s[2] ?></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    <a href="<?= BASE ?>/immobilienbewertung" class="btn-premium" style="width:100%;justify-content:center;margin-top:1.75rem">
                        <i class="fas fa-arrow-right"></i> Bewertung starten
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>


<!-- â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•
     LEISTUNGEN – Premium Services Grid
     â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â• -->
<section class="services-premium" id="services">
    <div class="container">
        <div class="section-header-center reveal">
            <span class="section-tag">Unsere Leistungen</span>
            <h2 class="section-heading">Unsere <span class="accent">Leistungen</span></h2>
            <div class="divider-gold centered"></div>
            <p class="section-subheading">Vom Immobilienkauf bis zur Hausverwaltung – wir begleiten Sie zuverlässig bei jedem Schritt rund um Ihre Immobilie.</p>
        </div>

        <div class="services-grid-compact">
            <a href="<?= BASE ?>/immobilien-angebote" class="svc-card reveal anim-delay-1">
                <div class="svc-card-icon"><i class="fas fa-home"></i></div>
                <span class="svc-card-name">Immobilienverkauf &amp; Kauf</span>
            </a>
            <a href="<?= BASE ?>/hausverwaltung" class="svc-card reveal anim-delay-2">
                <div class="svc-card-icon"><i class="fas fa-building"></i></div>
                <span class="svc-card-name">Hausverwaltung</span>
            </a>
            <a href="<?= BASE ?>/finanzierung" class="svc-card reveal anim-delay-3">
                <div class="svc-card-icon"><i class="fas fa-landmark"></i></div>
                <span class="svc-card-name">Immobilienfinanzierung</span>
            </a>
            <a href="<?= BASE ?>/immobilienbewertung" class="svc-card reveal anim-delay-4">
                <div class="svc-card-icon"><i class="fas fa-magnifying-glass-dollar"></i></div>
                <span class="svc-card-name">Immobilienbewertung</span>
            </a>
            <a href="<?= BASE ?>/kapitalanlage" class="svc-card reveal anim-delay-5">
                <div class="svc-card-icon"><i class="fas fa-chart-line"></i></div>
                <span class="svc-card-name">Kapitalanlage</span>
            </a>
            <a href="<?= BASE ?>/analyse-beratung" class="svc-card reveal anim-delay-6">
                <div class="svc-card-icon"><i class="fas fa-magnifying-glass-chart"></i></div>
                <span class="svc-card-name">Analyse &amp; Beratung</span>
            </a>
        </div>
    </div>
</section>


<!-- ═══════════════════════════════════════════════════════
     PROZESS – Wie es funktioniert
     ═══════════════════════════════════════════════════════ -->
<section class="onboarding-section" id="prozess">
    <div class="container">
        <div class="section-header-center reveal">
            <span class="section-tag">So funktioniert es</span>
            <h2 class="section-heading">In <span class="accent">4&nbsp;Schritten</span> zum Erfolg</h2>
            <p class="section-subline">Von der ersten Kontaktaufnahme bis zum erfolgreichen Abschluss &ndash; wir begleiten Sie persoenlich und professionell.</p>
        </div>
        <div class="onboarding-steps-grid">
            <div class="ob-step-card reveal reveal-delay-1" style="--step-color:#3070a0">
                <div class="ob-step-num-badge">01</div>
                <div class="ob-step-icon-wrap"><i class="fas fa-phone-alt"></i></div>
                <div class="ob-step-meta" style="background:#3070a01a;color:#3070a0">Kostenlos</div>
                <h3>Erstkontakt</h3>
                <p>Kostenlose Erstberatung &ndash; telefonisch, per E-Mail oder persoenlich in unserem Buero.</p>
            </div>
            <div class="ob-step-card reveal reveal-delay-2" style="--step-color:#059669">
                <div class="ob-step-num-badge">02</div>
                <div class="ob-step-icon-wrap"><i class="fas fa-magnifying-glass-dollar"></i></div>
                <div class="ob-step-meta" style="background:#0596691a;color:#059669">Marktgenau</div>
                <h3>Analyse &amp; Bewertung</h3>
                <p>Wir analysieren Ihre Immobilie und ermitteln den optimalen Marktwert anhand aktueller Daten.</p>
            </div>
            <div class="ob-step-card reveal reveal-delay-3" style="--step-color:#c9a84c">
                <div class="ob-step-num-badge">03</div>
                <div class="ob-step-icon-wrap"><i class="fas fa-bullhorn"></i></div>
                <div class="ob-step-meta" style="background:#c9a84c1a;color:#c9a84c">Reichweite</div>
                <h3>Vermarktung</h3>
                <p>Professionelle Praesentation auf allen relevanten Plattformen &ndash; fuer maximale Reichweite.</p>
            </div>
            <div class="ob-step-card reveal reveal-delay-4" style="--step-color:#0891b2">
                <div class="ob-step-num-badge">04</div>
                <div class="ob-step-icon-wrap"><i class="fas fa-handshake"></i></div>
                <div class="ob-step-meta" style="background:#0891b21a;color:#0891b2">Erfolgreich</div>
                <h3>Abschluss</h3>
                <p>Notartermin, Schluessseluebergabe und vollstaendige Begleitung bis zum erfolgreichen Abschluss.</p>
            </div>
        </div>
        <div class="reveal" style="text-align:center;margin-top:2.75rem">
            <a href="tel:+4916098787878" class="btn-premium" style="display:inline-flex">
                <i class="fas fa-phone"></i>&nbsp;Jetzt kostenlos beraten lassen
            </a>
        </div>
    </div>
</section>


<!-- ═══════════════════════════════════════════════════════
     ÜBER UNS
     ═══════════════════════════════════════════════════════ -->
<section class="uu-section" id="ueber-uns">
    <div class="container">

        <!-- Abschnitts-Header -->
        <div class="section-header-center reveal" style="margin-bottom:2.5rem;text-align:center">
            <span class="section-tag">Über uns</span>
            <h2 class="section-heading">Ihr vertrauensvoller <span class="accent">Immobilienpartner</span><br>im Rhein-Main-Gebiet</h2>
            <div class="divider-gold centered"></div>
        </div>

        <div class="uu-main-grid">

            <!-- ── Linke Spalte: Video + USPs ── -->
            <div class="uu-left reveal">

                <!-- Video im Originalformat -->
                <div class="uu-video-wrap">
                    <video
                        src="https://res.cloudinary.com/dlpdbr0ey/video/upload/v1773303497/Erkl%C3%A4r_Video_Immobilien_ghumman_1_fertig_kcq0db.mp4"
                        controls
                        playsinline
                        preload="metadata"
                        poster=""
                        aria-label="Erklärvideo Ghumman Immobilien">
                    </video>
                </div>

                <!-- Kennzahlen unterhalb des Videos -->
                <div class="uu-kpi-row">
                    <div class="uu-kpi">
                        <span class="uu-kpi-num">10+</span>
                        <span class="uu-kpi-label">Jahre Erfahrung</span>
                    </div>
                    <div class="uu-kpi">
                        <span class="uu-kpi-num">200+</span>
                        <span class="uu-kpi-label">Zufriedene Kunden</span>
                    </div>
                    <div class="uu-kpi">
                        <span class="uu-kpi-num">150+</span>
                        <span class="uu-kpi-label">Vermittelte Objekte</span>
                    </div>
                </div>
            </div>

            <!-- ── Rechte Spalte: Text ── -->
            <div class="uu-right reveal">

                <p class="uu-lead">
                    Ghumman Immobilien steht seit &uuml;ber einem Jahrzehnt f&uuml;r pers&ouml;nliche Beratung,
                    lokale Expertise und transparente Abwicklung. Als unabh&auml;ngiges Maklerb&uuml;ro
                    im Herzen des Rhein-Main-Gebiets begleiten wir K&auml;ufer, Verk&auml;ufer und
                    Eigent&uuml;mer mit vollem Engagement &ndash; von der ersten Bewertung bis zur
                    Schl&uuml;ssel&uuml;bergabe.
                </p>
                <p class="uu-lead">
                    Unser Ansatz ist einfach: Wir h&ouml;ren zu, verstehen Ihre individuellen Ziele
                    und handeln konsequent in Ihrem besten Interesse. Vertrauen, Diskretion und
                    fundierte Marktkenntnis sind das Fundament jeder unserer Beratungen.
                </p>

                <!-- CTA -->
                <div>
                    <a href="<?= BASE ?>/#kontakt" class="btn-premium">
                        Jetzt Kontakt aufnehmen
                    </a>
                </div>
            </div>

        </div>
    </div>
</section>



<!-- ═══════════════════════════════════════════════════════
     AKTUELLE IMMOBILIEN
     ═══════════════════════════════════════════════════════ -->
<section class="featured-section" id="immobilien">
    <div class="container">
        <div class="section-header-center reveal">
            <span class="section-tag">Aktuell verfügbar</span>
            <h2 class="section-heading">Aktuelle <span class="accent">Immobilienangebote</span></h2>
            <div class="divider-gold centered"></div>
            <p class="section-subheading">Entdecken Sie unsere aktuellen Miet- und Kaufobjekte im Rhein-Main-Gebiet.</p>
        </div>
        <div class="featured-grid" id="featuredGrid" data-server-rendered="<?= !empty($homepageProperties) ? '1' : '0' ?>">
            <?php if (!empty($homepageProperties)): ?>
                <?php if ($homepageFallbackMode === 'recent_available'): ?>
                    <p style="grid-column:1/-1;text-align:center;color:var(--premium-text-lt,#6b7280);margin-bottom:.75rem;">Aktuell werden die neuesten verfügbaren Angebote angezeigt, da keine Objekte explizit für die Startseite markiert sind.</p>
                <?php elseif ($homepageFallbackMode === 'recent_any_status'): ?>
                    <p style="grid-column:1/-1;text-align:center;color:var(--premium-text-lt,#6b7280);margin-bottom:.75rem;">Es sind derzeit keine aktiven Angebote verfügbar. Angezeigt werden die neuesten Objekte unabhängig vom Status.</p>
                <?php endif; ?>

                <?php foreach ($homepageProperties as $idx => $p): ?>
                    <?php
                    $title = trim((string)($p['title'] ?? 'Immobilienangebot'));
                    $location = trim((string)($p['location'] ?? 'Unbekannt'));
                    $offerType = mb_strtolower((string)($p['offer_type'] ?? ''), 'UTF-8');
                    $status = mb_strtolower((string)($p['status'] ?? ''), 'UTF-8');
                    $price = (float)($p['price'] ?? 0);
                    $area = (float)($p['area'] ?? 0);
                    $rooms = (string)($p['rooms'] ?? '');
                    $images = json_decode((string)($p['images'] ?? '[]'), true);
                    $firstImage = '';
                    if (is_array($images) && !empty($images)) {
                        if (is_string($images[0])) {
                            $firstImage = $images[0];
                        } elseif (is_array($images[0])) {
                            $firstImage = (string)($images[0]['url'] ?? '');
                        }
                    }
                    $image = trim((string)($p['main_image'] ?? ''));
                    if ($image === '') {
                        $image = $firstImage;
                    }
                    if ($image === '') {
                        $image = 'https://via.placeholder.com/400x300?text=Kein+Bild';
                    }

                    $detailUrl = !empty($p['slug'])
                        ? BASE . '/immobilie/' . rawurlencode((string)$p['slug'])
                        : BASE . '/property-detail?id=' . rawurlencode((string)($p['id'] ?? ''));

                    $badgeText = ($offerType === 'miete') ? 'Miete' : 'Kauf';
                    $badgeStyle = '';
                    if ($status === 'sold') {
                        $badgeText = 'Verkauft';
                        $badgeStyle = 'background:#dc2626';
                    } elseif ($status === 'rented') {
                        $badgeText = 'Vermietet';
                        $badgeStyle = 'background:#ea580c';
                    } elseif ($status === 'inactive') {
                        $badgeText = 'Inaktiv';
                        $badgeStyle = 'background:#64748b';
                    }

                    $priceLabel = $price > 0
                        ? number_format($price, 0, ',', '.') . ' €' . ($offerType === 'miete' ? '/Mo.' : '')
                        : 'Auf Anfrage';
                    ?>
                    <a href="<?= htmlspecialchars($detailUrl) ?>" class="featured-card reveal" data-reveal="<?= $idx % 2 === 0 ? 'up' : 'zoom' ?>">
                        <div class="featured-card-img">
                            <img src="<?= htmlspecialchars($image) ?>" alt="<?= htmlspecialchars($title) ?>" loading="lazy">
                            <span class="featured-badge" <?= $badgeStyle !== '' ? 'style="' . htmlspecialchars($badgeStyle) . '"' : '' ?>><?= htmlspecialchars($badgeText) ?></span>
                        </div>
                        <div class="featured-card-body">
                            <h4><?= htmlspecialchars($title) ?></h4>
                            <p class="featured-loc"><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($location) ?></p>
                            <div class="featured-meta">
                                <?php if ($rooms !== '' && $rooms !== '0'): ?><span><i class="fas fa-door-open"></i> <?= htmlspecialchars($rooms) ?> Zi.</span><?php endif; ?>
                                <?php if ($area > 0): ?><span><i class="fas fa-ruler-combined"></i> <?= htmlspecialchars(rtrim(rtrim(number_format($area, 1, '.', ''), '0'), '.')) ?> m²</span><?php endif; ?>
                            </div>
                            <div class="featured-price"><?= htmlspecialchars($priceLabel) ?></div>
                        </div>
                    </a>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="text-align:center;color:var(--premium-text-lt,#6b7280)">Derzeit sind keine Immobilien verfügbar.</p>
            <?php endif; ?>
        </div>
        <div style="text-align:center;margin-top:2.5rem" class="reveal">
            <a href="<?= BASE ?>/immobilien-angebote" class="btn-premium">
                <i class="fas fa-th-list"></i> Alle Angebote anzeigen
            </a>
        </div>
    </div>
</section>
<script>
    (function() {
        async function requestProperties(filters) {
            if (typeof PropertyAPI !== 'undefined') {
                var api = new PropertyAPI();
                if (filters && filters.featured === 1 && filters.status === 'available') {
                    return api.getFeaturedProperties(filters.limit || 6);
                }
                if (filters && !filters.featured && filters.status === 'available') {
                    return api.getRecentProperties(filters.limit || 6);
                }
                return api.getProperties(filters || {});
            }

            var params = new URLSearchParams();
            Object.keys(filters || {}).forEach(function(key) {
                var val = filters[key];
                if (val === null || val === undefined || val === '') return;
                params.append(key, val);
            });
            var base = (window.API_BASE_URL || '<?= BASE ?>/api').replace(/\/$/, '');
            var res = await fetch(base + '/properties.php?' + params.toString(), {
                headers: {
                    'Accept': 'application/json'
                }
            });
            if (!res.ok) {
                throw new Error('HTTP ' + res.status);
            }
            return await res.json();
        }

        async function initFeatured() {
            var grid = document.getElementById('featuredGrid');
            if (!grid) return;
            if (grid.getAttribute('data-server-rendered') === '1' && grid.querySelector('.featured-card')) {
                return;
            }

            var props = [];
            var fallbackMode = 'none';
            try {
                var response = await requestProperties({
                    status: 'available',
                    featured: 1,
                    limit: 6,
                    sort: 'newest'
                });
                props = Array.isArray(response.properties) ? response.properties : [];

                // Fallback: Wenn keine Startseiten-Objekte markiert sind,
                // zeigen wir die neuesten verfügbaren Angebote statt einer leeren Startseite.
                if (!props.length) {
                    var fallbackResponse = await requestProperties({
                        status: 'available',
                        limit: 6,
                        sort: 'newest'
                    });
                    props = Array.isArray(fallbackResponse.properties) ? fallbackResponse.properties : [];
                    if (props.length) {
                        fallbackMode = 'recent_available';
                    }
                }

                // Letzter Fallback: neueste Objekte unabhängig vom Status
                // (damit die Startseite nie komplett leer bleibt).
                if (!props.length) {
                    var anyResponse = await requestProperties({
                        limit: 6,
                        sort: 'newest'
                    });
                    props = Array.isArray(anyResponse.properties) ? anyResponse.properties : [];
                    if (props.length) {
                        fallbackMode = 'recent_any_status';
                    }
                }
            } catch (e) {
                console.warn('Featured API-Laden fehlgeschlagen:', e);
            }

            if (!props.length) {
                grid.innerHTML = '<p style="text-align:center;color:var(--premium-text-lt,#6b7280)">Derzeit sind keine als Startseite markierten Angebote aktiv.</p>';
                return;
            }

            var fallbackNote = '';
            if (fallbackMode === 'recent_available') {
                fallbackNote = '<p style="grid-column:1/-1;text-align:center;color:var(--premium-text-lt,#6b7280);margin-bottom:.75rem;">Aktuell werden die neuesten verfügbaren Angebote angezeigt, da keine Objekte explizit für die Startseite markiert sind.</p>';
            } else if (fallbackMode === 'recent_any_status') {
                fallbackNote = '<p style="grid-column:1/-1;text-align:center;color:var(--premium-text-lt,#6b7280);margin-bottom:.75rem;">Es sind derzeit keine aktiven Angebote verfügbar. Angezeigt werden die neuesten Objekte unabhängig vom Status.</p>';
            }

            grid.innerHTML = fallbackNote + props.map(function(p) {
                var title = String(p.title || 'Immobilienangebot');
                var price = Number(p.price || 0);
                var offerType = String(p.offer_type || '').toLowerCase();
                var image = p.main_image || (Array.isArray(p.images) && p.images.length ? (typeof p.images[0] === 'string' ? p.images[0] : (p.images[0].url || '')) : '');
                var location = p.location || p.city || 'Unbekannt';
                var area = Number(p.area || p.size || 0);
                var status = String(p.status || '').toLowerCase();
                var statusBadge = '';

                if (status === 'sold') {
                    statusBadge = '<span class="featured-badge" style="background:#dc2626">Verkauft</span>';
                } else if (status === 'rented') {
                    statusBadge = '<span class="featured-badge" style="background:#ea580c">Vermietet</span>';
                } else if (status === 'inactive') {
                    statusBadge = '<span class="featured-badge" style="background:#64748b">Inaktiv</span>';
                } else {
                    statusBadge = '<span class="featured-badge">' + (offerType === 'miete' ? 'Miete' : 'Kauf') + '</span>';
                }

                var priceStr = price ?
                    price.toLocaleString('de-DE') + '\u00a0\u20ac' + (offerType === 'miete' ? '/Mo.' : '') :
                    'Auf Anfrage';
                var detailUrl = p.slug ? ('<?= BASE ?>/immobilie/' + encodeURIComponent(p.slug)) : ('<?= BASE ?>/property-detail?id=' + encodeURIComponent(p.id));
                return '<a href="' + detailUrl + '" class="featured-card reveal">' +
                    '<div class="featured-card-img">' +
                    '<img src="' + (image || 'https://via.placeholder.com/400x300?text=Kein+Bild') + '" alt="' + title.replace(/"/g, '&quot;') + '" loading="lazy">' +
                    statusBadge +
                    '</div>' +
                    '<div class="featured-card-body">' +
                    '<h4>' + title + '</h4>' +
                    '<p class="featured-loc"><i class="fas fa-map-marker-alt"></i> ' + location + '</p>' +
                    '<div class="featured-meta">' +
                    (p.rooms ? '<span><i class="fas fa-door-open"></i> ' + p.rooms + ' Zi.</span>' : '') +
                    (area ? '<span><i class="fas fa-ruler-combined"></i> ' + area + ' m\u00b2</span>' : '') +
                    '</div>' +
                    '<div class="featured-price">' + priceStr + '</div>' +
                    '</div>' +
                    '</a>';
            }).join('');

            var revealCards = grid.querySelectorAll('.featured-card.reveal');
            revealCards.forEach(function(card, idx) {
                card.setAttribute('data-reveal', idx % 2 === 0 ? 'up' : 'zoom');
            });

            if ('IntersectionObserver' in window) {
                var io = new IntersectionObserver(function(entries) {
                    entries.forEach(function(entry) {
                        if (!entry.isIntersecting) return;
                        entry.target.classList.add('revealed', 'is-visible', 'visible');
                        io.unobserve(entry.target);
                    });
                }, {
                    threshold: 0.14,
                    rootMargin: '0px 0px -8% 0px'
                });

                revealCards.forEach(function(card) {
                    io.observe(card);
                });
            } else {
                revealCards.forEach(function(card) {
                    card.classList.add('revealed', 'is-visible', 'visible');
                });
            }
        }
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initFeatured);
        } else {
            initFeatured();
        }
    })();
</script>


<!-- ═══════════════════════════════════════════════════════
     KUNDENSTIMMEN
     ═══════════════════════════════════════════════════════ -->
<!-- Google Reviews styles → premium.css -->

<?php $googleReviews = require __DIR__ . '/includes/google-reviews.php'; ?>
<section class="google-reviews-section testimonials-section" id="bewertungen">
    <div class="container">
        <div class="section-header-center reveal" style="text-align:center">
            <span class="section-tag">Kundenstimmen</span>
            <h2 class="section-heading">Was unsere <span class="accent">Kunden sagen</span></h2>
            <div class="divider-gold centered"></div>
            <p class="section-subheading">Echte Rezensionen unserer Kunden – direkt von Google.</p>

            <div style="display:flex;justify-content:center;margin-top:1.2rem;">
                <a class="google-aggregate-badge reveal" href="<?= GOOGLE_REVIEWS_URL ?>" target="_blank" rel="noopener noreferrer" style="text-decoration:none">
                    <span class="g-logo" aria-hidden="true">G</span>
                    <div>
                        <div class="agg-stars" aria-hidden="true">★★★★★</div>
                        <div class="agg-count"><?= GOOGLE_REVIEWS_COUNT_LABEL ?> Google-Rezensionen · alle ansehen</div>
                    </div>
                </a>
            </div>
        </div>

        <div class="reviews-slider-wrapper reveal">
            <div class="reviews-track" id="reviewsTrack">

                <?php foreach ($googleReviews as $rv): ?>
                    <article class="review-card">
                        <div class="review-card-header">
                            <div class="review-avatar"><?= htmlspecialchars(mb_strtoupper(mb_substr($rv['name'], 0, 1)), ENT_QUOTES) ?></div>
                            <div class="review-meta">
                                <div class="review-name"><?= htmlspecialchars($rv['name'], ENT_QUOTES) ?></div>
                                <div class="review-date">Google-Rezension</div>
                            </div>
                            <div class="review-google-badge">
                                <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                    <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4" />
                                    <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853" />
                                    <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84z" fill="#FBBC05" />
                                    <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335" />
                                </svg>
                                Google
                            </div>
                        </div>
                        <div class="review-stars" aria-label="5 von 5 Sternen">★★★★★</div>
                        <p class="review-text">„<?= htmlspecialchars($rv['text'], ENT_QUOTES) ?>“</p>
                        <?php if (!empty($rv['reply'])): ?>
                            <div class="company-response">
                                <strong>Antwort von Immobilien Ghumman</strong>
                                <?= htmlspecialchars($rv['reply'], ENT_QUOTES) ?>
                            </div>
                        <?php endif; ?>
                    </article>
                <?php endforeach; ?>

            </div><!-- /reviews-track -->
        </div><!-- /reviews-slider-wrapper -->

        <div class="reviews-controls">
            <button class="reviews-btn" id="reviewsPrev" aria-label="Zurück"><i class="fas fa-chevron-left"></i></button>
            <div class="reviews-dots" id="reviewsDots"></div>
            <button class="reviews-btn" id="reviewsNext" aria-label="Weiter"><i class="fas fa-chevron-right"></i></button>
        </div>
    </div>
</section>

<script>
    (function() {
        const track = document.getElementById('reviewsTrack');
        const dotsContainer = document.getElementById('reviewsDots');
        if (!track) return;
        const cards = track.querySelectorAll('.review-card');
        let perPage = window.innerWidth > 900 ? 3 : window.innerWidth > 600 ? 2 : 1;
        const pages = Math.ceil(cards.length / perPage);
        let current = 0;

        // Build dots
        for (let i = 0; i < pages; i++) {
            const d = document.createElement('button');
            d.className = 'reviews-dot' + (i === 0 ? ' active' : '');
            d.setAttribute('aria-label', 'Seite ' + (i + 1));
            d.addEventListener('click', () => goTo(i));
            dotsContainer.appendChild(d);
        }

        function goTo(page) {
            current = Math.max(0, Math.min(page, pages - 1));
            const cardWidth = cards[0].getBoundingClientRect().width;
            const gap = 20;
            track.style.transform = 'translateX(-' + (current * perPage * (cardWidth + gap)) + 'px)';
            dotsContainer.querySelectorAll('.reviews-dot').forEach((d, i) => d.classList.toggle('active', i === current));
        }

        document.getElementById('reviewsPrev').addEventListener('click', () => goTo(current - 1));
        document.getElementById('reviewsNext').addEventListener('click', () => goTo(current + 1));

        // Auto-play
        let timer = setInterval(() => goTo((current + 1) % pages), 6000);
        track.closest('.reviews-slider-wrapper').addEventListener('mouseenter', () => clearInterval(timer));
        track.closest('.reviews-slider-wrapper').addEventListener('mouseleave', () => {
            timer = setInterval(() => goTo((current + 1) % pages), 6000);
        });

        // Responsive recalc
        window.addEventListener('resize', () => {
            perPage = window.innerWidth > 900 ? 3 : window.innerWidth > 600 ? 2 : 1;
            goTo(0);
        });
    })();
</script>


<!-- ═══════════════════════════════════════════════════════
     FAQ – Häufig gestellte Fragen (TABBED)
     ═══════════════════════════════════════════════════════ -->
<section class="faq-section" id="faq" style="padding:var(--section-gap) 0;background:var(--premium-offwhite)">
    <div class="container">
        <div class="section-header-center reveal">
            <span class="section-tag">FAQ</span>
            <h2 class="section-heading">Häufig gestellte <span class="accent">Fragen</span></h2>
            <div class="divider-gold centered"></div>
            <p class="section-subheading">Wählen Sie eine Leistung und erhalten Sie direkte Antworten auf Ihre Fragen.</p>
        </div>

        <!-- ── Tab Buttons ── -->
        <div class="faq-tabs reveal" role="tablist">
            <button class="faq-tab active" role="tab" aria-selected="true" data-tab="hausverwaltung">
                <i class="fas fa-building"></i> Hausverwaltung
            </button>
            <button class="faq-tab" role="tab" aria-selected="false" data-tab="kauf-verkauf">
                <i class="fas fa-home"></i> Immobilienkauf &amp; Verkauf
            </button>
            <button class="faq-tab" role="tab" aria-selected="false" data-tab="bewertung">
                <i class="fas fa-magnifying-glass-dollar"></i> Immobilienbewertung
            </button>
            <button class="faq-tab" role="tab" aria-selected="false" data-tab="vermietung">
                <i class="fas fa-key"></i> Vermietung
            </button>
        </div>

        <!-- ── Tab Panels ── -->
        <div class="faq-panels">

            <!-- PANEL: Hausverwaltung -->
            <div class="faq-panel active" data-panel="hausverwaltung" role="tabpanel">
                <div class="faq-acc-item">
                    <button class="faq-acc-trigger">Welche Leistungen sind in der Hausverwaltung enthalten?<span class="faq-acc-icon"><i class="fas fa-plus"></i></span></button>
                    <div class="faq-acc-body">
                        <div class="faq-acc-inner">Unsere Hausverwaltung umfasst Mietverwaltung, Nebenkostenabrechnung, Instandhaltungskoordination, Eigentümerversammlungen sowie rechtliche Betreuung. Wir übernehmen die vollständige Kommunikation mit Mietern, Handwerkern und Behörden.</div>
                    </div>
                </div>
                <div class="faq-acc-item">
                    <button class="faq-acc-trigger">Für welche Objektarten bieten Sie Hausverwaltung an?<span class="faq-acc-icon"><i class="fas fa-plus"></i></span></button>
                    <div class="faq-acc-body">
                        <div class="faq-acc-inner">Wir verwalten Eigentumswohnungen, Mehrfamilienhäuser, Wohnanlagen (WEG) sowie Gewerbeimmobilien im gesamten Rhein-Main-Gebiet. Auch für einzelne Wohnungseinheiten bieten wir eine flexible Mietverwaltung an.</div>
                    </div>
                </div>
                <div class="faq-acc-item">
                    <button class="faq-acc-trigger">Wie hoch sind die Kosten für die Hausverwaltung?<span class="faq-acc-icon"><i class="fas fa-plus"></i></span></button>
                    <div class="faq-acc-body">
                        <div class="faq-acc-inner">Die Verwaltungsgebühr richtet sich nach Objektgröße, Lage und Leistungsumfang. Wir erstellen Ihnen ein individuelles Angebot – transparent und ohne versteckte Kosten. Eine Erstberatung ist kostenlos.</div>
                    </div>
                </div>
                <div class="faq-acc-item">
                    <button class="faq-acc-trigger">Wie schnell reagieren Sie bei Mieteranfragen oder Störungen?<span class="faq-acc-icon"><i class="fas fa-plus"></i></span></button>
                    <div class="faq-acc-body">
                        <div class="faq-acc-inner">Mieteranfragen beantworten wir in der Regel innerhalb von 24 Stunden. Bei dringenden Störungen (z. B. Wasserrohrbruch) reagieren wir sofort und koordinieren die notwendigen Handwerker eigenständig.</div>
                    </div>
                </div>
                <div class="faq-acc-item">
                    <button class="faq-acc-trigger">Was passiert bei einem Mietausfall?<span class="faq-acc-icon"><i class="fas fa-plus"></i></span></button>
                    <div class="faq-acc-body">
                        <div class="faq-acc-inner">Wir mahnen säumige Mieter fristgerecht an und leiten bei Bedarf rechtliche Schritte ein. Wir arbeiten eng mit spezialisierten Rechtsanwälten zusammen und begleiten Sie durch den gesamten Prozess – bis zur Lösung.</div>
                    </div>
                </div>
                <div class="faq-acc-item">
                    <button class="faq-acc-trigger">Übernehmen Sie auch die Nebenkostenabrechnung?<span class="faq-acc-icon"><i class="fas fa-plus"></i></span></button>
                    <div class="faq-acc-body">
                        <div class="faq-acc-inner">Ja, die jährliche Betriebskostenabrechnung ist Bestandteil unserer Verwaltungsleistung. Wir erstellen sie fristgerecht, prüfen alle Belege und stellen sie Mietern und Eigentümern nachvollziehbar zur Verfügung.</div>
                    </div>
                </div>
                <div class="faq-acc-item">
                    <button class="faq-acc-trigger">Organisieren Sie auch Eigentümerversammlungen?<span class="faq-acc-icon"><i class="fas fa-plus"></i></span></button>
                    <div class="faq-acc-body">
                        <div class="faq-acc-inner">Ja. Wir laden ein, erstellen die Tagesordnung, leiten die Versammlung, protokollieren Beschlüsse und setzen diese anschließend um. Alles läuft rechtssicher nach WEG-Vorgaben.</div>
                    </div>
                </div>
                <div class="faq-acc-item">
                    <button class="faq-acc-trigger">Wie läuft die Übernahme der Verwaltung ab?<span class="faq-acc-icon"><i class="fas fa-plus"></i></span></button>
                    <div class="faq-acc-body">
                        <div class="faq-acc-inner">Nach Vertragsunterzeichnung koordinieren wir die vollständige Übergabe vom Vorverwalter oder direkt vom Eigentümer. Wir übernehmen alle relevanten Unterlagen, informieren die Mieter und sind innerhalb weniger Tage voll einsatzbereit.</div>
                    </div>
                </div>
            </div>

            <!-- PANEL: Immobilienkauf & Verkauf -->
            <div class="faq-panel" data-panel="kauf-verkauf" role="tabpanel">
                <div class="faq-acc-item">
                    <button class="faq-acc-trigger">Was kostet die Vermittlung beim Immobilienverkauf?<span class="faq-acc-icon"><i class="fas fa-plus"></i></span></button>
                    <div class="faq-acc-body">
                        <div class="faq-acc-inner">Unsere Maklerprovision richtet sich nach der gesetzlichen Regelung und wird transparent vorab mit Ihnen vereinbart. Käufer und Verkäufer teilen sich in der Regel die Courtage zu gleichen Teilen. Eine erste Beratung ist stets kostenlos und unverbindlich.</div>
                    </div>
                </div>
                <div class="faq-acc-item">
                    <button class="faq-acc-trigger">Wie lange dauert der Verkauf einer Immobilie?<span class="faq-acc-icon"><i class="fas fa-plus"></i></span></button>
                    <div class="faq-acc-body">
                        <div class="faq-acc-inner">Die Dauer hängt von Lage, Preis und Marktsituation ab. Im Rhein-Main-Gebiet erzielen wir durchschnittlich einen Verkaufsabschluss innerhalb von 4–10 Wochen nach Vermarktungsstart – dank unseres aktiven Interessentennetzwerks.</div>
                    </div>
                </div>
                <div class="faq-acc-item">
                    <button class="faq-acc-trigger">Wie wird der optimale Verkaufspreis meiner Immobilie ermittelt?<span class="faq-acc-icon"><i class="fas fa-plus"></i></span></button>
                    <div class="faq-acc-body">
                        <div class="faq-acc-inner">Wir erstellen eine fundierte Marktpreisanalyse auf Basis aktueller Vergleichsobjekte, Lagekriterien, Ausstattung und Zustand Ihrer Immobilie. Ziel ist ein realistischer Preis, der maximale Nachfrage erzeugt und keine Einbußen hinterlässt.</div>
                    </div>
                </div>
                <div class="faq-acc-item">
                    <button class="faq-acc-trigger">Welche Unterlagen werden für den Verkauf benötigt?<span class="faq-acc-icon"><i class="fas fa-plus"></i></span></button>
                    <div class="faq-acc-body">
                        <div class="faq-acc-inner">Typischerweise benötigen wir Grundbuchauszug, Energieausweis, Baupläne, Wohnflächenberechnung und ggf. Teilungserklärung. Wir unterstützen Sie dabei, fehlende Dokumente zu beschaffen – schnell und unkompliziert.</div>
                    </div>
                </div>
                <div class="faq-acc-item">
                    <button class="faq-acc-trigger">Wie präsentieren Sie meine Immobilie potenziellen Käufern?<span class="faq-acc-icon"><i class="fas fa-plus"></i></span></button>
                    <div class="faq-acc-body">
                        <div class="faq-acc-inner">Wir erstellen professionelle Fotos, ein ansprechendes Exposé und vermarkten Ihre Immobilie auf allen relevanten Portalen (ImmobilienScout24, Immowelt u. a.) sowie in unserem eigenen Käufernetzwerk.</div>
                    </div>
                </div>
                <div class="faq-acc-item">
                    <button class="faq-acc-trigger">Begleiten Sie uns auch zum Notartermin?<span class="faq-acc-icon"><i class="fas fa-plus"></i></span></button>
                    <div class="faq-acc-body">
                        <div class="faq-acc-inner">Ja, wir begleiten Sie selbstverständlich zum Notartermin und stehen als Ansprechpartner bereit. Wir koordinieren außerdem die Kommunikation mit Notar, Käufer und Banken bis zur vollständigen Vertragsabwicklung.</div>
                    </div>
                </div>
                <div class="faq-acc-item">
                    <button class="faq-acc-trigger">Kann ich meine Immobilie auch privat verkaufen?<span class="faq-acc-icon"><i class="fas fa-plus"></i></span></button>
                    <div class="faq-acc-body">
                        <div class="faq-acc-inner">Das ist möglich, birgt jedoch Risiken: Falsche Preisansätze, rechtliche Fehler oder schlechte Käuferqualifikation können teuer werden. Ein Makler sichert Sie rechtlich ab, erzielt in der Regel höhere Preise und spart Ihnen wertvolle Zeit.</div>
                    </div>
                </div>
                <div class="faq-acc-item">
                    <button class="faq-acc-trigger">Helfen Sie auch beim Kauf einer Immobilie?<span class="faq-acc-icon"><i class="fas fa-plus"></i></span></button>
                    <div class="faq-acc-body">
                        <div class="faq-acc-inner">Ja. Wir begleiten Kaufinteressenten von der Objektsuche über die Besichtigung bis zur Vertragsunterzeichnung. Durch unser breites Netzwerk haben wir auch Zugang zu Objekten, die noch nicht öffentlich inseriert sind.</div>
                    </div>
                </div>
            </div>

            <!-- PANEL: Immobilienbewertung -->
            <div class="faq-panel" data-panel="bewertung" role="tabpanel">
                <div class="faq-acc-item">
                    <button class="faq-acc-trigger">Ist die Immobilienbewertung wirklich kostenlos?<span class="faq-acc-icon"><i class="fas fa-plus"></i></span></button>
                    <div class="faq-acc-body">
                        <div class="faq-acc-inner">Ja, unsere Erstbewertung ist vollständig kostenlos und unverbindlich. Wir ermitteln den aktuellen Marktwert Ihrer Immobilie auf Basis von Vergleichsdaten und lokaler Marktkenntnis – ohne versteckte Kosten oder Folgeverpflichtungen.</div>
                    </div>
                </div>
                <div class="faq-acc-item">
                    <button class="faq-acc-trigger">Wie genau ist eine Online-Bewertung?<span class="faq-acc-icon"><i class="fas fa-plus"></i></span></button>
                    <div class="faq-acc-body">
                        <div class="faq-acc-inner">Unsere Online-Bewertung liefert eine fundierte erste Einschätzung. Für eine präzise Verkehrswertermittlung empfehlen wir zusätzlich eine persönliche Besichtigung durch unsere Experten – ebenfalls kostenlos.</div>
                    </div>
                </div>
                <div class="faq-acc-item">
                    <button class="faq-acc-trigger">Welche Faktoren beeinflussen den Wert meiner Immobilie?<span class="faq-acc-icon"><i class="fas fa-plus"></i></span></button>
                    <div class="faq-acc-body">
                        <div class="faq-acc-inner">Lage, Größe, Baujahr, Ausstattung, Zustand, Energieeffizienz und aktuelle Marktlage sind die wichtigsten Faktoren. Auch Mikrolage, Infrastruktur und Nachfrage in der Region spielen eine entscheidende Rolle.</div>
                    </div>
                </div>
                <div class="faq-acc-item">
                    <button class="faq-acc-trigger">Wie lange dauert eine Bewertung?<span class="faq-acc-icon"><i class="fas fa-plus"></i></span></button>
                    <div class="faq-acc-body">
                        <div class="faq-acc-inner">Die Online-Bewertung liefert Ihnen ein erstes Ergebnis in wenigen Minuten. Die ausführliche Bewertung mit persönlicher Besichtigung und schriftlichem Bericht erhalten Sie in der Regel innerhalb von 24–48 Stunden.</div>
                    </div>
                </div>
                <div class="faq-acc-item">
                    <button class="faq-acc-trigger">Benötige ich ein offizielles Gutachten für den Verkauf?<span class="faq-acc-icon"><i class="fas fa-plus"></i></span></button>
                    <div class="faq-acc-body">
                        <div class="faq-acc-inner">Für den normalen Verkauf ist kein gerichtsfestes Gutachten notwendig. Unsere Marktbewertung reicht dafür vollkommen aus. Ein zertifiziertes Gutachten wird nur bei Erbschafts- oder Scheidungsverfahren benötigt.</div>
                    </div>
                </div>
                <div class="faq-acc-item">
                    <button class="faq-acc-trigger">Was ist der Unterschied zwischen Verkehrswert und Marktwert?<span class="faq-acc-icon"><i class="fas fa-plus"></i></span></button>
                    <div class="faq-acc-body">
                        <div class="faq-acc-inner">Beide Begriffe bezeichnen im Wesentlichen dasselbe: den Preis, der bei einem freien Verkauf unter normalen Bedingungen erzielt werden kann. Der Begriff „Verkehrswert" ist juristisch (§ 194 BauGB) verankert, „Marktwert" wird allgemein und international verwendet.</div>
                    </div>
                </div>
                <div class="faq-acc-item">
                    <button class="faq-acc-trigger">Kann ich die Bewertung für Erbschafts- oder Scheidungszwecke nutzen?<span class="faq-acc-icon"><i class="fas fa-plus"></i></span></button>
                    <div class="faq-acc-body">
                        <div class="faq-acc-inner">Für rechtliche Verfahren wird ein zertifiziertes Gutachten eines öffentlich bestellten Sachverständigen benötigt. Wir vermitteln Ihnen auf Wunsch einen geeigneten Gutachter und bereiten alle Informationen dafür vor.</div>
                    </div>
                </div>
                <div class="faq-acc-item">
                    <button class="faq-acc-trigger">Wie aktuell sind die Vergleichsdaten für die Bewertung?<span class="faq-acc-icon"><i class="fas fa-plus"></i></span></button>
                    <div class="faq-acc-body">
                        <div class="faq-acc-inner">Wir arbeiten mit aktuellen Marktdaten, Kaufpreissammlungen der Gutachterausschüsse und laufend aktualisierten Vergleichsangeboten aus unserer Praxis – stets aktuell und regional präzise.</div>
                    </div>
                </div>
            </div>

            <!-- PANEL: Vermietung -->
            <div class="faq-panel" data-panel="vermietung" role="tabpanel">
                <div class="faq-acc-item">
                    <button class="faq-acc-trigger">Wie finde ich den richtigen Mieter für meine Wohnung?<span class="faq-acc-icon"><i class="fas fa-plus"></i></span></button>
                    <div class="faq-acc-body">
                        <div class="faq-acc-inner">Wir übernehmen die komplette Mietersuche: professionelles Inserat, Interessentenauswahl, Bonitätsprüfung und Besichtigungskoordination. So finden Sie schnell einen zuverlässigen Mieter, der zu Ihrer Immobilie passt.</div>
                    </div>
                </div>
                <div class="faq-acc-item">
                    <button class="faq-acc-trigger">Was kostet die Mieter-Vermittlung?<span class="faq-acc-icon"><i class="fas fa-plus"></i></span></button>
                    <div class="faq-acc-body">
                        <div class="faq-acc-inner">Seit der Mietrechtsreform trägt die Maklerprovision bei der Vermietung in der Regel der Auftraggeber (Vermieter). Die Höhe der Provision besprechen wir transparent im Erstgespräch – ohne versteckte Kosten.</div>
                    </div>
                </div>
                <div class="faq-acc-item">
                    <button class="faq-acc-trigger">Welche Unterlagen muss ein Mieter vorlegen?<span class="faq-acc-icon"><i class="fas fa-plus"></i></span></button>
                    <div class="faq-acc-body">
                        <div class="faq-acc-inner">Wir fordern standardmäßig Selbstauskunft, Einkommensnachweise, Schufa-Auskunft und ggf. Bürgschaft an. So stellen wir sicher, dass Ihr Mieter wirtschaftlich zuverlässig ist.</div>
                    </div>
                </div>
                <div class="faq-acc-item">
                    <button class="faq-acc-trigger">Was ist im Mietvertrag besonders wichtig?<span class="faq-acc-icon"><i class="fas fa-plus"></i></span></button>
                    <div class="faq-acc-body">
                        <div class="faq-acc-inner">Ein rechtssicherer Mietvertrag regelt Miethöhe, Nebenkosten, Kündigungsfristen, Schönheitsreparaturen und Hausordnung eindeutig. Wir erstellen oder prüfen den Mietvertrag für Sie nach aktueller Rechtslage.</div>
                    </div>
                </div>
                <div class="faq-acc-item">
                    <button class="faq-acc-trigger">Wie hoch darf ich die Miete ansetzen?<span class="faq-acc-icon"><i class="fas fa-plus"></i></span></button>
                    <div class="faq-acc-body">
                        <div class="faq-acc-inner">Die Miethöhe richtet sich nach dem lokalen Mietspiegel, Lage, Ausstattung und Zustand der Wohnung. Wir ermitteln die marktgerechte Miete für Ihre Immobilie und achten dabei auf die Einhaltung gesetzlicher Grenzen.</div>
                    </div>
                </div>
                <div class="faq-acc-item">
                    <button class="faq-acc-trigger">Gilt die Mietpreisbremse auch in Bad Vilbel und Umgebung?<span class="faq-acc-icon"><i class="fas fa-plus"></i></span></button>
                    <div class="faq-acc-body">
                        <div class="faq-acc-inner">Ja, die Mietpreisbremse gilt in Hessen in ausgewiesenen Gebieten mit angespanntem Wohnungsmarkt – darunter auch Teile des Rhein-Main-Gebiets. Wir prüfen für Sie, welche Regelungen konkret für Ihre Immobilie gelten.</div>
                    </div>
                </div>
                <div class="faq-acc-item">
                    <button class="faq-acc-trigger">Wie läuft eine Wohnungsübergabe ab?<span class="faq-acc-icon"><i class="fas fa-plus"></i></span></button>
                    <div class="faq-acc-body">
                        <div class="faq-acc-inner">Bei der Übergabe dokumentieren wir gemeinsam den Zustand der Wohnung in einem detaillierten Übergabeprotokoll, notieren alle Zählerstände und übergeben die Schlüssel. Dies schützt beide Seiten rechtlich zuverlässig.</div>
                    </div>
                </div>
                <div class="faq-acc-item">
                    <button class="faq-acc-trigger">Was passiert, wenn ein Mieter nicht zahlt?<span class="faq-acc-icon"><i class="fas fa-plus"></i></span></button>
                    <div class="faq-acc-body">
                        <div class="faq-acc-inner">Wir handeln sofort: Mahnung, fristlose Kündigung bei wiederholtem Verzug und ggf. Einleitung eines Räumungsverfahrens – in enger Abstimmung mit Fachanwälten. Regelmäßige Bonitätsprüfung bei der Mieterauswahl minimiert dieses Risiko von vornherein.</div>
                    </div>
                </div>
            </div>

        </div><!-- /.faq-panels -->

        <div style="text-align:center;margin-top:2.5rem" class="reveal">
            <a href="<?= BASE ?>/analyse-beratung" class="btn-premium">
                <i class="fas fa-comments"></i> Persönliche Beratung anfragen
            </a>
        </div>
    </div>
</section>

<!-- FAQ styles -> premium.css -->

<script>
    (function() {
        /* ── Tabs ── */
        var tabs = document.querySelectorAll('.faq-tab');
        var panels = document.querySelectorAll('.faq-panel');
        tabs.forEach(function(tab) {
            tab.addEventListener('click', function() {
                tabs.forEach(function(t) {
                    t.classList.remove('active');
                    t.setAttribute('aria-selected', 'false');
                });
                panels.forEach(function(p) {
                    p.classList.remove('active');
                });
                tab.classList.add('active');
                tab.setAttribute('aria-selected', 'true');
                var target = document.querySelector('.faq-panel[data-panel="' + tab.dataset.tab + '"]');
                if (target) target.classList.add('active');
            });
        });
        /* ── Accordion ── */
        document.querySelectorAll('.faq-acc-trigger').forEach(function(btn) {
            btn.addEventListener('click', function() {
                var item = btn.closest('.faq-acc-item');
                var panel = item.closest('.faq-panel');
                /* close siblings in same panel */
                panel.querySelectorAll('.faq-acc-item.open').forEach(function(open) {
                    if (open !== item) open.classList.remove('open');
                });
                item.classList.toggle('open');
            });
        });
    })();
</script>


<!-- Kontakt styles -> premium.css -->


<section class="kontakt-section-new" id="kontakt">
    <div class="container">
        <div class="section-header-center reveal" style="text-align:center">
            <span class="section-tag">Kontakt</span>
            <h2 class="section-heading">Sprechen Sie mit <span class="accent">uns</span></h2>
            <div class="divider-gold centered"></div>
            <p class="section-subheading">Kostenlos und unverbindlich – wir beraten Sie gerne persönlich.</p>
        </div>

        <div class="kontakt-two-col reveal">
            <!-- Left: Contact Info -->
            <div class="kontakt-info-col">
                <h3>Immobilien Ghumman</h3>
                <p class="k-tagline">Ihr Immobilienexperte in Bad Vilbel und dem Rhein-Main-Gebiet. Rufen Sie uns an oder schreiben Sie uns – wir melden uns schnellstmöglich.</p>

                <div class="kontakt-item">
                    <div class="kontakt-icon-wrap"><i class="fas fa-phone-alt"></i></div>
                    <div class="kontakt-item-text">
                        <strong>Telefon</strong>
                        <a href="tel:+4916098787878">0160 98787878</a>
                    </div>
                </div>

                <div class="kontakt-item">
                    <div class="kontakt-icon-wrap"><i class="fas fa-envelope"></i></div>
                    <div class="kontakt-item-text">
                        <strong>E-Mail</strong>
                        <a href="#kontaktFormNew">Kontaktformular öffnen</a>
                    </div>
                </div>

                <div class="kontakt-item">
                    <div class="kontakt-icon-wrap"><i class="fas fa-map-marker-alt"></i></div>
                    <div class="kontakt-item-text">
                        <strong>Standort</strong>
                        <span>Bad Vilbel, Hessen</span>
                    </div>
                </div>

                <div class="kontakt-item">
                    <div class="kontakt-icon-wrap"><i class="fas fa-clock"></i></div>
                    <div class="kontakt-item-text">
                        <strong>Erreichbarkeit</strong>
                        <span>Mo – Fr: 9:00 – 18:00 Uhr</span>
                    </div>
                </div>

                <hr class="kontakt-divider">

                <a href="https://wa.me/4916098787878?text=Hallo%2C%20ich%20interessiere%20mich%20f%C3%BCr%20Ihre%20Immobiliendienstleistungen." class="whatsapp-btn" target="_blank" rel="noopener noreferrer">
                    <i class="fab fa-whatsapp" style="font-size:1.2rem"></i> Via WhatsApp schreiben
                </a>
            </div>

            <!-- Right: Contact Form -->
            <div class="kontakt-form-col">

                <!-- Erfolg-Panel -->
                <div id="kformSuccess" style="display:none">
                    <span class="kform-success-icon"><i class="fas fa-check-circle"></i></span>
                    <h4 class="kform-success-title">Vielen Dank!</h4>
                    <p class="kform-success-text">Ihre Anfrage wurde erfolgreich übermittelt. Wir melden uns schnellstmöglich bei Ihnen.<br>Eine Bestätigungs-E-Mail wurde an Ihre Adresse gesendet.</p>
                    <a href="tel:+4916098787878" class="kform-success-cta"><i class="fas fa-phone"></i> 0160 9878 7878</a>
                </div>

                <!-- Formular-Panel -->
                <div id="kformFormWrap">
                    <div class="kform-header">
                        <h3><i class="fas fa-paper-plane" style="color:#3b82f6;margin-right:8px;font-size:1rem"></i>Jetzt kostenlos anfragen</h3>
                        <p class="kform-subtitle">Wir antworten in der Regel innerhalb von 24 Stunden.</p>
                    </div>

                    <form id="kontaktFormNew" novalidate autocomplete="on">
                        <?= csrf_field() ?>
                        <input type="text" name="website" style="display:none" tabindex="-1" autocomplete="off">
                        <input type="hidden" name="form_type" value="Allgemeine Kontaktanfrage (Homepage)">

                        <div class="kform-grid">
                            <div class="kform-field">
                                <label for="kf_name">Name *</label>
                                <input type="text" id="kf_name" name="name" placeholder="Ihr vollständiger Name" required>
                            </div>
                            <div class="kform-field">
                                <label for="kf_email">E-Mail *</label>
                                <input type="email" id="kf_email" name="email" placeholder="ihre@email.de" required>
                            </div>
                            <div class="kform-field">
                                <label for="kf_phone">Telefonnummer</label>
                                <input type="tel" id="kf_phone" name="phone" placeholder="0160 00000000">
                            </div>
                            <div class="kform-field">
                                <label for="kf_subject">Betreff</label>
                                <input type="text" id="kf_subject" name="subject" placeholder="z.B. Hausverwaltung">
                            </div>
                            <div class="kform-field kform-full">
                                <label for="kf_message">Nachricht</label>
                                <textarea id="kf_message" name="message" placeholder="Beschreiben Sie kurz Ihr Anliegen…"></textarea>
                            </div>
                            <?php if (defined('TURNSTILE_ENABLED') && TURNSTILE_ENABLED): ?>
                                <div class="kform-field kform-full">
                                    <label><i class="fas fa-shield-alt" style="color:#3b82f6;margin-right:5px"></i>Sicherheitsprüfung *</label>
                                    <div class="cf-turnstile" data-sitekey="<?= htmlspecialchars(TURNSTILE_SITE_KEY, ENT_QUOTES) ?>"></div>
                                    <small class="kform-captcha-note">Zum Schutz vor Spam setzen wir Cloudflare Turnstile ein.</small>
                                </div>
                            <?php endif; ?>
                        </div>

                        <button type="submit" id="kformSubmitBtn" class="kform-submit-btn">
                            <span class="kbtn-text"><i class="fas fa-paper-plane" style="margin-right:8px"></i>Kostenlose Beratung sichern</span>
                            <span class="kbtn-spinner"><i class="fas fa-spinner fa-spin" style="margin-right:8px"></i>Wird gesendet…</span>
                        </button>

                        <p class="kform-trust"><i class="fas fa-lock"></i> Ihre Daten werden vertraulich behandelt und nicht weitergegeben.</p>

                        <div id="kformError" role="alert" aria-live="polite" style="display:none">
                            <i class="fas fa-exclamation-circle"></i>
                            <span id="kformErrorMsg">Ein Fehler ist aufgetreten. Bitte versuchen Sie es erneut.</span>
                            <div id="kformMailtoFallback" style="display:none">
                                <p>Alternativ können Sie uns direkt per E-Mail kontaktieren:</p>
                                <a href="mailto:info@immobilienghumman.de?subject=Anfrage" id="kformMailtoBtn" class="kform-mailto-btn"><i class="fas fa-envelope"></i> E-Mail-App öffnen</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    (function() {
        const form = document.getElementById('kontaktFormNew');
        if (!form) return;

        let failCount = 0;

        const fieldErr = (id, on) => {
            document.getElementById(id)?.closest('.kform-field')?.classList.toggle('has-error', on);
        };

        form.addEventListener('submit', async (e) => {
            e.preventDefault();

            const nameF = document.getElementById('kf_name');
            const emailF = document.getElementById('kf_email');
            [nameF, emailF].forEach(f => f?.closest('.kform-field')?.classList.remove('has-error'));

            let ok = true;
            if (!nameF?.value.trim()) {
                fieldErr('kf_name', true);
                ok = false;
            }
            if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailF?.value.trim() || '')) {
                fieldErr('kf_email', true);
                ok = false;
            }
            if (!ok) {
                showErr('Bitte füllen Sie alle Pflichtfelder korrekt aus.');
                return;
            }

            const btn = document.getElementById('kformSubmitBtn');
            const formWrap = document.getElementById('kformFormWrap');
            btn.disabled = true;
            btn.classList.add('is-loading');
            document.getElementById('kformError').style.display = 'none';

            let succeeded = false;
            try {
                const apiBase = (window.API_BASE_URL || 'https://www.immobilienghumman.de/api').replace(/\/$/, '');
                const ctrl = new AbortController();
                const tmo = setTimeout(() => ctrl.abort(), 70000);
                const res = await fetch(`${apiBase}/contact.php?action=general`, {
                    method: 'POST',
                    body: new FormData(form),
                    credentials: 'same-origin',
                    signal: ctrl.signal,
                });
                clearTimeout(tmo);

                const ct = res.headers.get('content-type') || '';
                const raw = await res.text();
                let json = null;
                if (ct.includes('application/json') || raw.trim().startsWith('{')) {
                    try {
                        json = JSON.parse(raw.replace(/^\uFEFF+/, '').trim());
                    } catch (parseErr) {
                        json = null;
                    }
                }

                if (res.ok && json?.success) {
                    succeeded = true;
                    formWrap.style.display = 'none';
                    const sv = document.getElementById('kformSuccess');
                    sv.style.display = 'block';
                    sv.scrollIntoView({
                        behavior: 'smooth',
                        block: 'nearest'
                    });
                } else {
                    failCount++;
                    showErr(json?.message || 'Serverfehler (HTTP\u00a0' + res.status + '). Bitte versuchen Sie es erneut.');
                    if (failCount >= 1) buildMailto();
                }
            } catch (err) {
                failCount++;
                if (err && err.name === 'AbortError') {
                    showErr('Die Anfrage dauert ungewoehnlich lange. Bitte versuchen Sie es erneut oder kontaktieren Sie uns direkt per E-Mail.');
                } else {
                    showErr('Verbindungsfehler. Bitte prüfen Sie Ihre Verbindung und versuchen Sie es erneut.');
                }
                buildMailto();
            } finally {
                if (!succeeded) {
                    btn.disabled = false;
                    btn.classList.remove('is-loading');
                }
            }
        });

        function showErr(msg) {
            document.getElementById('kformErrorMsg').textContent = msg;
            const el = document.getElementById('kformError');
            el.style.display = 'block';
            el.scrollIntoView({
                behavior: 'smooth',
                block: 'nearest'
            });
        }

        function buildMailto() {
            const n = document.getElementById('kf_name')?.value || '';
            const e = document.getElementById('kf_email')?.value || '';
            const p = document.getElementById('kf_phone')?.value || '';
            const s = document.getElementById('kf_subject')?.value || 'Anfrage';
            const m = document.getElementById('kf_message')?.value || '';
            const body = `Sehr geehrte Damen und Herren,\n\n${m}\n\nMit freundlichen Grüßen\n${n}${p ? '\nTel.: ' + p : ''}\n${e}`;
            const link = document.getElementById('kformMailtoBtn');
            if (link) {
                link.href = `mailto:info@immobilienghumman.de?subject=${encodeURIComponent(s)}&body=${encodeURIComponent(body)}`;
                document.getElementById('kformMailtoFallback').style.display = 'block';
            }
        }

        document.getElementById('kformMailtoBtn')?.addEventListener('click', function(e) {
            e.preventDefault();
            buildMailto();
            if (this.href) {
                window.location.href = this.href;
            }
        });

        form.querySelectorAll('input, textarea').forEach(el => {
            el.addEventListener('input', () => el.closest('.kform-field')?.classList.remove('has-error'));
        });
    })();
</script>


<button id="backToTop" aria-label="Nach oben scrollen">
    <i class="fas fa-chevron-up"></i>
</button>

<script>
    /* ── Video Modal (Hero-Button "Video ansehen") ── */
    (function() {
        var modal = document.getElementById('videoModal');
        var video = document.getElementById('heroVideo');
        var playBtn = document.getElementById('heroPlayBtn');
        var closeBtn = document.getElementById('videoModalClose');
        var backdrop = document.getElementById('videoModalBackdrop');
        if (!modal || !video || !playBtn) return;

        function openModal() {
            modal.classList.add('is-open');
            document.body.style.overflow = 'hidden';
            video.play().catch(function() {
                /* autoplay blocked – user must press play manually */
            });
            closeBtn && closeBtn.focus();
        }

        function closeModal() {
            modal.classList.remove('is-open');
            document.body.style.overflow = '';
            video.pause();
            video.currentTime = 0;
        }

        playBtn.addEventListener('click', openModal);
        closeBtn && closeBtn.addEventListener('click', closeModal);
        backdrop && backdrop.addEventListener('click', closeModal);
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && modal.classList.contains('is-open')) closeModal();
        });
    })();
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>