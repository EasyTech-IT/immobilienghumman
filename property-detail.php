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

<div id="propertyInquiryFormSource" hidden>
    <form id="propertyInquiryForm" class="pd-form" method="post" action="<?= BASE ?>/api/contact.php?action=inquiry">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(generate_csrf_token(), ENT_QUOTES) ?>">
        <input type="hidden" name="form_type" value="immobilien_anfrage_detailseite">
        <input type="hidden" name="property_id" id="inquiry_property_id" value="">
        <input type="hidden" name="property_title" id="inquiry_property_title" value="">
        <input type="hidden" name="property_link" id="inquiry_property_link" value="">

        <div class="pd-form-row">
            <div class="pd-form-field">
                <label for="pd-inquiry-first-name">Vorname *</label>
                <input id="pd-inquiry-first-name" type="text" name="vorname" autocomplete="given-name" required>
            </div>
            <div class="pd-form-field">
                <label for="pd-inquiry-last-name">Nachname *</label>
                <input id="pd-inquiry-last-name" type="text" name="nachname" autocomplete="family-name" required>
            </div>
        </div>
        <div class="pd-form-row">
            <div class="pd-form-field">
            <label for="pd-inquiry-email">E-Mail *</label>
            <input id="pd-inquiry-email" type="email" name="email" autocomplete="email" required>
            </div>
            <div class="pd-form-field">
                <label for="pd-inquiry-phone">Telefon *</label>
                <input id="pd-inquiry-phone" type="tel" name="phone" autocomplete="tel" maxlength="50" required>
            </div>
        </div>
        <p class="pd-form-note">Für die Exposé-Anfrage benötigen wir zusätzlich Ihre Anschrift.</p>
        <div class="pd-form-row">
            <div class="pd-form-field">
                <label for="pd-inquiry-street">Straße (für Exposé erforderlich)</label>
                <input id="pd-inquiry-street" type="text" name="street" autocomplete="address-line1" maxlength="100">
            </div>
            <div class="pd-form-field">
                <label for="pd-inquiry-house-number">Hausnummer (für Exposé erforderlich)</label>
                <input id="pd-inquiry-house-number" type="text" name="house_number" maxlength="100">
            </div>
        </div>
        <div class="pd-form-row">
            <div class="pd-form-field">
                <label for="pd-inquiry-postal-code">Postleitzahl (für Exposé erforderlich)</label>
                <input id="pd-inquiry-postal-code" type="text" name="postal_code" autocomplete="postal-code" maxlength="100">
            </div>
            <div class="pd-form-field">
                <label for="pd-inquiry-city">Stadt (für Exposé erforderlich)</label>
                <input id="pd-inquiry-city" type="text" name="city" autocomplete="address-level2" maxlength="100">
            </div>
        </div>
        <div class="pd-form-field">
            <label for="pd-inquiry-message">Ihre Nachricht</label>
            <textarea id="pd-inquiry-message" name="message" rows="4"></textarea>
        </div>

        <?php $consentPrefix = 'pd'; include __DIR__ . '/includes/consent-fields.php'; ?>
        <p class="pd-form-note">Für das Exposé bestätigen Sie zunächst Ihre E-Mail-Adresse. Anschließend senden wir Ihnen das freigegebene Exposé automatisch zu.</p>
        <button type="submit" class="pd-btn pd-btn-primary pd-btn-block">
            <i class="fas fa-paper-plane"></i> Anfrage senden
        </button>
        <button type="submit" name="request_expose" value="1" class="pd-btn pd-btn-outline pd-btn-block">
            <i class="far fa-file-pdf"></i> Exposé anfordern
        </button>
        <div id="propertyInquiryMsg" class="pd-form-msg" role="status"></div>
    </form>
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
            var btn = e.submitter || form.querySelector('button[type="submit"]');
            var buttons = form.querySelectorAll('button[type="submit"]');
            var old = btn.innerHTML;
            buttons.forEach(function(button) { button.disabled = true; });
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Wird gesendet ...';
            var data = new FormData(form);
            if (btn.name === 'request_expose') data.set('form_type', 'expose_anfrage');

            fetch(form.action, {
                    method: 'POST',
                    body: data,
                    credentials: 'same-origin'
                })
                .then(parseApiResponse)
                .then(function(json) {
                    if (json.success && json._httpOk) {
                        show(true, json.message || 'Vielen Dank. Ihre Anfrage wurde gesendet.');
                        var propertyFields = ['inquiry_property_id', 'inquiry_property_title', 'inquiry_property_link'].map(function(id) {
                            var input = document.getElementById(id);
                            return input ? input.value : '';
                        });
                        form.reset();
                        ['inquiry_property_id', 'inquiry_property_title', 'inquiry_property_link'].forEach(function(id, index) {
                            var input = document.getElementById(id);
                            if (input) input.value = propertyFields[index];
                        });
                    } else {
                        show(false, json.message || 'Anfrage konnte nicht gesendet werden. Bitte rufen Sie uns an: +49 160 98787878');
                    }
                })
                .catch(function() {
                    show(false, 'Die Übertragung wurde unterbrochen. Bitte kontaktieren Sie uns telefonisch, bevor Sie die Anfrage erneut senden: +49 160 98787878');
                })
                .finally(function() {
                    var widget = form.querySelector('.cf-turnstile');
                    buttons.forEach(function(button) { button.disabled = !!widget; });
                    btn.innerHTML = old;
                    if (widget) {
                        try {
                            if (!window.turnstile) throw new Error('Turnstile nicht verfügbar');
                            window.turnstile.reset(widget);
                        } catch (error) {
                            console.error('Sicherheitsprüfung konnte nicht zurückgesetzt werden:', error);
                            msg.textContent += ' Für eine weitere Anfrage laden Sie bitte die Seite neu.';
                        }
                    }
                });
        });
    })();
</script>

<!-- Footer -->

<?php require_once __DIR__ . '/includes/footer.php'; ?>
