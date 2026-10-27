<?php
// Legacy page — redirect permanently to the new property detail
require_once __DIR__ . '/includes/config.php';

$slug = isset($_GET['slug']) ? trim((string)$_GET['slug']) : '';
$id = isset($_GET['id']) ? $_GET['id'] : '';
if ($slug !== '') {
    header('Location: ' . BASE . '/immobilie/' . rawurlencode($slug), true, 301);
} elseif ($id !== '') {
    header('Location: ' . BASE . '/property-detail?id=' . urlencode($id), true, 301);
} else {
    header('Location: ' . BASE . '/immobilien-angebote', true, 301);
}
exit;
// --- original code below (kept for reference, never reached) ---
?>
$pageDescription = 'Detailansicht einer Immobilie von Immobilien Ghumman.';
$pageKeywords = 'Immobilie kaufen, Wohnung mieten, Details';
$activeNav = 'immobilien';
$extraHead = <<<'ENDCSS'
    <style>
    /* ===========================
    CSS VARIABLEN
    =========================== */
    :root {
    --primary-color: #1a56a4;
    --primary-dark: #0d4191;
    --secondary-color: #1e293b;
    --background-color: #f8fafc;
    --card-bg: #ffffff;
    --text-primary: #1e293b;
    --text-secondary: #64748b;
    --text-muted: #94a3b8;
    --success-color: #10b981;
    --accent-color: #3b82f6;
    --border-color: #e2e8f0;
    --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
    --radius-sm: 0.375rem;
    --radius-md: 0.5rem;
    --radius-lg: 0.75rem;
    --radius-xl: 1rem;
    --radius-2xl: 1.5rem;
    --font-family: Montserrat, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    }

    /* ===========================
    RESET & BASIS
    =========================== */
    * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    }

    html {
    scroll-behavior: smooth;
    }

    body {
    font-family: var(--font-family);
    background-color: var(--background-color);
    color: var(--text-primary);
    line-height: 1.6;
    }

    a {
    text-decoration: none;
    color: inherit;
    }

    img {
    max-width: 100%;
    height: auto;
    }

    .container {
    max-width: 1280px;
    margin: 0 auto;
    padding: 0 1.5rem;
    }

    /* ===========================
    HEADER & NAVIGATION
    =========================== */
    .header {
    background: white;
    box-shadow: var(--shadow-sm);
    position: sticky;
    top: 0;
    z-index: 1000;
    }

    .navbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1rem 1.5rem;
    max-width: 1280px;
    margin: 0 auto;
    }

    .nav-logo img {
    height: 50px;
    width: auto;
    }

    .nav-menu {
    display: flex;
    list-style: none;
    gap: 2rem;
    }

    .nav-link {
    font-weight: 500;
    color: var(--text-secondary);
    transition: color 0.3s ease;
    }

    .nav-link:hover,
    .nav-link.active {
    color: var(--primary-color);
    }

    .nav-contacts {
    display: flex;
    gap: 0.75rem;
    }

    .contact-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
    }

    .contact-icon.whatsapp {
    background: #25d366;
    color: white;
    }

    .contact-icon.email {
    background: var(--primary-color);
    color: white;
    }

    .contact-icon.phone {
    background: var(--secondary-color);
    color: white;
    }

    .hamburger {
    display: none;
    flex-direction: column;
    gap: 5px;
    cursor: pointer;
    padding: 10px;
    }

    .hamburger span {
    width: 25px;
    height: 3px;
    background: var(--secondary-color);
    border-radius: 3px;
    }

    /* ===========================
    ZURÜCK NAVIGATION
    =========================== */
    .breadcrumb {
    padding: 1rem 0;
    background: white;
    border-bottom: 1px solid var(--border-color);
    }

    .breadcrumb a {
    color: var(--primary-color);
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    font-weight: 500;
    transition: opacity 0.3s;
    }

    .breadcrumb a:hover {
    opacity: 0.7;
    }

    .breadcrumb svg {
    width: 20px;
    height: 20px;
    }

    /* ===========================
    DETAIL LAYOUT
    =========================== */
    .detail-section {
    padding: 2rem 0 4rem;
    }

    .detail-grid {
    display: grid;
    grid-template-columns: 1fr 380px;
    gap: 2rem;
    }

    /* ===========================
    GALERIE
    =========================== */
    .galerie {
    background: white;
    border-radius: var(--radius-xl);
    overflow: hidden;
    box-shadow: var(--shadow-md);
    }

    .galerie-hauptbild {
    position: relative;
    height: 450px;
    background: #f1f5f9;
    }

    .galerie-hauptbild img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    }

    .galerie-nav {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    width: 48px;
    height: 48px;
    background: rgba(255, 255, 255, 0.95);
    border: none;
    border-radius: 50%;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
    box-shadow: var(--shadow-md);
    }

    .galerie-nav:hover {
    background: white;
    transform: translateY(-50%) scale(1.1);
    }

    .galerie-nav.prev {
    left: 1rem;
    }

    .galerie-nav.next {
    right: 1rem;
    }

    .galerie-nav svg {
    width: 24px;
    height: 24px;
    fill: var(--text-primary);
    }

    .galerie-thumbs {
    display: flex;
    gap: 0.5rem;
    padding: 1rem;
    overflow-x: auto;
    }

    .galerie-thumb {
    width: 80px;
    height: 60px;
    flex-shrink: 0;
    border-radius: var(--radius-md);
    overflow: hidden;
    cursor: pointer;
    opacity: 0.6;
    transition: all 0.3s ease;
    border: 2px solid transparent;
    }

    .galerie-thumb.active,
    .galerie-thumb:hover {
    opacity: 1;
    border-color: var(--primary-color);
    }

    .galerie-thumb img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    }

    /* ===========================
    CONTENT CARDS
    =========================== */
    .content-card {
    background: white;
    border-radius: var(--radius-xl);
    padding: 1.5rem;
    box-shadow: var(--shadow-md);
    margin-top: 1.5rem;
    }

    .content-card h2 {
    font-size: 1.25rem;
    color: var(--text-primary);
    margin-bottom: 1rem;
    padding-bottom: 0.75rem;
    border-bottom: 2px solid var(--border-color);
    }

    /* Eckdaten */
    .eckdaten-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
    gap: 1rem;
    }

    .eckdaten-item {
    text-align: center;
    padding: 1rem;
    background: var(--background-color);
    border-radius: var(--radius-lg);
    }

    .eckdaten-item svg {
    width: 28px;
    height: 28px;
    fill: var(--primary-color);
    margin-bottom: 0.5rem;
    }

    .eckdaten-wert {
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--text-primary);
    }

    .eckdaten-label {
    font-size: 0.75rem;
    color: var(--text-muted);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    }

    /* Beschreibung */
    .beschreibung-text {
    color: var(--text-secondary);
    line-height: 1.8;
    white-space: pre-line;
    }

    /* Ausstattung */
    .ausstattung-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 0.75rem;
    }

    .ausstattung-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem;
    background: var(--background-color);
    border-radius: var(--radius-md);
    }

    .ausstattung-item svg {
    width: 20px;
    height: 20px;
    fill: var(--success-color);
    flex-shrink: 0;
    }

    /* Kosten */
    .kosten-grid {
    display: grid;
    gap: 0.75rem;
    }

    .kosten-zeile {
    display: flex;
    justify-content: space-between;
    padding: 0.75rem;
    background: var(--background-color);
    border-radius: var(--radius-md);
    }

    .kosten-zeile.highlight {
    background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
    color: white;
    }

    .kosten-zeile.highlight .kosten-wert {
    color: white;
    }

    .kosten-wert {
    font-weight: 700;
    color: var(--primary-color);
    }

    /* ===========================
    SIDEBAR
    =========================== */
    .sidebar {
    position: sticky;
    top: 100px;
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
    }

    .preis-karte {
    background: white;
    border-radius: var(--radius-xl);
    padding: 1.5rem;
    box-shadow: var(--shadow-lg);
    border-top: 4px solid var(--primary-color);
    text-align: center;
    }

    .preis-hauptpreis {
    font-size: 2.25rem;
    font-weight: 700;
    color: var(--primary-color);
    }

    .preis-label {
    color: var(--text-muted);
    font-size: 0.9rem;
    margin-bottom: 1.5rem;
    }

    .aktions-buttons {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
    }

    .btn {
    padding: 1rem 1.5rem;
    border-radius: var(--radius-lg);
    font-weight: 600;
    font-size: 1rem;
    cursor: pointer;
    transition: all 0.3s ease;
    border: none;
    text-align: center;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    }

    .btn-primary {
    background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
    color: white;
    }

    .btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(51, 102, 153, 0.35);
    }

    .btn-whatsapp {
    background: #25d366;
    color: white;
    }

    .btn-whatsapp:hover {
    background: #1da851;
    }

    .btn-secondary {
    background: var(--background-color);
    color: var(--text-secondary);
    border: 2px solid var(--border-color);
    }

    .btn-secondary:hover {
    border-color: var(--primary-color);
    color: var(--primary-color);
    }

    .btn svg {
    width: 20px;
    height: 20px;
    fill: currentColor;
    }

    /* Kontakt Karte */
    .kontakt-karte {
    background: white;
    border-radius: var(--radius-xl);
    padding: 1.5rem;
    box-shadow: var(--shadow-md);
    }

    .kontakt-agent {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 1rem;
    }

    .agent-avatar {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: var(--background-color);
    padding: 8px;
    }

    .agent-info h4 {
    font-size: 1rem;
    margin-bottom: 0.25rem;
    }

    .agent-info p {
    font-size: 0.85rem;
    color: var(--text-muted);
    }

    .kontakt-details p {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 0;
    border-top: 1px solid var(--border-color);
    font-size: 0.9rem;
    }

    .kontakt-details svg {
    width: 18px;
    height: 18px;
    fill: var(--primary-color);
    }

    /* Video */
    .video-container {
    position: relative;
    padding-bottom: 56.25%;
    height: 0;
    border-radius: var(--radius-lg);
    overflow: hidden;
    background: #000;
    }

    .video-container iframe {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    }

    /* ===========================
    FOOTER
    =========================== */
    .footer {
    background: var(--secondary-color);
    color: white;
    padding: 3rem 0 1.5rem;
    margin-top: 2rem;
    }

    .footer-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 2rem;
    margin-bottom: 2rem;
    }

    .footer-section h3 {
    font-size: 1rem;
    margin-bottom: 1rem;
    }

    .footer-links {
    list-style: none;
    }

    .footer-links li {
    margin-bottom: 0.5rem;
    }

    .footer-links a {
    color: #94a3b8;
    transition: color 0.3s ease;
    }

    .footer-links a:hover {
    color: white;
    }

    .footer-bottom {
    text-align: center;
    padding-top: 1.5rem;
    border-top: 1px solid #334155;
    color: #94a3b8;
    font-size: 0.875rem;
    }

    /* ===========================
    WHATSAPP STICKY
    =========================== */
    .whatsapp-sticky {
    position: fixed;
    bottom: 2rem;
    right: 2rem;
    z-index: 999;
    }

    .whatsapp-sticky a {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 60px;
    height: 60px;
    background: #25d366;
    border-radius: 50%;
    box-shadow: 0 4px 20px rgba(37, 211, 102, 0.4);
    transition: all 0.3s ease;
    }

    .whatsapp-sticky a:hover {
    transform: scale(1.1);
    }

    .whatsapp-sticky svg {
    width: 32px;
    height: 32px;
    fill: white;
    }

    /* ===========================
    RESPONSIVE
    =========================== */
    @media (max-width: 992px) {
    .nav-menu {
    display: none;
    }

    .hamburger {
    display: flex;
    }

    .detail-grid {
    grid-template-columns: 1fr;
    }

    .sidebar {
    position: static;
    }
    }

    @media (max-width: 768px) {
    .galerie-hauptbild {
    height: 300px;
    }

    .eckdaten-grid {
    grid-template-columns: repeat(2, 1fr);
    }
    }

    /* Fehler Nachricht */
    .fehler-nachricht {
    text-align: center;
    padding: 4rem 2rem;
    background: white;
    border-radius: var(--radius-xl);
    box-shadow: var(--shadow-md);
    }

    .fehler-nachricht svg {
    width: 80px;
    height: 80px;
    fill: var(--text-muted);
    margin-bottom: 1rem;
    }

    .fehler-nachricht h2 {
    margin-bottom: 0.5rem;
    }

    .fehler-nachricht p {
    color: var(--text-secondary);
    margin-bottom: 1.5rem;
    }
    </style>
    ENDCSS;
    $extraScripts = '
    <script src="<?= BASE ?>/immobilien-daten.js"></script>';
    require_once __DIR__ . '/includes/header.php';
    ?>


    <!-- Breadcrumb -->
    <div class="breadcrumb">
        <div class="container">
            <a href="<?= BASE ?>/immobilien-uebersicht">
                <svg viewBox="0 0 24 24" fill="currentColor">
                    <path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z" />
                </svg>
                Zurück zur Übersicht
            </a>
        </div>
    </div>

    <!-- Detail Content -->
    <section class="detail-section">
        <div class="container">
            <div id="detail-content">
                <!-- Wird per JavaScript gefüllt -->
                <div class="fehler-nachricht" id="loading-state">
                    <p>Lade Immobiliendetails...</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->

    <?php require_once __DIR__ . '/includes/footer.php'; ?>