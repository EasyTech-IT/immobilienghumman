<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/expose-confirmation.php';

header('Cache-Control: no-store');
header('Referrer-Policy: no-referrer');
header('X-Robots-Tag: noindex, nofollow');
$token = (string)($_POST['token'] ?? $_GET['t'] ?? '');
$headline = 'E-Mail-Adresse bestätigen';
$text = 'Bitte bestätigen Sie Ihre Exposé-Anfrage. Danach versenden wir das Exposé per E-Mail.';
$showButton = ($_SERVER['REQUEST_METHOD'] ?? '') === 'GET' && preg_match('/^[a-f0-9]{64}$/', $token);

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    try {
        if (!csrf_verify((string)($_POST['csrf_token'] ?? ''))) throw new RuntimeException('Ungültiges Sicherheitstoken.');
        $pdo = db();
        expose_automatic_ensure_tables($pdo);
        $request = expose_confirm_request($pdo, $token);
        if (!$request) {
            http_response_code(400);
            $headline = 'Link nicht mehr gültig';
            $text = 'Der Link ist ungültig, abgelaufen oder wurde bereits verwendet. Bitte kontaktieren Sie uns bei Fragen persönlich.';
        } else {
            try {
                $pdf = expose_request_snapshot($pdo, (int)$request['inquiry_id'], $request['property']);
                $body = "Guten Tag " . $request['name'] . ",\n\nvielen Dank für Ihr Interesse. Anbei erhalten Sie das Exposé zu " . $request['property']['title'] . ".\n\nFür Fragen oder einen Besichtigungstermin sind wir gerne persönlich für Sie da.\n\nFreundliche Grüße\n" . SITE_NAME . "\n" . SITE_PHONE . "\n" . SITE_EMAIL;
                $html = '<div style="background:#f3f6f9;padding:24px;font-family:Arial,sans-serif;color:#1a2d3f"><div style="max-width:600px;margin:auto;background:#fff;padding:32px">'
                    . '<p style="font-size:13px;font-weight:bold;letter-spacing:1px;color:#1a3a5c;border-bottom:1px solid #3070a0;padding-bottom:12px">IMMOBILIEN GHUMMAN</p>'
                    . '<h1 style="font-size:24px;color:#1a3a5c">Ihr Exposé</h1><p>Guten Tag ' . expose_pdf_escape($request['name']) . ',</p>'
                    . '<p>vielen Dank für Ihr Interesse. Im Anhang finden Sie das Exposé zu <strong>' . expose_pdf_escape($request['property']['title']) . '</strong>.</p>'
                    . '<p>Für Fragen oder einen Besichtigungstermin sind wir gerne persönlich für Sie da.</p>'
                    . '<p>Freundliche Grüße<br>' . expose_pdf_escape(SITE_NAME) . '<br>' . expose_pdf_escape(SITE_PHONE) . '<br>' . expose_pdf_escape(SITE_EMAIL) . '</p></div></div>';
                $sent = expose_request_deliver($pdo, (int)$request['inquiry_id'], $pdf, static fn($contents) => mailer_send(
                    $request['email'], 'Ihr Exposé – ' . SITE_NAME, $body, $html, '', '', '', ['content' => $contents]
                ));
                if (!$sent) throw new RuntimeException('SMTP-Übergabe nicht bestätigt.');
                $headline = 'Vielen Dank';
                $text = 'Ihre E-Mail-Adresse ist bestätigt. Das Exposé wurde zum Versand an den E-Mail-Server übergeben. Bitte prüfen Sie auch Ihren Spam-Ordner.';
            } catch (Throwable $e) {
                error_log('Automatischer Exposé-Versand: ' . $e->getMessage());
                if (!app_mail_text(MAIL_TO, 'Exposé-Versand benötigt Prüfung – ' . SITE_NAME, 'Bei Exposé-Anfrage #' . (int)$request['inquiry_id'] . ' ist ein Fehler aufgetreten. Bitte prüfen Sie den Status und den Postausgang im Dashboard.')) {
                    error_log('Exposé-Fehlerbenachrichtigung konnte nicht gesendet werden.');
                }
                http_response_code(503);
                $headline = 'Versand wird geprüft';
                $text = 'Ihre E-Mail-Adresse wurde bestätigt, aber der Exposé-Versand konnte nicht bestätigt werden. Bitte kontaktieren Sie uns, bevor Sie eine neue Anfrage senden.';
            }
        }
    } catch (Throwable $e) {
        error_log('Exposé-Bestätigung: ' . $e->getMessage());
        http_response_code(503);
        $headline = 'Bestätigung derzeit nicht möglich';
        $text = 'Das Exposé ist derzeit nicht verfügbar oder die Bestätigung konnte nicht verarbeitet werden. Bitte kontaktieren Sie uns persönlich.';
    }
} elseif (!$showButton) {
    http_response_code(400);
    $headline = 'Link ungültig';
    $text = 'Dieser Bestätigungslink ist ungültig. Bitte kontaktieren Sie uns persönlich.';
}

$pageTitle = $headline . ' | ' . SITE_NAME;
$pageDescription = 'Bestätigung Ihrer Exposé-Anfrage.';
$robotsContent = 'noindex, nofollow';
$extraHead = '<meta name="referrer" content="no-referrer">';
$activeNav = '';
require_once __DIR__ . '/includes/header.php';
?>
<main style="padding:9rem 1rem 4rem;background:#f4f6f9;min-height:60vh;">
    <div style="max-width:520px;margin:0 auto;background:#fff;border:1px solid #e5e7eb;border-radius:16px;padding:2.2rem 1.6rem;text-align:center;">
        <h1 style="font-size:1.5rem;margin:0 0 .8rem;color:#1a3a5c;"><?= htmlspecialchars($headline, ENT_QUOTES) ?></h1>
        <p style="color:#374151;line-height:1.7;margin:0 0 1.5rem;"><?= htmlspecialchars($text, ENT_QUOTES) ?></p>
        <?php if ($showButton): ?>
            <form method="post" action="<?= BASE ?>/expose-confirm">
                <?= csrf_field() ?>
                <input type="hidden" name="token" value="<?= htmlspecialchars($token, ENT_QUOTES) ?>">
                <button type="submit" class="btn btn-primary">E-Mail-Adresse bestätigen und Exposé erhalten</button>
            </form>
        <?php else: ?>
            <a href="<?= BASE ?>/immobilien-angebote">Aktuelle Immobilien ansehen</a>
        <?php endif; ?>
    </div>
</main>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
