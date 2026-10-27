<?php

/**
 * includes/header.php
 * ===================
 * Gemeinsamer Seiten-Header: <!DOCTYPE html> bis </header>
 *
 * Erwartet folgende Variablen (vor dem include setzen):
 *   $pageTitle       – <title>-Text
 *   $pageDescription – Meta description
 *   $pageKeywords    – Meta keywords (optional)
 *   $ogUrl           – Canonical / OG URL (optional, Standard: SITE_URL)
 *   $extraHead       – Zusätzliches HTML im <head>, z.B. inline <style> (optional)
 *   $activeNav       – Aktive Nav-ID: 'home'|'immobilien'|'hausverwaltung'|'finanzierung' (optional)
 */
require_once __DIR__ . '/config.php';

$pageTitle       = $pageTitle       ?? SITE_NAME;
$pageDescription = $pageDescription ?? 'Immobilien Ghumman – Ihr erfahrener Immobilienmakler in Bad Vilbel.';
$pageKeywords    = $pageKeywords    ?? 'Immobilienmakler Bad Vilbel, Haus verkaufen, Wohnung mieten, Hausverwaltung';
if (!isset($ogUrl)) {
    // Default: self-referencing canonical built from the clean request path (no query string, no .php)
    $reqPath = (string) parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
    $reqPath = preg_replace('#(/index)?(\.php)?$#', '', $reqPath);
    $ogUrl   = rtrim(SITE_URL, '/') . ($reqPath === '' ? '' : '/' . ltrim($reqPath, '/'));
}
$extraHead       = $extraHead       ?? '';
$activeNav       = $activeNav       ?? '';

function nav_class(string $id, string $active): string
{
    return 'nav-link' . ($id === $active ? ' active' : '');
}
?>
<!DOCTYPE html>
<html lang="de">

<head>
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-Q5J1C2PTR2"></script>
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }
        gtag('js', new Date());
        gtag('config', 'G-Q5J1C2PTR2');
    </script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle, ENT_QUOTES) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://res.cloudinary.com" crossorigin>
    <link rel="preload" as="image" href="<?= SITE_LOGO ?>">

    <!-- Favicon -->
    <link rel="icon" type="image/png" sizes="32x32" href="<?= SITE_LOGO ?>">
    <link rel="apple-touch-icon" href="<?= SITE_LOGO ?>">

    <meta name="description" content="<?= htmlspecialchars($pageDescription, ENT_QUOTES) ?>">
    <meta name="keywords" content="<?= htmlspecialchars($pageKeywords,    ENT_QUOTES) ?>">
    <link rel="canonical" href="<?= htmlspecialchars($ogUrl, ENT_QUOTES) ?>">

    <!-- Zusätzliche SEO Meta Tags -->
    <meta name="author" content="<?= SITE_NAME ?>">
    <meta name="robots" content="<?= htmlspecialchars($robotsContent ?? 'index, follow', ENT_QUOTES) ?>">
    <meta name="language" content="de">
    <meta name="geo.region" content="DE-HE">
    <meta name="geo.placename" content="Bad Vilbel">
    <meta name="geo.position" content="50.1782;8.7331">
    <meta name="ICBM" content="50.1782, 8.7331">

    <!-- Open Graph -->
    <meta property="og:title" content="<?= htmlspecialchars($pageTitle,       ENT_QUOTES) ?>">
    <meta property="og:description" content="<?= htmlspecialchars($pageDescription, ENT_QUOTES) ?>">
    <meta property="og:image" content="<?= SITE_LOGO ?>">
    <meta property="og:url" content="<?= htmlspecialchars($ogUrl, ENT_QUOTES) ?>">
    <meta property="og:type" content="website">
    <meta property="og:locale" content="de_DE">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= htmlspecialchars($pageTitle,       ENT_QUOTES) ?>">
    <meta name="twitter:description" content="<?= htmlspecialchars($pageDescription, ENT_QUOTES) ?>">
    <meta name="twitter:image" content="<?= SITE_LOGO ?>">

    <!-- Strukturierte Daten -->
    <?php if (strpos($extraHead, 'application/ld+json') === false): ?>
    <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "RealEstateAgent",
            "name": "Immobilien Ghumman",
            "description": "Professioneller Immobilienmakler in Bad Vilbel für Verkauf, Vermietung, Hausverwaltung und Immobilienfinanzierung",
            "url": "https://www.immobilienghumman.de",
            "logo": "<?= SITE_LOGO ?>",
            "image": "<?= SITE_LOGO ?>",
            "telephone": "+49-160-98787878",
            "address": {
                "@type": "PostalAddress",
                "addressLocality": "Bad Vilbel",
                "addressRegion": "Hessen",
                "postalCode": "61118",
                "addressCountry": "DE"
            },
            "geo": {
                "@type": "GeoCoordinates",
                "latitude": 50.1782,
                "longitude": 8.7331
            },
            "openingHours": "Mo-Fr 09:00-18:00"
        }
    </script>
    <?php endif; ?>

    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,300;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="<?= BASE ?>/immobilien-styles.css">
    <link rel="stylesheet" href="<?= BASE ?>/premium.css?v=<?= (int)@filemtime(__DIR__ . '/../premium.css') ?>">
    <?= $extraHead ?>
    <script>
        window.API_BASE_URL = '<?= BASE ?>/api';
        window.SITE_BASE = '<?= BASE ?>';
    </script>
