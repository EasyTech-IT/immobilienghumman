<?php
require_once __DIR__ . '/includes/config.php';
header('Location: ' . BASE . '/hausverwaltung#leistung-eigentuemerversammlung', true, 301);
exit;

$pageTitle       = 'Eigentümerversammlung – Immobilien Ghumman';
$pageDescription = 'Professionelle Organisation und Durchführung von Eigentümerversammlungen.';
$pageKeywords    = 'Eigentümerversammlung, WEG-Verwaltung, Bad Vilbel';
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
            background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
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
            background: linear-gradient(135deg, #fadbd8 0%, #f8b4b0 100%);
            padding: 40px;
            border-radius: 16px;
            margin-bottom: 50px;
            border-left: 5px solid #e74c3c;
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
            border-bottom: 3px solid #e74c3c;
        }

        .content-block h3 {
            color: #c0392b;
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
            color: #e74c3c;
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
            background: #fadbd8;
            border-left: 4px solid #e74c3c;
            padding: 25px;
            border-radius: 8px;
            margin: 30px 0;
        }

        .info-box h3 {
            color: #c0392b;
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
            color: #e74c3c;
            transform: translateX(5px);
        }

        .cta-widget {
            background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
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
            color: #e74c3c;
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

        .timeline {
            position: relative;
            padding-left: 40px;
            margin: 30px 0;
        }

        .timeline::before {
            content: "";
            position: absolute;
            left: 10px;
            top: 0;
            bottom: 0;
            width: 3px;
            background: #e74c3c;
        }

        .timeline-item {
            position: relative;
            margin-bottom: 30px;
        }

        .timeline-item::before {
            content: "";
            position: absolute;
            left: -35px;
            top: 5px;
            width: 15px;
            height: 15px;
            border-radius: 50%;
            background: #e74c3c;
            border: 3px solid white;
            box-shadow: 0 0 0 3px #e74c3c;
        }

        .timeline-item h4 {
            color: #e74c3c;
            margin-bottom: 10px;
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
            color: #e74c3c;
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
            <span>Eigentueemerversammlung</span>
        </nav>
        <div class="page-hero-badge"><i class="fas fa-users"></i> WEG-Verwaltung nach aktuellem Recht</div>
        <h1>Eigentuemer<span class="accent">versammlungen</span></h1>
        <p>Professionelle Organisation und Durchfuehrung rechtssicherer Eigentuememerversammlungen &ndash; von der Einladung bis zum rechtskraeftigen Protokoll.</p>
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
                <div><span class="hero-stat-number">100%</span><span class="hero-stat-label">Rechtssichere Protokolle</span></div>
            </div>
            <div class="hero-stat"><i class="fas fa-gavel"></i>
                <div><span class="hero-stat-number">WEG 2020</span><span class="hero-stat-label">Reform umgesetzt</span></div>
            </div>
            <div class="hero-stat"><i class="fas fa-laptop"></i>
                <div><span class="hero-stat-number">Digital</span><span class="hero-stat-label">Online-Versammlungen</span></div>
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
                <h2>Was ist eine Eigentümerversammlung?</h2>
                <p>Die Eigentümerversammlung ist das oberste beschlussfassende Organ einer
                    Wohnungseigentümergemeinschaft (WEG). Hier treffen alle Wohnungseigentümer wichtige
                    Entscheidungen über die Verwaltung und Instandhaltung des Gemeinschaftseigentums. Als
                    professionelle Hausverwaltung organisieren und leiten wir Ihre Eigentümerversammlungen
                    rechtssicher und effizient – von der Einladung bis zum rechtskräftigen Protokoll.</p>
            </div>

            <!-- Rechtliche Grundlagen -->
            <div class="content-block">
                <h2>Rechtliche Grundlagen nach WEG</h2>

                <h3>Gesetzliche Vorgaben</h3>
                <p>Das Wohnungseigentumsgesetz (WEG) regelt alle Aspekte der Eigentümerversammlung:</p>
                <ul>
                    <li><strong>§ 23 WEG:</strong> Einberufung und Durchführung der Versammlung</li>
                    <li><strong>§ 24 WEG:</strong> Beschlussfassung und Stimmrecht</li>
                    <li><strong>§ 25 WEG:</strong> Anfechtung von Beschlüssen</li>
                    <li><strong>§ 28 WEG:</strong> Protokollierung der Versammlung</li>
                </ul>

                <h3>Häufigkeit der Versammlungen</h3>
                <p>Eigentümerversammlungen müssen regelmäßig stattfinden:</p>
                <ul>
                    <li><strong>Ordentliche Versammlung:</strong> Mindestens einmal jährlich (meist im Frühjahr)
                    </li>
                    <li><strong>Außerordentliche Versammlung:</strong> Bei Bedarf oder auf Antrag (mind. 30% der
                        Miteigentumsanteile)</li>
                    <li><strong>Eilfälle:</strong> Bei dringenden Angelegenheiten verkürzte Einladungsfrist möglich
                    </li>
                </ul>

                <h3>Einladungsfrist</h3>
                <p>Die ordnungsgemäße Einladung ist Voraussetzung für gültige Beschlüsse:</p>
                <ul>
                    <li><strong>Regelfall:</strong> Mindestens 2 Wochen vor der Versammlung (14 Tage)</li>
                    <li><strong>Eilfälle:</strong> 1 Woche mit Begründung der Dringlichkeit</li>
                    <li><strong>Teilungserklärung:</strong> Kann längere Fristen vorsehen</li>
                </ul>
            </div>

            <div class="highlight-box">
                <h3>⚠️ Wichtig zu wissen</h3>
                <p>Fehlerhafte Einladungen oder Verfahrensmängel können zur Unwirksamkeit aller gefassten Beschlüsse
                    führen! Eine professionelle Vorbereitung und Durchführung ist daher essentiell.</p>
            </div>

            <!-- Ablauf der Eigentümerversammlung -->
            <div class="content-block">
                <h2>Ablauf einer Eigentümerversammlung</h2>

                <div class="timeline">
                    <div class="timeline-item">
                        <h4>Phase 1: Vorbereitung (6-8 Wochen vorher)</h4>
                        <ul>
                            <li>Terminabstimmung und Raumreservierung</li>
                            <li>Erstellung der Tagesordnung mit allen Eigentümern</li>
                            <li>Vorbereitung notwendiger Unterlagen (Jahresabrechnung, Wirtschaftsplan)</li>
                            <li>Einholung von Angeboten bei geplanten Maßnahmen</li>
                        </ul>
                    </div>

                    <div class="timeline-item">
                        <h4>Phase 2: Einladung (2-3 Wochen vorher)</h4>
                        <ul>
                            <li>Schriftliche Einladung an alle Eigentümer (per Post oder E-Mail)</li>
                            <li>Bekanntgabe von Ort, Datum und Uhrzeit</li>
                            <li>Vollständige Tagesordnung mit allen Beschlussentwürfen</li>
                            <li>Beifügung relevanter Unterlagen (Abrechnungen, Gutachten)</li>
                        </ul>
                    </div>

                    <div class="timeline-item">
                        <h4>Phase 3: Durchführung (Versammlungstag)</h4>
                        <ul>
                            <li>Begrüßung und Feststellung der Beschlussfähigkeit</li>
                            <li>Wahl des Versammlungsleiters und Protokollführers</li>
                            <li>Abarbeitung der Tagesordnung Punkt für Punkt</li>
                            <li>Diskussion und Abstimmung über jeden Beschluss</li>
                            <li>Dokumentation aller Beschlüsse im Protokoll</li>
                        </ul>
                    </div>

                    <div class="timeline-item">
                        <h4>Phase 4: Nachbereitung (1-2 Wochen danach)</h4>
                        <ul>
                            <li>Erstellung des vollständigen Protokolls</li>
                            <li>Versand des Protokolls an alle Eigentümer</li>
                            <li>Umsetzung der beschlossenen Maßnahmen</li>
                            <li>Fristenkontrolle für Beschlussanfechtungen</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Tagesordnung -->
            <div class="content-block">
                <h2>Typische Tagesordnungspunkte</h2>

                <h3>Standardpunkte der jährlichen Versammlung</h3>
                <ol>
                    <li><strong>Begrüßung und Feststellung der Beschlussfähigkeit</strong></li>
                    <li><strong>Wahl des Versammlungsleiters und Protokollführers</strong></li>
                    <li><strong>Genehmigung des letzten Protokolls</strong></li>
                    <li><strong>Bericht des Verwalters über das abgelaufene Jahr</strong></li>
                    <li><strong>Beschlussfassung über die Jahresabrechnung</strong>
                        <ul>
                            <li>Entlastung des Verwalters</li>
                            <li>Genehmigung der Abrechnung</li>
                        </ul>
                    </li>
                    <li><strong>Beschlussfassung über den Wirtschaftsplan</strong>
                        <ul>
                            <li>Budget für das kommende Jahr</li>
                            <li>Höhe der monatlichen Hausgelder</li>
                        </ul>
                    </li>
                    <li><strong>Instandhaltungs- und Modernisierungsmaßnahmen</strong>
                        <ul>
                            <li>Dachsanierung, Fassadenrenovierung etc.</li>
                            <li>Auswahl von Handwerkern</li>
                        </ul>
                    </li>
                    <li><strong>Wahl/Bestellung des Verwalters</strong> (alle 3-5 Jahre)</li>
                    <li><strong>Wahl des Verwaltungsbeirats</strong> (falls vorhanden)</li>
                    <li><strong>Verschiedenes / Anträge der Eigentümer</strong></li>
                </ol>

                <h3>Zusätzliche mögliche Punkte</h3>
                <ul>
                    <li>Änderung der Hausordnung</li>
                    <li>Genehmigung von Sondernutzungsrechten</li>
                    <li>Beschlüsse zu Um- oder Anbauten</li>
                    <li>Installation von E-Ladesäulen oder Photovoltaik</li>
                    <li>Änderung der Gemeinschaftsordnung</li>
                </ul>
            </div>

            <!-- Beschlussfähigkeit und Abstimmung -->
            <div class="content-block">
                <h2>Beschlussfähigkeit und Abstimmungen</h2>

                <h3>Beschlussfähigkeit</h3>
                <p>Die Eigentümerversammlung ist grundsätzlich ohne Mindestanzahl beschlussfähig:</p>
                <ul>
                    <li><strong>Erste Versammlung:</strong> Beschlussfähig unabhängig von Teilnehmerzahl (wenn
                        ordnungsgemäß geladen)</li>
                    <li><strong>Zweite Versammlung:</strong> Bei Teilungserklärung können Quoren vorgesehen sein
                    </li>
                    <li><strong>Vertretung:</strong> Eigentümer können sich durch Vollmacht vertreten lassen</li>
                </ul>

                <h3>Stimmrecht</h3>
                <p>Das Stimmrecht richtet sich nach dem Miteigentumsanteil:</p>
                <ul>
                    <li>Ein Wohnungseigentümer = sein Miteigentumsanteil in Tausendsteln</li>
                    <li>Mehrere Wohnungen = Addition der Anteile</li>
                    <li>Miteigentum = Gemeinsame Ausübung des Stimmrechts</li>
                </ul>

                <h3>Mehrheiten für Beschlüsse</h3>
                <p>Je nach Beschlussgegenstand gelten unterschiedliche Mehrheitserfordernisse:</p>
                <ul>
                    <li><strong>Einfache Mehrheit:</strong> > 50% der abgegebenen Stimmen
                        <ul>
                            <li>Genehmigung der Jahresabrechnung</li>
                            <li>Wirtschaftsplan</li>
                            <li>Einfache Instandhaltungsmaßnahmen</li>
                        </ul>
                    </li>
                    <li><strong>Doppelt qualifizierte Mehrheit:</strong> > 66% der Anteile UND > 50% der Köpfe
                        <ul>
                            <li>Bauliche Veränderungen</li>
                            <li>Modernisierungen</li>
                        </ul>
                    </li>
                    <li><strong>Einstimmigkeit:</strong> 100% Zustimmung
                        <ul>
                            <li>Änderung der Teilungserklärung</li>
                            <li>Wesentliche Veränderungen am Gemeinschaftseigentum</li>
                        </ul>
                    </li>
                </ul>
            </div>

            <div class="info-box">
                <h3>ℹ️ Gut zu wissen</h3>
                <p>Seit der WEG-Reform 2020 sind viele Modernisierungsmaßnahmen (z.B. Barrierefreiheit, E-Mobilität,
                    Einbruchschutz) mit einfacher Mehrheit möglich, sofern kein unverhältnismäßiger Aufwand
                    entsteht.</p>
            </div>

            <!-- Protokoll -->
            <div class="content-block">
                <h2>Das Versammlungsprotokoll</h2>

                <h3>Pflichtinhalte des Protokolls</h3>
                <p>Ein rechtssicheres Protokoll muss folgende Angaben enthalten:</p>
                <ul>
                    <li><strong>Ort, Datum und Uhrzeit</strong> der Versammlung</li>
                    <li><strong>Teilnehmerliste</strong> mit allen anwesenden und vertretenen Eigentümern</li>
                    <li><strong>Feststellung der Beschlussfähigkeit</strong></li>
                    <li><strong>Name des Versammlungsleiters und Protokollführers</strong></li>
                    <li><strong>Vollständige Tagesordnung</strong></li>
                    <li><strong>Alle gefassten Beschlüsse</strong> im Wortlaut</li>
                    <li><strong>Abstimmungsergebnisse</strong> (Ja/Nein/Enthaltung mit Angabe der Anteile)</li>
                    <li><strong>Widersprüche</strong> einzelner Eigentümer zu Protokoll</li>
                    <li><strong>Unterschriften</strong> von Versammlungsleiter und Protokollführer</li>
                </ul>

                <h3>Versand und Fristen</h3>
                <ul>
                    <li>Protokoll muss innerhalb von 2 Wochen erstellt werden</li>
                    <li>Versand an alle Eigentümer (auch nicht anwesende)</li>
                    <li>Beschlüsse sind sofort wirksam (außer bei rechtzeitiger Anfechtung)</li>
                    <li>Anfechtungsfrist: 1 Monat ab Protokollzugang</li>
                </ul>

                <h3>Aufbewahrungspflicht</h3>
                <ul>
                    <li>Protokolle müssen dauerhaft aufbewahrt werden</li>
                    <li>Jeder Eigentümer hat Einsichtsrecht</li>
                    <li>Bei Verkauf: Protokolle der letzten 3 Jahre für Käufer relevant</li>
                </ul>
            </div>

            <!-- Unser Service -->
            <div class="content-block">
                <h2>Unser Service für Eigentümerversammlungen</h2>

                <h3>Komplette Organisation</h3>
                <ul>
                    <li><strong>Terminplanung:</strong> Abstimmung eines passenden Termins mit allen Eigentümern
                    </li>
                    <li><strong>Raumorganisation:</strong> Bereitstellung geeigneter Räumlichkeiten</li>
                    <li><strong>Einladungsmanagement:</strong> Fristgerechte, rechtssichere Einladungen</li>
                    <li><strong>Unterlagenvorbereitung:</strong> Alle relevanten Dokumente professionell aufbereitet
                    </li>
                    <li><strong>Angebotseinholung:</strong> Bei geplanten Maßnahmen mehrere Vergleichsangebote</li>
                </ul>

                <h3>Professionelle Durchführung</h3>
                <ul>
                    <li><strong>Versammlungsleitung:</strong> Strukturierte und faire Gesprächsführung</li>
                    <li><strong>Protokollführung:</strong> Lückenlose Dokumentation aller Beschlüsse</li>
                    <li><strong>Rechtliche Beratung:</strong> Klärung rechtlicher Fragen während der Versammlung
                    </li>
                    <li><strong>Konfliktmanagement:</strong> Mediation bei Meinungsverschiedenheiten</li>
                    <li><strong>Zeitmanagement:</strong> Effiziente Abarbeitung aller Tagesordnungspunkte</li>
                </ul>

                <h3>Nachbearbeitung</h3>
                <ul>
                    <li><strong>Protokollerstellung:</strong> Rechtssicheres Protokoll innerhalb von 2 Wochen</li>
                    <li><strong>Protokollversand:</strong> An alle Eigentümer (auch nicht anwesende)</li>
                    <li><strong>Beschlussumsetzung:</strong> Sofortige Umsetzung gefasster Beschlüsse</li>
                    <li><strong>Fristenkontrolle:</strong> Überwachung von Anfechtungsfristen</li>
                </ul>
            </div>

            <!-- Vorteile -->
            <div class="content-block">
                <h2>Vorteile unserer professionellen Versammlungsleitung</h2>
                <ol>
                    <li><strong>Rechtssicherheit:</strong> Alle Beschlüsse sind anfechtungssicher</li>
                    <li><strong>Effizienz:</strong> Strukturierte Durchführung spart Zeit</li>
                    <li><strong>Neutralität:</strong> Unparteiische Moderation bei Konflikten</li>
                    <li><strong>Expertise:</strong> Jahrelange Erfahrung mit WEG-Recht</li>
                    <li><strong>Vollständigkeit:</strong> Alle Pflichtpunkte werden behandelt</li>
                    <li><strong>Dokumentation:</strong> Lückenlose Protokollierung</li>
                    <li><strong>Zeitersparnis:</strong> Sie müssen sich um nichts kümmern</li>
                    <li><strong>Konfliktprävention:</strong> Professionelles Konfliktmanagement</li>
                </ol>
            </div>
        </div>

        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-widget">
                <h3>Schnellnavigation</h3>
                <ul class="quick-links">
                    <li><a href="#grundlagen">→ Rechtliche Grundlagen</a></li>
                    <li><a href="#ablauf">→ Ablauf</a></li>
                    <li><a href="#tagesordnung">→ Tagesordnung</a></li>
                    <li><a href="#beschluss">→ Beschlussfassung</a></li>
                    <li><a href="#faq">→ Häufige Fragen</a></li>
                </ul>
            </div>

            <div class="sidebar-widget">
                <h3>Weitere Themen</h3>
                <ul class="quick-links">
                    <li><a href="<?= BASE ?>/hausverwaltung-mietverwaltung">→ Mietverwaltung</a></li>
                    <li><a href="<?= BASE ?>/hausverwaltung-nebenkostenabrechnung">→ Nebenkostenabrechnung</a></li>
                    <li><a href="<?= BASE ?>/hausverwaltung-instandhaltung">→ Instandhaltung</a></li>
                    <li><a href="<?= BASE ?>/hausverwaltung-rechtliches">→ Rechtliche Fragen</a></li>
                </ul>
            </div>

            <div class="sidebar-widget cta-widget">
                <h3>Beratung gewünscht?</h3>
                <p>Wir unterstützen Sie bei Ihrer Eigentümerversammlung!</p>
                <a href="<?= BASE ?>/#contact" class="cta-btn">Jetzt Kontakt aufnehmen</a>
            </div>
        </aside>
    </div>
</section>

<!-- FAQ Section -->
<section class="faq-section" id="faq">
    <div class="faq-container">
        <div class="faq-header">
            <h2>Häufig gestellte Fragen zu Eigentümerversammlungen</h2>
            <p>Hier finden Sie Antworten auf die wichtigsten Fragen</p>
        </div>

        <div class="faq-item">
            <button class="faq-question" onclick="toggleFAQ(this)">
                <span>Wie oft muss eine Eigentümerversammlung stattfinden?</span>
                <svg class="faq-icon" width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z" />
                </svg>
            </button>
            <div class="faq-answer">
                <div class="faq-answer-content">
                    <p>Mindestens einmal jährlich muss eine ordentliche Eigentümerversammlung stattfinden. Diese
                        dient vor allem der:</p>
                    <ul>
                        <li>Beschlussfassung über die Jahresabrechnung</li>
                        <li>Genehmigung des Wirtschaftsplans für das kommende Jahr</li>
                        <li>Entlastung des Verwalters</li>
                    </ul>
                    <p>Zusätzlich können außerordentliche Versammlungen einberufen werden, wenn wichtige
                        Entscheidungen anstehen oder mindestens 30% der Miteigentumsanteile dies beantragen.</p>
                </div>
            </div>
        </div>

        <div class="faq-item">
            <button class="faq-question" onclick="toggleFAQ(this)">
                <span>Was passiert, wenn ich nicht teilnehmen kann?</span>
                <svg class="faq-icon" width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z" />
                </svg>
            </button>
            <div class="faq-answer">
                <div class="faq-answer-content">
                    <p>Wenn Sie nicht persönlich teilnehmen können, haben Sie mehrere Möglichkeiten:</p>
                    <ul>
                        <li><strong>Vollmacht:</strong> Sie können eine andere Person (z.B. Ehepartner, Nachbar)
                            bevollmächtigen, Sie zu vertreten</li>
                        <li><strong>Verwaltervollmacht:</strong> In manchen Fällen kann auch der Verwalter
                            bevollmächtigt werden</li>
                        <li><strong>Ohne Vertretung:</strong> Die Versammlung findet auch ohne Sie statt, Sie haben
                            aber kein Stimmrecht</li>
                    </ul>
                    <p>Wichtig: Gefasste Beschlüsse gelten auch für Sie, auch wenn Sie nicht anwesend waren! Sie
                        haben aber ein Anfechtungsrecht innerhalb eines Monats nach Protokollzugang.</p>
                </div>
            </div>
        </div>

        <div class="faq-item">
            <button class="faq-question" onclick="toggleFAQ(this)">
                <span>Kann ich Beschlüsse anfechten?</span>
                <svg class="faq-icon" width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z" />
                </svg>
            </button>
            <div class="faq-answer">
                <div class="faq-answer-content">
                    <p>Ja, Beschlüsse können angefochten werden, wenn:</p>
                    <ul>
                        <li>Sie gegen geltendes Recht verstoßen (z.B. Baurecht, WEG)</li>
                        <li>Formfehler bei der Einladung oder Durchführung vorliegen</li>
                        <li>Die erforderliche Mehrheit nicht erreicht wurde</li>
                        <li>Einzelne Eigentümer unbillig benachteiligt werden</li>
                    </ul>
                    <p><strong>Anfechtungsfrist:</strong> 1 Monat ab Zugang des Protokolls. Die Anfechtung muss
                        schriftlich beim Verwalter oder per Klage beim Amtsgericht erfolgen.</p>
                    <p>Wichtig: Während der Anfechtungsfrist können Beschlüsse noch nicht umgesetzt werden, es sei
                        denn, es besteht Eilbedürftigkeit.</p>
                </div>
            </div>
        </div>

        <div class="faq-item">
            <button class="faq-question" onclick="toggleFAQ(this)">
                <span>Wer darf an der Eigentümerversammlung teilnehmen?</span>
                <svg class="faq-icon" width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z" />
                </svg>
            </button>
            <div class="faq-answer">
                <div class="faq-answer-content">
                    <p>Teilnahmeberechtigt sind:</p>
                    <ul>
                        <li><strong>Alle Wohnungseigentümer:</strong> Mit Stimmrecht entsprechend ihrem
                            Miteigentumsanteil</li>
                        <li><strong>Bevollmächtigte Vertreter:</strong> Personen mit schriftlicher Vollmacht eines
                            Eigentümers</li>
                        <li><strong>Der Verwalter:</strong> Pflicht zur Teilnahme, hat aber kein Stimmrecht (außer
                            bei Vollmacht)</li>
                        <li><strong>Beiratsmitglieder:</strong> Haben Rederecht, aber nur Stimmrecht, wenn sie
                            selbst Eigentümer sind</li>
                    </ul>
                    <p>Nicht teilnahmeberechtigt sind:</p>
                    <ul>
                        <li>Mieter (außer bei speziellen Themen auf Einladung)</li>
                        <li>Kaufinteressenten (außer mit Zustimmung aller Eigentümer)</li>
                        <li>Externe Berater (nur auf Beschluss oder Einladung)</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="faq-item">
            <button class="faq-question" onclick="toggleFAQ(this)">
                <span>Was kostet die Organisation einer Eigentümerversammlung?</span>
                <svg class="faq-icon" width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z" />
                </svg>
            </button>
            <div class="faq-answer">
                <div class="faq-answer-content">
                    <p>Die Kosten sind in der Regel in der Hausverwaltungsgebühr enthalten. Bei uns umfasst der
                        Service:</p>
                    <ul>
                        <li>Komplette Organisation und Vorbereitung</li>
                        <li>Einladung und Unterlagenerstellung</li>
                        <li>Versammlungsleitung und Protokollführung</li>
                        <li>Protokollerstellung und -versand</li>
                    </ul>
                    <p><strong>Zusatzkosten</strong> können entstehen für:</p>
                    <ul>
                        <li>Raummiete (falls nicht in der WEG vorhanden): ca. 50-150€</li>
                        <li>Außerordentliche Versammlungen: Eventuell separate Abrechnung</li>
                        <li>Externe Gutachter oder Berater: Nach Aufwand</li>
                    </ul>
                    <p>Im Durchschnitt liegen die Gesamtkosten bei ca. 200-400€ pro Versammlung, die auf alle
                        Eigentümer nach Miteigentumsanteil umgelegt werden.</p>
                </div>
            </div>
        </div>

        <div class="faq-item">
            <button class="faq-question" onclick="toggleFAQ(this)">
                <span>Können Beschlüsse auch im Umlaufverfahren gefasst werden?</span>
                <svg class="faq-icon" width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z" />
                </svg>
            </button>
            <div class="faq-answer">
                <div class="faq-answer-content">
                    <p>Ja, seit der WEG-Reform 2020 sind Beschlüsse im Umlaufverfahren (Textform) möglich.
                        Voraussetzungen:</p>
                    <ul>
                        <li>Kein Eigentümer widerspricht innerhalb von 2 Wochen</li>
                        <li>Alle Eigentümer müssen ordnungsgemäß informiert werden</li>
                        <li>Der Beschlussvorschlag muss klar formuliert sein</li>
                        <li>Bei Widerspruch muss eine reguläre Versammlung einberufen werden</li>
                    </ul>
                    <p><strong>Geeignet für:</strong></p>
                    <ul>
                        <li>Unproblematische Entscheidungen</li>
                        <li>Dringende Angelegenheiten zwischen regulären Versammlungen</li>
                        <li>Kleinere Reparaturen oder Beauftragungen</li>
                    </ul>
                    <p><strong>Nicht geeignet für:</strong></p>
                    <ul>
                        <li>Komplexe oder strittige Themen</li>
                        <li>Beschlüsse, die ausführliche Diskussion erfordern</li>
                        <li>Jahresabrechnung und Wirtschaftsplan</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Kontakt-Hinweis -->
        <div class="info-box mt-40">
            <h3>💬 Weitere Fragen zu Eigentümerversammlungen?</h3>
            <p>Wir beraten Sie gerne zu allen rechtlichen und organisatorischen Fragen rund um
                Eigentümerversammlungen. Kontaktieren Sie uns für ein persönliches Gespräch!</p>
            <div class="mt-20">
                <a href="<?= BASE ?>/#contact" class="cta-btn inline-block">Jetzt Kontakt
                    aufnehmen</a>
            </div>
        </div>
    </div>
</section>

<!-- Contact CTA -->
<section class="contact-cta-section">
    <h2>Professionelle Eigentümerversammlungen gewünscht?</h2>
    <p>Lassen Sie uns die Organisation übernehmen – rechtssicher und effizient</p>
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