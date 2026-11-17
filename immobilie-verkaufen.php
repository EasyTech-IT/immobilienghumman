<?php
require_once __DIR__ . '/includes/config.php';

$pageTitle = 'Immobilie verkaufen: Begleitung in Bad Vilbel & Rhein-Main';
$pageDescription = 'Immobilienverkauf in Bad Vilbel und Rhein-Main: Marktwert einordnen, Vermarktung planen und den Verkauf bis zur Übergabe begleiten.';
$pageKeywords = 'Immobilie verkaufen Bad Vilbel, Haus verkaufen Rhein-Main, Immobilienbewertung';
$activeNav = 'immobilien';
$ogUrl = SITE_URL . '/immobilie-verkaufen';
$extraHead = '<link rel="stylesheet" href="' . BASE . '/immobilie-verkaufen.css?v=' . (@filemtime(__DIR__ . '/immobilie-verkaufen.css') ?: 1) . '">';

require_once __DIR__ . '/includes/header.php';
?>

<main class="seller-page">
    <section class="seller-hero">
        <div class="seller-container seller-hero-grid">
            <div class="seller-hero-copy">
                <p class="seller-eyebrow">Immobilienverkauf · Bad Vilbel &amp; Rhein-Main</p>
                <h1>Immobilie verkaufen im Rhein-Main-Gebiet</h1>
                <p class="seller-lead">Wir unterstützen Eigentümer bei Wertermittlung, Vermarktung und Verkauf – abgestimmt auf Objekt, Lage und Marktsituation.</p>
                <div class="seller-actions">
                    <a class="btn-premium" href="<?= BASE ?>/analyse-beratung?bereich=immobilie#analyser">
                        <i class="fas fa-calculator" aria-hidden="true"></i> Immobilienwert ermitteln
                    </a>
                    <a class="btn-outline-premium" href="#verkaufsprozess">Verkaufsablauf ansehen</a>
                </div>
                <p class="seller-note">Starten Sie mit einer kostenlosen, unverbindlichen Onlinebewertung.</p>
            </div>
            <div class="seller-hero-image">
                <img src="https://images.pexels.com/photos/1396122/pexels-photo-1396122.jpeg"
                    alt="Wohnhausansicht als Beispiel" fetchpriority="high">
            </div>
        </div>
    </section>

    <section class="seller-section" id="preisstrategie">
        <div class="seller-container">
            <div class="seller-section-heading">
                <p class="seller-eyebrow">Preisstrategie</p>
                <h2>Ein Angebotspreis, der zum Objekt passt</h2>
                <p>Wir betrachten Lage, Zustand, Ausstattung und aktuelle Vergleichsdaten. Die Onlinebewertung ist eine erste Orientierung; vor der Vermarktung wird der konkrete Rahmen gemeinsam festgelegt.</p>
            </div>
            <div class="seller-factor-grid">
                <article class="seller-factor">
                    <i class="fas fa-map-location-dot" aria-hidden="true"></i>
                    <h3>Lage und Nachfrage</h3>
                    <p>Standort, Umfeld und die Nachfrage in der jeweiligen Teilregion fließen in die Einordnung ein.</p>
                </article>
                <article class="seller-factor">
                    <i class="fas fa-house" aria-hidden="true"></i>
                    <h3>Zustand und Ausstattung</h3>
                    <p>Fläche, Baujahr, Modernisierungen und Ausstattungsmerkmale prägen die Vermarktungsstrategie.</p>
                </article>
                <article class="seller-factor">
                    <i class="fas fa-chart-line" aria-hidden="true"></i>
                    <h3>Vergleichswerte und Markt</h3>
                    <p>Vergleichbare Angebote und verfügbare Marktdaten helfen, einen nachvollziehbaren Preisrahmen abzuleiten.</p>
                </article>
            </div>
        </div>
    </section>

    <section class="seller-section seller-section-soft" id="verkaufsprozess">
        <div class="seller-container">
            <div class="seller-section-heading">
                <p class="seller-eyebrow">Verkaufsprozess</p>
                <h2>Von der ersten Einschätzung bis zur Übergabe</h2>
                <p>Die einzelnen Schritte richten sich nach Ihrer Immobilie und Ihrer Situation. Sie entscheiden gemeinsam mit uns, wie es weitergeht.</p>
            </div>
            <ol class="seller-process">
                <li>
                    <span class="seller-step-number">01</span>
                    <div><h3>Vorhaben und Unterlagen besprechen</h3><p>Wir klären Ihre Ziele und sichten die Angaben und Unterlagen zum Objekt.</p></div>
                </li>
                <li>
                    <span class="seller-step-number">02</span>
                    <div><h3>Preisrahmen und Strategie festlegen</h3><p>Auf Basis der Objektmerkmale und des Marktes besprechen wir die passende Positionierung.</p></div>
                </li>
                <li>
                    <span class="seller-step-number">03</span>
                    <div><h3>Vermarktung und Besichtigungen organisieren</h3><p>Nach Ihrer Freigabe bereiten wir die Präsentation vor und koordinieren Interessenten und Termine.</p></div>
                </li>
                <li>
                    <span class="seller-step-number">04</span>
                    <div><h3>Verhandlung und Übergabe begleiten</h3><p>Wir unterstützen die Abstimmung bis zum Notartermin und zur Übergabe der Immobilie.</p></div>
                </li>
            </ol>
        </div>
    </section>

    <section class="seller-section" id="naechste-schritte">
        <div class="seller-container">
            <div class="seller-section-heading">
                <p class="seller-eyebrow">Nächste Schritte</p>
                <h2>So beginnt Ihr Verkauf</h2>
                <p>Ein erster Überblick hilft, die nächsten Entscheidungen in Ruhe zu treffen.</p>
            </div>
            <div class="seller-next-grid">
                <article><span>1</span><h3>Wert einschätzen</h3><p>Geben Sie die wichtigsten Eckdaten für eine erste Onlinebewertung ein.</p></article>
                <article><span>2</span><h3>Situation besprechen</h3><p>Teilen Sie uns mit, was Sie vorhaben und welche Fragen noch offen sind.</p></article>
                <article><span>3</span><h3>Vorgehen abstimmen</h3><p>Wir besprechen mögliche Schritte und die Unterlagen, die dafür gebraucht werden.</p></article>
            </div>
            <a class="seller-inline-link" href="<?= BASE ?>/analyse-beratung?bereich=immobilie#analyser">
                Onlinebewertung starten <i class="fas fa-arrow-right" aria-hidden="true"></i>
            </a>
        </div>
    </section>

    <section class="seller-advisor">
        <div class="seller-container seller-advisor-grid">
            <div class="seller-brand-image">
                <img src="<?= htmlspecialchars(SITE_LOGO, ENT_QUOTES) ?>" alt="Immobilien Ghumman">
            </div>
            <div>
                <p class="seller-eyebrow">Ihr Ansprechpartner</p>
                <h2>Persönliche Begleitung beim Immobilienverkauf</h2>
                <p>Nasir Ghumman und das Team von Immobilien Ghumman besprechen mit Ihnen die Bewertung, die Vermarktung und die nächsten Schritte für Ihre Immobilie.</p>
                <a class="seller-inline-link" href="<?= BASE ?>/#kontakt">
                    Kontakt aufnehmen <i class="fas fa-arrow-right" aria-hidden="true"></i>
                </a>
            </div>
        </div>
    </section>

    <section class="seller-section seller-faq" id="fragen">
        <div class="seller-container">
            <div class="seller-section-heading">
                <p class="seller-eyebrow">Fragen und Antworten</p>
                <h2>Wichtige Fragen zum Verkauf</h2>
            </div>
            <div class="seller-faq-list">
                <article>
                    <h3>Wie wird der Angebotspreis festgelegt?</h3>
                    <p>Wir beziehen Lage, Zustand, Ausstattung und verfügbare Vergleichsdaten ein. Eine Onlinebewertung liefert eine erste Orientierung; die konkrete Preisstrategie wird anhand des Objekts abgestimmt.</p>
                </article>
                <article>
                    <h3>Welche Unterlagen sollte ich bereithalten?</h3>
                    <p>Hilfreich sind zum Beispiel Grundriss, Wohnflächenangabe, Energieausweis und Nachweise zu Modernisierungen. Welche Unterlagen im Einzelfall nötig sind, klären wir im Gespräch.</p>
                </article>
                <article>
                    <h3>Wie lange dauert ein Immobilienverkauf?</h3>
                    <p>Das hängt unter anderem von Objekt, Preis, Unterlagenlage und Nachfrage ab. Einen verlässlichen Zeitrahmen besprechen wir erst, wenn die Ausgangslage bekannt ist.</p>
                </article>
                <article>
                    <h3>Ist eine erste Onlinebewertung verbindlich?</h3>
                    <p>Nein. Sie dient der ersten Orientierung und ersetzt keine individuelle Prüfung der Immobilie oder eine persönliche Beratung.</p>
                </article>
            </div>
        </div>
    </section>

    <section class="seller-close">
        <div class="seller-container">
            <h2>Planen Sie den Verkauf Ihrer Immobilie?</h2>
            <p>Starten Sie mit den Eckdaten – wir besprechen anschließend die passenden nächsten Schritte.</p>
            <a class="btn-premium" href="<?= BASE ?>/analyse-beratung?bereich=immobilie#analyser">
                <i class="fas fa-calculator" aria-hidden="true"></i> Immobilienwert ermitteln
            </a>
        </div>
    </section>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
