<?php

/**
 * Immobilien Ghumman – PHP Konfiguration
 * =======================================
 * Zentrale Konfigurationsdatei
 */

// Fehlermeldungen nur in Entwicklung
$appEnv = strtolower(trim((string) getenv('IMMOBILIEN_APP_ENV')));
$isDevMode = ($appEnv === 'development' || $appEnv === 'dev' || $appEnv === 'local');

if ($isDevMode) {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
    define('IS_DEV', true);
} else {
    ini_set('display_errors', 0);
    error_reporting(0);
    define('IS_DEV', false);
}

// Basispfad: Live-Betrieb im Webroot
define('BASE', '');

// ── Lokale Overrides zuerst laden (vor allen Defaults!) ────
// Sensible Credentials NICHT in dieser Datei speichern.
// Stattdessen in includes/config.local.php ablegen (NICHT in Git!).
$localConfigFile = __DIR__ . '/config.local.php';
if (is_file($localConfigFile)) {
    require_once $localConfigFile;
}

// ── Datenbank (Fallback-Defaults – Produktion: via config.local.php) ──
if (!defined('DB_HOST'))    define('DB_HOST',    'PROD_DB_HOST_IN_LOCAL_CONFIG');
if (!defined('DB_PORT'))    define('DB_PORT',    3306);
if (!defined('DB_USER'))    define('DB_USER',    'PROD_DB_USER_IN_LOCAL_CONFIG');
if (!defined('DB_PASS'))    define('DB_PASS',    'PROD_DB_PASS_IN_LOCAL_CONFIG');
if (!defined('DB_NAME'))    define('DB_NAME',    'PROD_DB_NAME_IN_LOCAL_CONFIG');
if (!defined('DB_CHARSET')) define('DB_CHARSET', 'utf8mb4');

// ── E-Mail ─────────────────────────────────────────────────
if (!defined('MAIL_FROM'))           define('MAIL_FROM',           'info@immobilienghumman.de');
if (!defined('MAIL_FROM_NAME'))      define('MAIL_FROM_NAME',      'Immobilien Ghumman');
if (!defined('MAIL_TO'))             define('MAIL_TO',             'info@immobilienghumman.de');
if (!defined('MAIL_SUBJECT_PREFIX')) define('MAIL_SUBJECT_PREFIX', '[Immobilien Ghumman]');
if (!defined('MAIL_WEBHOOK_TOKEN'))  define('MAIL_WEBHOOK_TOKEN',  'SET_MAIL_WEBHOOK_TOKEN_IN_LOCAL_CONFIG');
if (!defined('MAILER_DRIVER'))       define('MAILER_DRIVER',       'socket_smtp');
if (!defined('SMTP_HOST'))           define('SMTP_HOST',           'smtp.ionos.de');
if (!defined('SMTP_PORT'))           define('SMTP_PORT',           587);
if (!defined('SMTP_USER'))           define('SMTP_USER',           '');
if (!defined('SMTP_PASS'))           define('SMTP_PASS',           '');
if (!defined('SMTP_SECURE'))         define('SMTP_SECURE',         'tls'); // tls|ssl|none
if (!defined('SMTP_AUTH'))           define('SMTP_AUTH',           true);
if (!defined('SMTP_TIMEOUT'))        define('SMTP_TIMEOUT',        20);

require_once __DIR__ . '/mailer.php';

// ── Cloudinary (Bild-Upload) – in config.local.php setzen! ──
if (!defined('CLOUDINARY_CLOUD_NAME')) define('CLOUDINARY_CLOUD_NAME', 'dlpdbr0ey');
if (!defined('CLOUDINARY_API_KEY'))    define('CLOUDINARY_API_KEY',    'HIER_CLOUDINARY_API_KEY_EINTRAGEN');
if (!defined('CLOUDINARY_API_SECRET')) define('CLOUDINARY_API_SECRET', 'HIER_CLOUDINARY_API_SECRET_EINTRAGEN');

// ── Sicherheit – in config.local.php setzen! ───────────────
// Auth-Token Secret (HMAC-Signierung der API-Bearer-Tokens)
if (!defined('AUTH_SECRET')) define('AUTH_SECRET',  'HIER_AUTH_SECRET_64_ZEICHEN_IN_LOCAL_CONFIG');
// Init-Token für database/init.php (einmaliges Setup)
if (!defined('INIT_TOKEN'))  define('INIT_TOKEN',   'immobilien-init-2026');

