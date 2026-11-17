const assert = require('assert');
const net = require('net');
const { spawn } = require('child_process');

async function exerciseSmtp(acceptRecipient) {
    let message = '';
    const server = net.createServer(socket => {
        let pending = '';
        let dataMode = false;
        socket.setEncoding('utf8');
        socket.write('220 test.example ESMTP\r\n');
        socket.on('data', chunk => {
            pending += chunk;
            while (true) {
                if (dataMode) {
                    const end = pending.indexOf('\r\n.\r\n');
                    if (end < 0) return;
                    message = pending.slice(0, end);
                    pending = pending.slice(end + 5);
                    dataMode = false;
                    socket.write('250 queued\r\n');
                } else {
                    const end = pending.indexOf('\r\n');
                    if (end < 0) return;
                    const line = pending.slice(0, end);
                    pending = pending.slice(end + 2);
                    if (line.startsWith('EHLO ')) socket.write('250 test.example\r\n');
                    else if (line.startsWith('MAIL FROM:')) socket.write('250 sender ok\r\n');
                    else if (line.startsWith('RCPT TO:')) socket.write(acceptRecipient ? '250 recipient ok\r\n' : '550 recipient rejected\r\n');
                    else if (line === 'DATA') { dataMode = true; socket.write('354 send data\r\n'); }
                    else if (line === 'QUIT') socket.end('221 bye\r\n');
                }
            }
        });
    });
    await new Promise(resolve => server.listen(0, '127.0.0.1', resolve));
    const code = [
        "require 'includes/mailer.php';",
        '$m = new SimpleSocketMailer("127.0.0.1", (int)$argv[1], "", "", "", false, 5);',
        '$pdf = str_repeat("0123456789abcdef", 65536);',
        'echo $m->send("sender@example.test", "Test", "receiver@example.test", "Exposé", "Test", "", "", ["content" => $pdf]) ? "sent" : "failed";'
    ].join(' ');
    const child = spawn('php', ['-r', code, String(server.address().port)]);
    let stdout = '';
    let stderr = '';
    child.stdout.on('data', data => { stdout += data; });
    child.stderr.on('data', data => { stderr += data; });
    const codeResult = await new Promise((resolve, reject) => {
        child.on('error', reject);
        child.on('close', resolve);
    });
    await new Promise(resolve => server.close(resolve));
    assert.strictEqual(codeResult, 0, stderr);
    assert.strictEqual(stdout, acceptRecipient ? 'sent' : 'failed');
    if (acceptRecipient) {
        const match = message.match(/Content-Disposition: attachment; filename="expose\.pdf"\r\nContent-Transfer-Encoding: base64\r\n\r\n([A-Za-z0-9+\/=\r\n]+)\r\n--=_ghumman_mixed_/);
        assert(match, 'PDF attachment missing');
        assert.deepStrictEqual(Buffer.from(match[1], 'base64'), Buffer.from('0123456789abcdef'.repeat(65536)));
    }
}

(async () => {
    await exerciseSmtp(true);
    await exerciseSmtp(false);
    console.log('SMTP attachment and rejection tests passed.');
})().catch(error => {
    console.error(error);
    process.exitCode = 1;
});
