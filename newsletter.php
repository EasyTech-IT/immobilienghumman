<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/subscribers.php';

$action = (string)($_GET['action'] ?? '');
$token  = (string)($_GET['t'] ?? '');
$ip     = $_SERVER['HTTP_CF_CONNECTING_IP'] ?? ($_SERVER['REMOTE_ADDR'] ?? '');

$icon = 'fa-circle-info';
$tone = '#2563eb';
$headline = 'Benachrichtigungen';
$text = 'Dieser Link ist ungültig oder abgelaufen.';

$isPost = ($_SERVER['REQUEST_METHOD'] ?? '') === 'POST';
$showButton = '';

try {
    $pdo = db();
    if (!$isPost && in_array($action, ['confirm', 'unsubscribe'], true) && preg_match('/^[a-f0-9]{48}$/', $token)) {
        // Erst auf Klick ausführen, damit Mail-Scanner den Link nicht automatisch auslösen
        $showButton = $action === 'confirm' ? 'Anmeldung jetzt bestätigen' : 'Jetzt abbestellen';
        $headline = $action === 'confirm' ? 'Anmeldung bestätigen' : 'Abbestellen';
        $text = $action === 'confirm'
            ? 'Bitte bestätigen Sie, dass wir Sie über neue Immobilien per E-Mail informieren dürfen.'
            : 'Möchten Sie keine Benachrichtigungen über neue Immobilien mehr erhalten?';
    } elseif ($action === 'confirm') {
        $row = subscribers_confirm($pdo, $token, $ip);
        if ($row) {
            $icon = 'fa-circle-check';
            $tone = '#16a34a';
            $headline = 'Anmeldung bestätigt';
            $text = 'Vielen Dank! Wir informieren Sie ab jetzt per E-Mail, sobald wir eine neue Immobilie veröffentlichen. Abbestellen ist jederzeit über den Link in jeder Mail möglich.';
        } else {
            $text = 'Dieser Bestätigungslink ist ungültig oder wurde bereits verwendet.';
        }
    } elseif ($action === 'unsubscribe') {
        $row = subscribers_unsubscribe($pdo, $token);
        if ($row) {
            $icon = 'fa-circle-check';
            $tone = '#16a34a';
            $headline = 'Abgemeldet';
            $text = 'Sie erhalten keine Benachrichtigungen über neue Immobilien mehr. Schade, dass Sie gehen – Sie können sich jederzeit wieder anmelden.';
        } else {
            $text = 'Dieser Abmeldelink ist ungültig.';
        }
    }
} catch (Throwable $e) {
    error_log('newsletter page: ' . $e->getMessage());
    $text = 'Es ist ein Fehler aufgetreten. Bitte versuchen Sie es später erneut oder schreiben Sie uns an ' . SITE_EMAIL . '.';
}

$pageTitle       = $headline . ' | ' . SITE_NAME;
$pageDescription = 'Benachrichtigungen über neue Immobilien von Immobilien Ghumman.';
$robotsContent   = 'noindex, nofollow';
$activeNav       = '';
require_once __DIR__ . '/includes/header.php';
?>
<main style="padding:9rem 1rem 4rem;background:#f4f6f9;min-height:60vh;">
    <div style="max-width:520px;margin:0 auto;background:#fff;border:1px solid #e5e7eb;border-radius:16px;padding:2.2rem 1.6rem;text-align:center;box-shadow:0 8px 28px rgba(17,24,39,.06);">
        <div style="font-size:3rem;color:<?= $tone ?>;line-height:1;"><i class="fas <?= $icon ?>"></i></div>
        <h1 style="font-size:1.5rem;margin:1rem 0 .6rem;color:#111827;"><?= htmlspecialchars($headline, ENT_QUOTES) ?></h1>
        <p style="color:#4b5563;line-height:1.7;margin:0 0 1.5rem;"><?= htmlspecialchars($text, ENT_QUOTES) ?></p>
        <?php if ($showButton !== ''): ?>
            <form method="post" action="<?= BASE ?>/newsletter?action=<?= htmlspecialchars($action, ENT_QUOTES) ?>&amp;t=<?= htmlspecialchars($token, ENT_QUOTES) ?>">
                <button type="submit" style="border:0;cursor:pointer;background:#2563eb;color:#fff;font-weight:700;font-size:1rem;padding:.85rem 1.6rem;border-radius:10px;"><?= htmlspecialchars($showButton, ENT_QUOTES) ?></button>
            </form>
        <?php else: ?>
            <a href="<?= BASE ?>/immobilien-angebote" style="display:inline-block;background:#2563eb;color:#fff;font-weight:700;text-decoration:none;padding:.8rem 1.4rem;border-radius:10px;">Aktuelle Immobilien ansehen</a>
        <?php endif; ?>
    </div>
</main>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
