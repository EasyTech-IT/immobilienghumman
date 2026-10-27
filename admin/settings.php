<?php

/**
 * admin/settings.php – Einstellungen, Passwort ändern, CSV-Export
 */
require_once __DIR__ . '/_auth_check.php';

$pdo     = db();
$msg     = '';
$msgType = '';

// ── CSV-Export Anfragen ───────────────────────────────────────
if (isset($_GET['export']) && $_GET['export'] === 'inquiries') {
    if (!csrf_verify($_GET['token'] ?? '')) {
        die('Ungültiges Sicherheitstoken.');
    }
    $rows = $pdo->query(
        'SELECT id, name, email, phone, message, status, created_at FROM contact_inquiries ORDER BY created_at DESC'
    )->fetchAll(PDO::FETCH_ASSOC);
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="anfragen-' . date('Y-m-d') . '.csv"');
    $out = fopen('php://output', 'w');
    // UTF-8 BOM für Excel
    fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF));
    fputcsv($out, ['ID', 'Name', 'E-Mail', 'Telefon', 'Nachricht', 'Status', 'Datum'], ';');
    foreach ($rows as $r) {
        fputcsv($out, array_values($r), ';');
    }
    fclose($out);
    exit;
}

// ── Passwort ändern ───────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    if (!csrf_verify($_POST['csrf_token'] ?? '')) {
        $msg = 'Ungültiges Sicherheitstoken.';
        $msgType = 'error';
    } else {
        $current = $_POST['current_password'] ?? '';
        $new     = $_POST['new_password']     ?? '';
        $confirm = $_POST['confirm_password'] ?? '';

        $stmt = $pdo->prepare('SELECT password_hash FROM admin_users WHERE id = ?');
        $stmt->execute([$admin['id']]);
        $row = $stmt->fetch();

        if (!password_verify($current, $row['password_hash'])) {
            $msg = 'Aktuelles Passwort ist falsch.';
            $msgType = 'error';
        } elseif (strlen($new) < 8) {
            $msg = 'Neues Passwort muss mindestens 8 Zeichen lang sein.';
            $msgType = 'error';
        } elseif ($new !== $confirm) {
            $msg = 'Neue Passw&ouml;rter stimmen nicht &uuml;berein.';
            $msgType = 'error';
        } else {
            $pdo->prepare('UPDATE admin_users SET password_hash = ? WHERE id = ?')
                ->execute([password_hash($new, PASSWORD_DEFAULT), $admin['id']]);
            $msg = 'Passwort erfolgreich ge&auml;ndert.';
            $msgType = 'success';
        }
    }
}

// ── Konfigurationsstatus prüfen ───────────────────────────────
$configOk = [
    'smtp'       => defined('SMTP_HOST')
        && !empty(SMTP_HOST)
        && defined('SMTP_PORT')
        && (int)SMTP_PORT > 0,
    'cloudinary' => defined('CLOUDINARY_CLOUD_NAME') && !empty(CLOUDINARY_CLOUD_NAME),
    'auth'       => defined('AUTH_SECRET')
        && AUTH_SECRET !== 'change-me-to-a-random-64-char-string-in-production',
];

