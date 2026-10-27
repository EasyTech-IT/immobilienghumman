<?php
require_once __DIR__ . '/includes/config.php';
header('Location: ' . BASE . '/hausverwaltung#leistung-digital', true, 301);
exit;

$pageTitle       = 'Digitale Hausverwaltung – Immobilien Ghumman';
$pageDescription = 'Moderne digitale Hausverwaltung mit Immobilien Ghumman.';
$pageKeywords    = 'digitale Hausverwaltung, Online-Hausverwaltung, Bad Vilbel';
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
            background: linear-gradient(135deg, #1a56a4 0%, #0d1b2e 100%);
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
            background: linear-gradient(135deg, #e0e7ff 0%, #ede9fe 100%);
            padding: 40px;
            border-radius: 16px;
            margin-bottom: 50px;
            border-left: 5px solid #1a56a4;
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
            border-bottom: 3px solid #1a56a4;
        }

        .content-block h3 {
            color: #0d1b2e;
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
            color: #1a56a4;
        }

        .feature-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 25px;
            margin: 30px 0;
        }

        .feature-card {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            padding: 25px;
            border-radius: 12px;
            border: 2px solid #e5e7eb;
            transition: all 0.3s;
        }

        .feature-card:hover {
            border-color: #1a56a4;
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.15);
        }

        .feature-card h4 {
            color: #1a56a4;
            margin-bottom: 12px;
            font-size: 1.2rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .feature-icon {
            font-size: 1.5rem;
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
            background: #e0e7ff;
            border-left: 4px solid #1a56a4;
            padding: 25px;
            border-radius: 8px;
            margin: 30px 0;
        }

        .info-box h3 {
            color: #0d1b2e;
            margin-top: 0;
        }

        .tech-box {
            background: #f0fdf4;
            border-left: 4px solid #10b981;
            padding: 25px;
            border-radius: 8px;
            margin: 30px 0;
        }

        .tech-box h3 {
            color: #10b981;
            margin-top: 0;
        }

        .comparison-table {
            width: 100%;
            border-collapse: collapse;
            margin: 30px 0;
            background: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            border-radius: 12px;
            overflow: hidden;
        }

        .comparison-table thead {
            background: linear-gradient(135deg, #1a56a4 0%, #0d1b2e 100%);
            color: white;
        }

        .comparison-table th {
            padding: 18px;
            text-align: left;
            font-weight: 600;
        }

        .comparison-table td {
            padding: 15px 18px;
            border-bottom: 1px solid #e5e7eb;
        }

        .comparison-table tr:hover {
            background: #f8fafc;
        }

        .check-icon {
            color: #10b981;
            font-weight: bold;
            font-size: 1.3rem;
        }

        .cross-icon {
            color: #ef4444;
            font-weight: bold;
            font-size: 1.3rem;
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
            color: #1a56a4;
            transform: translateX(5px);
        }

        .cta-widget {
            background: linear-gradient(135deg, #1a56a4 0%, #0d1b2e 100%);
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
            color: #1a56a4;
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

        .process-steps {
            counter-reset: step-counter;
            list-style: none;
            margin: 30px 0;
            margin-left: 0 !important;
        }

        .process-steps li {
            counter-increment: step-counter;
            position: relative;
            padding-left: 70px !important;
            margin-bottom: 30px;
        }

        .process-steps li::before {
            content: counter(step-counter);
            position: absolute;
            left: 0;
            top: 0;
            width: 45px;
            height: 45px;
            background: linear-gradient(135deg, #1a56a4 0%, #0d1b2e 100%);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
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
            color: #1a56a4;
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

            .feature-grid {
                grid-template-columns: 1fr;
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

            .comparison-table {
                font-size: 0.9rem;
            }
        }
    </style>';
$extraScripts    = '';
require_once __DIR__ . '/includes/header.php';
?>


<!-- Page Header -->
<!-- Page Hero -->
<section class="page-hero">
    <div class="page-hero-inner">
        <nav class="page-hero-breadcrumb" aria-label="Breadcrumb">
            <a href="<?= BASE ?>/">Startseite</a><span>/</span>
            <a href="<?= BASE ?>/hausverwaltung">Hausverwaltung</a><span>/</span>
            <span>Digitale Verwaltung</span>
        </nav>
        <div class="page-hero-badge"><i class="fas fa-laptop"></i> Papierlose, moderne Verwaltung</div>
        <h1>Digitale <span class="accent">Verwaltung</span></h1>
        <p>Moderne, papierlose Verwaltung f&uuml;r maximale Effizienz und Transparenz &ndash; immer und &uuml;berall erreichbar.</p>
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
            <div class="hero-stat"><i class="fas fa-file-alt"></i>
                <div><span class="hero-stat-number">100%</span><span class="hero-stat-label">Papierlos</span></div>
            </div>
            <div class="hero-stat"><i class="fas fa-clock"></i>
                <div><span class="hero-stat-number">24/7</span><span class="hero-stat-label">Online-Zugang</span></div>
            </div>
            <div class="hero-stat"><i class="fas fa-shield-alt"></i>
                <div><span class="hero-stat-number">DSGVO</span><span class="hero-stat-label">Konform</span></div>
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
                <h2>Willkommen in der digitalen Immobilienverwaltung</h2>
                <p>Die Digitalisierung revolutioniert die Immobilienverwaltung. Wir setzen auf modernste
                    Technologien, um Ihnen maximale Transparenz, Effizienz und Komfort zu bieten. Von
                    Online-Portalen über mobile Apps bis hin zu automatisierten Prozessen – erleben Sie
                    Hausverwaltung im 21. Jahrhundert. Alle Dokumente, Abrechnungen und Informationen jederzeit und
                    überall verfügbar, sicher in der Cloud gespeichert und DSGVO-konform verwaltet.</p>
            </div>

            <!-- Vorteile digitaler Verwaltung -->
            <div class="content-block">
                <h2>Vorteile der digitalen Verwaltung</h2>

                <div class="feature-grid">
                    <div class="feature-card">
                        <h4><span class="feature-icon">🌐</span> 24/7 Zugriff</h4>
                        <p>Ihre Dokumente und Daten jederzeit und von überall abrufbar – per Computer, Tablet oder
                            Smartphone.</p>
                    </div>

                    <div class="feature-card">
                        <h4><span class="feature-icon">📱</span> Mobile Verwaltung</h4>
                        <p>Apps für iOS und Android ermöglichen die Verwaltung unterwegs – Schäden melden, Dokumente
                            einsehen, Nachrichten versenden.</p>
                    </div>

                    <div class="feature-card">
                        <h4><span class="feature-icon">🌳</span> Papierlos & Umweltfreundlich</h4>
                        <p>Keine Papierberge mehr – alle Dokumente digital archiviert. Gut für die Umwelt und Ihr
                            Büro.</p>
                    </div>

                    <div class="feature-card">
                        <h4><span class="feature-icon">⚡</span> Schnelle Prozesse</h4>
                        <p>Automatisierte Workflows beschleunigen Abläufe – von der Mängelerfassung bis zur
                            Abrechnung.</p>
                    </div>

                    <div class="feature-card">
                        <h4><span class="feature-icon">🔍</span> Maximale Transparenz</h4>
                        <p>Alle Vorgänge nachvollziehbar dokumentiert – Sie haben den vollständigen Überblick über
                            Ihre Immobilie.</p>
                    </div>

                    <div class="feature-card">
                        <h4><span class="feature-icon">🔒</span> Höchste Sicherheit</h4>
                        <p>Verschlüsselte Datenübertragung, sichere Server in Deutschland, regelmäßige Backups und
                            DSGVO-Konformität.</p>
                    </div>
                </div>
            </div>

            <!-- Eigentümer-Portal -->
            <div class="content-block">
                <h2>Eigentümer-Portal</h2>

                <h3>Ihr persönlicher Verwaltungsbereich</h3>
                <p>Unser Eigentümer-Portal ist Ihre zentrale Anlaufstelle für alle Informationen rund um Ihre
                    Immobilie:</p>

                <ul>
                    <li><strong>Dokumentenarchiv:</strong> Alle wichtigen Unterlagen digital abrufbar
                        <ul>
                            <li>Jahresabrechnungen und Wirtschaftspläne</li>
                            <li>Protokolle der Eigentümerversammlungen</li>
                            <li>Versicherungsunterlagen</li>
                            <li>Verträge (Verwaltervertrag, Hausmeister, Dienstleister)</li>
                            <li>Gutachten und Reparaturberichte</li>
                        </ul>
                    </li>
                    <li><strong>Finanzen im Blick:</strong>
                        <ul>
                            <li>Aktuelle Kontoauszüge und Kontostände</li>
                            <li>Buchungsübersichten (Ein- und Ausgaben)</li>
                            <li>Zahlungsstatus Ihrer Hausgeldzahlungen</li>
                            <li>Rücklagenentwicklung grafisch dargestellt</li>
                        </ul>
                    </li>
                    <li><strong>Kommunikation:</strong>
                        <ul>
                            <li>Direktnachrichten an die Verwaltung</li>
                            <li>Ankündigungen und wichtige Mitteilungen</li>
                            <li>Schadensmeldungen mit Foto-Upload</li>
                            <li>Nachrichtenverlauf und Ticket-System</li>
                        </ul>
                    </li>
                    <li><strong>Versammlungsvorbereitung:</strong>
                        <ul>
                            <li>Einladungen mit Tagesordnung</li>
                            <li>Abstimmungen und Meinungsbilder</li>
                            <li>Anträge online einreichen</li>
                            <li>Vollmachten digital erteilen</li>
                        </ul>
                    </li>
                </ul>

                <div class="tech-box">
                    <h3>✨ Benutzerfreundlichkeit</h3>
                    <p>Unser Portal ist intuitiv bedienbar – auch ohne technische Vorkenntnisse. Klare Menüführung,
                        übersichtliche Darstellung und hilfreiche Tooltips unterstützen Sie bei jedem Schritt.</p>
                </div>
            </div>

            <!-- Mieter-App -->
            <div class="content-block">
                <h2>Mieter-App & Portal</h2>

                <h3>Digitaler Service für Ihre Mieter</h3>
                <p>Auch Mieter profitieren von unserer digitalen Verwaltung:</p>

                <ul>
                    <li><strong>Schadensmeldung:</strong> Defekte schnell und unkompliziert melden
                        <ul>
                            <li>Fotos direkt hochladen</li>
                            <li>Detaillierte Beschreibung mit Formular</li>
                            <li>Automatische Weiterleitung an zuständige Handwerker</li>
                            <li>Status-Tracking der Reparatur</li>
                        </ul>
                    </li>
                    <li><strong>Digitaler Briefkasten:</strong>
                        <ul>
                            <li>Wichtige Mitteilungen (Betriebskostenabrechnungen, Hausordnung)</li>
                            <li>Ankündigungen (Handwerkertermine, Modernisierungen)</li>
                            <li>Push-Benachrichtigungen bei neuen Nachrichten</li>
                        </ul>
                    </li>
                    <li><strong>Dokumentenverwaltung:</strong>
                        <ul>
                            <li>Mietvertrag und Übergabeprotokolle</li>
                            <li>Nebenkostenabrechnungen der Vorjahre</li>
                            <li>Bescheinigungen (z.B. für Jobcenter)</li>
                        </ul>
                    </li>
                    <li><strong>Kontakt zur Verwaltung:</strong>
                        <ul>
                            <li>Direkte Nachrichtenfunktion</li>
                            <li>FAQ-Bereich für häufige Fragen</li>
                            <li>Kontaktdaten und Öffnungszeiten</li>
                        </ul>
                    </li>
                </ul>

                <h3>Vorteile für Vermieter</h3>
                <p>Die Mieter-App entlastet auch Sie als Eigentümer:</p>
                <ul>
                    <li>Schnellere Schadensbearbeitung durch sofortige Meldung</li>
                    <li>Weniger Telefonanrufe und Papierkram</li>
                    <li>Höhere Mieterzufriedenheit durch modernen Service</li>
                    <li>Dokumentierte Kommunikation bei Konflikten</li>
                </ul>
            </div>

            <!-- Automatisierung -->
            <div class="content-block">
                <h2>Automatisierte Prozesse</h2>

                <h3>Effizienzsteigerung durch Automatisierung</h3>
                <p>Viele wiederkehrende Aufgaben können automatisiert werden:</p>

                <ol class="process-steps">
                    <li>
                        <strong>Automatische Zahlungsabwicklung</strong>
                        <p>Hausgeldzahlungen, Nebenkostenvorauszahlungen und Mieten werden automatisch überwacht.
                            Bei Zahlungsverzug erfolgt automatische Mahnung.</p>
                    </li>
                    <li>
                        <strong>Digitale Nebenkostenabrechnung</strong>
                        <p>Verbrauchsdaten werden automatisch ausgelesen (Smart Meter), Kosten berechnet und
                            Abrechnungen erstellt – fehlerfrei und fristgerecht.</p>
                    </li>
                    <li>
                        <strong>Fristenmanagement</strong>
                        <p>Wichtige Fristen (Abrechnungsfristen, Kündigungsfristen, Gewährleistungsfristen) werden
                            automatisch überwacht und Erinnerungen versendet.</p>
                    </li>
                    <li>
                        <strong>Dokumentenarchivierung</strong>
                        <p>Eingehende Dokumente (Rechnungen, Verträge) werden automatisch erkannt, verschlagwortet
                            und archiviert (OCR-Texterkennung).</p>
                    </li>
                    <li>
                        <strong>Reporting & Auswertungen</strong>
                        <p>Automatische Erstellung von Berichten: Liquiditätsübersichten, Kostenzusammenstellungen,
                            Vergleichsstatistiken.</p>
                    </li>
                </ol>
            </div>

            <!-- Cloud & Sicherheit -->
            <div class="content-block">
                <h2>Cloud-Speicherung & Datensicherheit</h2>

                <h3>Ihre Daten in sicheren Händen</h3>
                <p>Datensicherheit hat für uns höchste Priorität. Wir setzen auf modernste Sicherheitsstandards:</p>

                <ul>
                    <li><strong>Verschlüsselte Datenübertragung:</strong> Alle Daten werden über SSL/TLS
                        verschlüsselt übertragen (wie beim Online-Banking)</li>
                    <li><strong>Server-Standort Deutschland:</strong> Alle Daten werden ausschließlich auf Servern
                        in Deutschland gespeichert (DSGVO-konform)</li>
                    <li><strong>Zugriffsschutz:</strong> Mehrstufige Authentifizierung (2-Faktor-Login optional)
                        schützt vor unbefugtem Zugriff</li>
                    <li><strong>Regelmäßige Backups:</strong> Tägliche automatische Datensicherung mit mehrfacher
                        Redundanz</li>
                    <li><strong>Datenschutz:</strong> Strikte Einhaltung der DSGVO, regelmäßige Audits durch
                        externen Datenschutzbeauftragten</li>
                    <li><strong>Zugriffsrechte:</strong> Rollenbasiertes Rechtesystem – jeder sieht nur, was er
                        sehen darf</li>
                </ul>

                <h3>Zertifizierungen & Standards</h3>
                <ul>
                    <li>ISO 27001 (Informationssicherheit)</li>
                    <li>DSGVO-konform</li>
                    <li>Geprüft durch externe Datenschutzbeauftragte</li>
                    <li>Hosting bei zertifizierten deutschen Rechenzentren</li>
                </ul>

                <div class="info-box">
                    <h3>🔒 Ihre Privatsphäre ist geschützt</h3>
                    <p>Wir geben keine Daten an Dritte weiter, verkaufen keine Informationen und nutzen Ihre Daten
                        ausschließlich für die Verwaltung Ihrer Immobilie. Volle Transparenz durch
                        Datenschutzerklärung und jederzeit Auskunftsrecht nach DSGVO.</p>
                </div>
            </div>

            <!-- Vergleich: Digital vs. Analog -->
            <div class="content-block">
                <h2>Digital vs. Traditionelle Verwaltung</h2>

                <table class="comparison-table">
                    <thead>
                        <tr>
                            <th>Aspekt</th>
                            <th>Digitale Verwaltung</th>
                            <th>Traditionelle Verwaltung</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>Verfügbarkeit</strong></td>
                            <td>24/7 online <span class="check-icon">✓</span></td>
                            <td>Nur während Öffnungszeiten <span class="cross-icon">✗</span></td>
                        </tr>
                        <tr>
                            <td><strong>Dokumentenzugriff</strong></td>
                            <td>Sofort abrufbar <span class="check-icon">✓</span></td>
                            <td>Anfrage nötig, Wartezeit <span class="cross-icon">✗</span></td>
                        </tr>
                        <tr>
                            <td><strong>Kommunikation</strong></td>
                            <td>Digital, nachvollziehbar <span class="check-icon">✓</span></td>
                            <td>Telefon/Post, teils verzögert <span class="cross-icon">✗</span></td>
                        </tr>
                        <tr>
                            <td><strong>Schadensmeldung</strong></td>
                            <td>Mit Fotos, sofort <span class="check-icon">✓</span></td>
                            <td>Telefonisch beschreiben <span class="cross-icon">✗</span></td>
                        </tr>
                        <tr>
                            <td><strong>Abrechnungen</strong></td>
                            <td>Digital, interaktiv <span class="check-icon">✓</span></td>
                            <td>Papier per Post <span class="cross-icon">✗</span></td>
                        </tr>
                        <tr>
                            <td><strong>Archivierung</strong></td>
                            <td>Unbegrenzt, durchsuchbar <span class="check-icon">✓</span></td>
                            <td>Physischer Platz begrenzt <span class="cross-icon">✗</span></td>
                        </tr>
                        <tr>
                            <td><strong>Umweltfreundlich</strong></td>
                            <td>Papierlos <span class="check-icon">✓</span></td>
                            <td>Hoher Papierverbrauch <span class="cross-icon">✗</span></td>
                        </tr>
                        <tr>
                            <td><strong>Kosten</strong></td>
                            <td>Effizienter, niedriger <span class="check-icon">✓</span></td>
                            <td>Porto, Druck, Archivierung <span class="cross-icon">✗</span></td>
                        </tr>
                        <tr>
                            <td><strong>Transparenz</strong></td>
                            <td>Vollständige Einsicht <span class="check-icon">✓</span></td>
                            <td>Eingeschränkt <span class="cross-icon">✗</span></td>
                        </tr>
                        <tr>
                            <td><strong>Sicherheit</strong></td>
                            <td>Verschlüsselt, Backups <span class="check-icon">✓</span></td>
                            <td>Brand, Verlust möglich <span class="cross-icon">✗</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Integration & Schnittstellen -->
            <div class="content-block">
                <h2>Integrationen & Schnittstellen</h2>

                <h3>Nahtlose Anbindung an andere Systeme</h3>
                <p>Unsere digitale Verwaltung lässt sich mit vielen externen Systemen verbinden:</p>

                <ul>
                    <li><strong>Buchhaltungssoftware:</strong> DATEV, Lexware – automatischer Datenaustausch</li>
                    <li><strong>Banken:</strong> Automatischer Zahlungsabgleich (SEPA, CAMT-Format)</li>
                    <li><strong>Smart Home:</strong> Anbindung von intelligenten Zählern, Heizungssteuerung</li>
                    <li><strong>Handwerker-Portale:</strong> Direktvergabe von Aufträgen an Handwerkerplattformen
                    </li>
                    <li><strong>Energieversorger:</strong> Automatischer Abruf von Verbrauchsdaten</li>
                    <li><strong>Versicherungen:</strong> Schadensmeldungen direkt an Versicherer übermitteln</li>
                    <li><strong>Grundbuchämter:</strong> Elektronischer Grundbuchauszug (perspektivisch)</li>
                </ul>
            </div>

            <!-- Schulung & Support -->
            <div class="content-block">
                <h2>Schulung & Support</h2>

                <h3>Wir lassen Sie nicht allein</h3>
                <p>Die Umstellung auf digitale Verwaltung ist einfacher als gedacht. Wir unterstützen Sie:</p>

                <ul>
                    <li><strong>Persönliche Einführung:</strong> Gemeinsam richten wir Ihren Zugang ein und zeigen
                        Ihnen alle Funktionen</li>
                    <li><strong>Video-Tutorials:</strong> Schritt-für-Schritt-Anleitungen für alle wichtigen
                        Funktionen</li>
                    <li><strong>Benutzerhandbuch:</strong> Ausführliche Dokumentation als PDF zum Download</li>
                    <li><strong>Hotline & Support:</strong> Persönlicher Ansprechpartner bei Fragen (Telefon,
                        E-Mail, Chat)</li>
                    <li><strong>Webinare:</strong> Regelmäßige Online-Schulungen zu neuen Features</li>
                    <li><strong>FAQ-Bereich:</strong> Antworten auf häufige Fragen direkt im Portal</li>
                </ul>

                <h3>Technische Voraussetzungen</h3>
                <p>Minimal – Sie brauchen nur:</p>
                <ul>
                    <li>Internetfähiges Gerät (PC, Tablet, Smartphone)</li>
                    <li>Moderner Browser (Chrome, Firefox, Safari, Edge)</li>
                    <li>Internetverbindung</li>
                    <li>Das war's – keine Software-Installation nötig!</li>
                </ul>
            </div>

            <div class="highlight-box">
                <h3>🚀 Zukunftssicher aufgestellt</h3>
                <p>Mit unserer digitalen Verwaltung sind Sie bereit für die Zukunft. Wir entwickeln unsere Systeme
                    kontinuierlich weiter und passen uns an neue Technologien und gesetzliche Anforderungen an –
                    ohne Mehrkosten für Sie!</p>
            </div>
        </div>

        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-widget">
                <h3>Schnellnavigation</h3>
                <ul class="quick-links">
                    <li><a href="#vorteile">→ Vorteile</a></li>
                    <li><a href="#portal">→ Eigentümer-Portal</a></li>
                    <li><a href="#mieter-app">→ Mieter-App</a></li>
                    <li><a href="#sicherheit">→ Datensicherheit</a></li>
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
                <h3>Digital durchstarten?</h3>
                <p>Erleben Sie moderne Immobilienverwaltung!</p>
                <a href="<?= BASE ?>/#contact" class="cta-btn">Jetzt Demo anfragen</a>
            </div>
        </aside>
    </div>
</section>

<!-- FAQ Section -->
<section class="faq-section" id="faq">
    <div class="faq-container">
        <div class="faq-header">
            <h2>Häufig gestellte Fragen zur digitalen Verwaltung</h2>
            <p>Alles, was Sie über unsere digitalen Lösungen wissen müssen</p>
        </div>

        <div class="faq-item">
            <button class="faq-question" onclick="toggleFAQ(this)">
                <span>Wie sicher sind meine Daten im Online-Portal?</span>
                <svg class="faq-icon" width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z" />
                </svg>
            </button>
            <div class="faq-answer">
                <div class="faq-answer-content">
                    <p>Ihre Daten sind bei uns maximal geschützt – genauso sicher wie beim Online-Banking:</p>
                    <ul>
                        <li><strong>Verschlüsselte Übertragung:</strong> Alle Daten werden über SSL/TLS
                            verschlüsselt übertragen (erkennbar am Schloss-Symbol in der Browserzeile)</li>
                        <li><strong>Deutsche Server:</strong> Ihre Daten werden ausschließlich in zertifizierten
                            Rechenzentren in Deutschland gespeichert</li>
                        <li><strong>Mehrstufige Authentifizierung:</strong> Login nur mit Benutzername und starkem
                            Passwort, optional 2-Faktor-Authentifizierung</li>
                        <li><strong>Regelmäßige Backups:</strong> Tägliche automatische Sicherung mit mehrfacher
                            Redundanz</li>
                        <li><strong>Zugriffsprotokollierung:</strong> Jeder Login wird protokolliert – unbefugte
                            Zugriffe werden erkannt</li>
                        <li><strong>DSGVO-konform:</strong> Strikte Einhaltung aller Datenschutzvorschriften</li>
                    </ul>
                    <p>Zudem: Ihre Daten sind sicherer als in Papierform! Kein Risiko durch Brand, Wasserschaden
                        oder Verlust von Unterlagen.</p>
                </div>
            </div>
        </div>

        <div class="faq-item">
            <button class="faq-question" onclick="toggleFAQ(this)">
                <span>Kostet die digitale Verwaltung extra?</span>
                <svg class="faq-icon" width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z" />
                </svg>
            </button>
            <div class="faq-answer">
                <div class="faq-answer-content">
                    <p><strong>Nein, die digitale Verwaltung ist bei uns bereits im regulären Verwaltungshonorar
                            enthalten!</strong></p>
                    <p>Im Gegenteil – Sie sparen sogar Geld:</p>
                    <ul>
                        <li><strong>Keine Portokosten:</strong> Kein Versand von Abrechnungen und Protokollen per
                            Post</li>
                        <li><strong>Keine Druckkosten:</strong> Papierlose Dokumentenverwaltung</li>
                        <li><strong>Weniger Verwaltungsaufwand:</strong> Automatisierung spart Zeit und damit Kosten
                        </li>
                        <li><strong>Schnellere Prozesse:</strong> Weniger Fehler, weniger Nacharbeit</li>
                    </ul>
                    <p><strong>Einmalige Einrichtung:</strong> Der Zugang zum Portal wird einmalig für Sie
                        eingerichtet – kostenlos. Sie erhalten Ihre Zugangsdaten per E-Mail und können sofort
                        loslegen.</p>
                    <p><strong>Optional:</strong> Wenn Sie weiterhin Papierpost wünschen (zusätzlich zur digitalen
                        Variante), entstehen Mehrkosten für Porto und Druck. Wir empfehlen jedoch den vollständig
                        digitalen Weg.</p>
                </div>
            </div>
        </div>

        <div class="faq-item">
            <button class="faq-question" onclick="toggleFAQ(this)">
                <span>Was passiert, wenn ich keinen Computer habe?</span>
                <svg class="faq-icon" width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z" />
                </svg>
            </button>
            <div class="faq-answer">
                <div class="faq-answer-content">
                    <p>Kein Problem – unsere digitale Verwaltung ist flexibel:</p>
                    <ul>
                        <li><strong>Smartphone oder Tablet:</strong> Unser Portal funktioniert auch auf mobilen
                            Geräten perfekt (responsive Design)</li>
                        <li><strong>Internetcafé/Bibliothek:</strong> Sie können von jedem internetfähigen Gerät
                            zugreifen</li>
                        <li><strong>Unterstützung durch Angehörige:</strong> Kinder, Enkel oder Freunde können Sie
                            unterstützen</li>
                        <li><strong>Papierpost optional:</strong> Auf Wunsch senden wir Ihnen weiterhin wichtige
                            Dokumente per Post (gegen Aufpreis)</li>
                        <li><strong>Telefon-Service:</strong> Sie können uns jederzeit anrufen – wir helfen auch am
                            Telefon weiter</li>
                    </ul>
                    <p><strong>Unser Tipp:</strong> Viele ältere Eigentümer nutzen die Gelegenheit, um gemeinsam mit
                        Familienmitgliedern die digitale Verwaltung zu entdecken. Oft ist es einfacher als gedacht!
                    </p>
                    <p><strong>Persönliche Einführung:</strong> Bei Bedarf kommen wir auch persönlich vorbei und
                        zeigen Ihnen Schritt für Schritt, wie alles funktioniert.</p>
                </div>
            </div>
        </div>

        <div class="faq-item">
            <button class="faq-question" onclick="toggleFAQ(this)">
                <span>Kann ich meine alten Dokumente auch digital einsehen?</span>
                <svg class="faq-icon" width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z" />
                </svg>
            </button>
            <div class="faq-answer">
                <div class="faq-answer-content">
                    <p><strong>Ja, wir digitalisieren Ihre alten Unterlagen!</strong></p>
                    <p><strong>Standard-Digitalisierung (kostenlos):</strong></p>
                    <ul>
                        <li>Alle wichtigen Dokumente der letzten 3 Jahre werden eingescannt</li>
                        <li>Abrechnungen, Protokolle, Verträge, Versicherungen</li>
                        <li>Systematische Ablage nach Kategorien und Jahren</li>
                    </ul>
                    <p><strong>Erweiterte Digitalisierung (optional):</strong></p>
                    <ul>
                        <li>Digitalisierung von Dokumenten über 3 Jahre hinaus</li>
                        <li>OCR-Texterkennung für Durchsuchbarkeit</li>
                        <li>Verknüpfung mit Immobiliendaten</li>
                        <li>Kosten: Nach Aufwand (ca. 0,50€ pro Seite)</li>
                    </ul>
                    <p><strong>Aufbewahrungsfristen:</strong> Rechtlich relevante Dokumente müssen teilweise bis zu
                        30 Jahre aufbewahrt werden. Wir stellen sicher, dass alle Unterlagen gemäß gesetzlicher
                        Vorgaben archiviert werden.</p>
                    <p><strong>Zugriff:</strong> Nach der Digitalisierung haben Sie jederzeit Zugriff auf alle Ihre
                        Dokumente – egal wo Sie sind!</p>
                </div>
            </div>
        </div>

        <div class="faq-item">
            <button class="faq-question" onclick="toggleFAQ(this)">
                <span>Wie funktioniert die Mieter-App genau?</span>
                <svg class="faq-icon" width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z" />
                </svg>
            </button>
            <div class="faq-answer">
                <div class="faq-answer-content">
                    <p>Die Mieter-App ist eine mobile Anwendung für iOS und Android, die Mietern direkten Kontakt
                        zur Verwaltung ermöglicht:</p>

                    <p><strong>Installation & Zugang:</strong></p>
                    <ul>
                        <li>Download aus App Store (iOS) oder Google Play (Android)</li>
                        <li>Mieter erhält Zugangscode per E-Mail oder Brief</li>
                        <li>Einmalige Registrierung mit Mietvertragsdaten</li>
                        <li>Login mit Benutzername und Passwort</li>
                    </ul>

                    <p><strong>Hauptfunktionen:</strong></p>
                    <ul>
                        <li><strong>Schadensmeldung:</strong> Defekt fotografieren → Beschreibung eingeben →
                            Absenden → Handwerker wird automatisch informiert</li>
                        <li><strong>Nachrichtenfunktion:</strong> Chat ähnlich wie WhatsApp, aber verschlüsselt und
                            dokumentiert</li>
                        <li><strong>Dokumentenabruf:</strong> Betriebskostenabrechnungen, Mietvertrag, Hausordnung
                            jederzeit abrufbar</li>
                        <li><strong>Ankündigungen:</strong> Push-Benachrichtigungen bei wichtigen Mitteilungen (z.B.
                            Handwerkertermine)</li>
                    </ul>

                    <p><strong>Vorteile für Sie als Eigentümer:</strong></p>
                    <ul>
                        <li>Schnellere Schadensbehebung (weniger Folgeschäden)</li>
                        <li>Dokumentierte Kommunikation bei Streitigkeiten</li>
                        <li>Zufriedenere Mieter durch modernen Service</li>
                        <li>Weniger Anrufe außerhalb der Geschäftszeiten</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="faq-item">
            <button class="faq-question" onclick="toggleFAQ(this)">
                <span>Was passiert bei einem Serverausfall oder technischen Problemen?</span>
                <svg class="faq-icon" width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z" />
                </svg>
            </button>
            <div class="faq-answer">
                <div class="faq-answer-content">
                    <p><strong>Unsere Systeme sind hochverfügbar und redundant ausgelegt:</strong></p>

                    <p><strong>Ausfallsicherheit:</strong></p>
                    <ul>
                        <li><strong>Verfügbarkeit:</strong> 99,9% Uptime garantiert (weniger als 9 Stunden Ausfall
                            pro Jahr)</li>
                        <li><strong>Redundante Server:</strong> Mehrere Server verteilt – fällt einer aus, übernimmt
                            automatisch ein anderer</li>
                        <li><strong>Tägliche Backups:</strong> Bei Datenverlust können Daten innerhalb von 24
                            Stunden wiederhergestellt werden</li>
                        <li><strong>Monitoring:</strong> 24/7-Überwachung der Systeme – technische Probleme werden
                            sofort erkannt</li>
                    </ul>

                    <p><strong>Im unwahrscheinlichen Fall eines Ausfalls:</strong></p>
                    <ul>
                        <li>Sofortige Benachrichtigung per E-Mail und auf der Website</li>
                        <li>Telefonische Erreichbarkeit bleibt gewährleistet</li>
                        <li>Notfallprozesse für dringende Angelegenheiten (z.B. Schadensmeldungen)</li>
                        <li>Schnellstmögliche Wiederherstellung (meist innerhalb von Minuten)</li>
                    </ul>

                    <p><strong>Datensicherheit:</strong> Ihre Daten gehen niemals verloren – durch mehrfache Backups
                        an verschiedenen Standorten ist absolute Sicherheit gewährleistet!</p>
                </div>
            </div>
        </div>

        <!-- Kontakt-Hinweis -->
        <div class="info-box mt-40">
            <h3>🚀 Bereit für die digitale Zukunft?</h3>
            <p>Lassen Sie uns gemeinsam Ihre Immobilienverwaltung digitalisieren! Wir zeigen Ihnen in einer
                kostenlosen Demo, wie einfach und effizient digitale Verwaltung sein kann.</p>
            <div class="mt-20">
                <a href="<?= BASE ?>/#contact" class="cta-btn inline-block">Jetzt Demo-Termin
                    vereinbaren</a>
            </div>
        </div>
    </div>
</section>

<!-- Contact CTA -->
<section class="contact-cta-section">
    <h2>Bereit für moderne Immobilienverwaltung?</h2>
    <p>Erleben Sie die Vorteile digitaler Verwaltung – transparent, effizient und zukunftssicher</p>
    <div class="contact-buttons">
        <a href="tel:+4916098787878" class="contact-btn-primary">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                <path
                    d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z" />
            </svg>
            Jetzt anrufen
        </a>
        <a href="<?= BASE ?>/#contact" class="contact-btn-secondary">Kostenlose Demo</a>
    </div>
</section>

<!-- Footer -->

<?php require_once __DIR__ . '/includes/footer.php'; ?>