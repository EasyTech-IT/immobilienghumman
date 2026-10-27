<?php
require_once __DIR__ . '/includes/config.php';

http_response_code(500);

$pageTitle = 'Serverfehler (500) | Immobilien Ghumman';
$pageDescription = 'Es ist ein technischer Fehler aufgetreten. Laden Sie die Seite neu oder kontaktieren Sie uns.';
$activeNav = '';

require_once __DIR__ . '/includes/header.php';
?>

<section style="padding:clamp(3rem,8vw,6rem) 0;background:var(--premium-offwhite,#f8fafc)">
    <div class="container" style="max-width:920px">
        <div style="background:#fff;border:1px solid var(--premium-border,#e2e8f0);border-radius:20px;padding:clamp(1.25rem,3vw,2.25rem)">
            <p style="margin:0 0 .4rem;color:#64748b;font-weight:700;letter-spacing:.06em;text-transform:uppercase">Fehler 500</p>
            <h1 style="margin:.2rem 0 1rem;color:var(--premium-text,#0f172a)">Technisches Problem auf dem Server.</h1>
            <p style="margin:0 0 1.25rem;color:var(--premium-text-lt,#475569)">Bitte laden Sie die Seite erneut. Falls das Problem bleibt, helfen wir Ihnen direkt weiter.</p>

            <div style="display:flex;gap:.75rem;flex-wrap:wrap;margin:0 0 1rem">
                <a href="" onclick="window.location.reload();return false;" class="btn-premium"><i class="fas fa-rotate-right"></i> Neu laden</a>
                <a href="<?= BASE ?>/" class="btn-outline-premium"><i class="fas fa-house"></i> Startseite</a>
                <a href="<?= BASE ?>/#kontakt" class="btn-outline-premium"><i class="fas fa-envelope"></i> Kontakt</a>
                <a href="tel:+4916098787878" class="btn-outline-premium"><i class="fas fa-phone"></i> Anrufen</a>
            </div>

            <div style="padding:.85rem 1rem;background:#eff6ff;border:1px solid #bfdbfe;border-radius:10px;color:#1e3a8a">
                Status: Wir arbeiten an der Stabilitaet. Versuchen Sie es in 1-2 Minuten erneut.
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
