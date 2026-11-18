<?php
define('AUTH_SECRET', 'auth-revocation-test-secret-with-at-least-32-chars');
define('AUTH_SECRET_CONFIGURED', true);
require_once __DIR__ . '/../api/auth_helper.php';

$pdo = new PDO('sqlite::memory:');
$pdo->exec('CREATE TABLE admin_users (id INTEGER PRIMARY KEY, is_active INTEGER NOT NULL, auth_version INTEGER NOT NULL)');
$pdo->exec("CREATE TRIGGER admin_auth_version_on_status_change AFTER UPDATE OF is_active ON admin_users
    WHEN OLD.is_active <> NEW.is_active
    BEGIN UPDATE admin_users SET auth_version = auth_version + 1 WHERE id = NEW.id; END");
$pdo->exec('INSERT INTO admin_users (id, is_active, auth_version) VALUES (1, 1, 0)');

$failures = [];
if (!auth_admin_matches($pdo, 1, 0)) $failures[] = 'Active account with current version was rejected';
if (auth_admin_matches($pdo, 1, 1)) $failures[] = 'Account with stale auth version was accepted';

$token = create_token(['id' => 1, 'ver' => 0, 'role' => 'admin']);
$_SESSION = [];
$_SERVER['HTTP_AUTHORIZATION'] = 'Bearer ' . $token;
if (auth_current_identity($pdo) === false) $failures[] = 'Valid bearer token was rejected';

auth_revoke_admin($pdo, 1, 0);
if (auth_admin_matches($pdo, 1, 0)) $failures[] = 'Revoked credentials were accepted';
if (!auth_admin_matches($pdo, 1, 1)) $failures[] = 'New auth version was rejected';
if (auth_current_identity($pdo) !== false) $failures[] = 'Revoked bearer token was accepted';

$pdo->exec('UPDATE admin_users SET is_active = 0 WHERE id = 1');
if (auth_admin_matches($pdo, 1, 2)) $failures[] = 'Deactivated account was accepted';
$pdo->exec('UPDATE admin_users SET is_active = 1 WHERE id = 1');
if (auth_admin_matches($pdo, 1, 2)) $failures[] = 'Token from before account reactivation was accepted';
if (!auth_admin_matches($pdo, 1, 3)) $failures[] = 'Reactivated account current version was rejected';

if ($failures) {
    fwrite(STDERR, implode(PHP_EOL, $failures) . PHP_EOL);
    exit(1);
}

echo "Auth deactivation and revocation checks passed.\n";