$adminUsers   = $pdo->query('SELECT id, username, role, is_active, last_login, created_at FROM admin_users ORDER BY id')->fetchAll();
$inquiryCount = (int)$pdo->query('SELECT COUNT(*) FROM contact_inquiries')->fetchColumn();
$newInquiries = (int)$pdo->query("SELECT COUNT(*) FROM contact_inquiries WHERE status='new'")->fetchColumn();
$propCount    = (int)$pdo->query('SELECT COUNT(*) FROM properties')->fetchColumn();
?>
<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Einstellungen &ndash; Admin <?= htmlspecialchars(SITE_NAME) ?></title>
    <meta name="robots" content="noindex,nofollow">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" crossorigin="anonymous">
    <style>
        /* ── Page-specific styles ── */
        .step {
            display: flex;
            gap: 1rem;
            align-items: flex-start;
            margin-bottom: .85rem;
            padding: 1rem 1.1rem;
            border-radius: 10px;
            background: #f8fafd;
            border: 1px solid #e2eaf3;
        }

        .step.done {
            background: #f0fdf4;
            border-color: #bbf7d0;
        }

        .step .sn {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: #1a3a5c;
            color: #fff;
            font-weight: 700;
            font-size: .85rem;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .step.done .sn {
            background: #16a34a;
        }

        .step h4 {
            margin: 0 0 3px;
            font-size: .92rem;
            color: #111827;
        }

        .step p {
            margin: 0;
            font-size: .82rem;
            color: #6b7280;
            line-height: 1.55;
        }

        .step code {
            background: #e8f0fb;
            padding: 1px 6px;
            border-radius: 4px;
            font-size: .78rem;
            color: #1a3a5c;
            word-break: break-all;
        }

        .cfg-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: .75rem;
        }

        .cfg-item {
            display: flex;
            align-items: center;
            gap: .65rem;
            padding: .7rem 1rem;
            border-radius: 9px;
            background: #f8fafd;
            border: 1px solid #e2eaf3;
            font-size: .88rem;
        }

        .cfg-label {
            flex: 1;
            font-weight: 500;
            color: #374151;
        }

        .cfg-val {
            font-weight: 700;
            font-size: .78rem;
            text-transform: uppercase;
            letter-spacing: .04em;
        }

        .ok {
            color: #16a34a;
        }

        .warn {
            color: #d97706;
        }

        .badge.bon {
            background: #d1fae5;
            color: #065f46;
        }

        .badge.boff {
            background: #fee2e2;
            color: #991b1b;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: .88rem;
        }

        th {
            text-align: left;
            padding: .65rem 1rem;
            border-bottom: 1px solid #e8ecf0;
            color: #64748b;
            font-size: .75rem;
            font-weight: 700;
            text-transform: uppercase;
            background: #f8fafc;
        }

        td {
            padding: .75rem 1rem;
            border-bottom: 1px solid #f0f0f0;
        }

        .card {
            padding: 1.5rem;
        }

        .card h2 {
            margin: 0 0 1.25rem;
            font-size: 1rem;
            color: #1a3a5c;
            border-bottom: 2px solid #e8f0fb;
            padding-bottom: .65rem;
        }

        @media (max-width: 700px) {
            .cfg-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
    <?php
    $activePage  = 'settings';
    $newContacts = $newInquiries;
    require __DIR__ . '/_sidebar.php';
    ?>
    <div class="main">
        <header class="topbar">
            <div class="topbar-title"><i class="fa fa-gear" style="margin-right:.4rem"></i> Einstellungen</div>
            <div class="topbar-meta">
                <div class="topbar-user">
                    <div class="avatar"><?= strtoupper(substr(htmlspecialchars($admin['username']), 0, 1)) ?></div>
                    <span><?= htmlspecialchars($admin['username']) ?></span>
                </div>
            </div>
        </header>

        <!-- ── Main ── -->
        <div class="content">
            <h1>&#9881;&#65039; Einstellungen</h1>

            <?php if ($msg): ?>
                <div class="alert alert-<?= $msgType ?>"><?= $msg ?></div>
            <?php endif; ?>

            <!-- ── Setup-Checkliste ── -->
            <div class="card">
                <h2>&#128640; Setup-Checkliste</h2>

                <div class="step <?= $propCount > 0 ? 'done' : '' ?>">
                    <div class="sn"><?= $propCount > 0 ? '&#10003;' : '1' ?></div>
                    <div>
                        <h4>Datenbank initialisiert</h4>
                        <?php if ($propCount > 0): ?>
                            <p><?= $propCount ?> Immobilien und <?= $inquiryCount ?> Anfragen in der Datenbank.</p>
                        <?php else: ?>
                            <p>Aufrufen: <code>https://www.immobilienghumman.de/database/init.php?token=immobilien-init-2026</code></p>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="step <?= $configOk['smtp'] ? 'done' : '' ?>">
                    <div class="sn"><?= $configOk['smtp'] ? '&#10003;' : '2' ?></div>
                    <div>
                        <h4>SMTP/Socket-Mailer (E-Mail-Versand)</h4>
                        <?php if ($configOk['smtp']): ?>
                            <p>Konfiguriert &ndash; E-Mails werden erfolgreich versendet.</p>
                        <?php else: ?>
                            <p>In <code>includes/config.local.php</code> SMTP-Werte setzen:<br>
                                <code>SMTP_HOST</code>, <code>SMTP_PORT</code>, <code>SMTP_USER</code>, <code>SMTP_PASS</code>, <code>SMTP_SECURE</code>
                            </p>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="step <?= $configOk['cloudinary'] ? 'done' : '' ?>">
                    <div class="sn"><?= $configOk['cloudinary'] ? '&#10003;' : '3' ?></div>
                    <div>
                        <h4>Cloudinary (Bild-Upload)</h4>
                        <?php if ($configOk['cloudinary']): ?>
                            <p>Konfiguriert &ndash; Bilder werden in der Cloud gespeichert.</p>
                        <?php else: ?>
                            <p>In <code>includes/config.php</code> die drei Felder eintragen:<br>
                                <code>CLOUDINARY_CLOUD_NAME</code>, <code>CLOUDINARY_API_KEY</code>, <code>CLOUDINARY_API_SECRET</code><br>
                                Account: <a href="https://console.cloudinary.com/settings/api-keys" target="_blank" rel="noopener">console.cloudinary.com</a>
                            </p>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="step <?= $configOk['auth'] ? 'done' : '' ?>">
                    <div class="sn"><?= $configOk['auth'] ? '&#10003;' : '4' ?></div>
                    <div>
                        <h4>AUTH_SECRET f&uuml;r Produktion sichern</h4>
                        <?php if ($configOk['auth']): ?>
                            <p>AUTH_SECRET wurde ge&auml;ndert.</p>
                        <?php else: ?>
                            <p>In <code>includes/config.php</code> den Wert <code>AUTH_SECRET</code> auf einen langen, zuf&auml;lligen String setzen (mind. 32 Zeichen).</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- ── Konfigurationsstatus ── -->
            <div class="card">
                <h2>&#128295; Konfigurationsstatus</h2>
                <div class="cfg-grid">
                    <div class="cfg-item">
                        <span class="<?= $configOk['smtp'] ? 'ok' : 'warn' ?>"><?= $configOk['smtp'] ? '&#10003;' : '&#9888;' ?></span>
                        <span class="cfg-label">SMTP/Socket-Mailer (E-Mail)</span>
                        <span class="cfg-val <?= $configOk['smtp'] ? 'ok' : 'warn' ?>"><?= $configOk['smtp'] ? 'Aktiv' : 'Fehlt' ?></span>
                    </div>
                    <div class="cfg-item">
                        <span class="<?= $configOk['cloudinary'] ? 'ok' : 'warn' ?>"><?= $configOk['cloudinary'] ? '&#10003;' : '&#9888;' ?></span>
                        <span class="cfg-label">Cloudinary (Bilder)</span>
                        <span class="cfg-val <?= $configOk['cloudinary'] ? 'ok' : 'warn' ?>"><?= $configOk['cloudinary'] ? 'Aktiv' : 'Fehlt' ?></span>
                    </div>
                    <div class="cfg-item">
                        <span class="<?= $configOk['auth'] ? 'ok' : 'warn' ?>"><?= $configOk['auth'] ? '&#10003;' : '&#9888;' ?></span>
                        <span class="cfg-label">AUTH_SECRET</span>
                        <span class="cfg-val <?= $configOk['auth'] ? 'ok' : 'warn' ?>"><?= $configOk['auth'] ? 'Ge&auml;ndert' : 'Standard!' ?></span>
                    </div>
                    <div class="cfg-item">
                        <span class="ok">&#10003;</span>
                        <span class="cfg-label">Datenbank (MySQL)</span>
                        <span class="cfg-val ok">Verbunden</span>
                    </div>
                    <div class="cfg-item">
                        <span class="ok">&#10003;</span>
                        <span class="cfg-label">CSRF-Schutz</span>
                        <span class="cfg-val ok">Aktiv</span>
                    </div>
                    <div class="cfg-item">
                        <span class="ok">&#10003;</span>
                        <span class="cfg-label">PHP-Version</span>
                        <span class="cfg-val ok"><?= PHP_VERSION ?></span>
                    </div>
                </div>
            </div>

            <!-- ── Passwort ändern ── -->
            <div class="card">
                <h2>&#128272; Passwort &auml;ndern</h2>
                <form method="POST" autocomplete="off">
                    <?= csrf_field() ?>
                    <input type="hidden" name="change_password" value="1">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="cp">Aktuelles Passwort</label>
                            <input type="password" id="cp" name="current_password" required autocomplete="current-password">
                        </div>
                        <div></div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="np">Neues Passwort <span style="color:#9ca3af;font-weight:400">(mind. 8 Zeichen)</span></label>
                            <input type="password" id="np" name="new_password" required minlength="8" autocomplete="new-password">
                        </div>
                        <div class="form-group">
                            <label for="cfp">Passwort best&auml;tigen</label>
                            <input type="password" id="cfp" name="confirm_password" required minlength="8" autocomplete="new-password">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Passwort &auml;ndern</button>
                </form>
            </div>

            <!-- ── Admin-Benutzer ── -->
            <div class="card">
                <h2>&#128100; Admin-Benutzer</h2>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Benutzername</th>
                            <th>Rolle</th>
                            <th>Status</th>
                            <th>Letzter Login</th>
                            <th>Erstellt</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($adminUsers as $u): ?>
                            <tr>
                                <td><?= (int)$u['id'] ?></td>
                                <td>
                                    <strong><?= htmlspecialchars($u['username']) ?></strong>
                                    <?php if ($u['id'] == $admin['id']): ?>
                                        <span style="color:#2563eb;font-size:.75rem;margin-left:4px">(Sie)</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($u['role']) ?></td>
                                <td><span class="badge <?= $u['is_active'] ? 'bon' : 'boff' ?>"><?= $u['is_active'] ? 'Aktiv' : 'Gesperrt' ?></span></td>
                                <td><?= $u['last_login'] ? date('d.m.Y H:i', strtotime($u['last_login'])) : '&ndash;' ?></td>
                                <td><?= date('d.m.Y', strtotime($u['created_at'])) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- ── Daten-Export ── -->
            <div class="card">
                <h2>&#128229; Daten-Export</h2>
                <p style="color:#6b7280;font-size:.9rem;margin:0 0 1rem">
                    Alle Kontaktanfragen als CSV-Datei herunterladen (Excel-kompatibel, UTF-8 mit BOM).
                </p>
                <a href="<?= BASE ?>/admin/settings.php?export=inquiries&token=<?= urlencode(CSRF_TOKEN) ?>"
                    class="btn btn-success">
                    &#128203; Alle Anfragen exportieren (<?= $inquiryCount ?>)
                </a>
            </div>

            <!-- ── Systeminformationen ── -->
            <div class="card">
                <h2>&#8505;&#65039; Systeminformationen</h2>
                <div class="cfg-grid">
                    <div class="cfg-item"><span class="cfg-label">Immobilien gesamt</span><span class="cfg-val ok"><?= $propCount ?></span></div>
                    <div class="cfg-item"><span class="cfg-label">Anfragen gesamt</span><span class="cfg-val ok"><?= $inquiryCount ?></span></div>
                    <div class="cfg-item"><span class="cfg-label">Neue Anfragen</span><span class="cfg-val <?= $newInquiries ? 'warn' : 'ok' ?>"><?= $newInquiries ?></span></div>
                    <div class="cfg-item"><span class="cfg-label">Admin-Benutzer</span><span class="cfg-val ok"><?= count($adminUsers) ?></span></div>
                    <div class="cfg-item"><span class="cfg-label">Server-Software</span><span class="cfg-val ok"><?= htmlspecialchars(explode(' ', $_SERVER['SERVER_SOFTWARE'] ?? 'Apache')[0]) ?></span></div>
                    <div class="cfg-item"><span class="cfg-label">Angemeldet als</span><span class="cfg-val ok"><?= htmlspecialchars($admin['username']) ?></span></div>
                </div>
            </div>

        </div><!-- /content -->
    </div><!-- /main -->
</body>

</html>