</head>

<!-- Header Navigation -->
<header class="header">
    <nav class="navbar">
        <div class="nav-container">
            <!-- Logo -->
            <div class="nav-logo">
                <a href="<?= BASE ?>/">
                    <img src="<?= SITE_LOGO ?>" alt="Immobilien Ghumman Logo" class="logo">
                </a>
            </div>

            <!-- Desktop Navigation -->
            <ul class="nav-menu" id="site-menu">
                <li><a href="<?= BASE ?>/" class="<?= nav_class('home', $activeNav) ?>">Start</a></li>

                <!-- Immobilien Dropdown -->
                <li class="nav-item-dropdown">
                    <a href="<?= BASE ?>/immobilien-angebote" class="<?= nav_class('immobilien', $activeNav) ?>">
                        Immobilien
                        <svg class="nav-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <path d="M6 9l6 6 6-6" />
                        </svg>
                    </a>
                    <div class="nav-dropdown">
                        <a href="<?= BASE ?>/immobilien-angebote"><i class="fas fa-home"></i> Alle Immobilien</a>
                        <a href="<?= BASE ?>/immobilien-angebote?offer_type=Kauf"><i class="fas fa-key"></i> Immobilien kaufen</a>
                        <a href="<?= BASE ?>/immobilien-angebote?offer_type=Miete"><i class="fas fa-sign-hanging"></i> Immobilien mieten</a>
                        <div class="nav-dropdown-divider"></div>
                        <a href="<?= BASE ?>/kapitalanlage"><i class="fas fa-chart-line"></i> Kapitalanlagen</a>
                        <a href="<?= BASE ?>/immobilienbewertung"><i class="fas fa-magnifying-glass-dollar"></i> Immobilie bewerten</a>
                    </div>
                </li>

                <!-- Hausverwaltung Dropdown -->
                <li class="nav-item-dropdown">
                    <a href="<?= BASE ?>/hausverwaltung" class="<?= nav_class('hausverwaltung', $activeNav) ?>">
                        Hausverwaltung
                        <svg class="nav-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <path d="M6 9l6 6 6-6" />
                        </svg>
                    </a>
                    <div class="nav-dropdown">
                        <a href="<?= BASE ?>/hausverwaltung"><i class="fas fa-building"></i> Übersicht</a>
                        <a href="<?= BASE ?>/hausverwaltung#leistung-mietverwaltung"><i class="fas fa-file-contract"></i> Mietverwaltung</a>
                        <a href="<?= BASE ?>/hausverwaltung#leistung-nebenkostenabrechnung"><i class="fas fa-receipt"></i> Nebenkostenabrechnung</a>
                        <a href="<?= BASE ?>/hausverwaltung#leistung-instandhaltung"><i class="fas fa-tools"></i> Instandhaltung</a>
                        <a href="<?= BASE ?>/hausverwaltung#leistung-eigentuemerversammlung"><i class="fas fa-users"></i> Eigentümerversammlung</a>
                        <div class="nav-dropdown-divider"></div>
                        <a href="<?= BASE ?>/hausverwaltung#leistung-digital"><i class="fas fa-laptop"></i> Digitale Verwaltung</a>
                        <a href="<?= BASE ?>/hausverwaltung#leistung-rechtliches"><i class="fas fa-scale-balanced"></i> Rechtliches</a>
                    </div>
                </li>

                <!-- Finanzierung Dropdown -->
                <li class="nav-item-dropdown">
                    <a href="<?= BASE ?>/finanzierung" class="<?= nav_class('finanzierung', $activeNav) ?>">
                        Finanzierung
                        <svg class="nav-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <path d="M6 9l6 6 6-6" />
                        </svg>
                    </a>
                    <div class="nav-dropdown">
                        <a href="<?= BASE ?>/finanzierung"><i class="fas fa-landmark"></i> Baufinanzierung</a>
                        <a href="<?= BASE ?>/finanzierung#anschluss"><i class="fas fa-rotate"></i> Anschlussfinanzierung</a>
                        <a href="<?= BASE ?>/finanzierung#foerderungen"><i class="fas fa-hand-holding-dollar"></i> Förderungen & KfW</a>
                        <div class="nav-dropdown-divider"></div>
                        <a href="<?= BASE ?>/finanzierung#rechner"><i class="fas fa-calculator"></i> Finanzierungsrechner</a>
                    </div>
                </li>
                <!-- Mehr Dropdown -->
                <li class="nav-item-dropdown">
                    <a href="#" class="<?= in_array($activeNav, ['kapitalanlage', 'analyse', 'ratgeber', 'about', 'contact']) ? 'nav-link active' : 'nav-link' ?>">
                        Mehr
                        <svg class="nav-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <path d="M6 9l6 6 6-6" />
                        </svg>
                    </a>
                    <div class="nav-dropdown">
                        <a href="<?= BASE ?>/kapitalanlage"><i class="fas fa-chart-line"></i> Kapitalanlage</a>
                        <a href="<?= BASE ?>/analyse-beratung"><i class="fas fa-magnifying-glass-chart"></i> Analyse & Beratung</a>
                        <div class="nav-dropdown-divider"></div>
                        <a href="<?= BASE ?>/blog"><i class="fas fa-blog"></i> Immobilien-Blog</a>
                        <div class="nav-dropdown-divider"></div>
                        <a href="<?= BASE ?>/#ueber-uns"><i class="fas fa-circle-info"></i> Über uns</a>
                        <a href="<?= BASE ?>/#kontakt"><i class="fas fa-envelope"></i> Kontakt</a>
                    </div>
                </li>
            </ul>

            <!-- Nav Actions -->
            <div class="nav-contacts">
                <a href="<?= BASE ?>/analyse-beratung" class="btn-nav-cta">
                    <i class="fas fa-calculator"></i> Jetzt bewerten
                </a>
                <a href="https://wa.me/4916098787878" class="contact-icon whatsapp" target="_blank" rel="noopener noreferrer" title="WhatsApp">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.488" />
                    </svg>
                </a>
                <button class="nav-hamburger" id="navHamburger" aria-label="Menü öffnen">
                    <span></span><span></span><span></span>
                </button>
            </div>
        </div>
    </nav>
