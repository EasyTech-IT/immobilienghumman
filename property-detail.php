<?php
require_once __DIR__ . '/includes/config.php';
$slugParam       = trim((string)($_GET['slug'] ?? ''));
$idParam         = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$pageTitle       = 'Immobilien-Detail – Immobilien Ghumman';
$pageDescription = 'Detailansicht einer Immobilie von Immobilien Ghumman.';
$pageKeywords    = 'Immobilie kaufen, Wohnung mieten, Details';
$ogUrl           = $slugParam !== ''
    ? (SITE_URL . '/immobilie/' . rawurlencode($slugParam))
    : ($idParam > 0 ? (SITE_URL . '/property-detail?id=' . $idParam) : (SITE_URL . '/property-detail'));
$activeNav       = 'immobilien';
$extraHead       = '<style>
        /* Page Specific Styles */
        body {
            background-color: #f8f9fa;
        }

        /* Hero Section */
        .property-hero {
            min-height: 280px;
            background: linear-gradient(135deg, #1a2d3f 0%, #3070a0 100%);
            position: relative;
            display: flex;
            align-items: flex-end;
            padding-bottom: 4rem;
            margin-bottom: -4rem;
            /* Overlap effect */
        }

        .hero-content {
            position: relative;
            z-index: 2;
            color: white;
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1rem;
        }

        .property-title h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            color: white;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.5);
        }

        .property-location {
            font-size: 1.2rem;
            opacity: 0.9;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        /* Main Grid Layout */
        .property-main-grid {
            display: grid;
            grid-template-columns: 1fr 350px;
            gap: 2rem;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1rem 4rem 1rem;
            position: relative;
            z-index: 3;
        }

        @media (max-width: 900px) {
            .property-main-grid {
                grid-template-columns: 1fr;
            }
        }

        /* Left Column Content */
        .property-content-left {
            display: flex;
            flex-direction: column;
            gap: 2rem;
        }

        /* Gallery */
        .property-gallery {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }

        .gallery-main {
            position: relative;
            height: 500px;
            background: #eee;
        }

        .gallery-main img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .gallery-nav {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(255, 255, 255, 0.9);
            border: none;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            color: var(--primary-color);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .gallery-nav:hover {
            background: white;
            transform: translateY(-50%) scale(1.1);
        }

        .gallery-nav.prev {
            left: 20px;
        }

        .gallery-nav.next {
            right: 20px;
        }

        .gallery-thumbnails {
            display: flex;
            gap: 10px;
            padding: 15px;
            overflow-x: auto;
            background: white;
        }

        .thumbnail {
            width: 100px;
            height: 70px;
            flex-shrink: 0;
            border-radius: 6px;
            overflow: hidden;
            cursor: pointer;
            opacity: 0.7;
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }

        .thumbnail.active,
        .thumbnail:hover {
            opacity: 1;
            border-color: var(--primary-color);
        }

        .thumbnail img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* Key Facts Bar */
        .key-facts {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 1rem;
            background: white;
            padding: 1.5rem;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        }

        .fact-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            gap: 0.5rem;
            padding: 0.5rem;
        }

        .fact-item svg {
            color: var(--primary-color);
            width: 28px;
            height: 28px;
        }

        .fact-item strong {
            font-size: 1.1rem;
            color: var(--secondary-color);
        }

        .fact-item span {
            font-size: 0.85rem;
            color: #666;
        }

        /* Content Sections */
        .content-card {
            background: white;
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        }

        .content-card h2 {
            font-size: 1.5rem;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #eee;
            color: var(--secondary-color);
        }

        .description-text p {
            margin-bottom: 1rem;
            line-height: 1.8;
            color: #444;
        }

        /* Features Grid */
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 1rem;
        }

        .feature-item {
            display: flex;
            align-items: center;
            gap: 0.8rem;
            padding: 0.8rem;
            background: #f8f9fa;
            border-radius: 8px;
            transition: transform 0.2s;
        }

        .feature-item:hover {
            transform: translateY(-2px);
            background: #eef2f6;
        }

        .feature-item svg {
            color: var(--success-color);
        }

        /* Sidebar */
        .property-sidebar {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        .sidebar-card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            position: sticky;
            top: 100px;
        }

        .price-card {
            text-align: center;
            border-top: 4px solid var(--primary-color);
        }

        .property-price {
            font-size: 2rem;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 0.5rem;
            display: block;
        }

        .price-label {
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 1.5rem;
            display: block;
        }

        .action-buttons {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .btn-primary,
        .btn-secondary {
            padding: 1rem;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            text-align: center;
            border: none;
            width: 100%;
            font-size: 1rem;
            text-decoration: none;
            display: inline-block;
        }

        .btn-primary {
            background: var(--primary-color);
            color: white;
        }

        .btn-primary:hover {
            background: #2a5a85;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(51, 102, 153, 0.3);
        }

        .btn-secondary {
            background: white;
            border: 2px solid var(--primary-color);
            color: var(--primary-color);
        }

        .btn-secondary:hover {
            background: #f0f7ff;
        }

        .contact-agent {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1.5rem;
            text-align: left;
        }

        .agent-avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            object-fit: cover;
        }

        .agent-info h4 {
            margin: 0;
            font-size: 1.1rem;
        }

        .agent-info p {
            margin: 0;
            font-size: 0.9rem;
            color: #666;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .property-hero {
                min-height: 200px;
                margin-bottom: -2rem;
            }

            .property-title h1 {
                font-size: 1.8rem;
            }

            .gallery-main {
                height: 300px;
            }
        }
    </style>';