// ── Site-Einstellungen ──────────────────────────────────────
define('SITE_NAME',  'Immobilien Ghumman');
define('SITE_URL',   'https://www.immobilienghumman.de');
define('SITE_PHONE', '+49 160 98787878');
define('SITE_EMAIL', 'info@immobilienghumman.de');
define('SITE_LOGO',  'https://res.cloudinary.com/dlpdbr0ey/image/upload/v1755051245/7ae1aa7d-b239-4ee5-8090-6b8339e15d4e-removebg-preview_m6fipk.png');

// ── Cloudflare Turnstile (Spam-Schutz) ───────────────────────────────────
if (!defined('TURNSTILE_SITE_KEY'))   define('TURNSTILE_SITE_KEY', 'HIER_TURNSTILE_SITE_KEY_EINTRAGEN');
if (!defined('TURNSTILE_SECRET_KEY')) define('TURNSTILE_SECRET_KEY', 'HIER_TURNSTILE_SECRET_KEY_EINTRAGEN');
if (!defined('TURNSTILE_ENABLED')) {
    define(
        'TURNSTILE_ENABLED',
        TURNSTILE_SITE_KEY !== 'HIER_TURNSTILE_SITE_KEY_EINTRAGEN'
            && TURNSTILE_SECRET_KEY !== 'HIER_TURNSTILE_SECRET_KEY_EINTRAGEN'
            && TURNSTILE_SITE_KEY !== ''
            && TURNSTILE_SECRET_KEY !== ''
    );
}

// ── Session starten ─────────────────────────────────────────
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'secure'   => !IS_DEV,
        'httponly' => true,
        'samesite' => 'Strict',
    ]);
    session_start();
}

// ── CSRF-Token ──────────────────────────────────────────────
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
define('CSRF_TOKEN', $_SESSION['csrf_token']);

/**
 * Alias – Token als String zurückgeben (für manuelle Formulareinbettung)
 */
function generate_csrf_token(): string
{
    return CSRF_TOKEN;
}

/**
 * CSRF-Token im HTML-Formular ausgeben
 */
function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(CSRF_TOKEN, ENT_QUOTES) . '">';
}

/**
 * CSRF-Token prüfen – gibt true zurück wenn gültig, sonst false
 */
function csrf_verify(string $token = ''): bool
{
    if (empty($token)) {
        $token = $_POST['csrf_token'] ?? '';
    }
    return hash_equals(CSRF_TOKEN, $token);
}

/**
 * Validiert ein Cloudflare Turnstile Token serverseitig.
 */
