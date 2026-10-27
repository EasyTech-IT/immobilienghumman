<?php
require_once __DIR__ . '/includes/config.php';

http_response_code(404);

$pageTitle = 'Seite nicht gefunden (404) | Immobilien Ghumman';
$pageDescription = 'Die angeforderte Seite wurde nicht gefunden. Nutzen Sie Startseite, Suche oder Kontakt.';
$activeNav = '';

require_once __DIR__ . '/includes/header.php';
?>

<section style="padding:clamp(3rem,8vw,6rem) 0;background:var(--premium-offwhite,#f8fafc)">
    <div class="container" style="max-width:920px">
        <div style="background:#fff;border:1px solid var(--premium-border,#e2e8f0);border-radius:20px;padding:clamp(1.25rem,3vw,2.25rem)">
            <p style="margin:0 0 .4rem;color:#64748b;font-weight:700;letter-spacing:.06em;text-transform:uppercase">Fehler 404</p>
            <h1 style="margin:.2rem 0 1rem;color:var(--premium-text,#0f172a)">Diese Seite wurde leider nicht gefunden.</h1>
            <p style="margin:0 0 1.25rem;color:var(--premium-text-lt,#475569)">Kein Problem: Sie koennen direkt zu den wichtigsten Bereichen wechseln.</p>

            <form action="<?= BASE ?>/immobilien-angebote" method="GET" style="display:flex;gap:.6rem;flex-wrap:wrap;margin:0 0 1.25rem">
                <input type="text" name="location" placeholder="Region oder Stadt suchen" aria-label="Region oder Stadt" style="flex:1 1 260px;padding:.75rem .9rem;border:1px solid #cbd5e1;border-radius:10px">
                <button type="submit" class="btn-premium"><i class="fas fa-search"></i> Suche starten</button>
            </form>

            <div style="display:flex;gap:.75rem;flex-wrap:wrap;margin:0 0 1rem">
                <a href="<?= BASE ?>/" class="btn-premium"><i class="fas fa-house"></i> Zur Startseite</a>
                <a href="<?= BASE ?>/#kontakt" class="btn-outline-premium"><i class="fas fa-envelope"></i> Kontakt</a>
                <a href="<?= BASE ?>/sitemap.xml" class="btn-outline-premium"><i class="fas fa-sitemap"></i> Sitemap</a>
            </div>

            <h3 style="margin:1.25rem 0 .7rem;color:var(--premium-text,#0f172a)">Beliebte Seiten</h3>
            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:.6rem">
                <a href="<?= BASE ?>/immobilien-angebote" class="svc-card" style="text-decoration:none"><span class="svc-card-name">Immobilienangebote</span></a>
                <a href="<?= BASE ?>/immobilienbewertung" class="svc-card" style="text-decoration:none"><span class="svc-card-name">Immobilienbewertung</span></a>
                <a href="<?= BASE ?>/hausverwaltung" class="svc-card" style="text-decoration:none"><span class="svc-card-name">Hausverwaltung</span></a>
                <a href="tel:+4916098787878" class="svc-card" style="text-decoration:none"><span class="svc-card-name">Direkt anrufen</span></a>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