</header>

<!-- WhatsApp Floating Button -->
<div class="whatsapp-float">
    <span class="whatsapp-float-label">WhatsApp schreiben</span>
    <a href="https://wa.me/4916098787878?text=Guten%20Tag%2C%20ich%20interessiere%20mich%20f%C3%BCr%20Ihre%20Immobiliendienstleistungen." class="whatsapp-float-btn" target="_blank" rel="noopener noreferrer" aria-label="WhatsApp Chat">
        <i class="fab fa-whatsapp"></i>
    </a>
</div>

<!-- Sticky Bottom CTA Bar (mobile) -->
<div class="sticky-cta-bar">
    <a href="tel:+4916098787878" class="scta-call"><i class="fas fa-phone"></i> Anrufen</a>
    <a href="https://wa.me/4916098787878" class="scta-wa" target="_blank" rel="noopener noreferrer"><i class="fab fa-whatsapp"></i> WhatsApp</a>
    <a href="<?= BASE ?>/immobilienbewertung" class="scta-eval"><i class="fas fa-calculator"></i> Bewertung</a>
</div>

<!-- Mobile Menu Overlay -->
<div class="mobile-menu-overlay" id="mobileOverlay"></div>

<!-- Mobile Sidebar Menu -->
<div class="mobile-menu" id="mobileMenu" role="dialog" aria-modal="true" aria-label="Navigation">
    <div class="mobile-menu-header">
        <img src="<?= SITE_LOGO ?>" alt="Immobilien Ghumman" style="height:44px;">
        <button class="mobile-menu-close" id="mobileMenuClose" aria-label="Menü schließen">&times;</button>
    </div>
    <ul class="mobile-nav-list">
        <li><a href="<?= BASE ?>/" class="<?= $activeNav === 'home' ? 'active' : '' ?>"><i class="fas fa-home"></i> Startseite</a></li>

        <li>
            <button class="mobile-nav-toggle" data-target="mob-immobilien"><i class="fas fa-building"></i> Immobilien <i class="toggle-arrow fas fa-chevron-down"></i></button>
            <ul class="mobile-sub-list" id="mob-immobilien" style="display:none">
                <li><a href="<?= BASE ?>/immobilien-angebote">Alle Immobilien</a></li>
                <li><a href="<?= BASE ?>/immobilien-angebote?offer_type=Kauf">Kaufen</a></li>
                <li><a href="<?= BASE ?>/immobilien-angebote?offer_type=Miete">Mieten</a></li>
                <li><a href="<?= BASE ?>/kapitalanlage">Kapitalanlagen</a></li>
            </ul>
        </li>

        <li>
            <button class="mobile-nav-toggle" data-target="mob-hausverwaltung"><i class="fas fa-key"></i> Hausverwaltung <i class="toggle-arrow fas fa-chevron-down"></i></button>
            <ul class="mobile-sub-list" id="mob-hausverwaltung" style="display:none">
                <li><a href="<?= BASE ?>/hausverwaltung">Übersicht</a></li>
                <li><a href="<?= BASE ?>/hausverwaltung#leistung-mietverwaltung">Mietverwaltung</a></li>
                <li><a href="<?= BASE ?>/hausverwaltung#leistung-nebenkostenabrechnung">Nebenkostenabrechnung</a></li>
                <li><a href="<?= BASE ?>/hausverwaltung#leistung-instandhaltung">Instandhaltung</a></li>
                <li><a href="<?= BASE ?>/hausverwaltung#leistung-eigentuemerversammlung">Eigentümerversammlung</a></li>
                <li><a href="<?= BASE ?>/hausverwaltung#leistung-digital">Digitale Verwaltung</a></li>
            </ul>
        </li>

        <li>
            <button class="mobile-nav-toggle" data-target="mob-finanzierung"><i class="fas fa-landmark"></i> Finanzierung <i class="toggle-arrow fas fa-chevron-down"></i></button>
            <ul class="mobile-sub-list" id="mob-finanzierung" style="display:none">
                <li><a href="<?= BASE ?>/finanzierung">Baufinanzierung</a></li>
                <li><a href="<?= BASE ?>/finanzierung#anschluss">Anschlussfinanzierung</a></li>
                <li><a href="<?= BASE ?>/finanzierung#foerderungen">Förderungen & KfW</a></li>
                <li><a href="<?= BASE ?>/finanzierung#rechner">Finanzierungsrechner</a></li>
            </ul>
        </li>

        <li><a href="<?= BASE ?>/immobilienbewertung" class="<?= $activeNav === 'bewertung' ? 'active' : '' ?>"><i class="fas fa-calculator"></i> Immobilienbewertung</a></li>
        <li><a href="<?= BASE ?>/kapitalanlage" class="<?= $activeNav === 'kapitalanlage' ? 'active' : '' ?>"><i class="fas fa-chart-line"></i> Kapitalanlage</a></li>
        <li><a href="<?= BASE ?>/analyse-beratung" class="<?= $activeNav === 'analyse' ? 'active' : '' ?>"><i class="fas fa-magnifying-glass-chart"></i> Analyse & Beratung</a></li>
        <li><a href="<?= BASE ?>/ratgeber" class="<?= $activeNav === 'ratgeber' ? 'active' : '' ?>"><i class="fas fa-book-open"></i> Ratgeber</a></li>
        <li><a href="<?= BASE ?>/#ueber-uns"><i class="fas fa-circle-info"></i> Über uns</a></li>
        <li><a href="<?= BASE ?>/#faq"><i class="fas fa-circle-question"></i> FAQ</a></li>
    </ul>
    <div class="mobile-menu-contact">
        <a href="tel:+4916098787878"><i class="fas fa-phone"></i> 0160 9878 7878</a>
        <a href="https://wa.me/4916098787878" target="_blank" rel="noopener noreferrer"><i class="fab fa-whatsapp"></i> WhatsApp schreiben</a>
        <a href="<?= BASE ?>/#kontakt"><i class="fas fa-calendar-check"></i> Termin anfragen</a>
    </div>