function verify_turnstile_token(string $token, string $remoteIp = ''): bool
{
    if (!TURNSTILE_ENABLED) {
        return true;
    }

    if ($token === '') {
        return false;
    }

    $payload = [
        'secret'   => TURNSTILE_SECRET_KEY,
        'response' => $token,
    ];
    if ($remoteIp !== '') {
        $payload['remoteip'] = $remoteIp;
    }

    $ch = curl_init('https://challenges.cloudflare.com/turnstile/v0/siteverify');
    curl_setopt_array($ch, [
        CURLOPT_POST           => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 10,
        CURLOPT_POSTFIELDS     => http_build_query($payload),
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlErr  = curl_error($ch);
    curl_close($ch);

    if ($response === false || $httpCode < 200 || $httpCode >= 300) {
        error_log('Turnstile verify HTTP error: ' . $httpCode . ' ' . $curlErr);
        return false;
    }

    $decoded = json_decode($response, true);
    if (!is_array($decoded) || empty($decoded['success'])) {
        error_log('Turnstile verify failed: ' . $response);
        return false;
    }

    return true;
}

/**
 * Eingabe bereinigen
 */
function clean(string $value): string
{
    return htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
}

/**
 * Einheitlicher Versand ueber zentrale Socket-SMTP-Mailer-Klasse.
 */
function mailer_send(string $to, string $subject, string $textBody = '', string $htmlBody = '', string $replyTo = '', string $fromEmail = '', string $fromName = ''): bool
{
    $from = $fromEmail ?: MAIL_FROM;
    $name = $fromName ?: MAIL_FROM_NAME;

    if (MAILER_DRIVER !== 'socket_smtp') {
        error_log('MAILER_DRIVER nicht unterstuetzt: ' . MAILER_DRIVER);
        return false;
    }

    $mailer = new SimpleSocketMailer(
        SMTP_HOST,
        (int)SMTP_PORT,
        SMTP_USER,
        SMTP_PASS,
        SMTP_SECURE,
        (bool)SMTP_AUTH,
        (int)SMTP_TIMEOUT
    );

    return $mailer->send($from, $name, $to, $subject, $textBody, $htmlBody, $replyTo);
}

function app_mail_text(string $to, string $subject, string $textBody, string $replyTo = ''): bool
{
    return mailer_send($to, $subject, $textBody, '', $replyTo);
}

function app_mail_html(string $to, string $subject, string $htmlBody, string $fromEmail = '', string $fromName = ''): bool
{
    return mailer_send($to, $subject, '', $htmlBody, '', $fromEmail, $fromName);
}

// Rückwärtskompatibilität für bestehende Aufrufe.
function sendgrid_mail(string $to, string $subject, string $textBody, string $replyTo = ''): bool
{
    return app_mail_text($to, $subject, $textBody, $replyTo);
}

function sendgrid_html_mail(string $to, string $subject, string $htmlBody, string $fromEmail = '', string $fromName = ''): bool
{
    return app_mail_html($to, $subject, $htmlBody, $fromEmail, $fromName);
}

/**
 * Erstellt das HTML-Bestätigungs-E-Mail-Template für Anfragesteller.
 */
function get_confirmation_email_html(string $name, string $formType = 'Kontaktanfrage', bool $optinPending = false): string
{
    $logoUrl  = htmlspecialchars(SITE_LOGO,  ENT_QUOTES);
    $siteUrl  = rtrim(htmlspecialchars(SITE_URL, ENT_QUOTES), '/');
    $phone    = htmlspecialchars(SITE_PHONE, ENT_QUOTES);
    $siteName = htmlspecialchars(SITE_NAME,  ENT_QUOTES);
    $nameSafe = htmlspecialchars($name,      ENT_QUOTES);
    $typeSafe = htmlspecialchars($formType,  ENT_QUOTES);

    ob_start(); ?>
    <!DOCTYPE html>
    <html lang="de">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width,initial-scale=1">
        <title>Bestätigung Ihrer Anfrage</title>
    </head>

    <body style="margin:0;padding:0;background:#f1f5f9;font-family:'Helvetica Neue',Helvetica,Arial,sans-serif;">
        <div style="display:none;max-height:0;overflow:hidden;mso-hide:all;">Vielen Dank für Ihre Nachricht &#8203;</div>
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f1f5f9;">
            <tr>
                <td align="center" style="padding:40px 16px;">
                    <table role="presentation" width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;">

                        <tr>
                            <td style="background:linear-gradient(135deg,#1a3a6b 0%,#2563eb 100%);border-radius:16px 16px 0 0;padding:36px 40px 28px;text-align:center;">
                                <img src="<?= $logoUrl ?>" alt="<?= $siteName ?>" width="160" style="display:block;margin:0 auto;max-width:160px;height:auto;">
                                <p style="margin:14px 0 0;color:rgba(255,255,255,.6);font-size:.78rem;letter-spacing:.1em;text-transform:uppercase;">Ihr Immobilienmakler &middot; Rhein-Main-Gebiet</p>
                            </td>
                        </tr>

                        <tr>
                            <td style="background:#fff;border-left:1px solid #e2e8f0;border-right:1px solid #e2e8f0;padding:36px 40px 4px;text-align:center;">
                                <div style="display:inline-block;width:68px;height:68px;background:#dcfce7;border-radius:50%;border:3px solid #bbf7d0;line-height:62px;font-size:1.8rem;color:#16a34a;">&#10003;</div>
                                <h1 style="margin:20px 0 8px;font-size:1.5rem;font-weight:700;color:#0f172a;">Anfrage erhalten!</h1>
                                <p style="margin:0;color:#64748b;font-size:.95rem;">Ihre Nachricht ist bei uns eingegangen.</p>
                            </td>
                        </tr>

                        <tr>
                            <td style="background:#fff;padding:0 40px;border-left:1px solid #e2e8f0;border-right:1px solid #e2e8f0;">
                                <hr style="border:none;border-top:1px solid #e2e8f0;margin:24px 0;">
                            </td>
                        </tr>

                        <tr>
                            <td style="background:#fff;padding:0 40px 28px;border-left:1px solid #e2e8f0;border-right:1px solid #e2e8f0;">
                                <p style="margin:0 0 16px;color:#1e293b;font-size:1rem;line-height:1.75;">Sehr geehrte/r <strong><?= $nameSafe ?></strong>,</p>
                                <p style="margin:0 0 16px;color:#334155;font-size:1rem;line-height:1.75;">vielen Dank für Ihre Anfrage bei <strong><?= $siteName ?></strong>. Ihre Nachricht ist erfolgreich eingegangen und wird schnellstmöglich bearbeitet.</p>
                                <p style="margin:0 0 24px;color:#334155;font-size:1rem;line-height:1.75;">Unser Team meldet sich in der Regel <strong>innerhalb von 24&ndash;48&nbsp;Stunden</strong> an Werktagen bei Ihnen.</p>
                                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#eff6ff;border-radius:12px;border-left:4px solid #2563eb;margin:0 0 24px;">
                                    <tr>
                                        <td style="padding:14px 20px;">
                                            <p style="margin:0 0 3px;color:#1e40af;font-size:.8rem;font-weight:700;text-transform:uppercase;letter-spacing:.05em;">Ihre Anfrage</p>
                                            <p style="margin:0;color:#1d4ed8;font-size:.92rem;"><?= $typeSafe ?></p>
                                        </td>
                                    </tr>
                                </table>
                                <?php if ($optinPending): ?>
                                    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#fefce8;border-radius:12px;border-left:4px solid #eab308;margin:0 0 24px;">
                                        <tr>
                                            <td style="padding:14px 20px;">
                                                <p style="margin:0 0 3px;color:#854d0e;font-size:.8rem;font-weight:700;text-transform:uppercase;letter-spacing:.05em;">Noch ein Klick</p>
                                                <p style="margin:0;color:#713f12;font-size:.92rem;line-height:1.6;">Sie möchten über neue Immobilien informiert werden. Wir haben Ihnen dafür eine separate E-Mail zur Bestätigung geschickt. Erst nach Ihrem Klick auf den Link erhalten Sie Benachrichtigungen.</p>
                                            </td>
                                        </tr>
                                    </table>
                                <?php endif; ?>
                                <p style="margin:0 0 14px;color:#334155;font-size:.97rem;line-height:1.7;">Bei dringenden Anliegen erreichen Sie uns direkt:</p>
                                <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td style="padding-right:6px;width:50%;"><a href="tel:+4916098787878" style="display:block;background:#2563eb;color:#fff;font-weight:700;font-size:.88rem;text-decoration:none;text-align:center;padding:12px;border-radius:10px;">&#128222;&nbsp; <?= $phone ?></a></td>
                                        <td style="padding-left:6px;width:50%;"><a href="https://wa.me/4916098787878" style="display:block;background:#25d366;color:#fff;font-weight:700;font-size:.88rem;text-decoration:none;text-align:center;padding:12px;border-radius:10px;">&#128172;&nbsp; WhatsApp</a></td>
                                    </tr>
                                </table>
                            </td>
                        </tr>

                        <tr>
                            <td style="background:#fff;padding:0 40px;border-left:1px solid #e2e8f0;border-right:1px solid #e2e8f0;">
                                <hr style="border:none;border-top:1px solid #e2e8f0;margin:28px 0 0;">
                            </td>
                        </tr>

                        <tr>
                            <td style="background:#fff;padding:20px 40px 36px;border-left:1px solid #e2e8f0;border-right:1px solid #e2e8f0;">
                                <p style="margin:0 0 4px;color:#334155;font-size:1rem;line-height:1.75;">Vielen Dank für Ihr Vertrauen.</p>
                                <p style="margin:0 0 2px;color:#334155;font-size:1rem;">Mit freundlichen Grüßen</p>
                                <p style="margin:0;font-size:1.05rem;font-weight:700;color:#1a3a6b;"><?= $siteName ?></p>
                            </td>
                        </tr>

                        <tr>
                            <td style="background:#1a2d3f;border-radius:0 0 16px 16px;padding:26px 40px;text-align:center;">
                                <p style="margin:0 0 5px;color:#fff;font-size:.97rem;font-weight:700;"><?= $siteName ?></p>
                                <p style="margin:0 0 12px;color:rgba(255,255,255,.5);font-size:.8rem;">Bad Vilbel &amp; Rhein-Main-Gebiet</p>
                                <p style="margin:0 0 12px;font-size:.8rem;">
                                    <a href="tel:+4916098787878" style="color:rgba(255,255,255,.65);text-decoration:none;"><?= $phone ?></a>
                                    &nbsp;&middot;&nbsp;
                                    <a href="mailto:info@immobilienghumman.de" style="color:rgba(255,255,255,.65);text-decoration:none;">info@immobilienghumman.de</a>
                                    &nbsp;&middot;&nbsp;
                                    <a href="<?= $siteUrl ?>" style="color:rgba(255,255,255,.65);text-decoration:none;">www.immobilienghumman.de</a>
                                </p>
                                <p style="margin:0;font-size:.7rem;color:rgba(255,255,255,.3);">
                                    <a href="<?= $siteUrl ?>/impressum" style="color:rgba(255,255,255,.35);text-decoration:none;">Impressum</a>
                                    &nbsp;&middot;&nbsp;
                                    <a href="<?= $siteUrl ?>/datenschutz" style="color:rgba(255,255,255,.35);text-decoration:none;">Datenschutz</a>
                                </p>
                            </td>
                        </tr>

                    </table>
                </td>
            </tr>
        </table>
    </body>

    </html>
<?php
    return ob_get_clean();
}
