<?php
$pageTitle       = 'Immobilien als Kapitalanlage - Renditestarke Investments | Immobilien Ghumman';
$pageDescription = 'Renditestarke Immobilien als Kapitalanlage im Rhein-Main-Gebiet. Renditeanalyse, Standortbewertung und persönliche Beratung für Kapitalanleger.';
$pageKeywords    = 'Kapitalanlage Immobilien Frankfurt, Rendite Immobilien Rhein-Main, Eigentumswohnung als Kapitalanlage, Immobilien investieren Bad Vilbel, Renditeobjekte kaufen, Immobilienportfolio aufbauen';
$activeNav       = 'kapitalanlage';
require_once __DIR__ . '/includes/header.php';
?>

<!-- Page Hero -->
<section class="page-hero" style="
    padding: 7rem 1.5rem 5rem;
    background:
        linear-gradient(160deg, rgba(10,22,45,0.90) 0%, rgba(26,64,110,0.78) 55%, rgba(48,112,160,0.55) 100%),
        url('https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?ixlib=rb-4.0.3&auto=format&fit=crop&w=2070&q=80') center/cover no-repeat;
">
    <div class="page-hero-inner" style="max-width:820px">
        <nav class="page-hero-breadcrumb" aria-label="Breadcrumb">
            <a href="<?= BASE ?>/">Startseite</a>
            <span>/</span>
            <span>Kapitalanlage</span>
        </nav>
        <div class="page-hero-badge">
            <i class="fas fa-chart-line"></i>&nbsp; Renditestarke Investments im Rhein-Main-Gebiet
        </div>
        <h1 style="font-size:clamp(2rem,4.5vw,3.2rem);margin-bottom:1.1rem">
            Immobilien als <span class="accent">Kapitalanlage</span>
        </h1>
        <p style="font-size:1.08rem;max-width:640px;margin:0 auto 2rem">
            Aufbau eines soliden Immobilienportfolios im Rhein-Main-Gebiet – mit professioneller
            Renditeanalyse, Standortbewertung und persönlicher Beratung.
        </p>
        <div class="page-hero-btns">
            <a href="#beratung" class="btn-hero-primary">
                <i class="fas fa-calendar-check"></i> Kostenlose Beratung
            </a>
            <a href="<?= BASE ?>/immobilien-angebote?offer_type=Kauf" class="btn-hero-secondary">
                <i class="fas fa-home"></i> Angebote ansehen
            </a>
        </div>
        <!-- Mini-Kennzahlen -->
        <div style="display:flex;flex-wrap:wrap;justify-content:center;gap:2.5rem;margin-top:3rem;padding-top:2rem;border-top:1px solid rgba(255,255,255,0.15)">
            <div class="hero-stat">
                <strong>bis&nbsp;6&nbsp;%</strong>
                <span>Nettomietrendite</span>
            </div>
            <div class="hero-stat">
                <strong>120+</strong>
                <span>Kapitalanlagen vermittelt</span>
            </div>
            <div class="hero-stat">
                <strong>10+ J.</strong>
                <span>Marktkenntnis</span>
            </div>
            <div class="hero-stat">
                <strong>kostenlos</strong>
                <span>Erstberatung</span>
            </div>
        </div>
    </div>
</section>

<!-- Leistungen -->
<section style="padding:var(--section-gap) 0;background:#fff">
    <div class="container">
        <div class="section-header-center reveal">
            <span class="section-tag">Unser Angebot</span>
            <h2 class="section-heading">Was wir fuer <span class="accent">Investoren</span> tun</h2>
            <div class="divider-gold centered"></div>
        </div>
        <div class="services-grid">
            <?php
            $services = [
                ['fas fa-chart-bar', '#2d6cdf', 'Renditeanalyse', 'Wir berechnen exakt die Nettomietrendite, den Cashflow und die Gesamtrendite inkl. Steuereffekten und Nebenkosten.'],
                ['fas fa-map-marked-alt', '#059669', 'Standortbewertung', 'Detaillierte Analyse von Mietnachfrage, Leerstandsrisiko, Infrastruktur und Preisentwicklung fuer jeden Standort.'],
                ['fas fa-handshake', '#2563eb', 'Direktkontakte', 'Zugang zu Immobilien, die nicht öffentlich inseriert sind – über unser regionales Netzwerk und persönliche Kontakte.'],
                ['fas fa-gavel', '#7c3aed', 'Rechtliche Pruefung', 'Grundbuch, Teilungserklaerung, Mietvertraege, Altlasten – wir pruefen alle Unterlagen vollstaendig.'],
                ['fas fa-building', '#ef4444', 'Verwaltung nach Kauf', 'Nach dem Kauf uebernehmen wir auf Wunsch die komplette Hausverwaltung – Mietersuche, Abrechnung, Instandhaltung.'],
                ['fas fa-piggy-bank', '#0891b2', 'Steueroptimierung', 'Absetzbare Kosten, AfA, Verlustverrechnung – wir zeigen Ihnen, wie Sie Ihre Steuerlast optimieren.'],
            ];
            foreach ($services as $s): ?>
                <div class="service-card reveal">
                    <div class="service-card-icon" style="background:<?= $s[1] ?>18;color:<?= $s[1] ?>">
                        <i class="<?= $s[0] ?>"></i>
                    </div>
                    <h3><?= $s[2] ?></h3>
                    <p><?= $s[3] ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Renditerechner -->
