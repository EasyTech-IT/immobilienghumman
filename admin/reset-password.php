<?php

/**
 * admin/reset-password.php
 * Setzt das Admin-Passwort ueber ein zeitlich begrenztes Token zurueck.
 */
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';

if (!empty($_SESSION['admin_id'])) {
    header('Location: ' . BASE . '/admin/index.php');
    exit;
}

$token = trim($_GET['token'] ?? $_POST['token'] ?? '');
$msg = '';
$msgType = '';
$isValidToken = false;

if ($token !== '' && preg_match('/^[a-f0-9]{64}$/', $token)) {
    try {
        $pdo = db();
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

        $tokenHash = hash('sha256', $token);
        $stmt = $pdo->prepare('SELECT id, user_id FROM password_reset_tokens WHERE token_hash = ? AND used_at IS NULL AND expires_at > NOW() LIMIT 1');
        $stmt->execute([$tokenHash]);
        $tokenRow = $stmt->fetch();
        $isValidToken = (bool)$tokenRow;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!$isValidToken) {
                $msg = 'Dieser Link ist ungueltig oder abgelaufen.';
                $msgType = 'error';
            } elseif (!csrf_verify($_POST['csrf_token'] ?? '')) {
                $msg = 'Ungueltige Anfrage. Bitte Seite neu laden.';
                $msgType = 'error';
            } else {
                $new = $_POST['new_password'] ?? '';
                $confirm = $_POST['confirm_password'] ?? '';

                if (strlen($new) < 8) {
                    $msg = 'Das neue Passwort muss mindestens 8 Zeichen lang sein.';
                    $msgType = 'error';
                } elseif ($new !== $confirm) {
                    $msg = 'Die Passwoerter stimmen nicht ueberein.';
                    $msgType = 'error';
                } else {
                    $pdo->prepare('UPDATE admin_users SET password_hash = ? WHERE id = ?')
                        ->execute([password_hash($new, PASSWORD_DEFAULT), $tokenRow['user_id']]);

                    $pdo->prepare('UPDATE password_reset_tokens SET used_at = NOW() WHERE id = ?')
                        ->execute([$tokenRow['id']]);

                    $msg = 'Passwort erfolgreich aktualisiert. Sie koennen sich jetzt anmelden.';
                    $msgType = 'success';
                    $isValidToken = false;
                }
            }
        }
    } catch (Throwable $e) {
        error_log('reset-password error: ' . $e->getMessage());
        $msg = 'Reset ist momentan nicht verfuegbar. Bitte kontaktieren Sie den Administrator.';
        $msgType = 'error';
    }
} else {
    $msg = 'Kein gueltiger Reset-Token uebergeben.';
    $msgType = 'error';
}
?>
<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Neues Passwort setzen – <?= SITE_NAME ?></title>
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

        .form-group {
            margin-top: 1rem;
        }

        label {
            display: block;
            margin-bottom: .35rem;
            font-weight: 600;
            font-size: .9rem;
        }

        input[type=password] {
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
        <h1>Neues Passwort setzen</h1>

        <?php if ($msg): ?>
            <div class="msg <?= $msgType === 'success' ? 'success' : 'error' ?>"><?= htmlspecialchars($msg) ?></div>
        <?php endif; ?>

        <?php if ($isValidToken): ?>
            <form method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="token" value="<?= htmlspecialchars($token, ENT_QUOTES) ?>">
                <div class="form-group">
                    <label for="new_password">Neues Passwort</label>
                    <input type="password" id="new_password" name="new_password" minlength="8" required autocomplete="new-password">
                </div>
                <div class="form-group">
                    <label for="confirm_password">Passwort bestaetigen</label>
                    <input type="password" id="confirm_password" name="confirm_password" minlength="8" required autocomplete="new-password">
                </div>
                <button type="submit" class="btn">Passwort speichern</button>
            </form>
        <?php endif; ?>

        <div class="help-links">
            <a href="<?= BASE ?>/admin/login.php">Zum Login</a>
            <a href="<?= BASE ?>/admin/forgot-password.php">Neuen Reset-Link anfordern</a>
        </div>
    </div>
</body>

</html>