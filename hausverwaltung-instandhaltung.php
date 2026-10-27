<?php
require_once __DIR__ . '/includes/config.php';
header('Location: ' . BASE . '/hausverwaltung#leistung-instandhaltung', true, 301);
exit;

$pageTitle       = 'Instandhaltung – Immobilien Ghumman';
$pageDescription = 'Professionelles Instandhaltungsmanagement für Ihre Immobilie.';
$pageKeywords    = 'Instandhaltung, Immobilienpflege, Reparaturen, Bad Vilbel';
$activeNav       = 'hausverwaltung';
$extraHead       = '<style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Montserrat, sans-serif;
            line-height: 1.6;
            color: #333;
            overflow-x: hidden;
        }

        .page-header {
            background: linear-gradient(135deg, #16a085 0%, #1abc9c 100%);
            color: white;
            padding: 120px 20px 80px;
            text-align: center;
        }

        .breadcrumb {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
            margin-bottom: 20px;
            font-size: 14px;
            opacity: 0.9;
        }

        .breadcrumb a {
            color: white;
            text-decoration: none;
            transition: opacity 0.3s;
        }

        .breadcrumb a:hover {
            opacity: 0.7;
        }

        .page-header h1 {
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 20px;
        }

        .page-header p {
            font-size: 1.2rem;
            max-width: 700px;
            margin: 0 auto;
            opacity: 0.95;
        }

        .content-section {
            padding: 80px 20px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .content-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 60px;
            margin-bottom: 60px;
        }

        .main-content {
            background: white;
        }

        .content-intro {
            background: linear-gradient(135deg, #e8f8f5 0%, #d1f2eb 100%);
            padding: 40px;
            border-radius: 16px;
            margin-bottom: 50px;
            border-left: 5px solid #16a085;
        }

        .content-intro h2 {
            color: #1e293b;
            font-size: 2rem;
            margin-bottom: 20px;
        }

        .content-intro p {
            font-size: 1.1rem;
            line-height: 1.8;
            color: #475569;
        }

        .content-block {
            margin-bottom: 50px;
        }

        .content-block h2 {
            color: #1e293b;
            font-size: 2rem;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 3px solid #16a085;
        }

        .content-block h3 {
            color: #16a085;
            font-size: 1.5rem;
            margin-top: 30px;
            margin-bottom: 15px;
        }

        .content-block p {
            font-size: 1.05rem;
            line-height: 1.8;
            color: #4a5568;
            margin-bottom: 20px;
        }

        .content-block ul,
        .content-block ol {
            margin-left: 25px;
            margin-bottom: 25px;
        }

        .content-block li {
            font-size: 1.05rem;
            line-height: 1.8;
            color: #4a5568;
            margin-bottom: 12px;
            padding-left: 10px;
        }

        .content-block li strong {
            color: #16a085;
        }

        .highlight-box {
            background: #fff8e1;
            border-left: 4px solid #ffd700;
            padding: 25px;
            border-radius: 8px;
            margin: 30px 0;
        }

        .highlight-box h3 {
            color: #f59e0b;
            margin-top: 0;
        }

        .info-box {
            background: #e8f8f5;
            border-left: 4px solid #1abc9c;
            padding: 25px;
            border-radius: 8px;
            margin: 30px 0;
        }

        .info-box h3 {
            color: #16a085;
            margin-top: 0;
        }

        .sidebar {
            position: sticky;
            top: 100px;
            align-self: start;
        }

        .sidebar-widget {
            background: white;
            border-radius: 16px;
            padding: 30px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            margin-bottom: 30px;
        }

        .sidebar-widget h3 {
            color: #1e293b;
            font-size: 1.3rem;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #e5e7eb;
        }

        .quick-links {
            list-style: none;
        }

        .quick-links li {
            margin-bottom: 12px;
        }

        .quick-links a {
            color: #475569;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px;
            border-radius: 8px;
            transition: all 0.3s;
        }

        .quick-links a:hover {
            background: #f1f5f9;
            color: #16a085;
            transform: translateX(5px);
        }

        .cta-widget {
            background: linear-gradient(135deg, #16a085 0%, #1abc9c 100%);
            color: white;
            text-align: center;
        }

        .cta-widget h3 {
            color: white;
            border: none;
        }

        .cta-widget p {
            margin-bottom: 20px;
            opacity: 0.95;
        }

        .cta-btn {
            display: inline-block;
            background: white;
            color: #16a085;
            padding: 12px 24px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
        }

        .cta-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
        }

        .faq-section {
            background: #f8fafc;
            padding: 80px 20px;
        }

        .faq-container {
            max-width: 900px;
            margin: 0 auto;
        }

        .faq-header {
            text-align: center;
            margin-bottom: 60px;
        }

        .faq-header h2 {
            font-size: 2.5rem;
            color: #1e293b;
            margin-bottom: 15px;
        }

        .faq-header p {
            font-size: 1.1rem;
            color: #64748b;
        }

        .faq-item {
            background: white;
            border-radius: 12px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            overflow: hidden;
            transition: all 0.3s;
        }

        .faq-item:hover {
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }

        .faq-question {
            width: 100%;
            background: none;
            border: none;
            padding: 25px 30px;
            text-align: left;
            font-size: 1.1rem;
            font-weight: 600;
            color: #1e293b;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: all 0.3s;
        }

        .faq-question:hover {
            color: #16a085;
        }

        .faq-icon {
            flex-shrink: 0;
            transition: transform 0.3s;
        }

        .faq-item.active .faq-icon {
            transform: rotate(45deg);
        }

        .faq-answer {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease;
        }

        .faq-item.active .faq-answer {
            max-height: 1000px;
        }

        .faq-answer-content {
            padding: 0 30px 30px;
            color: #475569;
            line-height: 1.8;
        }

        .faq-answer-content ul {
            margin: 15px 0 15px 20px;
        }

        .faq-answer-content li {
            margin-bottom: 10px;
        }

        .contact-cta-section {
            background: linear-gradient(135deg, #8e44ad 0%, #9b59b6 100%);
            color: white;
            text-align: center;
            padding: 60px 20px;
        }

        .contact-cta-section h2 {
            font-size: 2.5rem;
            margin-bottom: 15px;
        }

        .contact-cta-section p {
            font-size: 1.2rem;
            margin-bottom: 30px;
            opacity: 0.95;
        }

        .contact-buttons {
            display: flex;
            justify-content: center;
            gap: 20px;
            flex-wrap: wrap;
        }

        .contact-btn-primary {
            background: white;
            color: #8e44ad;
            padding: 15px 35px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }

        .contact-btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
        }

        .contact-btn-secondary {
            background: transparent;
            color: white;
            border: 2px solid white;
            padding: 15px 35px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
        }

        .contact-btn-secondary:hover {
            background: white;
            color: #8e44ad;
        }

        @media (max-width: 968px) {
            .content-grid {
                grid-template-columns: 1fr;
                gap: 40px;
            }

            .sidebar {
                position: static;
            }

            .page-header h1 {
                font-size: 2.2rem;
            }

            .content-intro {
                padding: 30px;
            }
        }

        @media (max-width: 640px) {
            .page-header {
                padding: 100px 20px 60px;
            }

            .page-header h1 {
                font-size: 1.8rem;
            }

            .content-section {
                padding: 50px 20px;
            }

            .faq-question {
                font-size: 1rem;
                padding: 20px;
            }

            .contact-buttons {
                flex-direction: column;
                align-items: center;
            }
        }
    </style>';
$extraScripts    = '';
require_once __DIR__ . '/includes/header.php';
?>


<!-- Page Hero -->
<section class="page-hero">
    <div class="page-hero-inner">
        <nav class="page-hero-breadcrumb" aria-label="Breadcrumb">
            <a href="<?= BASE ?>/">Startseite</a><span>/</span>
            <a href="<?= BASE ?>/hausverwaltung">Hausverwaltung</a><span>/</span>
            <span>Instandhaltung</span>
        </nav>
        <div class="page-hero-badge"><i class="fas fa-tools"></i> Werterhaltung &amp; Wertentwicklung</div>
        <h1>Instandhaltung &amp; <span class="accent">Reparaturen</span></h1>
        <p>Professionelles Management fuer Wartung und Reparaturen Ihrer Immobilie &ndash; schnell, zuverlaessig und mit geprüften Fachbetrieben.</p>
        <div class="page-hero-btns">
            <a href="tel:+4916098787878" class="btn-hero-primary"><i class="fas fa-phone"></i> Jetzt anfragen</a>
            <a href="<?= BASE ?>/hausverwaltung" class="btn-hero-secondary"><i class="fas fa-building"></i> Alle Leistungen</a>
        </div>
    </div>
</section>
<!-- Stats Strip -->
<section class="hero-stats-strip">
    <div class="container">
        <div class="stats-grid-inline">
            <div class="hero-stat"><i class="fas fa-clock"></i>
                <div><span class="hero-stat-number">24 Std.</span><span class="hero-stat-label">Reaktionszeit bei Schaeden</span></div>
            </div>
            <div class="hero-stat"><i class="fas fa-network-wired"></i>
                <div><span class="hero-stat-number">50+</span><span class="hero-stat-label">Gepruefte Fachbetriebe</span></div>
            </div>
            <div class="hero-stat"><i class="fas fa-shield-alt"></i>
                <div><span class="hero-stat-number">Werterhalt</span><span class="hero-stat-label">Langfristig gesichert</span></div>
            </div>
            <div class="hero-stat"><i class="fas fa-award"></i>
                <div><span class="hero-stat-number">10+ J.</span><span class="hero-stat-label">Erfahrung</span></div>
            </div>
        </div>
    </div>
</section>

<!-- Main Content -->
<section class="content-section">
    <div class="content-grid">
        <div class="main-content">
            <!-- Intro -->
            <div class="content-intro">
                <h2>Was ist Instandhaltungsmanagement?</h2>
                <p>Instandhaltung und Reparaturen sind essenzielle Aufgaben zur Werterhaltung und Wertsteigerung
                    Ihrer Immobilie. Als professionelle Hausverwaltung koordinieren wir alle notwendigen Arbeiten –
                    von der präventiven Wartung bis zur schnellen Schadensbehebung. Unser Ziel ist es, Ihre
                    Immobilie in bestem Zustand zu halten und teure Folgeschäden zu vermeiden.</p>
            </div>

            <!-- Arten der Instandhaltung -->
            <div class="content-block">
                <h2>Arten der Instandhaltung</h2>

                <h3>1. Präventive Instandhaltung (Wartung)</h3>
                <p>Vorbeugende Maßnahmen zur Vermeidung von Schäden und Ausfällen:</p>
                <ul>
                    <li><strong>Heizungswartung:</strong> Jährliche Inspektion und Wartung der Heizanlage für
                        optimale Effizienz</li>
                    <li><strong>Dachinspektion:</strong> Regelmäßige Kontrolle auf Schäden, lockere Ziegel und
                        Undichtigkeiten</li>
                    <li><strong>Fassadenkontrolle:</strong> Überprüfung auf Risse, Feuchtigkeitsschäden und
                        Putzabplatzungen</li>
                    <li><strong>Regenrinnen:</strong> Reinigung und Kontrolle der Entwässerungssysteme</li>
                    <li><strong>Aufzugwartung:</strong> TÜV-Prüfungen und regelmäßige Wartungen nach Vorschrift</li>
                    <li><strong>Brandschutz:</strong> Prüfung von Rauchmeldern, Feuerlöschern und Fluchtwegen</li>
                    <li><strong>Elektroinstallationen:</strong> Kontrolle der elektrischen Anlagen und Sicherungen
                    </li>
                </ul>

                <h3>2. Korrektive Instandhaltung (Reparaturen)</h3>
                <p>Schnelle Behebung aufgetretener Schäden und Defekte:</p>
                <ul>
                    <li><strong>Notfallreparaturen:</strong> Sofortmaßnahmen bei Wasserrohrbrüchen,
                        Heizungsausfällen etc.</li>
                    <li><strong>Standardreparaturen:</strong> Behebung von Defekten an Türen, Fenstern,
                        Sanitäranlagen</li>
                    <li><strong>Kleinreparaturen:</strong> Austausch von Glühbirnen, Dichtungen, Armaturen</li>
                    <li><strong>Schadensbehebung:</strong> Reparatur nach Wasserschäden, Sturmschäden etc.</li>
                </ul>

                <h3>3. Zustandsverbessernde Instandhaltung (Modernisierung)</h3>
                <p>Maßnahmen zur Verbesserung des Gebäudezustands:</p>
                <ul>
                    <li><strong>Energetische Sanierung:</strong> Dämmung, neue Fenster, moderne Heizung</li>
                    <li><strong>Barrierefreiheit:</strong> Einbau von Aufzügen, Rampen, ebenerdigen Duschen</li>
                    <li><strong>Technische Updates:</strong> Smart Home, Photovoltaik, Ladeinfrastruktur</li>
                    <li><strong>Modernisierung:</strong> Neue Böden, Bäder, Küchen in Mietwohnungen</li>
                </ul>
            </div>

            <div class="highlight-box">
                <h3>💡 Wichtig zu wissen</h3>
                <p>Präventive Wartung ist deutlich kostengünstiger als Reparaturen! Durch regelmäßige Inspektionen
                    können teure Folgeschäden frühzeitig vermieden werden. Ein gut gewartetes Gebäude hat zudem
                    einen höheren Marktwert.</p>
            </div>

            <!-- Unser Service -->
            <div class="content-block">
                <h2>Unser Instandhaltungsservice</h2>

                <h3>Schadensmeldung und Koordination</h3>
                <p>Wir sind Ihr zentraler Ansprechpartner für alle Instandhaltungsthemen:</p>
                <ul>
                    <li><strong>24/7 Notfallhotline:</strong> Erreichbarkeit bei dringenden Schadensfällen</li>
                    <li><strong>Zentrale Schadensmeldung:</strong> Mieter melden Schäden direkt bei uns</li>
                    <li><strong>Schnelle Bewertung:</strong> Einschätzung der Dringlichkeit und des Umfangs</li>
                    <li><strong>Handwerkerauswahl:</strong> Beauftragung qualifizierter Fachfirmen aus unserem
                        Netzwerk</li>
                    <li><strong>Terminkoordination:</strong> Organisation mit Mietern und Handwerkern</li>
                    <li><strong>Qualitätskontrolle:</strong> Überprüfung der ausgeführten Arbeiten</li>
                    <li><strong>Dokumentation:</strong> Lückenlose Erfassung aller Maßnahmen</li>
                </ul>

                <h3>Präventiver Wartungsplan</h3>
                <p>Wir erstellen für jede Immobilie einen individuellen Wartungsplan:</p>
                <ul>
                    <li>Regelmäßige Objektbegehungen (quartalsweise oder halbjährlich)</li>
                    <li>Checklisten für alle relevanten Gebäudeteile</li>
                    <li>Terminierung gesetzlich vorgeschriebener Prüfungen</li>
                    <li>Dokumentation aller Wartungsarbeiten</li>
                    <li>Frühwarnsystem bei erkennbaren Verschleißerscheinungen</li>
                </ul>

                <h3>Handwerkernetzwerk</h3>
                <p>Wir arbeiten ausschließlich mit geprüften und zuverlässigen Partnerfirmen:</p>
                <ul>
                    <li><strong>Qualifikation:</strong> Alle Handwerker sind Fachbetriebe mit entsprechenden
                        Zertifikaten</li>
                    <li><strong>Verfügbarkeit:</strong> Kurze Reaktionszeiten auch bei dringenden Fällen</li>
                    <li><strong>Faire Preise:</strong> Langjährige Partnerschaften sichern günstige Konditionen</li>
                    <li><strong>Garantie:</strong> Gewährleistung auf alle ausgeführten Arbeiten</li>
                    <li><strong>Versicherungsschutz:</strong> Alle Firmen verfügen über entsprechende Versicherungen
                    </li>
                </ul>
            </div>

            <!-- Kostenkontrolle -->
            <div class="content-block">
                <h2>Kostenkontrolle und Budgetplanung</h2>

                <h3>Transparente Kostenabrechnung</h3>
                <p>Alle Instandhaltungskosten werden transparent dokumentiert und abgerechnet:</p>
                <ul>
                    <li>Vorab-Information bei größeren Reparaturen (ab vereinbartem Betrag)</li>
                    <li>Einholung mehrerer Angebote bei umfangreichen Arbeiten</li>
                    <li>Detaillierte Kostenaufstellung mit allen Belegen</li>
                    <li>Regelmäßige Reportings über angefallene Kosten</li>
                </ul>

                <h3>Instandhaltungsrücklage (bei WEG)</h3>
                <p>Bei Wohnungseigentümergemeinschaften verwalten wir die Rücklage:</p>
                <ul>
                    <li>Berechnung der angemessenen Rücklagenhöhe</li>
                    <li>Verwaltung auf separatem Konto</li>
                    <li>Verwendung nur nach Eigentümerbeschluss</li>
                    <li>Transparente Dokumentation aller Zu- und Abgänge</li>
                </ul>

                <h3>Langfristige Instandhaltungsplanung</h3>
                <p>Wir erstellen mehrjährige Instandhaltungspläne:</p>
                <ul>
                    <li>Analyse des Gebäudezustands</li>
                    <li>Prognose anstehender Maßnahmen (5-10 Jahre)</li>
                    <li>Kostenschätzungen für größere Arbeiten</li>
                    <li>Priorisierung nach Dringlichkeit</li>
                    <li>Budgetplanung zur Vermeidung von Finanzierungsengpässen</li>
                </ul>
            </div>

            <div class="info-box">
                <h3>✓ Unser Versprechen</h3>
                <p>Bei Notfällen (Wasserrohrbruch, Heizungsausfall etc.) reagieren wir innerhalb von 2 Stunden.
                    Standardreparaturen werden innerhalb von 48 Stunden beauftragt. Sie erhalten transparente
                    Kostenvoranschläge und haben jederzeit Einblick in alle Vorgänge.</p>
            </div>

            <!-- Rechtliche Aspekte -->
            <div class="content-block">
                <h2>Rechtliche Aspekte der Instandhaltung</h2>

                <h3>Vermieterpflichten</h3>
                <p>Als Vermieter sind Sie zur Instandhaltung verpflichtet:</p>
                <ul>
                    <li>Erhaltung der Mietsache in vertragsgemäßem Zustand</li>
                    <li>Beseitigung von Mängeln während der Mietzeit</li>
                    <li>Durchführung gesetzlich vorgeschriebener Prüfungen</li>
                    <li>Verkehrssicherungspflicht (z.B. Winterdienst)</li>
                </ul>

                <h3>Kleinreparaturklausel</h3>
                <p>Bestimmte Kleinreparaturen können auf Mieter umgelegt werden:</p>
                <ul>
                    <li><strong>Höchstgrenze:</strong> Max. 100€ pro Einzelreparatur</li>
                    <li><strong>Jahreshöchstbetrag:</strong> Max. 8% der Jahresnettomiete oder 200€</li>
                    <li><strong>Umlagefähig:</strong> Nur Bagatellschäden an oft genutzten Gegenständen</li>
                    <li><strong>Nicht umlagefähig:</strong> Verschleißreparaturen, Schäden an der Bausubstanz</li>
                </ul>

                <h3>Modernisierung vs. Instandhaltung</h3>
                <p>Die Unterscheidung ist wichtig für die Mieterhöhung:</p>
                <ul>
                    <li><strong>Instandhaltung:</strong> Wiederherstellung des ursprünglichen Zustands (nicht
                        umlagefähig)</li>
                    <li><strong>Modernisierung:</strong> Verbesserung über bisherigen Standard (8% der Kosten
                        umlagefähig)</li>
                </ul>
            </div>

            <!-- Vorteile -->
            <div class="content-block">
                <h2>Vorteile unseres Instandhaltungsmanagements</h2>
                <ol>
                    <li><strong>Werterhalt:</strong> Ihre Immobilie bleibt in optimalem Zustand</li>
                    <li><strong>Kostenersparnis:</strong> Präventive Wartung verhindert teure Folgeschäden</li>
                    <li><strong>Zeitersparnis:</strong> Wir kümmern uns um alles – Sie müssen sich um nichts kümmern
                    </li>
                    <li><strong>Schnelle Reaktion:</strong> Kurze Wege zu qualifizierten Handwerkern</li>
                    <li><strong>Mieterzufriedenheit:</strong> Schnelle Schadensbehebung erhöht die Zufriedenheit
                    </li>
                    <li><strong>Rechtssicherheit:</strong> Alle Pflichten werden fristgerecht erfüllt</li>
                    <li><strong>Faire Preise:</strong> Unser Netzwerk garantiert gute Konditionen</li>
                    <li><strong>Dokumentation:</strong> Lückenlose Nachweise für alle Arbeiten</li>
                    <li><strong>Planbarkeit:</strong> Langfristige Budgetplanung möglich</li>
                </ol>
            </div>
        </div>

        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-widget">
                <h3>Schnellnavigation</h3>
                <ul class="quick-links">
                    <li><a href="#arten">→ Arten der Instandhaltung</a></li>
                    <li><a href="#service">→ Unser Service</a></li>
                    <li><a href="#kosten">→ Kostenkontrolle</a></li>
                    <li><a href="#recht">→ Rechtliche Aspekte</a></li>
                    <li><a href="#faq">→ Häufige Fragen</a></li>
                </ul>
            </div>

            <div class="sidebar-widget">
                <h3>Weitere Themen</h3>
                <ul class="quick-links">
                    <li><a href="<?= BASE ?>/hausverwaltung-mietverwaltung">→ Mietverwaltung</a></li>
                    <li><a href="<?= BASE ?>/hausverwaltung-nebenkostenabrechnung">→ Nebenkostenabrechnung</a></li>
                    <li><a href="<?= BASE ?>/hausverwaltung-eigentuemerversammlung">→ Eigentümerversammlung</a></li>
                    <li><a href="<?= BASE ?>/hausverwaltung-rechtliches">→ Rechtliche Fragen</a></li>
                </ul>
            </div>

            <div class="sidebar-widget cta-widget">
                <h3>Notfall-Hotline</h3>
                <p>Bei dringenden Schadensfällen erreichen Sie uns!</p>
                <a href="tel:+4916098787878" class="cta-btn">Jetzt anrufen</a>
            </div>
        </aside>
    </div>
</section>

<!-- FAQ Section -->
<section class="faq-section" id="faq">
    <div class="faq-container">
        <div class="faq-header">
            <h2>Häufig gestellte Fragen zur Instandhaltung</h2>
            <p>Hier finden Sie Antworten auf die wichtigsten Fragen</p>
        </div>

        <div class="faq-item">
            <button class="faq-question" onclick="toggleFAQ(this)">
                <span>Wer trägt die Kosten für Reparaturen?</span>
                <svg class="faq-icon" width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z" />
                </svg>
            </button>
            <div class="faq-answer">
                <div class="faq-answer-content">
                    <p>Grundsätzlich trägt der Vermieter/Eigentümer die Kosten für Reparaturen und Instandhaltung.
                        Ausnahmen:</p>
                    <ul>
                        <li><strong>Kleinreparaturen:</strong> Können auf Mieter umgelegt werden (max. 100€ pro
                            Reparatur, max. 8% der Jahresmiete gesamt)</li>
                        <li><strong>Mieter-Verursachung:</strong> Schäden durch unsachgemäße Nutzung trägt der
                            Mieter</li>
                        <li><strong>Schönheitsreparaturen:</strong> Können vertraglich auf Mieter übertragen werden
                        </li>
                    </ul>
                    <p>Bei Wohnungseigentümergemeinschaften werden größere Reparaturen aus der
                        Instandhaltungsrücklage finanziert.</p>
                </div>
            </div>
        </div>

        <div class="faq-item">
            <button class="faq-question" onclick="toggleFAQ(this)">
                <span>Wie schnell werden Reparaturen durchgeführt?</span>
                <svg class="faq-icon" width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z" />
                </svg>
            </button>
            <div class="faq-answer">
                <div class="faq-answer-content">
                    <p>Die Reaktionszeit hängt von der Dringlichkeit ab:</p>
                    <ul>
                        <li><strong>Notfälle:</strong> Innerhalb von 2 Stunden (Wasserrohrbruch, Heizungsausfall im
                            Winter, Einbruchschäden)</li>
                        <li><strong>Dringend:</strong> Innerhalb von 24 Stunden (defekte Tür, undichte
                            Wasserleitung)</li>
                        <li><strong>Normal:</strong> Innerhalb von 48 Stunden Beauftragung (defekte Armaturen,
                            Kleinreparaturen)</li>
                        <li><strong>Geplant:</strong> Nach Terminabsprache (Wartungen, nicht dringende Reparaturen)
                        </li>
                    </ul>
                    <p>Wir informieren Sie und die Mieter über den voraussichtlichen Zeitplan.</p>
                </div>
            </div>
        </div>

        <div class="faq-item">
            <button class="faq-question" onclick="toggleFAQ(this)">
                <span>Wie hoch sind die durchschnittlichen Instandhaltungskosten?</span>
                <svg class="faq-icon" width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z" />
                </svg>
            </button>
            <div class="faq-answer">
                <div class="faq-answer-content">
                    <p>Die Instandhaltungskosten variieren je nach Gebäudealter und -zustand:</p>
                    <ul>
                        <li><strong>Neubau (0-10 Jahre):</strong> ca. 7-10 €/m² Wohnfläche pro Jahr</li>
                        <li><strong>Mittleres Alter (10-30 Jahre):</strong> ca. 10-15 €/m² pro Jahr</li>
                        <li><strong>Altbau (über 30 Jahre):</strong> ca. 15-20 €/m² pro Jahr</li>
                    </ul>
                    <p>Diese Werte sind Durchschnittswerte und können stark variieren. Bei einer 100m² Wohnung in
                        einem 20 Jahre alten Gebäude sollten Sie also mit etwa 1.000-1.500€ pro Jahr rechnen.</p>
                    <p>Wichtig: Durch präventive Wartung können langfristig Kosten gespart werden!</p>
                </div>
            </div>
        </div>

        <div class="faq-item">
            <button class="faq-question" onclick="toggleFAQ(this)">
                <span>Muss ich jeden Handwerker selbst bezahlen?</span>
                <svg class="faq-icon" width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z" />
                </svg>
            </button>
            <div class="faq-answer">
                <div class="faq-answer-content">
                    <p>Nein, das hängt von unserer Vereinbarung ab:</p>
                    <ul>
                        <li><strong>Variante 1 - Direktabrechnung:</strong> Handwerker rechnet direkt mit Ihnen ab
                            (Sie behalten volle Kostenkontrolle)</li>
                        <li><strong>Variante 2 - Über Hausverwaltung:</strong> Wir bezahlen die Handwerker aus Ihren
                            Vorauszahlungen und rechnen monatlich/quartalsweise ab</li>
                        <li><strong>Variante 3 - Notfall-Vollmacht:</strong> Bei Notfällen beauftragen wir sofort,
                            Sie zahlen später</li>
                    </ul>
                    <p>Wir klären gemeinsam, welches Modell für Sie am besten passt. Kleinbeträge bis zu einem
                        vereinbarten Limit (z.B. 500€) können wir auch ohne vorherige Rücksprache beauftragen.</p>
                </div>
            </div>
        </div>

        <div class="faq-item">
            <button class="faq-question" onclick="toggleFAQ(this)">
                <span>Kann ich eigene Handwerker beauftragen?</span>
                <svg class="faq-icon" width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z" />
                </svg>
            </button>
            <div class="faq-answer">
                <div class="faq-answer-content">
                    <p>Ja, das ist grundsätzlich möglich! Sie haben mehrere Optionen:</p>
                    <ul>
                        <li><strong>Eigene Handwerker:</strong> Sie können uns Ihre bevorzugten Handwerker nennen,
                            die wir dann beauftragen</li>
                        <li><strong>Unser Netzwerk:</strong> Wir arbeiten mit geprüften Partnerfirmen zu guten
                            Konditionen</li>
                        <li><strong>Mischung:</strong> Für bestimmte Arbeiten Ihre Handwerker, für andere unser
                            Netzwerk</li>
                    </ul>
                    <p>Wichtig ist, dass die Handwerker über entsprechende Qualifikationen und Versicherungen
                        verfügen. Bei Notfällen beauftragen wir aus Zeitgründen unsere Partnerfirmen.</p>
                </div>
            </div>
        </div>

        <div class="faq-item">
            <button class="faq-question" onclick="toggleFAQ(this)">
                <span>Was passiert, wenn ein Mieter einen Schaden verursacht?</span>
                <svg class="faq-icon" width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z" />
                </svg>
            </button>
            <div class="faq-answer">
                <div class="faq-answer-content">
                    <p>Bei durch Mieter verursachten Schäden gehen wir wie folgt vor:</p>
                    <ol>
                        <li><strong>Schadensdokumentation:</strong> Fotodokumentation und schriftliche Feststellung
                        </li>
                        <li><strong>Schadensursache klären:</strong> War es Verschleiß oder unsachgemäße Nutzung?
                        </li>
                        <li><strong>Mieterinformation:</strong> Schriftliche Mitteilung über den Schaden und die
                            Kostenübernahme</li>
                        <li><strong>Reparatur beauftragen:</strong> Schnelle Behebung des Schadens</li>
                        <li><strong>Kostenerstattung:</strong> Rechnungsstellung an den Mieter bzw. dessen
                            Haftpflichtversicherung</li>
                    </ol>
                    <p>Wir kümmern uns um die komplette Abwicklung einschließlich der Kommunikation mit
                        Versicherungen.</p>
                </div>
            </div>
        </div>

        <!-- Kontakt-Hinweis -->
        <div class="info-box mt-40">
            <h3>💬 Weitere Fragen zur Instandhaltung?</h3>
            <p>Wir beraten Sie gerne persönlich zu allen Fragen rund um Wartung und Reparaturen. Kontaktieren Sie
                uns für ein unverbindliches Gespräch!</p>
            <div class="mt-20">
                <a href="<?= BASE ?>/#contact" class="cta-btn inline-block">Jetzt Kontakt
                    aufnehmen</a>
            </div>
        </div>
    </div>
</section>

<!-- Contact CTA -->
<section class="contact-cta-section">
    <h2>Professionelles Instandhaltungsmanagement gewünscht?</h2>
    <p>Lassen Sie uns die Werterhaltung Ihrer Immobilie übernehmen</p>
    <div class="contact-buttons">
        <a href="tel:+4916098787878" class="contact-btn-primary">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                <path
                    d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z" />
            </svg>
            Jetzt anrufen
        </a>
        <a href="<?= BASE ?>/#contact" class="contact-btn-secondary">Beratung vereinbaren</a>
    </div>
</section>

<!-- Footer -->

<?php require_once __DIR__ . '/includes/footer.php'; ?>