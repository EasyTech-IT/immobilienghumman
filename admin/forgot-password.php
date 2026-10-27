<?php

/**
 * admin/forgot-password.php
 * Fordert einen Passwort-Reset-Link fuer den Admin-Bereich an.
 */
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';

if (!empty($_SESSION['admin_id'])) {
    header('Location: ' . BASE . '/admin/index.php');
    exit;
}

$msg = '';
$msgType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify($_POST['csrf_token'] ?? '')) {
        $msg = 'Ungueltige Anfrage. Bitte laden Sie die Seite neu.';
        $msgType = 'error';
    } else {
        $username = trim($_POST['username'] ?? '');

        // Einheitliche Antwortzeit und neutrale Nachricht, um User-Enumeration zu vermeiden.
        $successMsg = 'Falls der Benutzer existiert, wurde ein Reset-Link versendet.';

        if ($username === '') {
            $msg = 'Bitte geben Sie den Benutzernamen ein.';
            $msgType = 'error';
        } else {
            try {
                $pdo = db();

                // Tabelle bei Bedarf automatisch bereitstellen.
                $pdo->exec("CREATE TABLE IF NOT EXISTS password_reset_tokens (
                    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
                    user_id INT UNSIGNED NOT NULL,
                    token_hash CHAR(64) NOT NULL UNIQUE,
                    expires_at DATETIME NOT NULL,
                    used_at DATETIME NULL,
                    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    PRIMARY KEY (id),
                    INDEX idx_user_id (user_id),
                    INDEX idx_expires_at (expires_at)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

                $stmt = $pdo->prepare('SELECT id, username FROM admin_users WHERE username = ? AND is_active = 1 LIMIT 1');
                $stmt->execute([$username]);
                $user = $stmt->fetch();

                if ($user) {
                    $token = bin2hex(random_bytes(32));
                    $tokenHash = hash('sha256', $token);

                    // Alte, ungenutzte Token fuer den User invalidieren.
                    $pdo->prepare('UPDATE password_reset_tokens SET used_at = NOW() WHERE user_id = ? AND used_at IS NULL')
                        ->execute([$user['id']]);

                    $pdo->prepare('INSERT INTO password_reset_tokens (user_id, token_hash, expires_at) VALUES (?, ?, DATE_ADD(NOW(), INTERVAL 30 MINUTE))')
                        ->execute([$user['id'], $tokenHash]);

                    $resetUrl = rtrim(SITE_URL, '/') . '/admin/reset-password.php?token=' . urlencode($token);

                    $subject = MAIL_SUBJECT_PREFIX . ' Admin Passwort-Reset angefordert';
                    $body = "Ein Passwort-Reset wurde angefordert.\n\n";
                    $body .= "Benutzer: {$user['username']}\n";
                    $body .= "Reset-Link (30 Minuten gueltig):\n{$resetUrl}\n\n";
                    $body .= "Falls Sie das nicht waren, ignorieren Sie diese Nachricht.";

                    // Versand an konfigurierte Admin-Adresse.
                    app_mail_text(MAIL_TO, $subject, $body);
                }

                $msg = $successMsg;
                $msgType = 'success';
            } catch (Throwable $e) {
                error_log('forgot-password error: ' . $e->getMessage());
                $msg = 'Reset konnte momentan nicht gestartet werden. Bitte kontaktieren Sie den Administrator.';
                $msgType = 'error';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Passwort vergessen – <?= SITE_NAME ?></title>
    <meta name="robots" content="noindex, nofollow">
    <link rel="stylesheet" href="<?= BASE ?>/styles.css">
    <style>
        body {
            background: #f5f5f5;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
        }

        .box {
            background: #fff;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 4px 24px rgba(0, 0, 0, .12);
            width: 100%;
            max-width: 430px;
        }

        h1 {
            margin: 0 0 1rem;
            font-size: 1.25rem;
            color: #1a3a5c;
        }

        p {
            color: #475569;
            font-size: .92rem;
            line-height: 1.55;
        }

        .form-group {
            margin-top: 1rem;
        }

        label {
            display: block;
            margin-bottom: .35rem;
            font-weight: 600;
            font-size: .9rem;
        }

        input[type=text] {
            width: 100%;
            padding: .65rem .9rem;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 1rem;
            box-sizing: border-box;
        }

        .btn {
            width: 100%;
            margin-top: 1rem;
            padding: .75rem;
            background: #1a3a5c;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: .95rem;
            font-weight: 700;
            cursor: pointer;
        }

        .msg {
            border-radius: 8px;
            padding: .7rem .9rem;
            margin-top: .8rem;
            font-size: .9rem;
        }

        .msg.success {
            background: #ecfdf3;
            border: 1px solid #86efac;
            color: #166534;
        }

        .msg.error {
            background: #fef2f2;
            border: 1px solid #fca5a5;
            color: #991b1b;
        }

        .help-links {
            margin-top: 1rem;
            display: flex;
            gap: .8rem;
            flex-wrap: wrap;
        }

        .help-links a {
            font-size: .86rem;
            color: #1a3a5c;
            text-decoration: none;
            font-weight: 600;
        }

        .help-links a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <div class="box">
        <h1>Passwort zuruecksetzen</h1>
        <p>Geben Sie Ihren Admin-Benutzernamen ein. Sie erhalten einen Reset-Link an die konfigurierte Admin-E-Mail-Adresse.</p>

        <?php if ($msg): ?>
            <div class="msg <?= $msgType === 'success' ? 'success' : 'error' ?>"><?= htmlspecialchars($msg) ?></div>
        <?php endif; ?>

        <form method="post">
            <?= csrf_field() ?>
            <div class="form-group">
                <label for="username">Benutzername</label>
                <input type="text" id="username" name="username" required autocomplete="username">
            </div>
            <button type="submit" class="btn">Reset-Link anfordern</button>
        </form>

        <div class="help-links">
            <a href="<?= BASE ?>/admin/login.php">Zurueck zum Login</a>
            <a href="tel:+4916098787878">Admin anrufen</a>
        </div>
    </div>
</body>

</html>