$extraScripts    =
    '<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="">
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV/XN/WLcE=" crossorigin=""></script>
<script src="' . BASE . '/js/api-client.js"></script>
<script src="' . BASE . '/js/error-tracker.js"></script>
<script src="' . BASE . '/js/favorites.js"></script>
<script src="' . BASE . '/property-detail-loader.js"></script>';
require_once __DIR__ . '/includes/header.php';
?>


<!-- Hero Section -->
<section class="property-hero">
    <div class="container hero-content">
        <div class="property-header">
            <div class="property-title">
                <h1>Laden...</h1>
            </div>
            <div class="property-location">
                <i class="fas fa-map-marker-alt"></i>
                <span>Wird geladen...</span>
            </div>
        </div>
    </div>
</section>

<!-- Main Content Grid -->
<main class="property-main-grid">

    <!-- Left Column -->
    <div class="property-content-left">

        <!-- Gallery -->
        <section class="property-gallery">
            <div class="gallery-main">
                <img id="main-image" src="https://via.placeholder.com/800x500?text=Laden..." alt="Immobilienbild">
                <button class="gallery-nav prev" onclick="previousImage()">❮</button>
                <button class="gallery-nav next" onclick="nextImage()">❯</button>
            </div>
            <div id="gallery-thumbnails" class="gallery-thumbnails">
                <!-- Thumbnails populated by JS -->
            </div>
        </section>

        <!-- Key Facts -->
        <section class="key-facts">
            <!-- Populated by JS -->
        </section>

        <!-- Description -->
        <section class="property-details">
            <div class="content-card content-section">
                <!-- Populated by JS -->
            </div>
        </section>

        <!-- Features -->
        <section class="content-card property-features-section">
            <!-- Populated by JS -->
        </section>

        <!-- Location -->
        <section class="content-card location-info">
            <!-- Populated by JS -->
        </section>

        <!-- Leaflet Map -->
        <section class="content-card property-map-section" id="property-map-section" style="display:none">
            <h2 style="margin:0 0 1rem;font-size:1.25rem;"><i class="fas fa-map-marker-alt" style="color:var(--primary)"></i> Lage &amp; Standort</h2>
            <div id="property-map" style="height:320px;border-radius:var(--radius-md,8px);overflow:hidden;z-index:0;"></div>
            <p class="map-disclaimer" style="font-size:.8rem;color:#9ca3af;margin:.5rem 0 0;">Kartenansicht ist ungefähr. Genaue Adresse nach Anfrage.</p>
        </section>

        <!-- Complete Info -->
        <section class="complete-property-info">
            <!-- Populated by JS -->
        </section>

    </div>

    <!-- Right Column (Sidebar) -->
    <aside class="property-sidebar">
        <div class="sidebar-card price-card">
            <span class="price-label">Kaufpreis / Miete</span>
            <div class="property-price">
                <!-- Populated by JS -->
            </div>
            <div class="action-buttons">
                <a href="#contact-form" class="btn-primary"
                    onclick="document.getElementById('contact-modal').style.display='flex'">
                    <i class="far fa-envelope"></i> Kontakt aufnehmen
                </a>
                <button class="btn-secondary" onclick="window.print()">
                    <i class="fas fa-print"></i> Exposé drucken
                </button>
                <button class="btn-secondary fav-btn" id="detail-fav-btn" data-fav-id="" aria-label="Zu Favoriten hinzufügen" title="Zu Favoriten hinzufügen">
                    <i class="far fa-heart"></i> Merken
                </button>
            </div>
        </div>

        <div class="sidebar-card contact-card">
            <div class="contact-agent">
                <img src="https://res.cloudinary.com/dlpdbr0ey/image/upload/v1755051245/7ae1aa7d-b239-4ee5-8090-6b8339e15d4e-removebg-preview_m6fipk.png"
                    alt="Makler" class="agent-avatar" style="background: #f0f0f0; padding: 5px;">
                <div class="agent-info">
                    <h4>Immobilien Ghumman</h4>
                    <p>Ihr Immobilienexperte</p>
                </div>
            </div>
            <div class="contact-details">
                <p><i class="fas fa-phone"></i> <a href="tel:+4916098787878">+49 160 98787878</a></p>
                <p><i class="fas fa-envelope"></i> <a href="#contact-modal" onclick="document.getElementById('contact-modal').style.display='flex'">Kontaktformular öffnen</a></p>
            </div>
        </div>
    </aside>

