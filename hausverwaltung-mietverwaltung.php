<?php
require_once __DIR__ . '/includes/config.php';
header('Location: ' . BASE . '/hausverwaltung#leistung-mietverwaltung', true, 301);
exit;

$pageTitle       = 'Mietverwaltung – Immobilien Ghumman';
$pageDescription = 'Professionelle Mietverwaltung – wir kümmern uns um alles.';
$pageKeywords    = 'Mietverwaltung, Mieter, Mietvertrag, Bad Vilbel';
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

        /* Header Styles */
        .page-header {
            background: linear-gradient(135deg, #2980b9 0%, #3498db 100%);
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

        /* Content Section */
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
            background: linear-gradient(135deg, #f8f9ff 0%, #e3e9ff 100%);
            padding: 40px;
            border-radius: 16px;
            margin-bottom: 50px;
            border-left: 5px solid #2980b9;
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
            border-bottom: 3px solid #2980b9;
        }

        .content-block h3 {
            color: #2c3e50;
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
            color: #2980b9;
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
            background: #e3f2fd;
            border-left: 4px solid #2196f3;
            padding: 25px;
            border-radius: 8px;
            margin: 30px 0;
        }

        .info-box h3 {
            color: #1976d2;
            margin-top: 0;
        }

        /* Sidebar */
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
            color: #2980b9;
            transform: translateX(5px);
        }

        .cta-widget {
            background: linear-gradient(135deg, #2980b9 0%, #3498db 100%);
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
            color: #2980b9;
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

        /* FAQ Section */
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
            color: #2980b9;
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

        /* Contact CTA */
        .contact-cta-section {
            background: linear-gradient(135deg, #1a56a4 0%, #0d1b2e 100%);
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
            color: #1a56a4;
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
            color: #1a56a4;
        }

        /* Responsive */
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
            <span>Mietverwaltung</span>
        </nav>
        <div class="page-hero-badge"><i class="fas fa-file-contract"></i> Mietobjekte professionell betreut</div>
        <h1>Professionelle <span class="accent">Mietverwaltung</span></h1>
        <p>Umfassende Betreuung Ihrer Mietobjekte &ndash; von der Vertragsgestaltung und Mietersuche bis zum Mietinkasso und der Betriebskostenabrechnung.</p>
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
            <div class="hero-stat"><i class="fas fa-home"></i>
                <div><span class="hero-stat-number">200+</span><span class="hero-stat-label">Verwaltete Einheiten</span></div>
            </div>
            <div class="hero-stat"><i class="fas fa-clock"></i>
                <div><span class="hero-stat-number">24 Std.</span><span class="hero-stat-label">Reaktionszeit</span></div>
            </div>
            <div class="hero-stat"><i class="fas fa-shield-alt"></i>
                <div><span class="hero-stat-number">100%</span><span class="hero-stat-label">Rechtssicher</span></div>
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
                <h2>Was ist professionelle Mietverwaltung?</h2>
                <p>Die Mietverwaltung umfasst alle administrativen und organisatorischen Aufgaben, die mit der
                    Vermietung Ihrer Immobilie verbunden sind. Als erfahrene Hausverwaltung übernehmen wir für Sie
                    die komplette Betreuung Ihrer Mietobjekte – von der Mietersuche über die Vertragsgestaltung bis
                    hin zum laufenden Mietinkasso und der Mieterbetreuung.</p>
            </div>

            <!-- Leistungen -->
            <div class="content-block">
                <h2>Unsere Leistungen in der Mietverwaltung</h2>

                <h3>1. Mietersuche und -auswahl</h3>
                <p>Die Suche nach geeigneten Mietern ist ein entscheidender Faktor für den langfristigen Erfolg
                    Ihrer Vermietung. Wir kümmern uns um:</p>
                <ul>
                    <li><strong>Professionelle Immobilienanzeigen:</strong> Erstellung ansprechender Exposés mit
                        hochwertigen Fotos und detaillierten Objektbeschreibungen</li>
                    <li><strong>Multi-Channel-Marketing:</strong> Veröffentlichung auf allen relevanten
                        Immobilienportalen und in lokalen Medien</li>
                    <li><strong>Besichtigungstermine:</strong> Organisation und Durchführung von Besichtigungen zu
                        optimalen Zeiten</li>
                    <li><strong>Bonitätsprüfung:</strong> Sorgfältige Prüfung der Bonität und Zahlungsfähigkeit
                        interessierter Mieter</li>
                    <li><strong>Referenzprüfung:</strong> Einholung und Bewertung von Mietervergangenheiten und
                        Vermieterbescheinigungen</li>
                </ul>

                <h3>2. Mietvertragsgestaltung</h3>
                <p>Ein rechtssicherer Mietvertrag ist das Fundament einer erfolgreichen Vermietung. Wir bieten:</p>
                <ul>
                    <li><strong>Rechtssichere Verträge:</strong> Verwendung aktueller Mietvertragsvorlagen unter
                        Berücksichtigung der neuesten Rechtsprechung</li>
                    <li><strong>Individuelle Anpassungen:</strong> Berücksichtigung Ihrer spezifischen Wünsche und
                        Anforderungen</li>
                    <li><strong>Nebenkosten-Regelungen:</strong> Klare und transparente Vereinbarungen zu
                        Betriebskosten und Nebenkosten</li>
                    <li><strong>Übergabeprotokolle:</strong> Detaillierte Dokumentation des Objektzustands bei Ein-
                        und Auszug</li>
                    <li><strong>Kautionsverwaltung:</strong> Sichere Verwaltung der Mietkaution auf separaten Konten
                    </li>
                </ul>

                <h3>3. Mietinkasso und Zahlungsüberwachung</h3>
                <p>Zuverlässige Mietzahlungen sind essentiell für Ihre finanzielle Planung. Unser Service umfasst:
                </p>
                <ul>
                    <li><strong>Monatliches Mietinkasso:</strong> Überwachung aller Mietzahlungseingänge und
                        zeitnahe Informationen bei Zahlungsverzug</li>
                    <li><strong>Mahnwesen:</strong> Professionelles und rechtssicheres Mahnverfahren bei
                        ausbleibenden Zahlungen</li>
                    <li><strong>Nebenkostenabrechnung:</strong> Erstellung korrekter und nachvollziehbarer
                        Betriebskostenabrechnungen nach aktueller Rechtslage</li>
                    <li><strong>Mietanpassungen:</strong> Prüfung und Durchführung von Mieterhöhungen unter
                        Beachtung aller gesetzlichen Vorgaben</li>
                    <li><strong>Auszahlungsmanagement:</strong> Regelmäßige und pünktliche Überweisung Ihrer
                        Mieteinnahmen</li>
                </ul>

                <h3>4. Mieterbetreuung</h3>
                <p>Eine gute Mieterbeziehung führt zu weniger Fluktuation und einem gepflegten Objekt. Wir sind für
                    Ihre Mieter da:</p>
                <ul>
                    <li><strong>Zentrale Anlaufstelle:</strong> Wir sind Ansprechpartner für alle Anliegen Ihrer
                        Mieter</li>
                    <li><strong>Schadensmeldungen:</strong> Entgegennahme und Koordination von Reparaturmeldungen
                    </li>
                    <li><strong>Handwerkerkoordination:</strong> Organisation und Überwachung notwendiger
                        Reparaturen</li>
                    <li><strong>Konfliktlösung:</strong> Mediation bei Unstimmigkeiten zwischen Mietern oder mit
                        Nachbarn</li>
                    <li><strong>Regelmäßige Objektbegehungen:</strong> Kontrolle des Objektzustands und frühzeitiges
                        Erkennen von Mängeln</li>
                </ul>
            </div>

            <div class="highlight-box">
                <h3>💡 Wichtig zu wissen</h3>
                <p>Eine professionelle Mietverwaltung spart Ihnen nicht nur Zeit und Nerven, sondern schützt Sie
                    auch vor rechtlichen Fallstricken. Fehler in Mietverträgen oder Nebenkostenabrechnungen können
                    zu kostspieligen Rechtsstreitigkeiten führen. Mit unserer Expertise sind Sie auf der sicheren
                    Seite.</p>
            </div>

            <!-- Vorteile -->
            <div class="content-block">
                <h2>Ihre Vorteile einer professionellen Mietverwaltung</h2>
                <ol>
                    <li><strong>Zeitersparnis:</strong> Sie müssen sich nicht um zeitaufwendige administrative
                        Aufgaben kümmern</li>
                    <li><strong>Rechtssicherheit:</strong> Alle Vorgänge entsprechen der aktuellen Gesetzeslage</li>
                    <li><strong>Professionelles Mahnwesen:</strong> Bei Zahlungsausfällen handeln wir schnell und
                        konsequent</li>
                    <li><strong>Optimale Mieterauswahl:</strong> Minimierung von Mietausfällen durch sorgfältige
                        Mieterprüfung</li>
                    <li><strong>Objektwerterhalt:</strong> Regelmäßige Kontrollen sichern den Wert Ihrer Immobilie
                    </li>
                    <li><strong>Transparenz:</strong> Sie erhalten regelmäßige Berichte über alle Vorgänge</li>
                    <li><strong>Konfliktmanagement:</strong> Wir lösen Probleme professionell und ohne emotionale
                        Belastung für Sie</li>
                    <li><strong>Steuerliche Vorteile:</strong> Verwaltungskosten sind als Werbungskosten steuerlich
                        absetzbar</li>
                </ol>
            </div>

            <div class="info-box">
                <h3>ℹ️ Gut zu wissen</h3>
                <p>Die Kosten für eine professionelle Mietverwaltung können Sie in Ihrer Steuererklärung als
                    Werbungskosten bei den Einkünften aus Vermietung und Verpachtung geltend machen. Dies reduziert
                    Ihre steuerliche Belastung erheblich.</p>
            </div>

            <!-- Prozess -->
            <div class="content-block">
                <h2>So läuft die Mietverwaltung ab</h2>

                <h3>Phase 1: Aufnahme und Analyse</h3>
                <p>Zu Beginn unserer Zusammenarbeit führen wir eine umfassende Objektanalyse durch. Wir erfassen
                    alle relevanten Daten Ihrer Immobilie, prüfen bestehende Mietverträge und bewerten die aktuelle
                    Miethöhe im Vergleich zum Marktniveau.</p>

                <h3>Phase 2: Laufende Verwaltung</h3>
                <p>In der täglichen Verwaltungsarbeit übernehmen wir alle anfallenden Aufgaben: vom Mietinkasso über
                    die Mieterbetreuung bis zur Organisation von Reparaturen. Sie werden regelmäßig über alle
                    wichtigen Vorgänge informiert.</p>

                <h3>Phase 3: Abrechnung und Reporting</h3>
                <p>Einmal jährlich erstellen wir die Nebenkostenabrechnungen für Ihre Mieter und stellen Ihnen einen
                    detaillierten Jahresbericht über Einnahmen, Ausgaben und den Zustand Ihrer Immobilie zur
                    Verfügung.</p>

                <h3>Phase 4: Mieterwechsel</h3>
                <p>Bei einem Mieterwechsel koordinieren wir die Wohnungsübergabe, führen Abnahmeprotokolle durch,
                    rechnen die Kaution ab und kümmern uns um die Suche nach einem neuen Mieter – alles aus einer
                    Hand.</p>
            </div>

            <!-- Kosten -->
            <div class="content-block">
                <h2>Kosten der Mietverwaltung</h2>
                <p>Die Kosten für eine professionelle Mietverwaltung sind transparent und planbar. Üblicherweise
                    berechnen wir einen Prozentsatz der Jahresnettokaltmiete (in der Regel zwischen 20-30% der
                    Monatskaltmiete) sowie eventuell zusätzliche Kosten für besondere Leistungen wie
                    Neuvermietungen.</p>

                <p>Dabei gilt: Die Investition in eine professionelle Mietverwaltung rechnet sich durch:</p>
                <ul>
                    <li>Minimierung von Leerstandszeiten</li>
                    <li>Vermeidung von Mietausfällen durch sorgfältige Mieterauswahl</li>
                    <li>Zeitersparnis und Stressreduktion</li>
                    <li>Schutz vor rechtlichen Fehlern und deren Folgekosten</li>
                    <li>Steuerliche Absetzbarkeit der Verwaltungskosten</li>
                </ul>
            </div>
        </div>

        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-widget">
                <h3>Schnellnavigation</h3>
                <ul class="quick-links">
                    <li><a href="#leistungen">→ Unsere Leistungen</a></li>
                    <li><a href="#vorteile">→ Ihre Vorteile</a></li>
                    <li><a href="#prozess">→ Ablauf</a></li>
                    <li><a href="#kosten">→ Kosten</a></li>
                    <li><a href="#faq">→ Häufige Fragen</a></li>
                </ul>
            </div>

            <div class="sidebar-widget">
                <h3>Weitere Themen</h3>
                <ul class="quick-links">
                    <li><a href="<?= BASE ?>/hausverwaltung-nebenkostenabrechnung">→ Nebenkostenabrechnung</a></li>
                    <li><a href="<?= BASE ?>/hausverwaltung-instandhaltung">→ Instandhaltung</a></li>
                    <li><a href="<?= BASE ?>/hausverwaltung-eigentuemerversammlung">→ Eigentümerversammlung</a></li>
                    <li><a href="<?= BASE ?>/hausverwaltung-rechtliches">→ Rechtliche Fragen</a></li>
                </ul>
            </div>

            <div class="sidebar-widget cta-widget">
                <h3>Persönliche Beratung</h3>
                <p>Lassen Sie sich unverbindlich beraten!</p>
                <a href="<?= BASE ?>/#contact" class="cta-btn">Jetzt Kontakt aufnehmen</a>
            </div>
        </aside>
    </div>
</section>

<!-- FAQ Section -->
<section class="faq-section" id="faq">
    <div class="faq-container">
        <div class="faq-header">
            <h2>Häufig gestellte Fragen zur Mietverwaltung</h2>
            <p>Hier finden Sie Antworten auf die wichtigsten Fragen</p>
        </div>

        <div class="faq-item">
            <button class="faq-question" onclick="toggleFAQ(this)">
                <span>Was kostet eine professionelle Mietverwaltung?</span>
                <svg class="faq-icon" width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z" />
                </svg>
            </button>
            <div class="faq-answer">
                <div class="faq-answer-content">
                    <p>Die Kosten für eine Mietverwaltung liegen üblicherweise zwischen 20-30% der monatlichen
                        Nettokaltmiete. Bei einem Mietobjekt mit 1.000 Euro Kaltmiete würden die monatlichen
                        Verwaltungskosten also zwischen 200-300 Euro betragen.</p>
                    <p>Zusätzliche Kosten können anfallen für:</p>
                    <ul>
                        <li>Neuvermietungen (einmalige Provision)</li>
                        <li>Größere Reparaturkoordinationen</li>
                        <li>Rechtliche Vertretungen</li>
                    </ul>
                    <p>Diese Kosten sind transparent und werden vorab mit Ihnen abgestimmt. Wichtig: Die
                        Verwaltungskosten können Sie steuerlich als Werbungskosten absetzen.</p>
                </div>
            </div>
        </div>

        <div class="faq-item">
            <button class="faq-question" onclick="toggleFAQ(this)">
                <span>Wie lange dauert die Suche nach einem geeigneten Mieter?</span>
                <svg class="faq-icon" width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z" />
                </svg>
            </button>
            <div class="faq-answer">
                <div class="faq-answer-content">
                    <p>Die Dauer der Mietersuche hängt von verschiedenen Faktoren ab:</p>
                    <ul>
                        <li><strong>Lage der Immobilie:</strong> In gefragten Lagen finden sich meist innerhalb von
                            1-3 Wochen Mieter</li>
                        <li><strong>Miethöhe:</strong> Marktgerechte Mieten verkürzen die Suchzeit deutlich</li>
                        <li><strong>Objektzustand:</strong> Gut gepflegte Objekte vermieten sich schneller</li>
                        <li><strong>Saison:</strong> Im Frühjahr und Herbst ist die Nachfrage höher</li>
                    </ul>
                    <p>Im Durchschnitt dauert es 2-6 Wochen, bis ein passender Mieter gefunden ist. Durch unser
                        professionelles Marketing und unsere umfangreiche Erfahrung können wir Leerzeiten
                        minimieren.</p>
                </div>
            </div>
        </div>

        <div class="faq-item">
            <button class="faq-question" onclick="toggleFAQ(this)">
                <span>Was passiert, wenn der Mieter nicht zahlt?</span>
                <svg class="faq-icon" width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z" />
                </svg>
            </button>
            <div class="faq-answer">
                <div class="faq-answer-content">
                    <p>Bei Zahlungsverzug handeln wir schnell und professionell:</p>
                    <ol>
                        <li><strong>Sofortige Information:</strong> Sie werden umgehend über ausbleibende Zahlungen
                            informiert</li>
                        <li><strong>Zahlungserinnerung:</strong> Der Mieter erhält eine freundliche
                            Zahlungserinnerung</li>
                        <li><strong>Mahnung:</strong> Bei weiterem Verzug erfolgt eine rechtssichere Mahnung</li>
                        <li><strong>Rechtliche Schritte:</strong> Wir leiten bei Bedarf gerichtliche Mahnverfahren
                            ein</li>
                        <li><strong>Kündigung:</strong> Als letztes Mittel sprechen wir die Kündigung aus</li>
                    </ol>
                    <p>Durch unsere sorgfältige Mieterauswahl mit Bonitätsprüfung minimieren wir das Risiko von
                        Zahlungsausfällen von vornherein.</p>
                </div>
            </div>
        </div>

        <div class="faq-item">
            <button class="faq-question" onclick="toggleFAQ(this)">
                <span>Kann ich als Eigentümer auch selbst Entscheidungen treffen?</span>
                <svg class="faq-icon" width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z" />
                </svg>
            </button>
            <div class="faq-answer">
                <div class="faq-answer-content">
                    <p>Selbstverständlich! Sie bleiben als Eigentümer immer in der Entscheidungsgewalt. Unser
                        Service bedeutet nicht, dass Sie die Kontrolle abgeben:</p>
                    <ul>
                        <li>Bei wichtigen Entscheidungen (z.B. Mieterauswahl, größere Reparaturen) holen wir Ihre
                            Zustimmung ein</li>
                        <li>Sie erhalten regelmäßige Updates über alle Vorgänge</li>
                        <li>Sie können jederzeit Einblick in alle Unterlagen nehmen</li>
                        <li>Wir sind Ihr verlängerter Arm, nicht Ihr Ersatz</li>
                    </ul>
                    <p>Unser Ziel ist es, Sie zu entlasten, nicht zu entmündigen. Sie bestimmen den Grad der
                        Delegation.</p>
                </div>
            </div>
        </div>

        <div class="faq-item">
            <button class="faq-question" onclick="toggleFAQ(this)">
                <span>Wie werden Reparaturen und Instandhaltungen gehandhabt?</span>
                <svg class="faq-icon" width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z" />
                </svg>
            </button>
            <div class="faq-answer">
                <div class="faq-answer-content">
                    <p>Wir koordinieren alle notwendigen Reparaturen professionell:</p>
                    <ul>
                        <li><strong>Schadensmeldung:</strong> Mieter melden Schäden direkt bei uns</li>
                        <li><strong>Bewertung:</strong> Wir prüfen Dringlichkeit und Umfang</li>
                        <li><strong>Handwerkerauswahl:</strong> Beauftragung zuverlässiger Partnerfirmen</li>
                        <li><strong>Terminkoordination:</strong> Organisation der Arbeiten mit Mieter und Handwerker
                        </li>
                        <li><strong>Qualitätskontrolle:</strong> Überprüfung der ausgeführten Arbeiten</li>
                        <li><strong>Abrechnung:</strong> Transparente Kostendokumentation</li>
                    </ul>
                    <p>Bei Kosten über einem vereinbarten Betrag (z.B. 500 Euro) holen wir vorher Ihre Zustimmung
                        ein. Kleinere Reparaturen werden direkt beauftragt, um schnelle Hilfe zu gewährleisten.</p>
                </div>
            </div>
        </div>

        <div class="faq-item">
            <button class="faq-question" onclick="toggleFAQ(this)">
                <span>Ist eine Mindestvertragslaufzeit erforderlich?</span>
                <svg class="faq-icon" width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z" />
                </svg>
            </button>
            <div class="faq-answer">
                <div class="faq-answer-content">
                    <p>Wir bieten flexible Vertragsmodelle:</p>
                    <ul>
                        <li><strong>Standardvertrag:</strong> 12 Monate Mindestlaufzeit, danach monatlich kündbar
                        </li>
                        <li><strong>Langzeitvertrag:</strong> 24 oder 36 Monate mit vergünstigten Konditionen</li>
                        <li><strong>Flexible Lösung:</strong> Individuelle Vereinbarungen nach Ihren Wünschen</li>
                    </ul>
                    <p>Die Kündigungsfrist beträgt in der Regel 3 Monate zum Quartalsende. Wir setzen auf
                        langfristige Partnerschaften, zwingen Sie aber nicht zu unflexiblen Verträgen.</p>
                </div>
            </div>
        </div>

        <!-- Kontakt-Hinweis -->
        <div class="info-box mt-40">
            <h3>💬 Ihre Frage ist nicht dabei?</h3>
            <p>Kein Problem! Wir beantworten gerne alle Ihre individuellen Fragen zur Mietverwaltung. Kontaktieren
                Sie uns einfach über unser Kontaktformular, per Telefon oder WhatsApp – wir sind für Sie da!</p>
            <div class="mt-20">
                <a href="<?= BASE ?>/#contact" class="cta-btn inline-block">Zur Kontaktseite</a>
            </div>
        </div>
    </div>
</section>

<!-- Contact CTA -->
<section class="contact-cta-section">
    <h2>Professionelle Mietverwaltung gewünscht?</h2>
    <p>Lassen Sie uns gemeinsam die beste Lösung für Ihre Immobilie finden</p>
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