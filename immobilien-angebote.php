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

$extraHead .= '<link rel="stylesheet" href="' . BASE . '/immobilien-angebote.css?v=' . (@filemtime(__DIR__ . '/immobilien-angebote.css') ?: 1) . '">';

$extraScripts =
    '<script src="' . BASE . '/js/api-client.js"></script>
<script src="' . BASE . '/js/immobilien-angebote-loader.js?v=' . (@filemtime(__DIR__ . '/js/immobilien-angebote-loader.js') ?: 1) . '"></script>';

require_once __DIR__ . '/includes/header.php';
?>

<main class="angebote-page-v2">
<section class="angebote-hero">
    <div class="container">
        <h1>Finden Sie Ihr neues Zuhause<br>in <span>Frankfurt &amp; Rhein-Main</span></h1>
        <p>Entdecken Sie ausgewählte Wohnungen, Häuser und weitere Immobilien in der Region.</p>
        <form class="angebote-search" id="propertySearchForm" role="search">
            <label class="sr-only" for="propertySearch">Adresse, Ort oder Stichwort suchen</label>
            <input id="propertySearch" type="search" placeholder="Adresse, Stadt oder Region suchen" autocomplete="off">
            <button type="submit"><i class="fas fa-magnifying-glass" aria-hidden="true"></i> Suchen</button>
        </form>
    </div>
</section>

<section class="angebote-section">
    <div class="container">
        <div class="angebote-controls">
            <div class="results-info" id="resultsInfo" aria-live="polite">Immobilien werden geladen …</div>
            <div class="angebote-controls-right">
                <label class="sort-label" for="propertySort">Sortieren</label>
                <select class="sort-select" id="propertySort">
                    <option value="newest">Neueste zuerst</option>
                    <option value="price_asc">Preis aufsteigend</option>
                    <option value="price_desc">Preis absteigend</option>
                    <option value="area_desc">Wohnfläche absteigend</option>
                </select>
            </div>
        </div>
        <div class="filter-buttons">
            <button class="filter-btn active" type="button" data-filter="all" aria-pressed="true">Alle Angebote</button>
            <button class="filter-btn" type="button" data-filter="kauf" aria-pressed="false">Kaufen</button>
            <button class="filter-btn" type="button" data-filter="miete" aria-pressed="false">Mieten</button>
            <button class="filter-btn" type="button" data-filter="haus" aria-pressed="false">Häuser</button>
            <button class="filter-btn" type="button" data-filter="wohnung" aria-pressed="false">Wohnungen</button>
            <button class="filter-btn" type="button" data-filter="gewerbe" aria-pressed="false">Gewerbe</button>
        </div>
