const assert = require('assert');
const fs = require('fs');
const os = require('os');
const path = require('path');
const net = require('net');
const { spawn, spawnSync } = require('child_process');

const root = path.resolve(__dirname, '..');
const dir = fs.mkdtempSync(path.join(os.tmpdir(), 'expose-http-test-'));
const dirs = ['api', 'admin', 'includes', 'vendor', 'sessions', 'assets'];
const created = [];
for (const name of dirs) fs.mkdirSync(path.join(dir, name));
function write(name, content) {
    const target = path.join(dir, name);
    fs.writeFileSync(target, content);
    created.push(target);
}
for (const name of ['api/contact.php', 'admin/_auth_check.php', 'admin/_sidebar.php', 'admin/inquiries.php', 'admin/expose-download.php', 'admin/expose-approve.php', 'expose-confirm.php', 'includes/expose-confirmation.php', 'includes/expose-pdf.php', 'includes/private-expose.php', 'includes/inquiry-mail.php', 'includes/analyse-fragen.php', 'assets/expose-logo.png']) {
    write(name, fs.readFileSync(path.join(root, name)));
}
write('includes/header.php', '<?php ?>');
write('includes/footer.php', '<?php ?>');
write('includes/subscribers.php', '<?php function subscribers_request_optin(PDO $pdo, string $email, string $name, string $ip, string $source): string { file_put_contents(getenv("TEST_NEWSLETTER"), $email . "\\n", FILE_APPEND); return "pending"; }');
write('api/auth_helper.php', '<?php function ensure_admin_auth_versioning(PDO $pdo): bool { return true; }');
write('vendor/autoload.php', '<?php require getenv("TEST_AUTOLOAD");');
write('includes/config.php', `<?php
session_save_path(getenv('TEST_SESSION_DIR'));
session_start();
define('BASE', '');
define('SITE_URL', 'https://example.test');
define('SITE_NAME', 'Example');
define('SITE_PHONE', '01234');
define('SITE_EMAIL', 'mail@example.test');
define('SITE_LOGO', '/logo.png');
define('MAIL_FROM', 'mail@example.test');
define('MAIL_TO', 'admin@example.test');
define('MAIL_SUBJECT_PREFIX', '[Test]');
define('CSRF_TOKEN', 'test-token');
function csrf_field(): string { return '<input type="hidden" name="csrf_token" value="test-token">'; }
function csrf_verify(string $token = ''): bool { return $token === 'test-token'; }
function verify_turnstile_token(string $token, string $ip = ''): bool { return true; }
function app_mail_text(string $to, string $subject, string $body, string $reply = ''): bool {
    if ($to === 'confirmfail@example.test') return false;
    if ($to !== MAIL_TO) file_put_contents(getenv('TEST_CONFIRMATION'), $body);
    return true;
}
function mailer_send(string $to, string $subject, string $text = '', string $html = '', string $reply = '', string $from = '', string $name = '', ?array $attachment = null): bool {
    if ($reply === 'notifyfail@example.test') return false;
    if ($attachment !== null) {
        if ($to === 'fail@example.test') return false;
        file_put_contents(getenv('TEST_ATTACHMENT'), $attachment['content']);
        file_put_contents(getenv('TEST_MAIL_HTML'), $html);
    }
    return true;
}
`);
write('includes/db.php', `<?php
require_once __DIR__ . '/config.php';
class TestPDO extends PDO {
    public function exec(string $sql): int|false {
        if (strpos($sql, 'CREATE TABLE IF NOT EXISTS expose_requests') !== false) {
            $sql = 'CREATE TABLE IF NOT EXISTS expose_requests (inquiry_id INTEGER PRIMARY KEY REFERENCES contact_inquiries(id) ON DELETE CASCADE, pdf BLOB NOT NULL, delivery_status TEXT NOT NULL)';
        } elseif (strpos($sql, 'CREATE TABLE IF NOT EXISTS expose_confirmations') !== false) {
            $sql = 'CREATE TABLE IF NOT EXISTS expose_confirmations (inquiry_id INTEGER PRIMARY KEY REFERENCES expose_requests(inquiry_id) ON DELETE CASCADE, token_hash TEXT UNIQUE NOT NULL, expires_at INTEGER NOT NULL, used_at INTEGER NULL)';
        } elseif (strpos($sql, 'CREATE TABLE IF NOT EXISTS expose_approvals') !== false) {
            $sql = 'CREATE TABLE IF NOT EXISTS expose_approvals (property_id INTEGER PRIMARY KEY, property_hash TEXT NOT NULL)';
        }
        return parent::exec($sql);
    }
    public function prepare(string $sql, array $options = []): PDOStatement|false {
        return parent::prepare(str_replace('NOW()', 'CURRENT_TIMESTAMP', $sql), $options);
    }
}
function db(): PDO {
    static $db;
    if ($db) return $db;
    $db = new TestPDO('sqlite:' . getenv('TEST_DATABASE'));
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $db->exec('PRAGMA foreign_keys = ON');
    $db->exec('CREATE TABLE IF NOT EXISTS rate_limits (ip_address TEXT, action TEXT, hit_count INTEGER, window_start TEXT)');
    $db->exec('CREATE TABLE IF NOT EXISTS contact_inquiries (id INTEGER PRIMARY KEY AUTOINCREMENT, name TEXT, email TEXT, phone TEXT, message TEXT, property_id INTEGER, ip_address TEXT, status TEXT DEFAULT "new", created_at TEXT)');
    $db->exec('CREATE TABLE IF NOT EXISTS admin_users (id INTEGER PRIMARY KEY, username TEXT, role TEXT, is_active INTEGER, auth_version INTEGER NOT NULL DEFAULT 0)');
    $db->exec("INSERT OR IGNORE INTO admin_users VALUES (1, 'test', 'admin', 1, 0)");
    $db->exec('CREATE TABLE IF NOT EXISTS properties (id INTEGER PRIMARY KEY, title TEXT, slug TEXT, status TEXT, type TEXT, offer_type TEXT, price REAL, location TEXT, zip TEXT, address TEXT, description TEXT, features TEXT, extra_data TEXT, images TEXT, main_image TEXT)');
    $db->exec("INSERT OR IGNORE INTO properties VALUES (1, 'Testwohnung', 'testwohnung', 'available', 'Wohnung', 'Kauf', 200000, 'Musterstadt', '12345', 'Musterweg 1', 'Testbeschreibung', '[]', '{}', '[]', '')");
    $db->exec("INSERT OR IGNORE INTO properties VALUES (2, 'Fehlerobjekt', 'fehlerobjekt', 'available', 'Wohnung', 'Kauf', 200000, 'Musterstadt', '12345', '', '', '[]', '{}', '[\\"https://example.test/invalid.jpg\\"]', '')");
    return $db;
}
`);
write('test-login.php', '<?php require "includes/config.php"; $_SESSION["admin_id"] = 1; $_SESSION["admin_auth_version"] = 0; echo "ok";');
write('test-status.php', '<?php require "includes/db.php"; echo json_encode(db()->query("SELECT inquiry_id, delivery_status FROM expose_requests ORDER BY inquiry_id")->fetchAll());');
write('test-update.php', '<?php require "includes/db.php"; db()->exec("UPDATE properties SET title = \'Aktualisierte Testwohnung\' WHERE id = 1"); echo "ok";');
write('test-expire.php', '<?php require "includes/db.php"; db()->exec("UPDATE expose_confirmations SET expires_at = 0 WHERE inquiry_id = 3"); echo "ok";');
write('test-clear-rate.php', '<?php require "includes/db.php"; db()->exec("DELETE FROM rate_limits"); echo "ok";');

