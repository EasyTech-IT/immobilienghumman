<?php
$pageTitle       = 'Datenschutzerklärung | Immobilien Ghumman';
$pageDescription = 'Datenschutzerklärung von Immobilien Ghumman gemäß DSGVO. Informationen zur Datenverarbeitung, Cookies und Ihren Rechten.';
$pageKeywords    = 'Datenschutz, DSGVO, Immobilien Ghumman';
$activeNav       = '';
$extraHead       = '<style>
.legal-container { background:#fff; border-radius:var(--radius-lg); padding:3rem; box-shadow:0 4px 24px rgba(0,0,0,0.07); max-width:900px; margin:0 auto; }
.legal-header { border-bottom:2px solid var(--premium-offwhite); padding-bottom:1.5rem; margin-bottom:2rem; }
.legal-header h1 { font-size:2rem; color:var(--premium-dark); margin:0 0 .5rem; }
.legal-header .subtitle { color:var(--premium-text-muted); margin:0; }
.legal-header .last-updated { display:inline-block;background:var(--premium-offwhite);padding:.25rem .75rem;border-radius:20px;font-size:.8rem;color:var(--premium-text-muted);margin-top:.5rem; }
.legal-section { margin:2.5rem 0; }
.legal-section h2 { color:var(--premium-dark); font-size:1.3rem; border-left:4px solid var(--premium-accent); padding-left:1rem; margin-bottom:1rem; }
.legal-section h3 { color:var(--premium-dark); font-size:1.05rem; margin:1.25rem 0 .5rem; }
.legal-section p, .legal-section li { color:var(--premium-text-muted); line-height:1.75; }
.legal-section ul { padding-left:1.5rem; }
.toc { background:var(--premium-offwhite); border-radius:var(--radius-md); padding:1.5rem 2rem; margin:1.5rem 0; }
.toc h3 { margin:0 0 .75rem; color:var(--premium-dark); }
.toc ul { padding-left:1.25rem; margin:0; }
.toc li { margin:.3rem 0; }
.toc a { color:var(--premium-accent); text-decoration:none; font-size:.9rem; }
.toc a:hover { text-decoration:underline; }
.highlight-box { background:rgba(45,108,223,0.06); border:1px solid rgba(45,108,223,0.15); border-radius:var(--radius-md); padding:1.25rem 1.5rem; margin:1rem 0; }
.contact-card { background:var(--premium-navy); border-radius:var(--radius-md); padding:2rem; color:#fff; margin:1.5rem 0; }
.contact-card h3 { color:var(--premium-gold-lt); border-bottom:1px solid rgba(255,255,255,0.15); padding-bottom:.75rem; margin-bottom:1rem; }
.contact-card p { color:rgba(255,255,255,0.85); margin:.4rem 0; }
.contact-card a { color:var(--premium-gold-lt); }
.info-box { background:var(--premium-offwhite); border-left:4px solid var(--premium-accent); padding:1rem 1.5rem; border-radius:0 var(--radius-md) var(--radius-md) 0; margin:1rem 0; }
.rights-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(220px,1fr)); gap:1rem; margin:1rem 0; }
.right-item { background:var(--premium-offwhite); padding:1rem 1.25rem; border-radius:var(--radius-md); border:1px solid #e5e7eb; }
.right-item strong { color:var(--premium-accent); display:block; margin-bottom:.25rem; }
.right-item span { font-size:.85rem; color:var(--premium-text-muted); }
.cookie-settings-btn { display:inline-flex;align-items:center;gap:.5rem;background:var(--premium-accent);color:#fff;padding:.6rem 1.25rem;border-radius:var(--radius-md);font-weight:600;font-size:.9rem;border:none;cursor:pointer;text-decoration:none; }
.back-link { text-align:center; margin-top:3rem; padding-top:2rem; border-top:1px solid var(--premium-offwhite); }
.text-brand-link { color:var(--premium-accent); }
.cta-button { display:inline-flex;align-items:center;gap:.5rem;background:var(--premium-accent);color:#fff;padding:.75rem 1.5rem;border-radius:var(--radius-md);font-weight:600;text-decoration:none; }
@media(max-width:640px){.legal-container{padding:1.5rem;} .rights-grid{grid-template-columns:1fr;} }
</style>';
$extraScripts    = '';
require_once __DIR__ . '/includes/header.php';
?>


<section style="padding:5rem 0 var(--section-gap);background:var(--premium-offwhite)">
    <div class="container">
        <div class="legal-container">

            <!-- Header -->
            <div class="legal-header">
                <h1>Datenschutzerklärung</h1>
                <p class="subtitle">Informationen zum Umgang mit Ihren personenbezogenen Daten gemäß DSGVO</p>
                <span class="last-updated">Stand: Juli 2026</span>
            </div>

            <!-- Inhaltsverzeichnis -->
            <div class="toc">
                <h3>📋 Inhaltsverzeichnis</h3>
                <ul>
                    <li><a href="#verantwortlicher">1. Verantwortlicher</a></li>
                    <li><a href="#grundsaetze">2. Grundsätze der Datenverarbeitung</a></li>
                    <li><a href="#erhebung">3. Erhebung personenbezogener Daten</a></li>
                    <li><a href="#cookies">4. Cookies und Tracking</a></li>
                    <li><a href="#analytics">5. Google Analytics</a></li>
                    <li><a href="#kontaktformular">6. Kontaktformular</a></li>
                    <li><a href="#cloudinary">7. Cloudinary (Bildhosting)</a></li>
                    <li><a href="#ssl">8. SSL-Verschlüsselung</a></li>
                    <li><a href="#rechte">9. Ihre Rechte als Betroffener</a></li>
                    <li><a href="#speicherdauer">10. Speicherdauer</a></li>
                    <li><a href="#sicherheit">11. Datensicherheit</a></li>
                    <li><a href="#aenderungen">12. Änderungen</a></li>
                </ul>
            </div>

            <!-- 1. Verantwortlicher -->
            <section class="legal-section" id="verantwortlicher">
                <h2><span class="section-number">1</span> Verantwortlicher</h2>
                <p>Verantwortlicher im Sinne der Datenschutz-Grundverordnung (DSGVO) und anderer nationaler
                    Datenschutzgesetze sowie sonstiger datenschutzrechtlicher Bestimmungen ist:</p>

                <div class="contact-box">
                    <p><strong>Immobilien Ghumman</strong></p>
                    <p>Inhaber: Nasir Ghumman</p>
                    <p>Homburger Str. 100</p>
                    <p>61118 Bad Vilbel</p>
                    <p>Deutschland</p>
                    <p class="mt-15">
                        <strong>Telefon:</strong> +49 (0) 160 98787878<br>
                        <strong>E-Mail:</strong> <a href="mailto:info@immobilienghumman.de"
                            class="text-brand-link">info@immobilienghumman.de</a><br>
                        <strong>Website:</strong> <a href="https://www.immobilienghumman.de"
                            class="text-brand-link">www.immobilienghumman.de</a>
                    </p>
                </div>
            </section>

            <!-- 2. Grundsätze -->
            <section class="legal-section" id="grundsaetze">
                <h2><span class="section-number">2</span> Grundsätze der Datenverarbeitung</h2>
                <p>Der Schutz Ihrer persönlichen Daten ist uns ein wichtiges Anliegen. Wir verarbeiten Ihre
                    personenbezogenen Daten im Einklang mit den geltenden Datenschutzgesetzen, insbesondere der
                    EU-Datenschutz-Grundverordnung (DSGVO) und dem Bundesdatenschutzgesetz (BDSG).</p>

                <p>Wir halten uns an folgende Grundsätze:</p>
                <ul>
                    <li><strong>Rechtmäßigkeit:</strong> Wir verarbeiten Daten nur auf Grundlage einer gesetzlichen
                        Erlaubnis oder Ihrer Einwilligung.</li>
                    <li><strong>Zweckbindung:</strong> Daten werden nur für festgelegte, eindeutige und legitime
                        Zwecke erhoben.</li>
                    <li><strong>Datenminimierung:</strong> Wir erheben nur die Daten, die für den jeweiligen Zweck
                        erforderlich sind.</li>
                    <li><strong>Richtigkeit:</strong> Wir bemühen uns, Ihre Daten aktuell und korrekt zu halten.
                    </li>
                    <li><strong>Speicherbegrenzung:</strong> Daten werden nur so lange gespeichert, wie es für den
                        Zweck erforderlich ist.</li>
                    <li><strong>Integrität und Vertraulichkeit:</strong> Wir schützen Ihre Daten durch angemessene
                        technische und organisatorische Maßnahmen.</li>
                </ul>
            </section>

            <!-- 3. Erhebung -->
            <section class="legal-section" id="erhebung">
                <h2><span class="section-number">3</span> Erhebung und Speicherung personenbezogener Daten</h2>

                <h3>3.1 Beim Besuch unserer Website</h3>
                <p>Beim Aufrufen unserer Website werden durch den auf Ihrem Endgerät zum Einsatz kommenden Browser
                    automatisch Informationen an den Server unserer Website gesendet. Diese Informationen werden
                    temporär in einem sog. Logfile gespeichert:</p>
                <ul>
                    <li>IP-Adresse des anfragenden Rechners (anonymisiert)</li>
                    <li>Datum und Uhrzeit des Zugriffs</li>
                    <li>Name und URL der abgerufenen Datei</li>
                    <li>Website, von der aus der Zugriff erfolgt (Referrer-URL)</li>
                    <li>Verwendeter Browser und ggf. das Betriebssystem Ihres Rechners</li>
                    <li>Name Ihres Access-Providers</li>
                </ul>
                <p>Die genannten Daten werden durch uns zu folgenden Zwecken verarbeitet:</p>
                <ul>
                    <li>Gewährleistung eines reibungslosen Verbindungsaufbaus der Website</li>
                    <li>Gewährleistung einer komfortablen Nutzung unserer Website</li>
                    <li>Auswertung der Systemsicherheit und -stabilität</li>
                    <li>Zu weiteren administrativen Zwecken</li>
                </ul>
                <p><strong>Rechtsgrundlage:</strong> Art. 6 Abs. 1 lit. f DSGVO (berechtigtes Interesse)</p>

                <h3>3.2 Bei Nutzung unserer Dienste</h3>
                <p>Wenn Sie unsere Dienstleistungen in Anspruch nehmen möchten (z.B. Immobilienanfragen,
                    Beratungstermine, Hausverwaltungsanfragen), erheben wir folgende Daten:</p>
                <ul>
                    <li>Vor- und Nachname</li>
                    <li>E-Mail-Adresse</li>
                    <li>Telefonnummer</li>
                    <li>Anschrift (bei Bedarf)</li>
                    <li>Inhalt Ihrer Anfrage</li>
                    <li>Ggf. Informationen zu Ihrer Immobilie (bei Hausverwaltungsanfragen)</li>
                </ul>
                <p><strong>Rechtsgrundlage:</strong> Art. 6 Abs. 1 lit. b DSGVO (Vertragserfüllung) oder Art. 6 Abs.
                    1 lit. a DSGVO (Einwilligung)</p>
            </section>

            <!-- 4. Cookies -->
            <section class="legal-section" id="cookies">
                <h2><span class="section-number">4</span> Cookies und Tracking</h2>
                <p>Unsere Website verwendet Cookies. Cookies sind kleine Textdateien, die auf Ihrem Endgerät
                    gespeichert werden und die Ihr Browser speichert.</p>

                <h3>4.1 Technisch notwendige Cookies</h3>
                <p>Diese Cookies sind für den Betrieb der Website erforderlich und können nicht deaktiviert werden.
                    Sie speichern keine persönlich identifizierbaren Informationen.</p>
                <ul>
                    <li><strong>cookie_consent:</strong> Speichert Ihre Cookie-Präferenzen (1 Jahr)</li>
                    <li><strong>Session-Cookies:</strong> Werden nach Schließen des Browsers gelöscht</li>
                </ul>
                <p><strong>Rechtsgrundlage:</strong> § 25 Abs. 2 TTDSG (technisch notwendige Cookies) sowie Art. 6 Abs. 1 lit. f DSGVO (berechtigtes Interesse)</p>

                <h3>4.2 Analyse-Cookies (optional)</h3>
                <p>Diese Cookies helfen uns zu verstehen, wie Besucher mit unserer Website interagieren. Alle Daten
                    werden anonymisiert erfasst. Diese Cookies werden nur mit Ihrer ausdrücklichen Einwilligung
                    gesetzt.</p>
                <p><strong>Rechtsgrundlage:</strong> § 25 Abs. 1 TTDSG sowie Art. 6 Abs. 1 lit. a DSGVO (Einwilligung)</p>

                <h3>4.3 Cookie-Einstellungen ändern</h3>
                <p>Sie können Ihre Cookie-Einstellungen jederzeit ändern:</p>
                <button type="button" class="cookie-settings-btn"
                    onclick="if(typeof CookieConsent !== 'undefined') CookieConsent.reset(); else alert('Cookie-Einstellungen werden beim nächsten Seitenaufruf angezeigt.');">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                        <path
                            d="M19.14 12.94c.04-.31.06-.63.06-.94 0-.31-.02-.63-.06-.94l2.03-1.58c.18-.14.23-.41.12-.61l-1.92-3.32c-.12-.22-.37-.29-.59-.22l-2.39.96c-.5-.38-1.03-.7-1.62-.94l-.36-2.54c-.04-.24-.24-.41-.48-.41h-3.84c-.24 0-.43.17-.47.41l-.36 2.54c-.59.24-1.13.57-1.62.94l-2.39-.96c-.22-.08-.47 0-.59.22L2.74 8.87c-.12.21-.08.47.12.61l2.03 1.58c-.04.31-.06.63-.06.94s.02.63.06.94l-2.03 1.58c-.18.14-.23.41-.12.61l1.92 3.32c.12.22.37.29.59.22l2.39-.96c.5.38 1.03.7 1.62.94l.36 2.54c.05.24.24.41.48.41h3.84c.24 0 .44-.17.47-.41l.36-2.54c.59-.24 1.13-.56 1.62-.94l2.39.96c.22.08.47 0 .59-.22l1.92-3.32c.12-.22.07-.47-.12-.61l-2.01-1.58zM12 15.6c-1.98 0-3.6-1.62-3.6-3.6s1.62-3.6 3.6-3.6 3.6 1.62 3.6 3.6-1.62 3.6-3.6 3.6z" />
                    </svg>
                    Cookie-Einstellungen öffnen
                </button>
            </section>

            <!-- 5. Google Analytics -->
            <section class="legal-section" id="analytics">
                <h2><span class="section-number">5</span> Google Analytics (GA4)</h2>
                <p>Diese Website nutzt Google Analytics 4 (GA4), einen Webanalysedienst der Google Ireland Limited
                    („Google"), Gordon House, Barrow Street, Dublin 4, Irland.</p>

                <p>Google Analytics verwendet Cookies, die eine Analyse Ihrer Benutzung der Website ermöglichen. Die
                    durch das Cookie erzeugten Informationen werden in der Regel an einen Server von Google in den
                    USA übertragen und dort gespeichert.</p>

                <h3>IP-Anonymisierung</h3>
                <p>Wir haben auf dieser Website die Funktion IP-Anonymisierung aktiviert. Dadurch wird Ihre
                    IP-Adresse von Google innerhalb von Mitgliedstaaten der Europäischen Union oder in anderen
                    Vertragsstaaten des Abkommens über den Europäischen Wirtschaftsraum vor der Übermittlung in die
                    USA gekürzt.</p>

                <h3>Einwilligung erforderlich</h3>
                <p>Google Analytics wird nur aktiviert, wenn Sie dem über unser Cookie-Banner ausdrücklich
                    zustimmen. Ohne Ihre Einwilligung werden keine Analyse-Daten erhoben.</p>

                <h3>Widerspruch / Opt-Out</h3>
                <p>Sie können die Speicherung der Cookies durch eine entsprechende Einstellung Ihrer
                    Browser-Software verhindern. Sie können darüber hinaus die Erfassung der durch das Cookie
                    erzeugten Daten durch Änderung Ihrer Cookie-Einstellungen auf dieser Website verhindern.</p>

                <p><strong>Rechtsgrundlage:</strong> § 25 Abs. 1 TTDSG sowie Art. 6 Abs. 1 lit. a DSGVO (Einwilligung)</p>
                <p><strong>Mehr Informationen:</strong> <a href="https://policies.google.com/privacy"
                        target="_blank" rel="noopener noreferrer" class="text-brand-link">Google Datenschutzerklärung</a></p>
            </section>

            <!-- 6. Kontaktformular -->
            <section class="legal-section" id="kontaktformular">
                <h2><span class="section-number">6</span> Kontaktformular und E-Mail-Kontakt</h2>
                <p>Wenn Sie uns per Kontaktformular oder E-Mail kontaktieren, werden Ihre Angaben einschließlich der
                    von Ihnen dort angegebenen Kontaktdaten zwecks Bearbeitung der Anfrage und für den Fall von
                    Anschlussfragen bei uns gespeichert.</p>

                <p>Folgende Daten werden verarbeitet:</p>
                <ul>
                    <li>Name</li>
                    <li>E-Mail-Adresse</li>
                    <li>Telefonnummer (falls angegeben)</li>
                    <li>Nachrichteninhalt</li>
                    <li>Zeitpunkt der Anfrage</li>
                    <li>Bei Immobilienanfragen: Interesse an bestimmten Objekten</li>
                </ul>

                <p>Diese Daten geben wir nicht ohne Ihre Einwilligung weiter.</p>

                <p><strong>Rechtsgrundlage:</strong> Art. 6 Abs. 1 lit. b DSGVO (vorvertragliche Maßnahmen) und Art.
                    6 Abs. 1 lit. f DSGVO (berechtigtes Interesse an der Bearbeitung von Kundenanfragen)</p>

                <p><strong>Speicherdauer:</strong> Ihre Anfragen werden nach abschließender Bearbeitung gelöscht,
                    sofern keine gesetzlichen Aufbewahrungspflichten entgegenstehen.</p>

                <p><strong>Spam-Schutz (Cloudflare Turnstile):</strong> Zur Absicherung unserer Formulare
                    nutzen wir Cloudflare Turnstile. Dabei wird vor dem Versand geprüft, ob eine Anfrage
                    mit hoher Wahrscheinlichkeit von einem Menschen stammt. Die Prüfung erfolgt über den
                    Dienst Cloudflare.</p>
                <p><strong>Rechtsgrundlage:</strong> Art. 6 Abs. 1 lit. f DSGVO (berechtigtes Interesse an der
                    Sicherheit unserer Website und dem Schutz vor automatisierten Spam-Anfragen)</p>
                <p><strong>Hinweis:</strong> Dabei können technische Daten (z. B. IP-Adresse, Browserinformationen)
                    an Cloudflare übertragen werden. Weitere Informationen finden Sie in der
                    <a href="https://www.cloudflare.com/privacypolicy/" target="_blank" rel="noopener noreferrer" class="text-brand-link">Datenschutzerklärung von Cloudflare</a>.
                </p>
            </section>

            <!-- 7. Cloudinary -->
            <section class="legal-section" id="cloudinary">
                <h2><span class="section-number">7</span> Cloudinary (Bildhosting)</h2>
                <p>Wir nutzen den Dienst Cloudinary für das Hosting und die Bereitstellung von Bildern auf unserer
                    Website, insbesondere für Immobilienfotos und Unternehmensbilder.</p>

                <div class="contact-box">
                    <p><strong>Anbieter:</strong> Cloudinary Ltd.</p>
                    <p>111 W Evelyn Ave, Suite 206</p>
                    <p>Sunnyvale, CA 94086, USA</p>
                </div>

                <p>Beim Laden von Bildern wird eine Verbindung zu den Servern von Cloudinary hergestellt. Dabei wird
                    Ihre IP-Adresse übertragen. Cloudinary ist nach dem EU-US Data Privacy Framework zertifiziert,
                    was ein angemessenes Datenschutzniveau gewährleistet.</p>

                <p><strong>Rechtsgrundlage:</strong> Art. 6 Abs. 1 lit. f DSGVO (berechtigtes Interesse an einer
                    ansprechenden Darstellung unserer Website und schnellen Ladezeiten)</p>
                <p><strong>Mehr Informationen:</strong> <a href="https://cloudinary.com/privacy" target="_blank"
                        rel="noopener noreferrer" class="text-brand-link">Cloudinary Datenschutzerklärung</a></p>
            </section>

            <!-- 8. SSL -->
            <section class="legal-section" id="ssl">
                <h2><span class="section-number">8</span> SSL-/TLS-Verschlüsselung</h2>
                <p>Diese Seite nutzt aus Sicherheitsgründen und zum Schutz der Übertragung vertraulicher Inhalte,
                    wie zum Beispiel Anfragen, die Sie an uns als Seitenbetreiber senden, eine SSL-bzw.
                    TLS-Verschlüsselung.</p>

                <p>Eine verschlüsselte Verbindung erkennen Sie daran, dass die Adresszeile des Browsers von
                    „http://" auf „https://" wechselt und an dem Schloss-Symbol in Ihrer Browserzeile.</p>

                <p>Wenn die SSL- bzw. TLS-Verschlüsselung aktiviert ist, können die Daten, die Sie an uns
                    übermitteln, nicht von Dritten mitgelesen werden.</p>
            </section>

            <!-- 9. Ihre Rechte -->
            <section class="legal-section" id="rechte">
                <h2><span class="section-number">9</span> Ihre Rechte als Betroffener</h2>
                <p>Sie haben gegenüber uns folgende Rechte hinsichtlich der Sie betreffenden personenbezogenen
                    Daten:</p>

                <div class="rights-grid">
                    <div class="right-item">
                        <strong>Auskunftsrecht (Art. 15 DSGVO)</strong>
                        <span>Sie können Auskunft über Ihre von uns verarbeiteten Daten verlangen.</span>
                    </div>
                    <div class="right-item">
                        <strong>Berichtigungsrecht (Art. 16 DSGVO)</strong>
                        <span>Sie können die Berichtigung unrichtiger Daten verlangen.</span>
                    </div>
                    <div class="right-item">
                        <strong>Löschungsrecht (Art. 17 DSGVO)</strong>
                        <span>Sie können die Löschung Ihrer Daten verlangen („Recht auf Vergessenwerden").</span>
                    </div>
                    <div class="right-item">
                        <strong>Einschränkung (Art. 18 DSGVO)</strong>
                        <span>Sie können die Einschränkung der Verarbeitung verlangen.</span>
                    </div>
                    <div class="right-item">
                        <strong>Datenübertragbarkeit (Art. 20 DSGVO)</strong>
                        <span>Sie können Ihre Daten in einem gängigen, maschinenlesbaren Format erhalten.</span>
                    </div>
                    <div class="right-item">
                        <strong>Widerspruchsrecht (Art. 21 DSGVO)</strong>
                        <span>Sie können der Verarbeitung jederzeit widersprechen.</span>
                    </div>
                </div>

                <h3>Widerruf Ihrer Einwilligung</h3>
                <p>Soweit die Verarbeitung auf Ihrer Einwilligung beruht, haben Sie das Recht, diese jederzeit zu
                    widerrufen. Die Rechtmäßigkeit der aufgrund der Einwilligung bis zum Widerruf erfolgten
                    Verarbeitung wird dadurch nicht berührt. Zur Ausübung des Widerrufsrechts können Sie uns
                    jederzeit kontaktieren.</p>

                <h3>Beschwerderecht bei der Aufsichtsbehörde</h3>
                <p>Unbeschadet eines anderweitigen verwaltungsrechtlichen oder gerichtlichen Rechtsbehelfs steht
                    Ihnen das Recht auf Beschwerde bei einer Aufsichtsbehörde zu, insbesondere in dem Mitgliedstaat
                    Ihres Aufenthaltsorts, Ihres Arbeitsplatzes oder des Orts des mutmaßlichen Verstoßes.</p>
                <div class="contact-box">
                    <p><strong>Zuständige Aufsichtsbehörde:</strong></p>
                    <p>Der Hessische Beauftragte für Datenschutz und Informationsfreiheit</p>
                    <p>Postfach 3163, 65021 Wiesbaden</p>
                    <p>Telefon: +49 611 1408-0</p>
                    <p>E-Mail: poststelle@datenschutz.hessen.de</p>
                    <p>Website: <a href="https://datenschutz.hessen.de" target="_blank" rel="noopener noreferrer"
                            class="text-brand-link">datenschutz.hessen.de</a></p>
                </div>
            </section>

            <!-- 10. Speicherdauer -->
            <section class="legal-section" id="speicherdauer">
                <h2><span class="section-number">10</span> Speicherdauer</h2>
                <p>Wir speichern Ihre personenbezogenen Daten nur so lange, wie es für die Erfüllung der Zwecke
                    erforderlich ist, für die sie erhoben wurden, oder wie es gesetzlich vorgeschrieben ist.</p>

                <ul>
                    <li><strong>Kontaktanfragen:</strong> Bis zur vollständigen Bearbeitung, maximal 2 Jahre</li>
                    <li><strong>Vertragsdaten:</strong> 10 Jahre gemäß § 257 HGB (handelsrechtliche
                        Aufbewahrungspflicht)</li>
                    <li><strong>Rechnungsdaten:</strong> 10 Jahre gemäß § 147 AO (steuerrechtliche
                        Aufbewahrungspflicht)</li>
                    <li><strong>Server-Logfiles:</strong> 7 Tage</li>
                    <li><strong>Cookie-Einwilligung:</strong> 1 Jahr</li>
                    <li><strong>Bewerberdaten:</strong> 6 Monate nach Abschluss des Bewerbungsverfahrens (sofern
                        zutreffend)</li>
                </ul>

                <p>Nach Ablauf der jeweiligen Frist werden die entsprechenden Daten routinemäßig gelöscht.</p>
            </section>

            <!-- 11. Datensicherheit -->
            <section class="legal-section" id="sicherheit">
                <h2><span class="section-number">11</span> Datensicherheit</h2>
                <p>Wir verwenden innerhalb des Website-Besuchs das verbreitete SSL-Verfahren (Secure Socket Layer)
                    in Verbindung mit der jeweils höchsten Verschlüsselungsstufe, die von Ihrem Browser unterstützt
                    wird.</p>

                <p>Wir bedienen uns geeigneter technischer und organisatorischer Sicherheitsmaßnahmen, um Ihre Daten
                    gegen zufällige oder vorsätzliche Manipulationen, teilweisen oder vollständigen Verlust,
                    Zerstörung oder gegen den unbefugten Zugriff Dritter zu schützen. Unsere Sicherheitsmaßnahmen
                    werden entsprechend der technologischen Entwicklung fortlaufend verbessert.</p>

                <p>Zu unseren Sicherheitsmaßnahmen gehören:</p>
                <ul>
                    <li>Verschlüsselung Ihrer Daten bei der Übertragung (SSL/TLS)</li>
                    <li>Sichere Server-Infrastruktur</li>
                    <li>Regelmäßige Sicherheitsupdates</li>
                    <li>Zugriffsbeschränkungen auf personenbezogene Daten</li>
                    <li>Regelmäßige Überprüfung unserer Sicherheitsmaßnahmen</li>
                </ul>

                <div class="highlight-box">
                    <p><strong>🔒 Hinweis:</strong> Die Datenübertragung im Internet kann Sicherheitslücken
                        aufweisen. Ein lückenloser Schutz der Daten vor dem Zugriff durch Dritte ist nicht möglich.
                        Wir empfehlen Ihnen, vertrauliche Informationen nicht unverschlüsselt per E-Mail zu
                        versenden.</p>
                </div>
            </section>

            <!-- 12. Änderungen -->
            <section class="legal-section" id="aenderungen">
                <h2><span class="section-number">12</span> Änderungen dieser Datenschutzerklärung</h2>
                <p>Wir behalten uns vor, diese Datenschutzerklärung anzupassen, damit sie stets den aktuellen
                    rechtlichen Anforderungen entspricht oder um Änderungen unserer Leistungen in der
                    Datenschutzerklärung umzusetzen, z.B. bei der Einführung neuer Services.</p>

                <p>Für Ihren erneuten Besuch gilt dann die neue Datenschutzerklärung. Wir empfehlen Ihnen, diese
                    Datenschutzerklärung regelmäßig zu lesen, um über den Schutz Ihrer Daten informiert zu bleiben.
                </p>

                <p>Bei wesentlichen Änderungen, die Ihre Rechte oder die Art der Datenverarbeitung betreffen, werden
                    wir Sie nach Möglichkeit gesondert informieren.</p>
            </section>

            <!-- Zurück zur Startseite -->
            <div class="back-link">
                <p class="text-muted-note">Bei Fragen zum Datenschutz kontaktieren Sie uns
                    gerne unter <a href="mailto:info@immobilienghumman.de"
                        class="text-brand-link">info@immobilienghumman.de</a></p>
                <a href="<?= BASE ?>/" class="cta-button">← Zurück zur Startseite</a>
            </div>

        </div>
    </div>
</section>

<!-- Footer -->

<?php require_once __DIR__ . '/includes/footer.php'; ?>