<section style="padding:var(--section-gap) 0;background:var(--premium-offwhite)">
    <div class="container">
        <div class="two-col">
            <div class="reveal">
                <span class="section-tag">Renditerechner</span>
                <h2 class="section-heading">Berechnen Sie Ihre <span class="accent">Rendite</span></h2>
                <div class="divider-gold"></div>
                <p style="color:var(--premium-text-lt);margin-bottom:2rem">Ermitteln Sie schnell die Nettomietrendite und den monatlichen Cashflow Ihrer geplanten Kapitalanlage.</p>
                <ul class="feature-list">
                    <li>Nettomietrendite und Bruttomietrendite</li>
                    <li>Monatlicher Cashflow nach Finanzierung</li>
                    <li>Amortisationszeitraum</li>
                    <li>Steuerliche Vorteile berechnen</li>
                </ul>
            </div>
            <div class="calculator-card reveal anim-delay-2">
                <h4 style="color:#fff;font-size:1.1rem;margin-bottom:1.5rem"><i class="fas fa-chart-line" style="color:#9ac0de;margin-right:.5rem"></i> Rendite-Kalkulator</h4>
                <div style="margin-bottom:1.25rem">
                    <label>Kaufpreis: <strong id="kpLabel" style="color:#9ac0de">300.000 &euro;</strong></label>
                    <input type="range" id="kpRange" min="50000" max="2000000" step="10000" value="300000">
                </div>
                <div style="margin-bottom:1.25rem">
                    <label>Jährliche Miete (netto): <strong id="mieteLabel" style="color:#9ac0de">12.000 &euro;</strong></label>
                    <input type="range" id="mieteRange" min="2400" max="60000" step="600" value="12000">
                </div>
                <div style="margin-bottom:1.25rem">
                    <label>Nebenkosten (% des KP): <strong id="nkLabel" style="color:#9ac0de">10 %</strong></label>
                    <input type="range" id="nkRange" min="5" max="15" step="0.5" value="10">
                </div>
                <div style="margin-bottom:1.25rem">
                    <label>Verwaltungskosten (p.a.): <strong id="vkLabel" style="color:#9ac0de">1.200 &euro;</strong></label>
                    <input type="range" id="vkRange" min="0" max="6000" step="100" value="1200">
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem">
                    <div class="calc-result">
                        <div class="calc-result-value" id="renditeResult">3,8 %</div>
                        <div class="calc-result-label">Nettomietrendite</div>
                    </div>
                    <div class="calc-result">
                        <div class="calc-result-value" id="cashflowResult">892 &euro;</div>
                        <div class="calc-result-label">Jahres-CF (netto)</div>
                    </div>
                </div>
                <a href="#beratung" class="btn-premium" style="width:100%;justify-content:center;margin-top:1.25rem">
                    <i class="fas fa-arrow-right"></i> Individuelle Beratung
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Warum Rhein-Main -->
<section style="padding:var(--section-gap) 0;background:#fff">
    <div class="container">
        <div class="section-header-center reveal">
            <span class="section-tag">Marktchancen</span>
            <h2 class="section-heading">Warum <span class="accent">Rhein-Main-Gebiet</span>?</h2>
            <div class="divider-gold centered"></div>
            <p class="section-subheading">Einer der attraktivsten Immobilienmaerkte Deutschlands mit stabilen Renditen und langfristiger Wertsteigerung.</p>
        </div>
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:1.5rem;margin-top:2.5rem">
            <?php
            $reasons = [
                ['fas fa-city', '#2d6cdf', 'Starker Wirtschaftsraum', 'Frankfurt als Finanzmetropole, Wiesbaden als Landeshauptstadt – hohe Kaufkraft und Mieternachfrage.'],
                ['fas fa-graduation-cap', '#059669', 'Wachsende Bevoelkerung', 'Kontinuierlicher Bevoelkerungszuwachs durch Zuzug und Universitaeten sichert langfristige Mieternachfrage.'],
                ['fas fa-train', '#2563eb', 'Exzellente Infrastruktur', 'Hervorragende Verkehrsanbindung, internationaler Flughafen, leistungsfähiger ÖPNV.'],
                ['fas fa-chart-line', '#7c3aed', 'Stabile Preisentwicklung', 'Kontinuierliche Wertsteigerungen mit moderatem Risiko im Vergleich zu anderen Top-7-Staedten.'],
            ];
            foreach ($reasons as $r): ?>
                <div class="info-card reveal">
                    <div class="info-card-icon" style="background:<?= $r[1] ?>18;color:<?= $r[1] ?>">
                        <i class="<?= $r[0] ?>"></i>
                    </div>
                    <h3><?= $r[2] ?></h3>
                    <p style="font-size:.875rem;color:var(--premium-text-lt)"><?= $r[3] ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Beratungsformular -->
