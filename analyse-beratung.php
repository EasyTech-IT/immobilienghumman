<?php
$pageTitle = 'Kostenlose Immobilienanalyse & Beratung | Immobilien Ghumman';
$pageDescription = 'Immobilienanalyse, Baufinanzierungsberatung und Hausverwaltung einfach online anfragen.';
$activeNav = 'analyse';
require_once __DIR__ . '/includes/config.php';
$extraHead = '<link rel="stylesheet" href="' . BASE . '/analyse-beratung.css?v=' . (int)filemtime(__DIR__ . '/analyse-beratung.css') . '">';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/analyse-fragen.php';
$wzIconsOnly = true;
require_once __DIR__ . '/includes/bewertung-wizard.php';
$flows = analyse_flows();
$escape = static fn($value) => htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
?>

<main class="analysis-page" id="analyser">
    <div class="analysis-shell">
        <div class="analysis-heading">
            <div>
                <h1>Bewertung und Beratung starten</h1>
                <p>Online-Formular: Wählen Sie Ihr Anliegen und beantworten Sie zwölf kurze Fragen. Wir beraten Sie persönlich und unverbindlich.</p>
            </div>
            <a href="tel:+4916098787878" class="analysis-phone"><i class="fas fa-phone" aria-hidden="true"></i> Lieber anrufen</a>
        </div>

        <div class="analysis-tabs" role="tablist" aria-label="Anliegen auswählen">
            <?php foreach ($flows as $type => $flow): ?>
                <?php $first = $type === 'analyse_immobilie'; ?>
                <button type="button" class="analysis-tab<?= $first ? ' is-active' : '' ?>" role="tab"
                        id="tab-<?= $escape($type) ?>" aria-controls="panel-<?= $escape($type) ?>"
                        aria-label="<?= $escape($flow['title']) ?>" aria-selected="<?= $first ? 'true' : 'false' ?>" tabindex="<?= $first ? '0' : '-1' ?>">
                    <span class="analysis-tab-icon"><i class="fas <?= $escape($flow['icon']) ?>" aria-hidden="true"></i></span>
                    <span><strong><span class="analysis-tab-full"><?= $escape($flow['title']) ?></span><span class="analysis-tab-short" aria-hidden="true"><?= $escape($type === 'analyse_immobilie' ? 'Immobilien' : ($type === 'analyse_finanzierung' ? 'Finanzierung' : 'Hausverwaltung')) ?></span></strong><small><?= $escape($flow['description']) ?></small><?php if ($first): ?><span class="analysis-recommended">Unsere Empfehlung</span><?php endif; ?></span>
                </button>
            <?php endforeach; ?>
        </div>

        <?php foreach ($flows as $type => $flow): ?>
            <?php $first = $type === 'analyse_immobilie'; ?>
            <section class="analysis-panel" role="tabpanel" id="panel-<?= $escape($type) ?>"
                     aria-labelledby="tab-<?= $escape($type) ?>"<?= $first ? '' : ' hidden' ?>>
                <div class="analysis-panel-header">
                    <div><h2><?= $escape($flow['title']) ?></h2><p><?= $escape($flow['description']) ?></p></div>
                    <span><i class="fas fa-shield-halved" aria-hidden="true"></i> Kostenlos &amp; unverbindlich</span>
                </div>
                <div class="analysis-progress">
                    <span class="analysis-progress-text" aria-live="polite">Frage 1 von 12</span>
                    <progress value="1" max="12" aria-label="Fortschritt"></progress>
                </div>

                <form class="analysis-form" action="<?= BASE ?>/api/contact.php?action=general" method="post" novalidate>
                    <input type="hidden" name="csrf_token" value="<?= $escape(generate_csrf_token()) ?>">
                    <input type="hidden" name="form_type" value="<?= $escape($type) ?>">
                    <input type="hidden" name="privacy_required" value="1">
                    <?php foreach ($flow['questions'] as $index => $question): ?>
                        <fieldset class="analysis-step" data-step="<?= $index + 1 ?>"<?= !empty($question['multiple']) ? ' data-multiple="true"' : '' ?><?= $index === 0 ? '' : ' hidden' ?>>
                            <legend tabindex="-1"><?= $escape($question['label']) ?></legend>
                            <?php if (!empty($question['hint'])): ?><p class="analysis-hint"><?= $escape($question['hint']) ?></p><?php endif; ?>
                            <?php if (!empty($question['multiple'])): ?><p class="analysis-hint">Mehrere Antworten möglich. Danach auf Weiter klicken.</p><?php endif; ?>
                                <div class="analysis-options">
                                    <?php foreach ($question['options'] as $option): ?>
                                        <label class="analysis-option">
                                            <?php $bound = $type === 'analyse_finanzierung' ? analyse_amount_bound($question['name'], $option) : null; ?>
                                            <input type="<?= !empty($question['multiple']) ? 'checkbox' : 'radio' ?>" name="analyse_<?= $escape($question['name']) ?><?= !empty($question['multiple']) ? '[]' : '' ?>" value="<?= $escape($option) ?>"<?= $bound !== null ? ' data-amount-bound="' . $bound . '"' : '' ?><?= !empty($question['multiple']) && $option === $question['exclusive'] ? ' data-exclusive="true"' : '' ?><?= empty($question['multiple']) ? ' required' : '' ?>>
                                            <span><?= wz_icon(analyse_option_icon($question['name'], $option)) ?><span class="analysis-option-label"><?= $escape($option) ?></span><span class="analysis-option-mark" aria-hidden="true"><i class="fas fa-check"></i></span></span>
                                        </label>
                                    <?php endforeach; ?>
                                </div>
                        </fieldset>
                    <?php endforeach; ?>

                    <fieldset class="analysis-step analysis-review" data-step="13" hidden>
                        <legend tabindex="-1">Prüfen Sie Ihre Angaben</legend>
                        <p class="analysis-hint">Hier sehen Sie alle Antworten. Sie können jede Angabe vor dem Absenden ändern.</p>
                        <dl class="analysis-review-list"></dl>
                        <?php if ($type === 'analyse_finanzierung'): ?>
                            <p class="analysis-calculation">Eine genaue Finanzierungsberechnung besprechen wir persönlich mit Ihnen. Die ausgewählten Bereiche sind noch kein Finanzierungsangebot.</p>
                        <?php endif; ?>
                    </fieldset>

                    <fieldset class="analysis-step analysis-contact" data-step="14" hidden>
                        <legend tabindex="-1">Wie erreichen wir Sie?</legend>
                        <p class="analysis-hint">Sie erhalten eine Eingangsbestätigung per E-Mail.</p>
                        <div class="analysis-contact-grid">
                            <label>Name <input name="name" autocomplete="name" maxlength="200" required></label>
                            <label>E-Mail <input type="email" name="email" autocomplete="email" maxlength="200" required></label>
                            <label>Telefon <span>(optional)</span><input type="tel" name="phone" autocomplete="tel" maxlength="50"></label>
                            <label>Ort oder Adresse des Objekts <span>(optional)</span><input name="adresse" autocomplete="off" maxlength="200"></label>
                        </div>
                        <label class="analysis-consent">
                            <input type="checkbox" name="privacy_accept" value="1" required>
                            <span>Ich stimme der Verarbeitung und Weiterleitung meiner Angaben an Immobilien Ghumman und EasyTech IT zur Bearbeitung meiner Anfrage gemäß der <a href="<?= BASE ?>/datenschutz" target="_blank" rel="noopener">Datenschutzerklärung</a> zu. *</span>
                        </label>
                        <?php if (TURNSTILE_ENABLED): ?>
                            <div class="analysis-turnstile"><div class="cf-turnstile" data-sitekey="<?= $escape(TURNSTILE_SITE_KEY) ?>" data-theme="light"></div></div>
                        <?php endif; ?>
                    </fieldset>
                    <p class="analysis-error" role="alert" hidden></p>
                    <div class="analysis-actions">
                        <button class="analysis-back" type="button" hidden><i class="fas fa-arrow-left" aria-hidden="true"></i> Zurück</button>
                        <button class="analysis-next" type="button">Weiter <i class="fas fa-arrow-right" aria-hidden="true"></i></button>
                        <button class="analysis-submit" type="submit" hidden>Anfrage senden <i class="fas fa-arrow-right" aria-hidden="true"></i></button>
                    </div>
                </form>
                <div class="analysis-result" role="status" hidden></div>
            </section>
        <?php endforeach; ?>

        <div class="analysis-support">
            <div><i class="fas fa-lock" aria-hidden="true"></i><span>Ihre Angaben werden vertraulich behandelt.</span></div>
            <div><i class="fas fa-comments" aria-hidden="true"></i><span>Persönliche Beratung statt automatischer Bewertung.</span></div>
        </div>
        <div class="analysis-trust" aria-label="Unsere Vertrauenssiegel">
            <img src="<?= BASE ?>/assets/trust/wer-kennt-den-besten.png" alt="Wer kennt den Besten: Sehr gut" loading="lazy">
            <img src="<?= BASE ?>/assets/trust/empfehlungssiegel.png" alt="Trustsiegel.de: 100 Prozent Empfehlungen" loading="lazy">
            <img src="<?= BASE ?>/assets/trust/cylex-trusted.webp" alt="Cylex Trusted" loading="lazy">
        </div>
        <?php $googleReviews = require __DIR__ . '/includes/google-reviews.php'; ?>
        <section class="analysis-info analysis-reviews" aria-labelledby="analysis-reviews">
            <div class="analysis-info-heading">
                <h2 id="analysis-reviews">Erfahrungen mit Immobilien Ghumman</h2>
                <p>Eine Auswahl der Google-Rezensionen, die auch auf unserer Startseite stehen.</p>
            </div>
            <p class="analysis-scroll-hint">Weitere Bewertungen seitlich ansehen <span aria-hidden="true">→</span></p>
            <div class="analysis-review-cards" tabindex="0" role="region" aria-label="Kundenbewertungen">
                <?php foreach (array_slice($googleReviews, 0, 4) as $review): ?>
                    <article>
                        <div class="analysis-stars" aria-label="5 von 5 Sternen">★★★★★</div>
                        <p>„<?= $escape($review['text']) ?>“</p>
                        <strong><?= $escape($review['name']) ?></strong>
                        <span>Google-Rezension</span>
                    </article>
                <?php endforeach; ?>
            </div>
            <div class="analysis-faq-actions"><a class="analysis-info-cta" href="<?= $escape(GOOGLE_REVIEWS_URL) ?>" target="_blank" rel="noopener noreferrer">Alle Bewertungen ansehen</a></div>
        </section>
        <section class="analysis-info analysis-regions" aria-labelledby="analysis-regions">
            <div class="analysis-info-heading">
                <h2 id="analysis-regions">Immobilienberatung in Ihrer Region</h2>
                <p>Von Bad Vilbel aus begleiten wir Sie im Rhein-Main-Gebiet.</p>
            </div>
            <p class="analysis-scroll-hint">Regionen seitlich ansehen <span aria-hidden="true">→</span></p>
            <div class="analysis-region-cards" tabindex="0" role="region" aria-label="Unsere Regionen">
                <article><i class="fas fa-location-dot" aria-hidden="true"></i><h3>Bad Vilbel</h3><p>Unser Standort und die nähere Umgebung.</p></article>
                <article><i class="fas fa-city" aria-hidden="true"></i><h3>Frankfurt am Main</h3><p>Beratung für Ihre Immobilie in Frankfurt.</p></article>
                <article><i class="fas fa-map" aria-hidden="true"></i><h3>Wetterau</h3><p>Für Eigentümer und Interessenten in der Region.</p></article>
                <article><i class="fas fa-house" aria-hidden="true"></i><h3>Rhein-Main</h3><p>Persönliche Begleitung im Rhein-Main-Gebiet.</p></article>
            </div>
            <div class="analysis-faq-actions"><a class="analysis-info-cta" href="<?= BASE ?>/analyse-beratung?bereich=immobilie#analyser" data-analysis-target="immobilie">Immobilienanalyse starten</a></div>
        </section>
        <section class="analysis-info" aria-labelledby="analysis-faq">
            <div class="analysis-info-heading"><h2 id="analysis-faq">Häufige Fragen zur Immobilienbewertung</h2><p>Das sollten Sie vor Ihrer Anfrage wissen.</p></div>
            <div class="analysis-faq">
                <details><summary>Was kostet die Anfrage zur Immobilienbewertung?</summary><p>Ihre Anfrage und die erste Beratung sind kostenlos und unverbindlich. Den weiteren Ablauf besprechen wir persönlich mit Ihnen.</p></details>
                <details><summary>Welche Angaben helfen bei der Bewertung?</summary><p>Art, Lage, Größe und Zustand der Immobilie sind wichtige Anhaltspunkte. Auch unbekannte Angaben können Sie im Formular auswählen.</p></details>
                <details><summary>Erhalte ich sofort einen Immobilienwert?</summary><p>Nein. Wir prüfen Ihre Angaben und besprechen die nächsten Schritte persönlich mit Ihnen.</p></details>
                <details><summary>Was passiert nach dem Absenden?</summary><p>Ihre Anfrage wird zur Bearbeitung an Immobilien Ghumman und EasyTech IT weitergeleitet. Sie erhalten eine Eingangsbestätigung per E-Mail.</p></details>
                <details><summary>Kann ich meine Antworten ändern?</summary><p>Ja. In der Übersicht vor dem Kontaktformular können Sie jede Antwort überprüfen und ändern.</p></details>
                <details><summary>Ist die Bewertung ein Gutachten für Gericht oder Finanzamt?</summary><p>Eine persönliche Immobilienbewertung ersetzt nicht automatisch ein Verkehrswertgutachten. Teilen Sie uns den Anlass Ihrer Bewertung mit.</p></details>
            </div>
            <div class="analysis-faq-actions">
                <a class="analysis-info-cta" href="<?= BASE ?>/analyse-beratung?bereich=finanzierung#analyser" data-analysis-target="finanzierung">Baufinanzierung anfragen</a>
                <a class="analysis-info-cta" href="<?= BASE ?>/analyse-beratung?bereich=hausverwaltung#analyser" data-analysis-target="hausverwaltung">Hausverwaltung anfragen</a>
            </div>
        </section>
    </div>
</main>
<script src="<?= BASE ?>/js/analyse-beratung.js?v=<?= (int)filemtime(__DIR__ . '/js/analyse-beratung.js') ?>" defer></script>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