<?php
$ssrProps = [];
try {
    $port = defined('DB_PORT') ? DB_PORT : 3306;
    $charset = defined('DB_CHARSET') ? DB_CHARSET : 'utf8mb4';
    $ssrPdo = new PDO('mysql:host=' . DB_HOST . ';port=' . $port . ';dbname=' . DB_NAME . ';charset=' . $charset, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    $ssrProps = $ssrPdo->query(
        "SELECT id, title, slug, location, zip, offer_type, type, price, area, rooms,
                warm_rent, main_image, images, created_at
           FROM properties WHERE status = 'available' AND slug <> '' ORDER BY created_at DESC LIMIT 100"
    )->fetchAll();
} catch (Throwable $e) {
    error_log('Angebote SSR failed: ' . $e->getMessage());
}
?>
        <div id="properties-grid" aria-live="polite">
            <?php if ($ssrProps): ?>
                <div class="properties-grid-inner">
                    <?php foreach ($ssrProps as $sp): ?>
                        <?php
                        $ssrImages = json_decode((string)($sp['images'] ?? ''), true);
                        $ssrImage = trim((string)($sp['main_image'] ?? ''));
                        if ($ssrImage === '' && is_array($ssrImages) && $ssrImages) {
                            $firstImage = reset($ssrImages);
                            $ssrImage = is_array($firstImage) ? (string)($firstImage['url'] ?? '') : (string)$firstImage;
                        }
                        $ssrLocation = trim((string)($sp['location'] ?? ''));
                        $ssrZip = trim((string)($sp['zip'] ?? ''));
                        $ssrAddress = trim($ssrLocation . ($ssrZip !== '' ? ' · ' . $ssrZip : ''));
                        $ssrPublicNumber = (int)$sp['id'] + 1110;
                        $ssrIsNew = !empty($sp['created_at']) && strtotime((string)$sp['created_at']) >= strtotime('-14 days');
                        $ssrOfferType = strtolower((string)($sp['offer_type'] ?? ''));
                        $ssrIsPurchase = $ssrOfferType === 'kauf';
                        $ssrPriceNumber = (float)($sp['price'] ?? 0);
                        $ssrAreaNumber = (float)($sp['area'] ?? 0);
                        $ssrRoomsNumber = (float)($sp['rooms'] ?? 0);
                        $ssrWarmRentNumber = (float)($sp['warm_rent'] ?? 0);
                        $ssrPrice = $ssrPriceNumber > 0 ? number_format($ssrPriceNumber, 0, ',', '.') . ' €' : 'Auf Anfrage';
                        $ssrFacts = [[
                            'icon' => 'fas fa-tag',
                            'value' => $ssrPrice,
                            'label' => $ssrIsPurchase ? 'Kaufpreis' : 'Kaltmiete',
                        ]];
                        if ($ssrIsPurchase) {
                            if ($ssrAreaNumber > 0) {
                                $ssrFacts[] = ['icon' => 'far fa-square', 'value' => number_format($ssrAreaNumber, 1, ',', '.') . ' m²', 'label' => 'Wohnfläche'];
                            }
                            if ($ssrRoomsNumber > 0) {
                                $ssrFacts[] = ['icon' => 'fas fa-door-open', 'value' => number_format($ssrRoomsNumber, 1, ',', '.'), 'label' => 'Zimmer'];
                            }
                            if ($ssrPriceNumber > 0 && $ssrAreaNumber > 0) {
                                $ssrFacts[] = [
                                    'icon' => 'fas fa-euro-sign',
                                    'value' => number_format($ssrPriceNumber / $ssrAreaNumber, 0, ',', '.') . ' € / m²',
                                    'label' => 'Kaufpreis pro m²',
                                ];
                            }
                        } else {
                            if ($ssrRoomsNumber > 0) {
                                $ssrFacts[] = ['icon' => 'fas fa-door-open', 'value' => number_format($ssrRoomsNumber, 1, ',', '.'), 'label' => 'Zimmer'];
                            }
                            if ($ssrWarmRentNumber > 0) {
                                $ssrFacts[] = ['icon' => 'fas fa-euro-sign', 'value' => number_format($ssrWarmRentNumber, 0, ',', '.') . ' €', 'label' => 'Warmmiete'];
                            }
                            if ($ssrAreaNumber > 0) {
                                $ssrFacts[] = ['icon' => 'far fa-square', 'value' => number_format($ssrAreaNumber, 1, ',', '.') . ' m²', 'label' => 'Wohnfläche'];
                            }
                        }
                        $ssrSearch = implode(' ', [$sp['title'] ?? '', $ssrLocation, $ssrZip]);
                        ?>
                        <article class="property-card"
                            data-type="<?= htmlspecialchars(strtolower((string)$sp['type']), ENT_QUOTES) ?>"
                            data-pricing-type="<?= htmlspecialchars($ssrOfferType, ENT_QUOTES) ?>"
                            data-price="<?= (float)($sp['price'] ?? 0) ?>"
                            data-area="<?= (float)($sp['area'] ?? 0) ?>"
                            data-created-at="<?= htmlspecialchars((string)($sp['created_at'] ?? ''), ENT_QUOTES) ?>"
                            data-location="<?= htmlspecialchars(strtolower($ssrLocation), ENT_QUOTES) ?>"
                            data-search="<?= htmlspecialchars(mb_strtolower($ssrSearch, 'UTF-8'), ENT_QUOTES) ?>">
                            <a class="property-card-link-overlay"
                                href="<?= BASE ?>/immobilie/<?= $ssrPublicNumber ?>"
                                aria-label="<?= htmlspecialchars('Immobilienangebot in ' . $ssrAddress . ' ansehen', ENT_QUOTES) ?>"></a>
                            <div class="property-image">
                                <?php if ($ssrImage !== ''): ?>
                                    <img src="<?= htmlspecialchars($ssrImage, ENT_QUOTES) ?>"
                                        alt="<?= htmlspecialchars('Immobilienangebot in ' . $ssrAddress, ENT_QUOTES) ?>"
                                        loading="lazy" decoding="async">
                                <?php else: ?>
                                    <div class="property-image-placeholder" aria-hidden="true"><i class="fas fa-house"></i></div>
                                <?php endif; ?>
                                <span class="property-offer-badge"><?= $ssrOfferType === 'kauf' ? 'Kaufen' : 'Mieten' ?></span>
                                <?php if ($ssrIsNew): ?><span class="new-badge">NEU</span><?php endif; ?>
                                <button type="button" class="property-card-fav fav-btn"
                                    data-fav-id="<?= (int)$sp['id'] ?>"
                                    aria-label="<?= htmlspecialchars('Immobilie in ' . $ssrAddress . ' merken', ENT_QUOTES) ?>"
                                    title="Zu Favoriten hinzufügen"><i class="far fa-heart" aria-hidden="true"></i></button>
                                <div class="property-image-location"><i class="fas fa-location-dot" aria-hidden="true"></i> <?= htmlspecialchars($ssrAddress, ENT_QUOTES) ?></div>
                            </div>
                            <div class="property-content">
                                <div class="property-facts property-facts--count-<?= count($ssrFacts) ?>">
                                    <?php foreach ($ssrFacts as $ssrFact): ?>
                                        <div class="property-fact">
                                            <i class="<?= htmlspecialchars($ssrFact['icon'], ENT_QUOTES) ?>" aria-hidden="true"></i>
                                            <div>
                                                <strong><?= htmlspecialchars($ssrFact['value'], ENT_QUOTES) ?></strong>
                                                <span><?= htmlspecialchars($ssrFact['label'], ENT_QUOTES) ?></span>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
        <p class="no-results" id="noResults" hidden>Zu dieser Suche wurden keine passenden Immobilien gefunden.</p>
    </div>
</section>

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
</main>

<script>
    (function() {
        'use strict';

        var cardsContainer = document.getElementById('properties-grid');
        var searchInput = document.getElementById('propertySearch');
        var searchForm = document.getElementById('propertySearchForm');
        var sortSelect = document.getElementById('propertySort');
        var resultsInfo = document.getElementById('resultsInfo');
        var noResults = document.getElementById('noResults');
        var activeFilter = 'all';
        var params = new URLSearchParams(window.location.search);
        var offerType = (params.get('offer_type') || '').toLowerCase();
        var location = params.get('location') || '';

        if (offerType === 'kauf' || offerType === 'miete') {
            activeFilter = offerType;
        }
        searchInput.value = location;

        function updateResults() {
            var grid = cardsContainer.querySelector('.properties-grid-inner') || cardsContainer;
            var cards = Array.prototype.slice.call(grid.querySelectorAll('.property-card'));
            var query = searchInput.value.trim().toLocaleLowerCase('de-DE');
            var sortBy = sortSelect.value;
            var visibleCount = 0;

            cards.sort(function(a, b) {
                if (sortBy === 'price_asc') return Number(a.dataset.price) - Number(b.dataset.price);
                if (sortBy === 'price_desc') return Number(b.dataset.price) - Number(a.dataset.price);
                if (sortBy === 'area_desc') return Number(b.dataset.area) - Number(a.dataset.area);
                return new Date(b.dataset.createdAt || 0) - new Date(a.dataset.createdAt || 0);
            });

            cards.forEach(function(card) {
                grid.appendChild(card);
                var typeMatches = activeFilter === 'all'
                    || (activeFilter === 'kauf' || activeFilter === 'miete'
                        ? card.dataset.pricingType === activeFilter
                        : card.dataset.type === activeFilter);
                var searchMatches = !query || (card.dataset.search || '').indexOf(query) !== -1;
                var visible = typeMatches && searchMatches;
                card.hidden = !visible;
                if (visible) visibleCount++;
            });

            resultsInfo.textContent = visibleCount + ' ' + (visibleCount === 1 ? 'Objekt' : 'Objekte');
            noResults.hidden = cards.length === 0 || visibleCount > 0;
        }

        document.querySelectorAll('.filter-btn').forEach(function(button) {
            var selected = button.dataset.filter === activeFilter;
            button.classList.toggle('active', selected);
            button.setAttribute('aria-pressed', selected ? 'true' : 'false');
            button.addEventListener('click', function() {
                activeFilter = this.dataset.filter;
                document.querySelectorAll('.filter-btn').forEach(function(item) {
                    var isActive = item === button;
                    item.classList.toggle('active', isActive);
                    item.setAttribute('aria-pressed', isActive ? 'true' : 'false');
                });
                updateResults();
            });
        });

        searchInput.addEventListener('input', updateResults);
        searchForm.addEventListener('submit', function(event) {
            event.preventDefault();
            updateResults();
        });
        sortSelect.addEventListener('change', updateResults);
        document.addEventListener('propertiesRendered', updateResults);
    })();
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
