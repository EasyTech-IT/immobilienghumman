const assert = require('assert');
const fs = require('fs');
const os = require('os');
const path = require('path');
const net = require('net');
const { spawn, spawnSync } = require('child_process');

const root = path.resolve(__dirname, '..');
const dir = fs.mkdtempSync(path.join(os.tmpdir(), 'expose-http-test-'));
const dirs = ['api', 'admin', 'includes', 'vendor', 'sessions'];
const created = [];
for (const name of dirs) fs.mkdirSync(path.join(dir, name));
function write(name, content) {
    const target = path.join(dir, name);
    fs.writeFileSync(target, content);
    created.push(target);
}
for (const name of ['api/contact.php', 'admin/_auth_check.php', 'admin/expose-download.php', 'includes/expose-pdf.php', 'includes/inquiry-mail.php', 'includes/analyse-fragen.php']) {
    write(name, fs.readFileSync(path.join(root, name)));
}
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
define('MAIL_TO', 'admin@example.test');
define('MAIL_SUBJECT_PREFIX', '[Test]');
function csrf_verify(string $token = ''): bool { return $token === 'test-token'; }
function verify_turnstile_token(string $token, string $ip = ''): bool { return true; }
function mailer_send(string $to, string $subject, string $text = '', string $html = '', string $reply = '', string $from = '', string $name = '', ?array $attachment = null): bool {
    if ($attachment !== null) {
        if ($to === 'fail@example.test') return false;
        file_put_contents(getenv('TEST_ATTACHMENT'), $attachment['content']);
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
    const database = path.join(dir, 'test.sqlite');
    created.push(attachment, database);
    const child = spawn('php', ['-S', `127.0.0.1:${port}`, '-t', dir], {
        env: {
            ...process.env,
            TEST_AUTOLOAD: path.join(root, 'vendor', 'autoload.php'),
            TEST_SESSION_DIR: path.join(dir, 'sessions'),
            TEST_DATABASE: database,
            TEST_ATTACHMENT: attachment
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
                privacy_accept: '1', ...extra
            });
            const response = await fetch(url + '/api/contact.php?action=inquiry', { method: 'POST', body: fields });
            return { status: response.status, body: await response.json() };
        };
        const result = await request('anna@example.test');
        assert.strictEqual(result.status, 200, JSON.stringify(result.body));
        assert.strictEqual(result.body.success, true);
        const anonymous = await fetch(url + '/admin/expose-download.php?inquiry_id=1', { redirect: 'manual' });
        assert.strictEqual(anonymous.status, 302);
        const login = await fetch(url + '/test-login.php');
        const cookie = login.headers.get('set-cookie').split(';')[0];
        const download = await fetch(url + '/admin/expose-download.php?inquiry_id=1', { headers: { Cookie: cookie } });
        assert.strictEqual(download.status, 200);
        assert.strictEqual(download.headers.get('content-type'), 'application/pdf');
        assert.deepStrictEqual(Buffer.from(await download.arrayBuffer()), fs.readFileSync(attachment));
        const propertyPdf = await fetch(url + '/admin/expose-download.php?property_id=1', { headers: { Cookie: cookie } });
        assert.strictEqual(propertyPdf.status, 200);
        assert.notDeepStrictEqual(Buffer.from(await propertyPdf.arrayBuffer()), fs.readFileSync(attachment));
        const failed = await request('fail@example.test');
        assert.strictEqual(failed.status, 503);
        assert.strictEqual(failed.body.success, false);
        assert.strictEqual(failed.body.stored, true);
        const statuses = await (await fetch(url + '/test-status.php')).json();
        assert.deepStrictEqual(statuses.map(x => x.delivery_status), ['sent', 'unknown']);
        const failedCopy = await fetch(url + '/admin/expose-download.php?inquiry_id=2', { headers: { Cookie: cookie } });
        assert.strictEqual(failedCopy.status, 200);
        assert.notDeepStrictEqual(Buffer.from(await failedCopy.arrayBuffer()), fs.readFileSync(attachment));
        const invalid = await request('anna@example.test', { privacy_accept: '0' });
        assert.strictEqual(invalid.status, 400);
        const missing = await request('anna@example.test', { property_id: '99' });
        assert.strictEqual(missing.status, 404);
        const renderFailure = await request('anna@example.test', { property_id: '2' });
        assert.strictEqual(renderFailure.status, 500);
        assert.strictEqual(renderFailure.body.success, false);
        const finalStatuses = await (await fetch(url + '/test-status.php')).json();
        assert.strictEqual(finalStatuses.length, 2);
        console.log('HTTP request, authenticated snapshot and delivery failure tests passed.');
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