</main>

<!-- Contact Modal (Hidden by default) -->
<div id="contact-modal" class="modal-overlay"
    style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 2000; align-items: center; justify-content: center;">
    <div class="modal-content"
        style="background: white; padding: 2rem; border-radius: 12px; max-width: 500px; width: 90%; position: relative;">
        <span class="close-modal" onclick="document.getElementById('contact-modal').style.display='none'"
            style="position: absolute; top: 1rem; right: 1rem; cursor: pointer; font-size: 1.5rem;">&times;</span>
        <h2>Interesse an dieser Immobilie?</h2>
        <p>Ihre Anfrage wird direkt im Admin erfasst und per E-Mail bestätigt.</p>

        <form id="propertyInquiryForm" method="post" action="<?= BASE ?>/api/contact.php?action=inquiry" style="margin-top:1rem;display:grid;gap:.75rem;">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(generate_csrf_token(), ENT_QUOTES) ?>">
            <input type="hidden" name="form_type" value="immobilien_anfrage_detailseite">
            <input type="hidden" name="property_id" id="inquiry_property_id" value="">
            <input type="hidden" name="property_title" id="inquiry_property_title" value="">
            <input type="hidden" name="property_link" id="inquiry_property_link" value="">

            <input type="text" name="name" placeholder="Ihr Name *" required>
            <input type="email" name="email" placeholder="Ihre E-Mail *" required>
            <input type="tel" name="phone" placeholder="Telefon">
            <textarea name="message" rows="4" placeholder="Ihre Nachricht"></textarea>

            <button type="submit" class="btn-primary link-block-clean">
                <i class="fas fa-paper-plane"></i> Anfrage senden
            </button>
            <div id="propertyInquiryMsg" style="display:none;border-radius:8px;padding:.65rem .8rem;font-size:.9rem;"></div>
        </form>

        <div style="margin-top:1rem;display:flex;flex-direction:column;gap:.65rem;">
            <a href="tel:+4916098787878" class="btn-secondary link-block-clean">
                <i class="fas fa-phone"></i> Jetzt anrufen
            </a>
            <a href="https://wa.me/4916098787878" class="btn-secondary link-block-clean link-whatsapp-outline">
                <i class="fab fa-whatsapp"></i> WhatsApp
            </a>
        </div>
    </div>
</div>

<script>
    (function() {
        var form = document.getElementById('propertyInquiryForm');
        var msg = document.getElementById('propertyInquiryMsg');
        if (!form || !msg) return;

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
                return json;
            });
        }

        form.addEventListener('submit', function(e) {
            e.preventDefault();
            var btn = form.querySelector('button[type="submit"]');
            var old = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Wird gesendet ...';

            fetch(form.action, {
                    method: 'POST',
                    body: new FormData(form),
                    credentials: 'same-origin'
                })
                .then(parseApiResponse)
                .then(function(json) {
                    msg.style.display = 'block';
                    if (json.success && json._httpOk) {
                        msg.style.background = '#d1fae5';
                        msg.style.color = '#065f46';
                        msg.textContent = 'Vielen Dank. Ihre Anfrage wurde gesendet.';
                        form.reset();
                    } else {
                        msg.style.background = '#fee2e2';
                        msg.style.color = '#991b1b';
                        msg.textContent = json.message || 'Anfrage konnte nicht gesendet werden.';
                    }
                })
                .catch(function() {
                    msg.style.display = 'block';
                    msg.style.background = '#fee2e2';
                    msg.style.color = '#991b1b';
                    msg.textContent = 'Verbindungsfehler. Bitte versuchen Sie es erneut.';
                })
                .finally(function() {
                    btn.disabled = false;
                    btn.innerHTML = old;
                });
        });
    })();
</script>

<!-- Footer -->

<?php require_once __DIR__ . '/includes/footer.php'; ?>