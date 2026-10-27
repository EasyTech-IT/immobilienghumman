<?php

/**
 * admin/login.php – Admin-Login
 */
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';

// Bereits eingeloggt?
if (!empty($_SESSION['admin_id'])) {
    header('Location: ' . BASE . '/admin/index.php');
    exit;
}

$error = '';
$info = '';

$nextRaw = $_POST['next'] ?? $_GET['next'] ?? '';
$nextPath = '';
if (is_string($nextRaw) && strpos($nextRaw, BASE . '/admin/') === 0) {
    $nextPath = $nextRaw;
} elseif (is_string($nextRaw) && BASE === '' && strpos($nextRaw, '/admin/') === 0) {
    $nextPath = $nextRaw;
}

$reason = $_GET['reason'] ?? '';
if ($reason === 'session_expired') {
    $info = 'Ihre Sitzung ist abgelaufen. Bitte melden Sie sich erneut an.';
} elseif ($reason === 'account_invalid') {
    $info = 'Ihr Benutzerkonto ist nicht mehr aktiv. Bitte kontaktieren Sie den Administrator.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF prüfen
    if (!csrf_verify($_POST['csrf_token'] ?? '')) {
        $error = 'Ungültige Anfrage. Bitte Seite neu laden.';
    } else {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        // Rate limiting
        if (!isset($_SESSION['login_attempts'])) {
            $_SESSION['login_attempts'] = 0;
            $_SESSION['login_lockout'] = 0;
        }
        if ($_SESSION['login_lockout'] > time()) {
            $wait = ceil(($_SESSION['login_lockout'] - time()) / 60);
            $error = "Zu viele Fehlversuche. Bitte warten Sie $wait Minute(n).";
        } elseif (!empty($username) && !empty($password)) {
            $pdo  = db();
            $stmt = $pdo->prepare('SELECT id, username, password_hash, role FROM admin_users WHERE username = ? AND is_active = 1');
            $stmt->execute([$username]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password_hash'])) {
                $_SESSION['login_attempts'] = 0;
                // Regenerate session ID BEFORE writing sensitive data (prevents session fixation)
                session_regenerate_id(false);
                $_SESSION['admin_id']   = $user['id'];
                $_SESSION['admin_role'] = $user['role'];
                // Flush session to disk before redirect to prevent race condition on Windows
                session_write_close();
                $pdo->prepare('UPDATE admin_users SET last_login = NOW() WHERE id = ?')->execute([$user['id']]);
                header('Location: ' . ($nextPath ?: (BASE . '/admin/index.php')));
                exit;
            } else {
                $_SESSION['login_attempts']++;
                if ($_SESSION['login_attempts'] >= 5) {
                    $_SESSION['login_lockout']  = time() + 900;
                    $_SESSION['login_attempts'] = 0;
                }
                $error = 'Ungültige Anmeldedaten.';
            }
        } else {
            $error = 'Bitte alle Felder ausfüllen.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin-Login – <?= SITE_NAME ?></title>
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

        .login-box {
            background: #fff;
            padding: 2.5rem;
            border-radius: 12px;
            box-shadow: 0 4px 24px rgba(0, 0, 0, .12);
            width: 100%;
            max-width: 380px;
        }

        .login-box h1 {
            margin: 0 0 1.5rem;
            font-size: 1.4rem;
            color: #1a3a5c;
            text-align: center;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        label {
            display: block;
            margin-bottom: .35rem;
            font-weight: 600;
            color: #333;
            font-size: .9rem;
        }

        input[type=text],
        input[type=password] {
            width: 100%;
            padding: .65rem .9rem;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 1rem;
            box-sizing: border-box;
        }

        input:focus {
            outline: none;
            border-color: #1a3a5c;
            box-shadow: 0 0 0 3px rgba(26, 58, 92, .15);
        }

        .btn-login {
            width: 100%;
            padding: .8rem;
            background: #1a3a5c;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            margin-top: .5rem;
        }

        .btn-login:hover {
            background: #2a5298;
        }

        .error {
            background: #fee;
            border: 1px solid #f88;
            border-radius: 8px;
            padding: .75rem 1rem;
            color: #c00;
            margin-bottom: 1rem;
            font-size: .9rem;
        }

        .info {
            background: #eff6ff;
            border: 1px solid #93c5fd;
            border-radius: 8px;
            padding: .75rem 1rem;
            color: #1e3a8a;
            margin-bottom: 1rem;
            font-size: .9rem;
        }

        .logo {
            display: block;
            margin: 0 auto 1.5rem;
            max-height: 60px;
        }

        .login-help {
            margin-top: 1rem;
            font-size: .86rem;
            color: #475569;
            display: grid;
            gap: .35rem;
        }

        .login-help a {
            color: #1a3a5c;
            text-decoration: none;
            font-weight: 600;
        }

        .login-help a:hover {
            text-decoration: underline;
        }

        .forgot-link {
            display: inline-block;
            margin-top: .7rem;
            font-size: .85rem;
            color: #1a3a5c;
            text-decoration: none;
            font-weight: 600;
        }

        .forgot-link:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <div class="login-box">
        <img src="<?= SITE_LOGO ?>" alt="Logo" class="logo">
        <h1>Admin-Bereich</h1>
        <?php if ($info): ?><div class="info"><?= htmlspecialchars($info) ?></div><?php endif; ?>
        <?php if ($error): ?><div class="error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
        <form method="post">
            <?= csrf_field() ?>
            <input type="hidden" name="next" value="<?= htmlspecialchars($nextPath, ENT_QUOTES) ?>">
            <div class="form-group">
                <label for="username">Benutzername</label>
                <input type="text" id="username" name="username" autocomplete="username" required>
            </div>
            <div class="form-group">
                <label for="password">Passwort</label>
                <input type="password" id="password" name="password" autocomplete="current-password" required>
            </div>
            <button type="submit" class="btn-login">Anmelden</button>
            <a class="forgot-link" href="<?= BASE ?>/admin/forgot-password.php">Passwort vergessen?</a>
        </form>
        <div class="login-help">
            <span>Passwort vergessen? Bitte kontaktieren Sie den Administrator.</span>
            <a href="tel:+4916098787878">Telefon: 0160 9878 7878</a>
            <a href="<?= BASE ?>/impressum">E-Mail und Kontakt im Impressum</a>
        </div>
    </div>
</body>

</html>