async function unusedPort() {
    const server = net.createServer();
    await new Promise(resolve => server.listen(0, '127.0.0.1', resolve));
    const port = server.address().port;
    await new Promise(resolve => server.close(resolve));
    return port;
}
async function main() {
    const port = await unusedPort();
    const attachment = path.join(dir, 'attachment.pdf');
    const confirmation = path.join(dir, 'confirmation.txt');
    const newsletter = path.join(dir, 'newsletter.txt');
    const mailHtml = path.join(dir, 'mail.html');
    const database = path.join(dir, 'test.sqlite');
    created.push(attachment, database, confirmation, newsletter, mailHtml);
    const child = spawn('php', ['-S', `127.0.0.1:${port}`, '-t', dir], {
        env: {
            ...process.env,
            TEST_AUTOLOAD: path.join(root, 'vendor', 'autoload.php'),
            TEST_SESSION_DIR: path.join(dir, 'sessions'),
            TEST_DATABASE: database,
            TEST_ATTACHMENT: attachment,
            TEST_CONFIRMATION: confirmation,
            TEST_NEWSLETTER: newsletter,
            TEST_MAIL_HTML: mailHtml
        },
        stdio: ['ignore', 'ignore', 'pipe']
    });
    let errors = '';
    child.stderr.on('data', chunk => { errors += chunk; });
    const url = `http://127.0.0.1:${port}`;
    try {
        let ready = false;
        for (let i = 0; i < 50 && !ready; i++) {
            try { await fetch(url + '/test-login.php'); ready = true; }
            catch { await new Promise(resolve => setTimeout(resolve, 100)); }
        }
        assert(ready, 'PHP test server did not start: ' + errors);
        const request = async (email, extra = {}) => {
            const fields = new URLSearchParams({
                csrf_token: 'test-token', form_type: 'expose_anfrage', property_id: '1',
                vorname: 'Anna', nachname: 'Beispiel', email, phone: '01234',
                street: 'Musterstraße', house_number: '1', postal_code: '12345', city: 'Musterstadt',
                privacy_accept: '1', ...extra
            });
            const response = await fetch(url + '/api/contact.php?action=inquiry', { method: 'POST', body: fields });
            return { status: response.status, body: await response.json() };
        };
        const anonymous = await fetch(url + '/admin/expose-download.php?inquiry_id=1', { redirect: 'manual' });
        assert.strictEqual(anonymous.status, 302);
        assert.strictEqual((await request('anna@example.test')).status, 503, 'unapproved property must not send');
        const login = await fetch(url + '/test-login.php');
        const cookie = login.headers.get('set-cookie').split(';')[0];
        const detail = async id => (await fetch(url + '/admin/inquiries.php?id=' + id, { headers: { Cookie: cookie } })).text();
        const approve = async token => {
            const response = await fetch(url + '/admin/expose-approve.php', {
                method: 'POST', headers: { Cookie: cookie }, redirect: 'manual',
                body: new URLSearchParams({ csrf_token: token, property_id: '1' })
            });
            await response.text();
            return response.status;
        };
        assert.strictEqual(await approve('wrong'), 403);
        assert.strictEqual(await approve('test-token'), 302);
        for (const name of ['includes/expose-pdf.php', 'assets/expose-logo.png']) {
            const file = path.join(dir, name);
            const original = fs.readFileSync(file);
            try {
                fs.appendFileSync(file, '\n');
                assert.strictEqual((await request('anna@example.test')).status, 503, 'changed template or logo requires renewed approval');
            } finally {
                fs.writeFileSync(file, original);
            }
        }
        await (await fetch(url + '/test-clear-rate.php')).text();
        assert.strictEqual((await request('anna@example.test', { street: '' })).status, 400);
        const invalid = await request('anna@example.test', { privacy_accept: '0' });
        assert.strictEqual(invalid.status, 400);
        const result = await request('anna@example.test');
        assert.strictEqual(result.status, 200, JSON.stringify(result.body));
        assert(result.body.message.includes('bestätigen'));
        assert(!fs.existsSync(attachment), 'request must not send an attachment before confirmation');
        assert((await detail(1)).includes('Wartet auf Bestätigung der E-Mail-Adresse.'));
        const token = () => fs.readFileSync(confirmation, 'utf8').match(/expose-confirm\?t=([a-f0-9]{64})/)[1];
        const confirm = async (value, csrf = 'test-token') => {
            const response = await fetch(url + '/expose-confirm.php', {
                method: 'POST', headers: { Cookie: cookie },
                body: new URLSearchParams({ token: value, csrf_token: csrf })
            });
            return { status: response.status, body: await response.text() };
        };
        const firstToken = token();
        const landing = await fetch(url + '/expose-confirm.php?t=' + firstToken);
        assert.strictEqual(landing.status, 200);
        const landingHtml = await landing.text();
        assert(landingHtml.includes('E-Mail-Adresse bestätigen'));
        assert(landingHtml.includes('action="/expose-confirm"'), 'confirmation form must not redirect POST to a clean URL');
        assert(!fs.existsSync(attachment), 'opening link must not send (mail scanners)');
        assert.strictEqual((await confirm(firstToken, 'wrong')).status, 503);
        assert(!fs.existsSync(attachment), 'invalid CSRF must not send');
        assert.strictEqual((await confirm(firstToken)).status, 200);
        assert(fs.readFileSync(mailHtml, 'utf8').includes('Guten Tag Anna Beispiel'));
        assert(!fs.existsSync(newsletter), 'Exposé confirmation must not grant marketing consent');
        const sentPdf = fs.readFileSync(attachment);
        const download = await fetch(url + '/admin/expose-download.php?inquiry_id=1', { headers: { Cookie: cookie } });
        assert.deepStrictEqual(Buffer.from(await download.arrayBuffer()), sentPdf);
        assert((await detail(1)).includes('Exposé versendet (an den E-Mail-Server übergeben).'));
        assert.strictEqual((await confirm(firstToken)).status, 400, 'token replay must not send');
        await (await fetch(url + '/test-update.php')).text();
        assert.strictEqual((await request('anna@example.test')).status, 503, 'changed property requires renewed approval');
        assert.strictEqual(await approve('test-token'), 302);
        await (await fetch(url + '/test-clear-rate.php')).text();
        assert.strictEqual((await request('fail@example.test')).status, 200);
        const failure = await confirm(token());
        assert.strictEqual(failure.status, 503);
        assert((await detail(2)).includes('Versand fehlgeschlagen oder nicht bestätigt.'));
        assert.strictEqual((await request('anna@example.test')).status, 200);
        const expiredToken = token();
        await (await fetch(url + '/test-expire.php')).text();
        assert.strictEqual((await confirm(expiredToken)).status, 400);
        const resend = await fetch(url + '/admin/inquiries.php', {
            method: 'POST', headers: { Cookie: cookie }, redirect: 'manual',
            body: new URLSearchParams({ csrf_token: 'test-token', reply_id: '3', resend_confirmation: '1' })
        });
        assert.strictEqual(resend.status, 302);
        await resend.text();
        const renewedToken = token();
        assert.notStrictEqual(renewedToken, expiredToken);
        assert.strictEqual((await confirm(expiredToken)).status, 400);
        assert.strictEqual((await confirm(renewedToken)).status, 200);
        const notifyFailure = await request('notifyfail@example.test');
        assert.strictEqual(notifyFailure.status, 503);
        assert.strictEqual(notifyFailure.body.stored, true);
        assert(notifyFailure.body.message.includes('nicht erneut senden'));
        const confirmFailure = await request('confirmfail@example.test');
        assert.strictEqual(confirmFailure.status, 503);
        assert.strictEqual(confirmFailure.body.stored, true);
        const finalStatuses = await (await fetch(url + '/test-status.php')).json();
        assert.deepStrictEqual(finalStatuses.map(x => x.delivery_status), ['sent', 'unknown', 'sent', 'pending_email', 'pending_email']);
        await (await fetch(url + '/test-clear-rate.php')).text();
        assert.strictEqual((await request('news@example.test', { newsletter_optin: '1' })).status, 200);
        assert.strictEqual(fs.readFileSync(newsletter, 'utf8'), 'news@example.test\n', 'newsletter opt-in remains separate');
        console.log('Confirmed automatic Exposé request and delivery tests passed.');
    } finally {
        if (child.exitCode === null) {
            const exited = new Promise(resolve => child.on('exit', resolve));
            if (process.platform === 'win32') {
                const stop = spawnSync('powershell', ['-NoProfile', '-Command', `Stop-Process -Id ${child.pid}`]);
                assert.strictEqual(stop.status, 0, String(stop.stderr));
            } else {
                child.kill();
            }
            await exited;
        }
        for (const file of created) if (fs.existsSync(file)) fs.unlinkSync(file);
        for (const file of fs.readdirSync(path.join(dir, 'sessions'))) fs.unlinkSync(path.join(dir, 'sessions', file));
        for (const name of dirs) fs.rmdirSync(path.join(dir, name));
        fs.rmdirSync(dir);
    }
}
main().catch(error => { console.error(error); process.exitCode = 1; });
