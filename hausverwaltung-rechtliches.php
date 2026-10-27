<?php
require_once __DIR__ . '/includes/config.php';
header('Location: ' . BASE . '/hausverwaltung#leistung-rechtliches', true, 301);
exit;

$pageTitle       = 'Rechtliches zur Hausverwaltung – Immobilien Ghumman';
$pageDescription = 'Rechtliche Grundlagen der Hausverwaltung – WEG, Mietrecht und mehr.';
$pageKeywords    = 'Mietrecht, WEG, Hausverwaltung Recht, Bad Vilbel';
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
            background: linear-gradient(135deg, #d35400 0%, #e67e22 100%);
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
            background: linear-gradient(135deg, #fdebd0 0%, #fadbd8 100%);
            padding: 40px;
            border-radius: 16px;
            margin-bottom: 50px;
            border-left: 5px solid #e67e22;
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
            border-bottom: 3px solid #e67e22;
        }

        .content-block h3 {
            color: #d35400;
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
            color: #e67e22;
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
            background: #fdebd0;
            border-left: 4px solid #e67e22;
            padding: 25px;
            border-radius: 8px;
            margin: 30px 0;
        }

        .info-box h3 {
            color: #d35400;
            margin-top: 0;
        }

        .warning-box {
            background: #fee;
            border-left: 4px solid #dc2626;
            padding: 25px;
            border-radius: 8px;
            margin: 30px 0;
        }

        .warning-box h3 {
            color: #dc2626;
            margin-top: 0;
        }

        .law-section {
            background: #f8fafc;
            padding: 30px;
            border-radius: 12px;
            margin: 30px 0;
            border: 2px solid #e5e7eb;
        }

        .law-section h4 {
            color: #d35400;
            margin-bottom: 15px;
            font-size: 1.2rem;
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
            color: #e67e22;
            transform: translateX(5px);
        }

        .cta-widget {
            background: linear-gradient(135deg, #d35400 0%, #e67e22 100%);
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
            color: #e67e22;
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

        .icon-list {
            list-style: none;
            margin-left: 0 !important;
        }

        .icon-list li {
            padding-left: 35px !important;
            position: relative;
            margin-bottom: 15px;
        }

        .icon-list li::before {
            content: "⚖️";
            position: absolute;
            left: 0;
            font-size: 1.3rem;
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
            color: #e67e22;
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
            background: linear-gradient(135deg, #34495e 0%, #2c3e50 100%);
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
            color: #34495e;
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
            color: #34495e;
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
            <span>Rechtliches</span>
        </nav>
        <div class="page-hero-badge"><i class="fas fa-gavel"></i> WEG-, Miet- &amp; Baurecht</div>
        <h1>Rechtliche Fragen &amp; <span class="accent">Beratung</span></h1>
        <p>Umfassende juristische Expertise fuer rechtssichere Immobilienverwaltung &ndash; von WEG-Recht ueber Mietrecht bis zu Versicherungsfragen.</p>
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
            <div class="hero-stat"><i class="fas fa-balance-scale"></i>
                <div><span class="hero-stat-number">100%</span><span class="hero-stat-label">Rechtssichere Verwaltung</span></div>
            </div>
            <div class="hero-stat"><i class="fas fa-book"></i>
                <div><span class="hero-stat-number">Aktuell</span><span class="hero-stat-label">Aktuelle Rechtsprechung</span></div>
            </div>
            <div class="hero-stat"><i class="fas fa-shield-alt"></i>
                <div><span class="hero-stat-number">Geschuetzt</span><span class="hero-stat-label">Rechtlich abgesichert</span></div>
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
                <h2>Rechtsberatung in der Hausverwaltung</h2>
                <p>Das Immobilienrecht ist komplex und ändert sich ständig. Von WEG-Recht über Mietrecht bis hin zu
                    Baurecht und Versicherungsfragen – wir kennen alle relevanten Gesetze und Urteile. Mit unserer
                    juristischen Expertise sichern wir Sie rechtlich ab und vermeiden kostspielige Fehler. Verlassen
                    Sie sich auf unsere langjährige Erfahrung in allen rechtlichen Fragen der Immobilienverwaltung.
                </p>
            </div>

            <!-- WEG-Recht -->
            <div class="content-block">
                <h2>Wohnungseigentumsrecht (WEG)</h2>

                <h3>Grundlagen des WEG</h3>
                <p>Das Wohnungseigentumsgesetz (WEG) ist die zentrale Rechtsgrundlage für jede
                    Eigentümergemeinschaft. Es regelt:</p>
                <ul class="icon-list">
                    <li><strong>Teilungserklärung:</strong> Aufteilung in Sonder- und Gemeinschaftseigentum</li>
                    <li><strong>Gemeinschaftsordnung:</strong> Regeln des Zusammenlebens in der WEG</li>
                    <li><strong>Verwaltungspflichten:</strong> Rechte und Pflichten des Verwalters</li>
                    <li><strong>Beschlussfassung:</strong> Abstimmungsregeln in Eigentümerversammlungen</li>
                    <li><strong>Kostenverteilung:</strong> Verteilerschlüssel für Gemeinschaftskosten</li>
                </ul>

                <div class="law-section">
                    <h4>WEG-Reform 2020 – Die wichtigsten Änderungen</h4>
                    <ul>
                        <li><strong>Einfachere Modernisierung:</strong> Bauliche Veränderungen für Barrierefreiheit,
                            E-Mobilität und Einbruchschutz können mit einfacher Mehrheit beschlossen werden</li>
                        <li><strong>Umlaufverfahren:</strong> Beschlüsse können jetzt auch schriftlich (Textform)
                            ohne Versammlung gefasst werden</li>
                        <li><strong>Online-Versammlungen:</strong> Teilnahme per Video ist nun ausdrücklich erlaubt
                        </li>
                        <li><strong>Verbot von Verbot:</strong> Haustiere, Kinderwagen oder Fahrräder dürfen nicht
                            generell verboten werden</li>
                        <li><strong>Notgeschäftsführung:</strong> Bei Verhinderung des Verwalters klare Regelungen
                            zur Vertretung</li>
                    </ul>
                </div>

                <h3>Gemeinschaftseigentum vs. Sondereigentum</h3>
                <p>Die korrekte Abgrenzung ist entscheidend für Kosten- und Haftungsfragen:</p>
                <ul>
                    <li><strong>Gemeinschaftseigentum:</strong>
                        <ul>
                            <li>Tragende Wände, Fassade, Dach</li>
                            <li>Treppenhaus, Aufzug, Keller</li>
                            <li>Heizungsanlage (zentral), Wasserrohre (Hauptleitungen)</li>
                            <li>Kosten werden nach Miteigentumsanteil oder Nutzung verteilt</li>
                        </ul>
                    </li>
                    <li><strong>Sondereigentum:</strong>
                        <ul>
                            <li>Innenwände (nicht tragend), Bodenbeläge, Tapeten</li>
                            <li>Wohnungstüren (Innenseite), Fenster (Innenseite)</li>
                            <li>Sanitäranlagen, Heizkörper (in der Wohnung)</li>
                            <li>Kosten trägt der jeweilige Eigentümer allein</li>
                        </ul>
                    </li>
                </ul>

                <h3>Häufige WEG-Streitfälle</h3>
                <ol>
                    <li><strong>Beschlussanfechtung:</strong> Fehlerhafte Einladung oder Mehrheiten</li>
                    <li><strong>Kostenverteilung:</strong> Streit über Umlageschlüssel</li>
                    <li><strong>Bauliche Veränderungen:</strong> Balkonverglasung, Markisen, Satellitenschüsseln
                    </li>
                    <li><strong>Sondernutzungsrechte:</strong> Garten, Parkplätze, Terrassen</li>
                    <li><strong>Lärmbelästigung:</strong> Durch Nachbarn oder Renovierungsarbeiten</li>
                </ol>
            </div>

            <div class="warning-box">
                <h3>⚠️ Vorsicht Falle!</h3>
                <p>Viele Eigentümer übersehen die <strong>Anfechtungsfrist von 1 Monat</strong> für fehlerhafte
                    Beschlüsse. Nach Ablauf dieser Frist werden selbst rechtswidrige Beschlüsse bestandskräftig!
                    Eine schnelle rechtliche Prüfung ist daher essentiell.</p>
            </div>

            <!-- Mietrecht -->
            <div class="content-block">
                <h2>Mietrecht</h2>

                <h3>Vermieterrechte und -pflichten</h3>
                <p>Das Mietrecht schützt beide Seiten – Vermieter und Mieter. Als Verwalter kennen wir alle Rechte
                    und Pflichten:</p>

                <h4>Vermieterpflichten</h4>
                <ul>
                    <li><strong>Mängelbehebung:</strong> Defekte müssen zeitnah behoben werden</li>
                    <li><strong>Verkehrssicherung:</strong> Winterdienst, Treppenhaus, Beleuchtung</li>
                    <li><strong>Betriebskostenabrechnung:</strong> Fristgerecht und korrekt erstellen</li>
                    <li><strong>Einhaltung Mietpreisbremse:</strong> Bei Neuvermietung beachten</li>
                    <li><strong>Schönheitsreparaturen:</strong> Nur bei wirksamer Klausel auf Mieter übertragbar
                    </li>
                </ul>

                <h4>Vermieterrechte</h4>
                <ul>
                    <li><strong>Mieterhöhung:</strong> Bis zur ortsüblichen Vergleichsmiete (alle 15 Monate, max.
                        20% in 3 Jahren)</li>
                    <li><strong>Modernisierungsumlage:</strong> 8% der Kosten auf Jahresmiete umlegen</li>
                    <li><strong>Kündigungsrecht:</strong> Bei Eigenbedarf, Pflichtverletzung oder Zahlungsverzug
                    </li>
                    <li><strong>Zugang zur Wohnung:</strong> Nach Ankündigung für Reparaturen oder Besichtigungen
                    </li>
                    <li><strong>Kaution:</strong> Bis zu 3 Nettokaltmieten als Sicherheit</li>
                </ul>

                <h3>Kündigungsfristen und -gründe</h3>

                <div class="law-section">
                    <h4>Ordentliche Kündigung (für Vermieter)</h4>
                    <ul>
                        <li>Bis 5 Jahre Mietdauer: 3 Monate</li>
                        <li>5-8 Jahre Mietdauer: 6 Monate</li>
                        <li>Ab 8 Jahren Mietdauer: 9 Monate</li>
                        <li>Kündigungsgrund erforderlich: Eigenbedarf, Hinderung ordnungsgemäßer Verwaltung,
                            Verwertungsabsicht</li>
                    </ul>
                </div>

                <div class="law-section">
                    <h4>Fristlose Kündigung (außerordentlich)</h4>
                    <p>Nur bei schwerwiegenden Gründen:</p>
                    <ul>
                        <li><strong>Zahlungsverzug:</strong> Rückstand von 2 Monatsmieten oder über 2 Monate
                            Teilschulden</li>
                        <li><strong>Vertragsverletzung:</strong> Unerlaubte Untervermietung, Störung des
                            Hausfriedens</li>
                        <li><strong>Sachbeschädigung:</strong> Mutwillige Beschädigung der Mietsache</li>
                    </ul>
                    <p><strong>Wichtig:</strong> Meist muss vorher abgemahnt werden!</p>
                </div>

                <h3>Mietminderung</h3>
                <p>Mieter dürfen die Miete mindern, wenn Mängel vorliegen. Übliche Minderungsquoten:</p>
                <ul>
                    <li><strong>Heizungsausfall im Winter:</strong> 75-100%</li>
                    <li><strong>Kein Warmwasser:</strong> 10-20%</li>
                    <li><strong>Schimmel:</strong> 10-50% (je nach Ausmaß)</li>
                    <li><strong>Baulärm:</strong> 10-25%</li>
                    <li><strong>Aufzug defekt:</strong> 5-15%</li>
                </ul>
                <p><strong>Unser Service:</strong> Wir prüfen Mietminderungsansprüche rechtlich und wickeln
                    Mängelbeseitigung professionell ab.</p>
            </div>

            <div class="info-box">
                <h3>💡 Tipp für Vermieter</h3>
                <p>Eine professionelle Verwaltung minimiert Mietminderungsrisiken durch schnelle Mängelbeseitigung
                    und vorausschauende Instandhaltung. Zudem dokumentieren wir alle Vorgänge rechtssicher für
                    eventuelle Gerichtsverfahren.</p>
            </div>

            <!-- Vertragsrecht -->
            <div class="content-block">
                <h2>Vertragsrecht in der Immobilienverwaltung</h2>

                <h3>Verwaltungsvertrag</h3>
                <p>Der Verwaltungsvertrag regelt das Verhältnis zwischen WEG und Verwalter:</p>
                <ul>
                    <li><strong>Leistungsumfang:</strong> Was genau wird verwaltet (Buchhaltung, Versammlungen,
                        Instandhaltung)?</li>
                    <li><strong>Vergütung:</strong> Höhe und Zahlungsmodalitäten der Verwaltungsgebühr</li>
                    <li><strong>Laufzeit:</strong> Üblicherweise 3-5 Jahre, dann Neubestellung erforderlich</li>
                    <li><strong>Kündigungsmöglichkeiten:</strong> Ordentlich nach Ablauf, außerordentlich bei
                        wichtigem Grund</li>
                    <li><strong>Haftung:</strong> Haftungsbeschränkungen und Versicherungsschutz</li>
                </ul>

                <h3>Mietvertrag</h3>
                <p>Ein rechtssicherer Mietvertrag ist das Fundament jedes Mietverhältnisses. Wichtige Klauseln:</p>
                <ul>
                    <li><strong>Miethöhe:</strong> Kalt-, Warm-, Nebenkostenvorauszahlung klar definieren</li>
                    <li><strong>Kleinreparaturklausel:</strong> Maximal 100-120€ pro Reparatur, 8% der Jahresmiete
                        Obergrenze</li>
                    <li><strong>Schönheitsreparaturen:</strong> Nur wirksam, wenn Wohnung renoviert übergeben wurde
                    </li>
                    <li><strong>Haustiere:</strong> Pauschales Verbot unwirksam, Einzelfallentscheidung</li>
                    <li><strong>Untervermietung:</strong> Erlaubnispflicht, berechtigtes Interesse des Mieters
                        erforderlich</li>
                </ul>

                <h3>Handwerkerverträge</h3>
                <p>Bei der Beauftragung von Handwerkern achten wir auf:</p>
                <ul>
                    <li><strong>Leistungsbeschreibung:</strong> Präzise Formulierung des Auftrags</li>
                    <li><strong>Festpreisvereinbarung:</strong> Vermeidung von Kostenexplosionen</li>
                    <li><strong>Gewährleistungsfristen:</strong> 2 Jahre für Mängel nach BGB</li>
                    <li><strong>Versicherungsnachweis:</strong> Haftpflicht- und Betriebshaftpflichtversicherung
                    </li>
                    <li><strong>VOB/B-Vereinbarung:</strong> Bei größeren Baumaßnahmen empfehlenswert</li>
                </ul>
            </div>

            <!-- Haftungsrecht -->
            <div class="content-block">
                <h2>Haftung und Versicherungsrecht</h2>

                <h3>Haftung des Verwalters</h3>
                <p>Der Verwalter haftet für Pflichtverletzungen aus dem Verwaltungsvertrag:</p>
                <ul>
                    <li><strong>Verschulden:</strong> Nur bei Vorsatz und Fahrlässigkeit</li>
                    <li><strong>Beweislast:</strong> WEG muss Pflichtverletzung und Schaden beweisen</li>
                    <li><strong>Verjährung:</strong> Ansprüche verjähren nach 3 Jahren</li>
                    <li><strong>Versicherungsschutz:</strong> Wir sind vollumfänglich versichert (D&O,
                        Vermögensschaden)</li>
                </ul>

                <h3>Verkehrssicherungspflicht</h3>
                <p>Eigentümer/Verwalter müssen Gefahren vom Grundstück abwenden:</p>
                <ul>
                    <li><strong>Winterdienst:</strong> Schnee- und Eisbeseitigung auf Gehwegen (werktags 7-20 Uhr,
                        sonntags ab 9 Uhr)</li>
                    <li><strong>Beleuchtung:</strong> Ausreichende Beleuchtung von Wegen und Treppen</li>
                    <li><strong>Reparaturen:</strong> Defekte Stufen, lose Handläufe, lockere Dachziegel</li>
                    <li><strong>Baumkontrolle:</strong> Regelmäßige Überprüfung auf Standsicherheit</li>
                </ul>
                <p><strong>Haftung:</strong> Bei Verletzung drohen Schadensersatzansprüche und ggf. Strafbarkeit
                    (fahrlässige Körperverletzung).</p>

                <h3>Wichtige Versicherungen</h3>
                <ol>
                    <li><strong>Wohngebäudeversicherung:</strong> Pflicht! Schützt vor Feuer, Leitungswasser, Sturm
                    </li>
                    <li><strong>Haftpflichtversicherung:</strong> Für Schäden durch
                        Verkehrssicherungspflichtverletzung</li>
                    <li><strong>Glasversicherung:</strong> Bei großen Glasflächen empfehlenswert</li>
                    <li><strong>Elementarschadenversicherung:</strong> Hochwasser, Erdbeben, Starkregen (oft nicht
                        enthalten!)</li>
                    <li><strong>D&O-Versicherung für Verwalter:</strong> Schutz vor persönlicher Haftung</li>
                </ol>
            </div>

            <div class="highlight-box">
                <h3>✅ Wichtig zu wissen</h3>
                <p>Als professionelle Verwaltung übernehmen wir die Verkehrssicherungspflicht und sind umfassend
                    versichert. Sie sind als Eigentümer rechtlich abgesichert – vorausgesetzt, Sie haben uns
                    ordnungsgemäß beauftragt und zahlen die Versicherungsprämien.</p>
            </div>

            <!-- Baurecht -->
            <div class="content-block">
                <h2>Baurecht und Modernisierung</h2>

                <h3>Baugenehmigungen</h3>
                <p>Viele bauliche Veränderungen benötigen eine Genehmigung:</p>
                <ul>
                    <li><strong>Genehmigungspflichtig:</strong> Balkone, Wintergärten, Dachausbauten, Aufzüge</li>
                    <li><strong>Genehmigungsfrei:</strong> Innenwände (nicht tragend), Bodenbeläge, Sanitär</li>
                    <li><strong>Anzeigepflichtig:</strong> Kleinere Baumaßnahmen, je nach Landesbauordnung</li>
                </ul>

                <h3>Denkmalschutz</h3>
                <p>Bei denkmalgeschützten Gebäuden gelten besondere Regeln:</p>
                <ul>
                    <li>Strengere Genehmigungsverfahren</li>
                    <li>Einschränkungen bei Modernisierung (z.B. Fassadendämmung, Fensteraustausch)</li>
                    <li>Aber: Steuerliche Förderung möglich (Denkmal-AfA)</li>
                    <li>Zuschüsse für denkmalgerechte Sanierung</li>
                </ul>

                <h3>Energetische Sanierung</h3>
                <p>Gesetzliche Pflichten nach GEG (Gebäudeenergiegesetz):</p>
                <ul>
                    <li><strong>Heizungstausch:</strong> Öl- und Gasheizungen über 30 Jahre müssen ersetzt werden
                    </li>
                    <li><strong>Dämmung:</strong> Bei Modernisierung Mindeststandards einhalten</li>
                    <li><strong>Energieausweis:</strong> Pflicht bei Verkauf und Vermietung</li>
                    <li><strong>PV-Pflicht:</strong> In manchen Bundesländern bei Dachsanierung</li>
                </ul>
            </div>

            <!-- Unser Service -->
            <div class="content-block">
                <h2>Unser rechtlicher Service</h2>

                <h3>Präventive Rechtsberatung</h3>
                <ul>
                    <li>Prüfung und Optimierung von Verträgen (Miet-, Dienstleistungs-, Handwerkerverträge)</li>
                    <li>Rechtliche Begleitung von Eigentümerversammlungen</li>
                    <li>Schulungen zur aktuellen Rechtslage (WEG-Reform, Mietrecht-Änderungen)</li>
                    <li>Compliance-Prüfung (DSGVO, Geldwäsche, Maklerpflichten)</li>
                </ul>

                <h3>Rechtsdurchsetzung</h3>
                <ul>
                    <li>Außergerichtliche Geltendmachung von Ansprüchen (Mietrückstände, Schadensersatz)</li>
                    <li>Erstellung von Abmahnungen und Kündigungen</li>
                    <li>Kooperation mit spezialisierten Rechtsanwälten</li>
                    <li>Vertretung bei Behörden (Baugenehmigung, Denkmalschutz)</li>
                </ul>

                <h3>Streitbeilegung</h3>
                <ul>
                    <li>Mediation bei Eigentümerkonflikten</li>
                    <li>Verhandlung mit säumigen Mietern</li>
                    <li>Konfliktmanagement in der WEG</li>
                    <li>Gerichtliche Vertretung in Abstimmung mit Anwälten</li>
                </ul>
            </div>
        </div>

        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-widget">
                <h3>Schnellnavigation</h3>
                <ul class="quick-links">
                    <li><a href="#weg">→ WEG-Recht</a></li>
                    <li><a href="#mietrecht">→ Mietrecht</a></li>
                    <li><a href="#vertragsrecht">→ Vertragsrecht</a></li>
                    <li><a href="#haftung">→ Haftung</a></li>
                    <li><a href="#faq">→ Häufige Fragen</a></li>
                </ul>
            </div>

            <div class="sidebar-widget">
                <h3>Weitere Themen</h3>
                <ul class="quick-links">
                    <li><a href="<?= BASE ?>/hausverwaltung-mietverwaltung">→ Mietverwaltung</a></li>
                    <li><a href="<?= BASE ?>/hausverwaltung-nebenkostenabrechnung">→ Nebenkostenabrechnung</a></li>
                    <li><a href="<?= BASE ?>/hausverwaltung-instandhaltung">→ Instandhaltung</a></li>
                    <li><a href="<?= BASE ?>/hausverwaltung-eigentuemerversammlung">→ Eigentümerversammlungen</a></li>
                </ul>
            </div>

            <div class="sidebar-widget cta-widget">
                <h3>Rechtliche Fragen?</h3>
                <p>Wir beraten Sie kompetent zu allen rechtlichen Aspekten!</p>
                <a href="<?= BASE ?>/#contact" class="cta-btn">Jetzt Kontakt aufnehmen</a>
            </div>
        </aside>
    </div>
</section>

<!-- FAQ Section -->
<section class="faq-section" id="faq">
    <div class="faq-container">
        <div class="faq-header">
            <h2>Häufig gestellte rechtliche Fragen</h2>
            <p>Antworten auf die wichtigsten Rechtsfragen in der Immobilienverwaltung</p>
        </div>

        <div class="faq-item">
            <button class="faq-question" onclick="toggleFAQ(this)">
                <span>Kann ich als Vermieter eine fehlerhafte Nebenkostenabrechnung korrigieren?</span>
                <svg class="faq-icon" width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z" />
                </svg>
            </button>
            <div class="faq-answer">
                <div class="faq-answer-content">
                    <p>Ja, fehlerhafte Abrechnungen können korrigiert werden, allerdings mit Einschränkungen:</p>
                    <ul>
                        <li><strong>Zu Lasten des Mieters:</strong> Korrektur nur innerhalb der Abrechnungsfrist von
                            12 Monaten nach Abrechnungszeitraum. Danach verwirkt der Nachforderungsanspruch!</li>
                        <li><strong>Zu Gunsten des Mieters:</strong> Korrektur kann auch nach Fristablauf verlangt
                            werden (3 Jahre Verjährung)</li>
                        <li><strong>Formfehler:</strong> Bei schwerwiegenden Fehlern (z.B. fehlende Gesamtkosten)
                            gilt die Abrechnung als nicht erteilt</li>
                    </ul>
                    <p><strong>Praxistipp:</strong> Erstellen Sie Abrechnungen sorgfältig und lassen Sie diese vor
                        Versand prüfen. Nachkorrekturen sind umständlich und können zu Konflikten führen.</p>
                </div>
            </div>
        </div>

        <div class="faq-item">
            <button class="faq-question" onclick="toggleFAQ(this)">
                <span>Wann kann ich einem Mieter fristlos kündigen?</span>
                <svg class="faq-icon" width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z" />
                </svg>
            </button>
            <div class="faq-answer">
                <div class="faq-answer-content">
                    <p>Fristlose Kündigung ist nur bei schwerwiegenden Vertragsverletzungen möglich:</p>
                    <ul>
                        <li><strong>Zahlungsverzug:</strong> Rückstand von mindestens 2 Monatsmieten ODER über 2
                            Monate hinweg Teilschulden in Höhe von 2 Monatsmieten</li>
                        <li><strong>Vertragsverletzung:</strong> Unerlaubte Untervermietung, gewerbliche Nutzung
                            ohne Erlaubnis</li>
                        <li><strong>Gefährdung:</strong> Gefährdung der Mietsache oder anderer Personen</li>
                        <li><strong>Störung des Hausfriedens:</strong> Massive und wiederholte Belästigungen</li>
                    </ul>
                    <p><strong>Wichtig:</strong> Vor fristloser Kündigung meist <strong>Abmahnung
                            erforderlich</strong> (außer bei Zahlungsverzug über Grenze). Setzen Sie sich rechtlich
                        beraten lassen – fehlerhafte Kündigungen sind unwirksam!</p>
                    <p><strong>Schonfrist:</strong> Bei Zahlungsverzug hat der Mieter bis 2 Monate nach Zustellung
                        der Kündigung Zeit, den Rückstand zu begleichen (dann wird Kündigung unwirksam).</p>
                </div>
            </div>
        </div>

        <div class="faq-item">
            <button class="faq-question" onclick="toggleFAQ(this)">
                <span>Wer haftet für Schäden durch mangelnde Verkehrssicherung?</span>
                <svg class="faq-icon" width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z" />
                </svg>
            </button>
            <div class="faq-answer">
                <div class="faq-answer-content">
                    <p>Die Verkehrssicherungspflicht liegt beim Eigentümer, kann aber delegiert werden:</p>
                    <ul>
                        <li><strong>Grundsätzlich:</strong> Der Eigentümer haftet für Gefahren, die vom Grundstück
                            ausgehen</li>
                        <li><strong>Delegation an Verwalter:</strong> Wenn vertraglich vereinbart, übernimmt der
                            Verwalter die Pflicht und Haftung</li>
                        <li><strong>Delegation an Mieter:</strong> Winterdienst kann im Mietvertrag auf Mieter
                            übertragen werden (Vermieter muss aber kontrollieren!)</li>
                        <li><strong>Drittfirmen:</strong> Beauftragung von Hausmeistern/Winterdienstfirmen –
                            Überwachungspflicht bleibt!</li>
                    </ul>
                    <p><strong>Haftungsumfang:</strong></p>
                    <ul>
                        <li>Personenschäden (Schmerzensgeld, Behandlungskosten, Verdienstausfall)</li>
                        <li>Sachschäden (z.B. beschädigtes Fahrzeug)</li>
                        <li>Bei grober Fahrlässigkeit: Persönliche Haftung auch neben Versicherung!</li>
                    </ul>
                    <p><strong>Unser Service:</strong> Wir organisieren Verkehrssicherung, kontrollieren
                        Durchführung und sind umfassend versichert.</p>
                </div>
            </div>
        </div>

        <div class="faq-item">
            <button class="faq-question" onclick="toggleFAQ(this)">
                <span>Kann ein WEG-Beschluss auch nachträglich noch angefochten werden?</span>
                <svg class="faq-icon" width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z" />
                </svg>
            </button>
            <div class="faq-answer">
                <div class="faq-answer-content">
                    <p>Ja, aber nur innerhalb der <strong>Anfechtungsfrist von 1 Monat</strong> ab Protokollzugang!
                    </p>
                    <p><strong>Anfechtungsgründe:</strong></p>
                    <ul>
                        <li>Formfehler bei Einladung (zu kurze Frist, fehlende Tagesordnung)</li>
                        <li>Fehlerhafte Beschlussfassung (falsche Mehrheit, Beschlussgegenstand nicht in
                            Tagesordnung)</li>
                        <li>Rechtswidriger Beschlussinhalt (Verstoß gegen Gesetz, Gemeinschaftsordnung)</li>
                        <li>Unbillige Benachteiligung einzelner Eigentümer</li>
                    </ul>
                    <p><strong>Verfahren:</strong></p>
                    <ul>
                        <li>Schriftliche Anfechtung beim Verwalter oder direkte Klage beim Amtsgericht</li>
                        <li>Begründung der Anfechtung erforderlich</li>
                        <li>Während Anfechtungsfrist darf Beschluss nicht umgesetzt werden (Ausnahme:
                            Eilbedürftigkeit)</li>
                    </ul>
                    <p><strong>Nach Fristablauf:</strong> Beschluss wird bestandskräftig, auch wenn er rechtswidrig
                        war! Nur in Ausnahmefällen (z.B. Nichtigkeit) ist später noch Anfechtung möglich.</p>
                </div>
            </div>
        </div>

        <div class="faq-item">
            <button class="faq-question" onclick="toggleFAQ(this)">
                <span>Was ist der Unterschied zwischen Modernisierung und Instandhaltung?</span>
                <svg class="faq-icon" width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z" />
                </svg>
            </button>
            <div class="faq-answer">
                <div class="faq-answer-content">
                    <p>Die Unterscheidung ist wichtig für Kosten, Beschlussmehrheiten und Mieterhöhungen:</p>

                    <p><strong>Instandhaltung:</strong></p>
                    <ul>
                        <li>Erhaltung des vorhandenen Zustands</li>
                        <li>Reparatur von Verschleißschäden</li>
                        <li>Beispiele: Dachdeckung erneuern, Heizung reparieren, Fassade streichen</li>
                        <li>Finanzierung: Aus Instandhaltungsrücklage oder Umlage</li>
                        <li>Keine Mieterhöhung möglich</li>
                    </ul>

                    <p><strong>Modernisierung:</strong></p>
                    <ul>
                        <li>Verbesserung über bisherigen Standard hinaus</li>
                        <li>Nachhaltiger Mehrwert für die Immobilie</li>
                        <li>Beispiele: Wärmedämmung, neue Heizungsanlage (effizienter), Fahrstuhleinbau</li>
                        <li>Finanzierung: Sonderzahlung oder Kredit</li>
                        <li>Mieterhöhung möglich: 8% der Kosten auf Jahresmiete</li>
                        <li>WEG-Beschluss: Seit 2020 oft einfache Mehrheit ausreichend</li>
                    </ul>

                    <p><strong>Grenzfälle:</strong> Oft ist beides enthalten (z.B. neue Fenster: Instandhaltung +
                        Modernisierung durch bessere Dämmung). Dann muss aufgeteilt werden!</p>
                </div>
            </div>
        </div>

        <div class="faq-item">
            <button class="faq-question" onclick="toggleFAQ(this)">
                <span>Brauche ich als Eigentümer eine Rechtsschutzversicherung?</span>
                <svg class="faq-icon" width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z" />
                </svg>
            </button>
            <div class="faq-answer">
                <div class="faq-answer-content">
                    <p>Eine Rechtsschutzversicherung ist nicht verpflichtend, aber oft sehr sinnvoll:</p>

                    <p><strong>Typische Rechtsfälle:</strong></p>
                    <ul>
                        <li>Streitigkeiten mit Mietern (Kündigung, Mietminderung, Schadensersatz)</li>
                        <li>WEG-Konflikte (Beschlussanfechtung, Kostenverteilung)</li>
                        <li>Handwerkerstreitigkeiten (mangelhafte Arbeiten)</li>
                        <li>Nachbarschaftsstreitigkeiten (Lärm, Grenzfragen)</li>
                    </ul>

                    <p><strong>Kosten ohne Versicherung:</strong></p>
                    <ul>
                        <li>Erstberatung Anwalt: ca. 190€</li>
                        <li>Gerichtsverfahren (1. Instanz): 2.000-10.000€ je nach Streitwert</li>
                        <li>Bei Verlust: Zusätzlich Kosten der Gegenseite!</li>
                    </ul>

                    <p><strong>Empfohlene Bausteine:</strong></p>
                    <ul>
                        <li><strong>Wohnungs- und Grundstücksrechtsschutz:</strong> Für Vermieter und WEG-Eigentümer
                        </li>
                        <li><strong>Mietrechtsschutz (Vermieter):</strong> Speziell für Vermieter-Mieter-Konflikte
                        </li>
                        <li><strong>Wartezeit beachten:</strong> Meist 3 Monate, bei manchen Tarifen länger</li>
                    </ul>

                    <p><strong>Kosten:</strong> Ca. 200-400€/Jahr für umfassenden Schutz – bei einem einzigen
                        Rechtsstreit meist schon amortisiert!</p>
                </div>
            </div>
        </div>

        <!-- Kontakt-Hinweis -->
        <div class="info-box mt-40">
            <h3>⚖️ Individuelle Rechtsberatung gewünscht?</h3>
            <p>Jeder Fall ist anders – wir analysieren Ihre spezifische Situation und beraten Sie fundiert zu allen
                rechtlichen Fragen. Kontaktieren Sie uns für ein persönliches Gespräch!</p>
            <div class="mt-20">
                <a href="<?= BASE ?>/#contact" class="cta-btn inline-block">Jetzt Rechtsberatung
                    anfragen</a>
            </div>
        </div>
    </div>
</section>

<!-- Contact CTA -->
<section class="contact-cta-section">
    <h2>Rechtssichere Verwaltung gewünscht?</h2>
    <p>Profitieren Sie von unserer juristischen Expertise – für sorgenfreies Eigentum</p>
    <div class="contact-buttons">
        <a href="tel:+4916098787878" class="contact-btn-primary">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                <path
                    d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z" />
            </svg>
            Jetzt anrufen
        </a>
        <a href="<?= BASE ?>/#contact" class="contact-btn-secondary">Kostenlose Erstberatung</a>
    </div>
</section>

<!-- Footer -->

<?php require_once __DIR__ . '/includes/footer.php'; ?>