<section style="padding:var(--section-gap) 0;background:var(--premium-offwhite)" id="beratung">
    <div class="container" style="max-width:700px">
        <div class="section-header-center reveal">
            <span class="section-tag">Kostenlose Erstberatung</span>
            <h2 class="section-heading">Ihr <span class="accent">Investment-Gespräch</span></h2>
            <div class="divider-gold centered"></div>
            <p class="section-subheading">Erfahren Sie, welche Kapitalanlage zu Ihren Zielen und Ihrem Budget passt – kostenlos und unverbindlich.</p>
        </div>
        <form class="form-premium reveal" action="<?= BASE ?>/api/contact.php" method="POST" id="kapitalForm">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(generate_csrf_token(), ENT_QUOTES) ?>">
            <input type="hidden" name="form_type" value="kapitalanlage">
            <div class="form-row">
                <div class="form-group">
                    <label for="k_name">Name *</label>
                    <input type="text" id="k_name" name="name" placeholder="Ihr Name" required>
                </div>
                <div class="form-group">
                    <label for="k_email">E-Mail *</label>
                    <input type="email" id="k_email" name="email" placeholder="ihre@email.de" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="k_phone">Telefon</label>
                    <input type="tel" id="k_phone" name="phone" placeholder="+49 ...">
                </div>
                <div class="form-group">
                    <label for="k_budget">Investment-Budget</label>
                    <select id="k_budget" name="budget">
                        <option value="">Bitte waehlen</option>
                        <option value="bis 150.000 EUR">bis 150.000 &euro;</option>
                        <option value="150.000 - 300.000 EUR">150.000 &ndash; 300.000 &euro;</option>
                        <option value="300.000 - 500.000 EUR">300.000 &ndash; 500.000 &euro;</option>
                        <option value="500.000 - 1 Mio EUR">500.000 &ndash; 1 Mio &euro;</option>
                        <option value="ueber 1 Mio EUR">ueber 1 Mio &euro;</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label for="k_message">Ihre Ziele / Fragen</label>
                <textarea id="k_message" name="message" rows="3" placeholder="z. B. erste Kapitalanlage, Portfolioerweiterung, Renditeziel ..."></textarea>
            </div>
            <div class="form-group" style="display:flex;align-items:flex-start;gap:.75rem">
                <input type="checkbox" id="k_datenschutz" name="datenschutz" required style="margin-top:3px;flex-shrink:0">
                <label for="k_datenschutz" style="font-size:.85rem;cursor:pointer">Ich stimme der Verarbeitung meiner Daten gemaess der <a href="<?= BASE ?>/datenschutz" style="color:var(--premium-accent)">Datenschutzerklaerung</a> zu. *</label>
            </div>
            <button type="submit" class="btn-premium gold" style="width:100%;justify-content:center">
                <i class="fas fa-paper-plane"></i> Kostenlose Beratung anfragen
            </button>
            <div id="kapitalMsg" style="display:none;margin-top:1rem;padding:1rem;border-radius:var(--radius-md);text-align:center;font-weight:600"></div>
        </form>
    </div>
