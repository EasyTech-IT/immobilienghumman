<?php
require_once __DIR__ . '/includes/config.php';
$pageTitle       = 'Immobilienangebote – Immobilien Ghumman';
$pageDescription = 'Alle verfügbaren Immobilienangebote von Immobilien Ghumman. Häuser, Wohnungen und Gewerbeobjekte in Bad Vilbel und der Rhein-Main-Region.';
$pageKeywords    = 'Immobilien kaufen Bad Vilbel, Wohnung mieten Frankfurt, Haus kaufen Rhein-Main, Immobilienangebote';
$activeNav       = 'immobilien';
$ogUrl           = 'https://www.immobilienghumman.de/immobilien-angebote';

$extraHead = <<<'ENDCSS'
<style>
    /* ===========================
       ANGEBOTE PAGE STYLES
       =========================== */

    /* Hero Section */
    .angebote-hero {
        background: url('https://images.pexels.com/photos/1396122/pexels-photo-1396122.jpeg') center/cover;
        color: white;
        padding: 8rem 0 5rem;
        text-align: center;
        position: relative;
        overflow: hidden;
    }

    .angebote-hero::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0; bottom: 0;
        background: linear-gradient(160deg, rgba(10, 40, 100, 0.45) 0%, rgba(13, 65, 145, 0.35) 100%);
        z-index: 0;
    }

    .angebote-hero .container {
        position: relative;
        z-index: 1;
    }

    .angebote-hero h1 {
        font-size: 2.75rem;
        font-weight: 800;
        margin-bottom: 1rem;
        line-height: 1.2;
        color: #ffffff;
        text-shadow: 0 2px 16px rgba(0,0,0,0.6);
    }

    .angebote-hero p {
        font-size: 1.125rem;
        opacity: 1;
        color: #ffffff;
        max-width: 600px;
        margin: 0 auto 2rem;
        text-shadow: 0 1px 8px rgba(0,0,0,0.5);
    }

    .hero-stats {
        display: flex;
        justify-content: center;
        gap: 3rem;
        margin-top: 2rem;
        flex-wrap: wrap;
    }

    .hero-stat {
        text-align: center;
    }

    .hero-stat .stat-number {
        font-size: 2rem;
        font-weight: 700;
        display: block;
        color: #ffffff;
        text-shadow: 0 1px 8px rgba(0,0,0,0.5);
    }

    .hero-stat .stat-label {
        font-size: 0.875rem;
        color: rgba(255,255,255,0.95);
    }

    /* Filter Bar */
    .filter-bar {
        background: white;
        padding: 1.25rem 0;
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        position: sticky;
        top: 0;
        z-index: 100;
        border-bottom: 1px solid #e2e8f0;
    }

    .filter-bar .container {
        display: flex;
        align-items: center;
        gap: 1rem;
        flex-wrap: wrap;
    }

    .filter-label {
        font-weight: 600;
        color: #475569;
        font-size: 0.875rem;
        white-space: nowrap;
    }

    .filter-buttons {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
        flex: 1;
    }

    .filter-btn {
        padding: 0.5rem 1.25rem;
        border: 2px solid #e2e8f0;
        border-radius: 50px;
        background: white;
        color: #64748b;
        font-weight: 500;
        font-size: 0.875rem;
        cursor: pointer;
        transition: all 0.2s ease;
        font-family: inherit;
    }

    .filter-btn:hover {
        border-color: #1a56a4;
        color: #1a56a4;
    }

    .filter-btn.active {
        background: #1a56a4;
        border-color: #1a56a4;
        color: white;
    }

    /* Results Info */
    .results-info {
        padding: 1rem 0 0.25rem;
        color: #64748b;
        font-size: 0.9rem;
        min-height: 1.5rem;
    }

    /* Properties Section */
    .angebote-section {
        padding: 2rem 0 4rem;
        background: #f8fafc;
        min-height: 400px;
    }

    /* Section headers for new / archived splits */
    .property-section {
        margin-bottom: 3rem;
    }

    .section-subtitle {
        font-size: 1.25rem;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .count-badge {
        background: #1a56a4;
        color: white;
        padding: 0.2rem 0.75rem;
        border-radius: 50px;
        font-size: 0.875rem;
        font-weight: 600;
    }

    .properties-grid-inner {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 1.75rem;
    }

    /* Skeleton Loading */
    .skeleton-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 1.75rem;
    }

    .skeleton-card {
        background: white;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 4px 20px rgba(0,0,0,0.06);
    }

    .skeleton {
        background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
        background-size: 200% 100%;
        animation: shimmer 1.5s infinite;
    }

    @keyframes shimmer {
        0%   { background-position: -200% 0; }
        100% { background-position:  200% 0; }
    }

    .skeleton-img  { height: 220px; }
    .skeleton-body { padding: 1.25rem; }

    .skeleton-line {
        height: 16px;
        border-radius: 4px;
        margin-bottom: 0.75rem;
    }

    .w-80 { width: 80%; }
    .w-60 { width: 60%; }
    .w-40 { width: 40%; }

    /* NEU Badge */
    .new-badge {
        position: absolute;
        top: 12px;
        right: 12px;
        background: linear-gradient(135deg, #10b981, #059669);
        color: white;
        padding: 5px 14px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        z-index: 2;
    }

    /* Favourite button on card */
    .property-card-fav {
        position: absolute;
        bottom: 12px;
        right: 12px;
        background: rgba(255,255,255,0.9);
        border: none;
        border-radius: 50%;
        width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s ease;
        z-index: 2;
        color: #94a3b8;
        -webkit-backdrop-filter: blur(4px);
        backdrop-filter: blur(4px);
    }

    .property-card-fav:hover,
    .property-card-fav.fav-active {
        color: #ef4444;
        transform: scale(1.15);
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
        background: white;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.06);
    }

    .empty-state h3 {
        font-size: 1.5rem;
        margin-bottom: 0.75rem;
        color: #ffffff;
    }

    .empty-state p {
        color: #64748b;
        margin-bottom: 1.5rem;
    }

    /* CTA Section */
    .angebote-cta {
        background: linear-gradient(135deg, #1a56a4, #0d4191);
        color: white;
        padding: 4rem 0;
        text-align: center;
    }

    .angebote-cta h2 {
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 1rem;
        color: white;
    }

    .angebote-cta p {
        font-size: 1.125rem;
        opacity: 0.9;
        max-width: 550px;
        margin: 0 auto 2rem;
    }

    .cta-group {
        display: flex;
        gap: 1rem;
        justify-content: center;
        flex-wrap: wrap;
    }

    .cta-btn-primary {
        background: white;
        color: #1a56a4;
        padding: 0.875rem 2rem;
        border-radius: 8px;
        font-weight: 700;
        font-size: 1rem;
        text-decoration: none;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .cta-btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.2);
        color: #1a56a4;
    }

    .cta-btn-outline {
        background: transparent;
        color: white;
        padding: 0.875rem 2rem;
        border-radius: 8px;
        font-weight: 600;
        font-size: 1rem;
        text-decoration: none;
        border: 2px solid rgba(255,255,255,0.7);
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .cta-btn-outline:hover {
        background: rgba(255,255,255,0.1);
        border-color: white;
        color: white;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .angebote-hero {
            padding: 5rem 0 3rem;
        }

        .angebote-hero h1 {
            font-size: 1.875rem;
        }

        .hero-stats {
            gap: 1.5rem;
        }

        .filter-bar .container {
            flex-direction: column;
            align-items: flex-start;
        }

        .properties-grid-inner,
        .skeleton-grid {
            grid-template-columns: 1fr;
        }

        .angebote-cta h2 {
            font-size: 1.5rem;
        }
    }

    @media (max-width: 480px) {
        .filter-buttons {
            gap: 0.375rem;
        }

        .filter-btn {
            padding: 0.4rem 0.875rem;
            font-size: 0.8rem;
        }
    }
</style>
ENDCSS;

$extraScripts =
    '<script src="' . BASE . '/js/api-client.js"></script>
<script src="' . BASE . '/js/immobilien-angebote-loader.js"></script>';

require_once __DIR__ . '/includes/header.php';
?>

<!-- Hero Section -->
<section class="angebote-hero">
    <div class="container">
        <h1>Unsere Immobilienangebote</h1>
        <p>Entdecken Sie Ihr Traumobjekt – Häuser, Wohnungen und Grundstücke in Bad Vilbel und der Rhein-Main-Region</p>
        <div class="hero-stats">
            <div class="hero-stat">
                <span class="stat-number" id="stat-total">–</span>
                <span class="stat-label">Verfügbare Objekte</span>
            </div>
            <div class="hero-stat">
                <span class="stat-number" id="stat-new">–</span>
                <span class="stat-label">Neu eingestellt</span>
            </div>
            <div class="hero-stat">
                <span class="stat-number">10+</span>
                <span class="stat-label">Jahre Erfahrung</span>
            </div>
        </div>
    </div>
</section>

<!-- Filter Bar -->
<div class="filter-bar" id="filterBar">
    <div class="container">
        <span class="filter-label"><i class="fas fa-filter"></i> Filter:</span>
        <div class="filter-buttons">
            <button class="filter-btn active" data-filter="all">Alle Angebote</button>
            <button class="filter-btn" data-filter="kauf">Kaufen</button>
            <button class="filter-btn" data-filter="miete">Mieten</button>
            <button class="filter-btn" data-filter="haus">Haus</button>
            <button class="filter-btn" data-filter="wohnung">Wohnung</button>
            <button class="filter-btn" data-filter="gewerbe">Gewerbe</button>
        </div>
    </div>
</div>

<!-- Properties Section -->
<section class="angebote-section">
    <div class="container">
        <div class="results-info" id="resultsInfo"></div>
        <!-- Properties Grid – wird per immobilien-angebote-loader.js befüllt -->
<?php
// Crawlbare Basisliste; der JS-Loader ersetzt den Inhalt des Grids nach dem Laden.
$ssrProps = [];
try {
    $port = defined('DB_PORT') ? DB_PORT : 3306;
    $charset = defined('DB_CHARSET') ? DB_CHARSET : 'utf8mb4';
    $ssrPdo = new PDO('mysql:host=' . DB_HOST . ';port=' . $port . ';dbname=' . DB_NAME . ';charset=' . $charset, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    $ssrProps = $ssrPdo->query(
        "SELECT title, slug, location, offer_type, type, price, area, rooms, short_description
           FROM properties WHERE status = 'available' AND slug <> '' ORDER BY created_at DESC LIMIT 100"
    )->fetchAll();
} catch (Throwable $e) {
    error_log('Angebote SSR failed: ' . $e->getMessage());
}
?>
        <div id="properties-grid">
            <?php if ($ssrProps): ?>
                <ul class="ssr-property-list" style="list-style:none;padding:0;display:grid;gap:1rem">
                    <?php foreach ($ssrProps as $sp): ?>
                        <li>
                            <h2 style="font-size:1.1rem"><a href="<?= BASE ?>/immobilie/<?= rawurlencode($sp['slug']) ?>"><?= htmlspecialchars($sp['title'], ENT_QUOTES) ?></a></h2>
                            <p>
                                <?= htmlspecialchars(trim(($sp['type'] ?? '') . ' · ' . ($sp['offer_type'] ?? '') . ' · ' . ($sp['location'] ?? ''), ' ·'), ENT_QUOTES) ?>
                                <?php if (!empty($sp['rooms'])): ?> · <?= htmlspecialchars((string)$sp['rooms'], ENT_QUOTES) ?> Zimmer<?php endif; ?>
                                <?php if (!empty($sp['area'])): ?> · <?= htmlspecialchars((string)$sp['area'], ENT_QUOTES) ?> m²<?php endif; ?>
                                <?php if (!empty($sp['price'])): ?> · <?= htmlspecialchars((string)$sp['price'], ENT_QUOTES) ?><?php endif; ?>
                            </p>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="angebote-cta">
    <div class="container">
        <h2>Nicht das Richtige dabei?</h2>
        <p>Sagen Sie uns, was Sie suchen – wir finden das passende Objekt für Sie in unserem Netzwerk.</p>
        <div class="cta-group">
            <a href="https://wa.me/4916098787878?text=Hallo%2C%20ich%20suche%20eine%20Immobilie%20und%20ben%C3%B6tige%20Ihre%20Hilfe."
                class="cta-btn-primary" target="_blank" rel="noopener noreferrer">
                <i class="fab fa-whatsapp"></i> WhatsApp schreiben
            </a>
            <a href="<?= BASE ?>/#contact" class="cta-btn-outline">
                <i class="fas fa-envelope"></i> Anfrage stellen
            </a>
        </div>
    </div>
</section>

<script>
    (function() {
        'use strict';

        // ── Hilfs-Filter-Funktion ─────────────────────────────────────────
        // Erweitert filterByType (aus dem Loader) um kauf/miete-Filterung.
        function applyFilter(filter) {
            const cards = document.querySelectorAll('.property-card');
            const pricingFilters = ['kauf', 'miete'];

            cards.forEach(function(card) {
                var visible = true;
                if (filter === 'all') {
                    visible = true;
                } else if (pricingFilters.includes(filter)) {
                    visible = (card.dataset.pricingType === filter);
                } else {
                    visible = (card.dataset.type === filter);
                }
                card.style.display = visible ? '' : 'none';
            });

            updateResultsInfo();
        }

        function updateResultsInfo() {
            var visible = document.querySelectorAll('.property-card:not([style*="display: none"])').length;
            var infoEl = document.getElementById('resultsInfo');
            if (infoEl) {
                infoEl.textContent = visible + ' Immobilie' + (visible !== 1 ? 'n' : '') + ' gefunden';
            }
        }

        // ── Filter-Buttons verdrahten ─────────────────────────────────────
        document.querySelectorAll('.filter-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.filter-btn').forEach(function(b) {
                    b.classList.remove('active');
                });
                this.classList.add('active');
                applyFilter(this.dataset.filter);
            });
        });

        // ── URL-Parameter lesen und initialen Filter setzen ───────────────
        var params = new URLSearchParams(window.location.search);
        var offerType = params.get('offer_type'); // Kauf | Miete
        var location = params.get('location'); // Bad Vilbel | Frankfurt | …

        if (offerType) {
            var map = {
                'Kauf': 'kauf',
                'Miete': 'miete'
            };
            var initialFilter = map[offerType] || offerType.toLowerCase();
            document.querySelectorAll('.filter-btn').forEach(function(btn) {
                btn.classList.remove('active');
                if (btn.dataset.filter === initialFilter) {
                    btn.classList.add('active');
                }
            });
        }

        // ── Nach dem Laden: data-pricing-type ergänzen + Stats setzen ─────
        document.addEventListener('propertiesRendered', function() {
            // data-pricing-type für kauf/miete-Filter nachrüsten
            document.querySelectorAll('.property-card').forEach(function(card) {
                var priceEl = card.querySelector('.property-price');
                if (!priceEl) return;
                var txt = priceEl.textContent || '';
                if (txt.indexOf('Kaufpreis') !== -1) {
                    card.dataset.pricingType = 'kauf';
                } else if (txt.indexOf('Kaltmiete') !== -1 || txt.indexOf('Miete') !== -1) {
                    card.dataset.pricingType = 'miete';
                }
            });

            // Initialen Filter (aus URL) anwenden
            if (offerType) {
                var map2 = {
                    'Kauf': 'kauf',
                    'Miete': 'miete'
                };
                var f = map2[offerType] || offerType.toLowerCase();
                applyFilter(f);
            } else if (location) {
                // Ort-Filter: nur Karten mit passender location anzeigen
                document.querySelectorAll('.property-card').forEach(function(card) {
                    var loc = (card.dataset.location || '').toLowerCase();
                    card.style.display = loc.indexOf(location.toLowerCase()) !== -1 ? '' : 'none';
                });
                updateResultsInfo();
            } else {
                updateResultsInfo();
            }

            // Hero-Statistik befüllen
            var allCards = document.querySelectorAll('.property-card');
            var newCards = document.querySelectorAll('.new-badge');
            var statTotal = document.getElementById('stat-total');
            var statNew = document.getElementById('stat-new');
            if (statTotal) statTotal.textContent = allCards.length;
            if (statNew) statNew.textContent = newCards.length;
        });
    })();
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
