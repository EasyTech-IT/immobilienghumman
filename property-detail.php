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
// favorites.js wird unten bereits eingebunden – Doppelladen würde Klicks doppelt auslösen
$skipFavorites   = true;
$extraHead       = '<link rel="stylesheet" href="' . BASE . '/property-detail.css?v=' . (@filemtime(__DIR__ . '/property-detail.css') ?: 1) . '">';
$extraScripts    =
    '<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="">
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script src="' . BASE . '/js/error-tracker.js"></script>
<script src="' . BASE . '/js/favorites.js"></script>
<script src="' . BASE . '/property-detail-loader.js?v=' . (@filemtime(__DIR__ . '/property-detail-loader.js') ?: 1) . '"></script>';
require_once __DIR__ . '/includes/header.php';
?>

<div class="pd" id="pd-root" aria-live="polite">

    <!-- Top band with breadcrumb (sits under the floating navigation) -->
    <div class="pd-band">
        <div class="pd-container">
            <nav class="pd-crumbs" aria-label="Brotkrumen">
                <ol>
                    <li><a href="<?= BASE ?>/">Start</a></li>
                    <li><a href="<?= BASE ?>/immobilien-angebote">Immobilien</a></li>
                    <li><span aria-current="page" id="pd-crumb-title">Objekt</span></li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="pd-container pd-wrap">

        <!-- Loading skeleton -->
        <div id="pd-loading" class="pd-skeleton" role="status" aria-label="Immobilie wird geladen">
            <div class="pd-sk pd-sk-gallery"></div>
            <div class="pd-sk pd-sk-title"></div>
            <div class="pd-sk pd-sk-line short"></div>
            <div class="pd-sk pd-sk-line"></div>
            <div class="pd-sk pd-sk-line"></div>
        </div>

        <!-- Error state (filled by JS) -->
        <div id="pd-error" class="pd-error" role="alert" hidden></div>

        <!-- Property content (filled by JS) -->
        <div id="pd-content" hidden></div>

        <noscript>
            <div class="pd-error">
                <h2>JavaScript erforderlich</h2>
                <p>Bitte aktivieren Sie JavaScript, um die Immobilie anzuzeigen, oder kontaktieren Sie uns direkt.</p>
                <div class="pd-error-actions">
                    <a class="pd-btn pd-btn-primary" href="tel:+4916098787878"><i class="fas fa-phone"></i> +49 160 98787878</a>
                    <a class="pd-btn pd-btn-outline" href="<?= BASE ?>/immobilien-angebote">Alle Immobilien</a>
                </div>
            </div>
        </noscript>
    </div>
</div>

<!-- Contact modal -->
<div id="contact-modal" class="pd-modal" role="dialog" aria-modal="true" aria-labelledby="contact-modal-title" hidden>
    <div class="pd-modal-box">
        <button type="button" class="pd-modal-close" data-close-contact aria-label="Schließen">&times;</button>
        <h2 id="contact-modal-title">Interesse an dieser Immobilie?</h2>
        <p>Schreiben Sie uns – wir melden uns zeitnah bei Ihnen.</p>

        <form id="propertyInquiryForm" class="pd-form" method="post" action="<?= BASE ?>/api/contact.php?action=inquiry">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(generate_csrf_token(), ENT_QUOTES) ?>">
            <input type="hidden" name="form_type" value="immobilien_anfrage_detailseite">
            <input type="hidden" name="property_id" id="inquiry_property_id" value="">
            <input type="hidden" name="property_title" id="inquiry_property_title" value="">
            <input type="hidden" name="property_link" id="inquiry_property_link" value="">

            <input type="text" name="name" placeholder="Ihr Name *" autocomplete="name" required>
            <input type="email" name="email" placeholder="Ihre E-Mail *" autocomplete="email" required>
            <input type="tel" name="phone" placeholder="Telefon" autocomplete="tel">
            <textarea name="message" rows="4" placeholder="Ihre Nachricht"></textarea>

            <?php $consentPrefix = 'pd'; include __DIR__ . '/includes/consent-fields.php'; ?>
            <button type="submit" class="pd-btn pd-btn-primary pd-btn-block">
                <i class="fas fa-paper-plane"></i> Anfrage senden
            </button>
            <div id="propertyInquiryMsg" class="pd-form-msg" role="status"></div>
        </form>

        <div class="pd-modal-alt">
            <a href="tel:+4916098787878" class="pd-btn pd-btn-outline"><i class="fas fa-phone"></i> Anrufen</a>
            <a href="https://wa.me/4916098787878" id="modal-wa" class="pd-btn pd-btn-wa" target="_blank" rel="noopener noreferrer"><i class="fab fa-whatsapp"></i> WhatsApp</a>
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
                        message: 'Anfrage konnte nicht gesendet werden (HTTP ' + r.status + ').'
                    };
                }
                json._httpOk = r.ok;
                return json;
            });
        }

        function show(ok, text) {
            msg.style.display = 'block';
            msg.style.background = ok ? '#d1fae5' : '#fee2e2';
            msg.style.color = ok ? '#065f46' : '#991b1b';
            msg.textContent = text;
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
                    if (json.success && json._httpOk) {
                        show(true, 'Vielen Dank. Ihre Anfrage wurde gesendet.');
                        form.reset();
                    } else {
                        show(false, json.message || 'Anfrage konnte nicht gesendet werden. Bitte rufen Sie uns an: +49 160 98787878');
                    }
                })
                .catch(function() {
                    show(false, 'Verbindungsfehler. Bitte versuchen Sie es erneut oder rufen Sie uns an: +49 160 98787878');
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