</section>

<!-- CTA -->
<section class="cta-banner">
    <div class="container-sm">
        <h2>Investieren Sie in die Zukunft</h2>
        <p>Bauen Sie mit uns ein renditestarkes Immobilienportfolio im Rhein-Main-Gebiet auf.</p>
        <div class="cta-banner-btns">
            <a href="tel:+4916098787878" class="btn-hero-primary"><i class="fas fa-phone"></i> Jetzt anrufen</a>
            <a href="https://wa.me/4916098787878?text=Ich%20interessiere%20mich%20fuer%20Immobilien%20als%20Kapitalanlage." class="btn-hero-secondary" target="_blank" rel="noopener noreferrer"><i class="fab fa-whatsapp"></i> WhatsApp</a>
            <a href="<?= BASE ?>/immobilien-angebote" class="btn-hero-secondary"><i class="fas fa-home"></i> Angebote ansehen</a>
        </div>
    </div>
</section>

<script>
    // Rendite Kalkulator
    (function() {
        function parseApiResponse(r) {
            return r.text().then(function(raw) {
                var cleaned = (raw || '').replace(/^\uFEFF+/, '').trim();
                var json = {};
                try {
                    json = cleaned ? JSON.parse(cleaned) : {};
                } catch (e) {
                    json = {
                        success: false,
                        message: cleaned || ('HTTP ' + r.status)
                    };
                }
                json._httpOk = r.ok;
                json._status = r.status;
                return json;
            });
        }

        var kp = document.getElementById('kpRange');
        var miete = document.getElementById('mieteRange');
        var nk = document.getElementById('nkRange');
        var vk = document.getElementById('vkRange');

        function fmt(n) {
            return n.toLocaleString('de-DE');
        }

        function calc() {
            if (!kp) return;
            var K = parseInt(kp.value);
            var M = parseInt(miete.value);
            var N = parseFloat(nk.value) / 100;
            var V = parseInt(vk.value);
            document.getElementById('kpLabel').textContent = fmt(K) + ' \u20ac';
            document.getElementById('mieteLabel').textContent = fmt(M) + ' \u20ac';
            document.getElementById('nkLabel').textContent = nk.value + ' %';
            document.getElementById('vkLabel').textContent = fmt(V) + ' \u20ac';
            var gesamtkosten = K * (1 + N);
            var nettoertrag = M - V;
            var rendite = (nettoertrag / gesamtkosten * 100).toFixed(1);
            document.getElementById('renditeResult').textContent = rendite + ' %';
            document.getElementById('cashflowResult').textContent = fmt(Math.round(nettoertrag)) + ' \u20ac';
        }
        [kp, miete, nk, vk].forEach(function(el) {
            if (el) el.addEventListener('input', calc);
        });
        calc();

        // Form
        var form = document.getElementById('kapitalForm');
        var msg = document.getElementById('kapitalMsg');
        if (!form) return;
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            var btn = form.querySelector('button[type=submit]');
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Wird gesendet ...';
            var data = new FormData(form);
            fetch(form.action, {
                    method: 'POST',
                    body: data
                })
                .then(parseApiResponse)
                .then(function(json) {
                    msg.style.display = 'block';
                    if (json.success && json._httpOk) {
                        msg.style.background = '#d1fae5';
                        msg.style.color = '#065f46';
                        msg.textContent = 'Vielen Dank! Wir melden uns fuer Ihre Investmentberatung.';
                        form.reset();
                    } else {
                        msg.style.background = '#fee2e2';
                        msg.style.color = '#991b1b';
                        msg.textContent = json.message || 'Fehler. Bitte rufen Sie uns an.';
                    }
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fas fa-paper-plane"></i> Kostenlose Beratung anfragen';
                })
                .catch(function() {
                    msg.style.display = 'block';
                    msg.style.background = '#fee2e2';
                    msg.style.color = '#991b1b';
                    msg.textContent = 'Verbindungsfehler. Bitte rufen Sie uns direkt an.';
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fas fa-paper-plane"></i> Kostenlose Beratung anfragen';
                });
        });
    })();
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
