<?php

/**
 * Immobilien-Benachrichtigungen (Double-Opt-In)
 * ==============================================
 * - Anfragesteller können beim Absenden zustimmen, über neue Objekte per E-Mail informiert zu werden.
 * - Aktiv wird die Zustimmung erst nach Klick auf den Bestätigungslink (Double-Opt-In, DSGVO/UWG).
 * - Beim Veröffentlichen einer Immobilie im Admin gehen die Mails nur an bestätigte Abonnenten.
 * - Jede Mail enthält einen Abmeldelink; pro Immobilie und Abonnent wird nur einmal gesendet.
 */
require_once __DIR__ . '/config.php';

function subscribers_ensure_tables(PDO $pdo): void
{
    static $done = false;
    if ($done) {
        return;
    }
    $pdo->exec("CREATE TABLE IF NOT EXISTS subscribers (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        email VARCHAR(190) NOT NULL,
        name VARCHAR(190) NOT NULL DEFAULT '',
        status ENUM('pending','confirmed','unsubscribed') NOT NULL DEFAULT 'pending',
        confirm_token CHAR(48) NULL,
        unsubscribe_token CHAR(48) NOT NULL,
        source VARCHAR(120) NOT NULL DEFAULT '',
        consent_ip VARCHAR(64) NOT NULL DEFAULT '',
        consent_text VARCHAR(255) NOT NULL DEFAULT '',
        consent_at DATETIME NOT NULL,
        confirmed_at DATETIME NULL,
        confirmed_ip VARCHAR(64) NOT NULL DEFAULT '',
        unsubscribed_at DATETIME NULL,
        UNIQUE KEY uq_email (email),
        UNIQUE KEY uq_unsub (unsubscribe_token),
        KEY idx_confirm (confirm_token),
        KEY idx_status (status)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    $pdo->exec("CREATE TABLE IF NOT EXISTS property_notifications (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        property_id INT UNSIGNED NOT NULL,
        subscriber_id INT UNSIGNED NOT NULL,
        sent_at DATETIME NOT NULL,
        UNIQUE KEY uq_prop_sub (property_id, subscriber_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    $done = true;
}

function subscribers_token(): string
{
    return bin2hex(random_bytes(24));
}

function subscribers_consent_text(): string
{
    return 'Ich möchte per E-Mail über neue Immobilienangebote von Immobilien Ghumman informiert werden (jederzeit widerrufbar).';
}

/**
 * Legt eine Anmeldung an und verschickt die Bestätigungsmail (Double-Opt-In).
 * @return string 'pending' | 'already' | 'error'
 */
function subscribers_request_optin(PDO $pdo, string $email, string $name, string $ip, string $source): string
{
    subscribers_ensure_tables($pdo);
    $email = mb_strtolower(trim($email));
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return 'error';
    }

    $stmt = $pdo->prepare('SELECT id, status, unsubscribe_token FROM subscribers WHERE email = ? LIMIT 1');
    $stmt->execute([$email]);
    $row = $stmt->fetch();

    $confirmToken = subscribers_token();
    if ($row) {
        if ($row['status'] === 'confirmed') {
            return 'already';
        }
        $pdo->prepare("UPDATE subscribers SET status='pending', name=?, confirm_token=?, source=?, consent_ip=?, consent_text=?, consent_at=NOW(), unsubscribed_at=NULL WHERE id=?")
            ->execute([$name, $confirmToken, $source, $ip, subscribers_consent_text(), $row['id']]);
    } else {
        $pdo->prepare("INSERT INTO subscribers (email,name,status,confirm_token,unsubscribe_token,source,consent_ip,consent_text,consent_at) VALUES (?,?,'pending',?,?,?,?,?,NOW())")
            ->execute([$email, $name, $confirmToken, subscribers_token(), $source, $ip, subscribers_consent_text()]);
    }

    $url = rtrim(SITE_URL, '/') . '/newsletter?action=confirm&t=' . $confirmToken;
    $ok = app_mail_html($email, 'Bitte bestätigen Sie Ihre Anmeldung – ' . SITE_NAME, build_optin_email_html($name, $url));
    return $ok ? 'pending' : 'error';
}

function subscribers_confirm(PDO $pdo, string $token, string $ip): ?array
{
    subscribers_ensure_tables($pdo);
    if (!preg_match('/^[a-f0-9]{48}$/', $token)) {
        return null;
    }
    $stmt = $pdo->prepare("SELECT id, email, name, status FROM subscribers WHERE confirm_token = ? LIMIT 1");
    $stmt->execute([$token]);
    $row = $stmt->fetch();
    if (!$row) {
        return null;
    }
    if ($row['status'] !== 'confirmed') {
        $pdo->prepare("UPDATE subscribers SET status='confirmed', confirmed_at=NOW(), confirmed_ip=?, confirm_token=NULL WHERE id=?")
            ->execute([$ip, $row['id']]);
    }
    return $row;
}

function subscribers_unsubscribe(PDO $pdo, string $token): ?array
{
    subscribers_ensure_tables($pdo);
    if (!preg_match('/^[a-f0-9]{48}$/', $token)) {
        return null;
    }
    $stmt = $pdo->prepare('SELECT id, email, name FROM subscribers WHERE unsubscribe_token = ? LIMIT 1');
    $stmt->execute([$token]);
    $row = $stmt->fetch();
    if (!$row) {
        return null;
    }
    $pdo->prepare("UPDATE subscribers SET status='unsubscribed', unsubscribed_at=NOW(), confirm_token=NULL WHERE id=?")
        ->execute([$row['id']]);
    return $row;
}

/**
 * Sendet die Neuigkeits-Mail für eine Immobilie an alle bestätigten Abonnenten (einmal pro Abonnent).
 * @return array{sent:int, failed:int, total:int}
 */
function subscribers_notify_property(PDO $pdo, int $propertyId): array
{
    subscribers_ensure_tables($pdo);
    $result = ['sent' => 0, 'failed' => 0, 'total' => 0];

    $stmt = $pdo->prepare('SELECT id, title, slug, short_description, type, offer_type, price, area, rooms, location, zip, main_image, status FROM properties WHERE id = ? LIMIT 1');
    $stmt->execute([$propertyId]);
    $property = $stmt->fetch();
    if (!$property || $property['status'] !== 'available') {
        return $result;
    }

    $subs = $pdo->prepare("SELECT s.id, s.email, s.name, s.unsubscribe_token FROM subscribers s
        LEFT JOIN property_notifications n ON n.subscriber_id = s.id AND n.property_id = ?
        WHERE s.status = 'confirmed' AND n.id IS NULL");
    $subs->execute([$propertyId]);
    $recipients = $subs->fetchAll();
    $result['total'] = count($recipients);

    foreach ($recipients as $r) {
        // Vorab eintragen: verhindert Doppelversand bei parallelen Speichervorgängen
        $ins = $pdo->prepare('INSERT IGNORE INTO property_notifications (property_id, subscriber_id, sent_at) VALUES (?,?,NOW())');
        $ins->execute([$propertyId, $r['id']]);
        if ($ins->rowCount() === 0) {
            continue;
        }

        $unsubUrl = rtrim(SITE_URL, '/') . '/newsletter?action=unsubscribe&t=' . $r['unsubscribe_token'];
        $subject = 'Neu: ' . $property['title'] . ' – ' . SITE_NAME;
        $ok = app_mail_html($r['email'], $subject, build_property_notification_html($property, (string)$r['name'], $unsubUrl));
        if ($ok) {
            $result['sent']++;
        } else {
            $result['failed']++;
            $pdo->prepare('DELETE FROM property_notifications WHERE property_id=? AND subscriber_id=?')->execute([$propertyId, $r['id']]);
        }
        usleep(250000);
    }
    return $result;
}

function subscribers_mail_shell(string $title, string $bodyHtml, string $footerExtra = ''): string
{
    $logo = htmlspecialchars(SITE_LOGO, ENT_QUOTES);
    $site = htmlspecialchars(SITE_NAME, ENT_QUOTES);
    $url  = rtrim(htmlspecialchars(SITE_URL, ENT_QUOTES), '/');
    $phone = htmlspecialchars(SITE_PHONE, ENT_QUOTES);
    $titleSafe = htmlspecialchars($title, ENT_QUOTES);

    return '<!DOCTYPE html><html lang="de"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>' . $titleSafe . '</title></head>'
        . '<body style="margin:0;padding:0;background:#f1f5f9;font-family:\'Helvetica Neue\',Helvetica,Arial,sans-serif;">'
        . '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f1f5f9;"><tr><td align="center" style="padding:28px 12px;">'
        . '<table role="presentation" width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;background:#fff;border:1px solid #e2e8f0;border-radius:16px;overflow:hidden;">'
        . '<tr><td style="background:#1a3a6b;padding:22px;text-align:center;"><img src="' . $logo . '" alt="' . $site . '" width="140" style="display:block;margin:0 auto;max-width:140px;height:auto;"></td></tr>'
        . '<tr><td style="padding:28px 28px 8px;">' . $bodyHtml . '</td></tr>'
        . '<tr><td style="padding:18px 28px 26px;border-top:1px solid #e2e8f0;text-align:center;font-size:12px;line-height:1.6;color:#64748b;">'
        . '<strong style="color:#334155;">' . $site . '</strong> · Bad Vilbel &amp; Rhein-Main<br>'
        . '<a href="tel:+4916098787878" style="color:#64748b;">' . $phone . '</a> · <a href="mailto:' . htmlspecialchars(SITE_EMAIL, ENT_QUOTES) . '" style="color:#64748b;">' . htmlspecialchars(SITE_EMAIL, ENT_QUOTES) . '</a><br>'
        . '<a href="' . $url . '/impressum" style="color:#64748b;">Impressum</a> · <a href="' . $url . '/datenschutz" style="color:#64748b;">Datenschutz</a>'
        . $footerExtra
        . '</td></tr></table></td></tr></table></body></html>';
}

function build_optin_email_html(string $name, string $confirmUrl): string
{
    $n = htmlspecialchars($name !== '' ? $name : 'Interessent/in', ENT_QUOTES);
    $u = htmlspecialchars($confirmUrl, ENT_QUOTES);
    $consent = htmlspecialchars(subscribers_consent_text(), ENT_QUOTES);
    $body = '<h1 style="margin:0 0 12px;font-size:22px;color:#0f172a;">Fast geschafft, ' . $n . '!</h1>'
        . '<p style="margin:0 0 14px;font-size:15px;line-height:1.7;color:#334155;">Sie haben bei Ihrer Anfrage angegeben: <em>„' . $consent . '“</em></p>'
        . '<p style="margin:0 0 22px;font-size:15px;line-height:1.7;color:#334155;">Bitte bestätigen Sie Ihre E-Mail-Adresse mit einem Klick. Erst danach erhalten Sie von uns Nachrichten zu neuen Objekten.</p>'
        . '<p style="margin:0 0 22px;text-align:center;"><a href="' . $u . '" style="display:inline-block;background:#2563eb;color:#fff;font-weight:700;font-size:16px;text-decoration:none;padding:14px 28px;border-radius:10px;">Anmeldung bestätigen</a></p>'
        . '<p style="margin:0 0 6px;font-size:12px;line-height:1.6;color:#64748b;">Funktioniert der Button nicht? Kopieren Sie diesen Link in Ihren Browser:<br><a href="' . $u . '" style="color:#2563eb;word-break:break-all;">' . $u . '</a></p>'
        . '<p style="margin:10px 0 18px;font-size:12px;line-height:1.6;color:#64748b;">Sie haben sich nicht angemeldet? Dann ignorieren Sie diese E-Mail einfach – es wird nichts aktiviert.</p>';
    return subscribers_mail_shell('Anmeldung bestätigen', $body);
}

function build_property_notification_html(array $p, string $name, string $unsubUrl): string
{
    $title = htmlspecialchars((string)$p['title'], ENT_QUOTES);
    $isRent = strcasecmp((string)$p['offer_type'], 'miete') === 0;
    $price = ((float)$p['price'] > 0)
        ? '€ ' . number_format((float)$p['price'], 0, ',', '.') . ($isRent ? ' / Monat' : '')
        : 'Preis auf Anfrage';
    $place = trim(((string)($p['zip'] ?? '')) . ' ' . ((string)($p['location'] ?? '')));
    $link = rtrim(SITE_URL, '/') . '/immobilie/' . rawurlencode((string)$p['slug']);
    $linkSafe = htmlspecialchars($link, ENT_QUOTES);

    $facts = [];
    if (!empty($p['area']) && (float)$p['area'] > 0) {
        $facts[] = rtrim(rtrim(number_format((float)$p['area'], 1, ',', '.'), '0'), ',') . ' m²';
    }
    if (!empty($p['rooms']) && (float)$p['rooms'] > 0) {
        $facts[] = rtrim(rtrim(number_format((float)$p['rooms'], 1, ',', '.'), '0'), ',') . ' Zimmer';
    }
    if ($place !== '') {
        $facts[] = $place;
    }
    $factsHtml = $facts ? '<p style="margin:0 0 14px;font-size:14px;color:#475569;">' . htmlspecialchars(implode('  ·  ', $facts), ENT_QUOTES) . '</p>' : '';

    $img = '';
    $mainImage = trim((string)($p['main_image'] ?? ''));
    if ($mainImage !== '' && preg_match('#^https://#i', $mainImage)) {
        $img = '<a href="' . $linkSafe . '"><img src="' . htmlspecialchars($mainImage, ENT_QUOTES) . '" alt="' . $title . '" width="544" style="display:block;width:100%;max-width:544px;height:auto;border-radius:12px;margin:0 0 16px;"></a>';
    }

    $desc = trim((string)($p['short_description'] ?? ''));
    if (mb_strlen($desc) > 220) {
        $desc = mb_substr($desc, 0, 217) . '…';
    }
    $descHtml = $desc !== '' ? '<p style="margin:0 0 18px;font-size:15px;line-height:1.7;color:#334155;">' . htmlspecialchars($desc, ENT_QUOTES) . '</p>' : '';

    $hello = $name !== '' ? 'Hallo ' . htmlspecialchars($name, ENT_QUOTES) . ',' : 'Hallo,';
    $badge = $isRent ? 'Neu zur Miete' : 'Neu zum Kauf';

    $body = '<p style="margin:0 0 6px;font-size:15px;color:#334155;">' . $hello . '</p>'
        . '<p style="margin:0 0 16px;font-size:15px;color:#334155;">wir haben eine neue Immobilie für Sie:</p>'
        . $img
        . '<p style="margin:0 0 6px;"><span style="display:inline-block;background:#dbeafe;color:#1e40af;font-size:11px;font-weight:700;letter-spacing:.05em;text-transform:uppercase;padding:4px 10px;border-radius:999px;">' . $badge . '</span></p>'
        . '<h1 style="margin:0 0 6px;font-size:21px;line-height:1.3;color:#0f172a;">' . $title . '</h1>'
        . '<p style="margin:0 0 10px;font-size:22px;font-weight:800;color:#1a3a6b;">' . htmlspecialchars($price, ENT_QUOTES) . '</p>'
        . $factsHtml . $descHtml
        . '<p style="margin:0 0 22px;text-align:center;"><a href="' . $linkSafe . '" style="display:inline-block;background:#2563eb;color:#fff;font-weight:700;font-size:16px;text-decoration:none;padding:14px 28px;border-radius:10px;">Exposé ansehen</a></p>'
        . '<p style="margin:0 0 18px;font-size:14px;line-height:1.6;color:#475569;">Fragen oder Besichtigungswunsch? Antworten Sie einfach auf diese E-Mail oder rufen Sie uns an: <a href="tel:+4916098787878" style="color:#2563eb;">' . htmlspecialchars(SITE_PHONE, ENT_QUOTES) . '</a></p>';

    $footer = '<br><br>Sie erhalten diese E-Mail, weil Sie der Benachrichtigung über neue Immobilien zugestimmt haben.<br>'
        . '<a href="' . htmlspecialchars($unsubUrl, ENT_QUOTES) . '" style="color:#64748b;text-decoration:underline;">Benachrichtigungen abbestellen</a>';
    return subscribers_mail_shell((string)$p['title'], $body, $footer);
}