</div>

<script>
    (function() {
        var ham = document.getElementById('navHamburger');
        var menu = document.getElementById('mobileMenu');
        var overlay = document.getElementById('mobileOverlay');
        var closeBtn = document.getElementById('mobileMenuClose');

        function openMenu() {
            menu.classList.add('is-open');
            overlay.classList.add('is-open');
            document.body.style.overflow = 'hidden';
        }

        function closeMenu() {
            menu.classList.remove('is-open');
            overlay.classList.remove('is-open');
            document.body.style.overflow = '';
        }
        if (ham) ham.addEventListener('click', openMenu);
        if (closeBtn) closeBtn.addEventListener('click', closeMenu);
        if (overlay) overlay.addEventListener('click', closeMenu);
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') closeMenu();
        });

        // Accordion toggles
        document.querySelectorAll('.mobile-nav-toggle').forEach(function(btn) {
            btn.addEventListener('click', function() {
                var target = document.getElementById(btn.getAttribute('data-target'));
                var isOpen = target.style.display === 'block';
                // close all
                document.querySelectorAll('.mobile-sub-list').forEach(function(s) {
                    s.style.display = 'none';
                });
                document.querySelectorAll('.mobile-nav-toggle').forEach(function(b) {
                    b.classList.remove('is-open');
                });
                if (!isOpen) {
                    target.style.display = 'block';
                    btn.classList.add('is-open');
                }
            });
        });

        // AOS-like scroll animations
        function initAos() {
            var els = document.querySelectorAll('[data-aos]');
            var observer = new IntersectionObserver(function(entries) {
                entries.forEach(function(e) {
                    if (e.isIntersecting) {
                        e.target.classList.add('aos-animate');
                    }
                });
            }, {
                threshold: 0.08
            });
            els.forEach(function(el) {
                observer.observe(el);
            });
        }
        if ('IntersectionObserver' in window) {
            document.addEventListener('DOMContentLoaded', initAos);
        } else {
            document.querySelectorAll('[data-aos]').forEach(function(el) {
                el.classList.add('aos-animate');
            });
        }

        // Scroll progress bar + header shadow
        var scrollBar = document.getElementById('scrollProgress');
        var header = document.querySelector('.header');
        window.addEventListener('scroll', function() {
            var scrolled = (window.scrollY / (document.body.scrollHeight - window.innerHeight)) * 100;
            if (scrollBar) scrollBar.style.width = scrolled + '%';
            if (header) header.classList.toggle('scrolled', window.scrollY > 60);
        }, {
            passive: true
        });

        // Reveal on scroll (progressive enhancement)
        // IMPORTANT: must run after DOMContentLoaded so page content exists in DOM,
        // and in-viewport elements must be marked .visible BEFORE js-animate is added
        // to prevent any flash of invisible content.
        document.addEventListener('DOMContentLoaded', function() {
            if (!('IntersectionObserver' in window)) return;
            var revealObs = new IntersectionObserver(function(entries) {
                entries.forEach(function(e) {
                    if (e.isIntersecting) {
                        e.target.classList.add('visible');
                        revealObs.unobserve(e.target);
                    }
                });
            }, {
                threshold: 0.05
            });
            // Pre-mark elements already in the viewport as visible BEFORE enabling
            // js-animate, so they are never hidden by the opacity:0 rule.
            document.querySelectorAll('.reveal').forEach(function(el) {
                if (el.getBoundingClientRect().top < window.innerHeight * 0.98) {
                    el.classList.add('visible');
                }
                revealObs.observe(el);
            });
            // Activate animation class now – above-fold elements are already marked
            // visible so they won't flash white.
            document.documentElement.classList.add('js-animate');
        });

        // Counter animation
        function animateCounters() {
            document.querySelectorAll('.stat-number[data-target]').forEach(function(el) {
                var target = parseInt(el.getAttribute('data-target'), 10);
                var duration = 1800;
                var start = null;

                function step(ts) {
                    if (!start) start = ts;
                    var progress = Math.min((ts - start) / duration, 1);
                    var eased = 1 - Math.pow(1 - progress, 3);
                    el.textContent = Math.floor(eased * target) + (el.getAttribute('data-suffix') || '');
                    if (progress < 1) requestAnimationFrame(step);
                }
                requestAnimationFrame(step);
            });
        }
        var statsSection = document.querySelector('.stats-section');
        if (statsSection && 'IntersectionObserver' in window) {
            var statsObs = new IntersectionObserver(function(entries) {
                if (entries[0].isIntersecting) {
                    animateCounters();
                    statsObs.disconnect();
                }
            }, {
                threshold: 0.3
            });
            statsObs.observe(statsSection);
        }
    })();
</script>