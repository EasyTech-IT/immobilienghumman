<?php

/**
 * SimpleSocketMailer
 * Direkter SMTP-Versand ueber stream_socket_client (ohne mail() und ohne externe Library).
 */
class SimpleSocketMailer
{
    private $host;
    private $port;
    private $username;
    private $password;
    private $secure;
    private $auth;
    private $timeout;

    /** @var resource|null */
    private $socket = null;

    public function __construct(
        string $host,
        int $port = 587,
        string $username = '',
        string $password = '',
        string $secure = 'tls',
        bool $auth = true,
        int $timeout = 20
    ) {
        $this->host = $host;
        $this->port = $port;
        $this->username = $username;
        $this->password = $password;
        $this->secure = strtolower(trim($secure));
        $this->auth = $auth;
        $this->timeout = $timeout;
    }

    public function send(
        string $fromEmail,
        string $fromName,
        string $to,
        string $subject,
        string $textBody = '',
        string $htmlBody = '',
        string $replyTo = ''
    ): bool {
        if ($this->host === '' || $to === '') {
            error_log('SMTP Mailer: host/to fehlt.');
            return false;
        }

        if ($textBody === '' && $htmlBody !== '') {
            $textBody = trim(strip_tags(str_replace(['<br>', '<br/>', '<br />'], "\n", $htmlBody)));
        }

        try {
            $this->connect();
            $this->expect(220);

            $ehloName = $this->resolveEhloName($fromEmail);
            $this->command('EHLO ' . $ehloName, [250]);

            if ($this->secure === 'tls') {
                $this->command('STARTTLS', [220]);
                if (!stream_socket_enable_crypto($this->socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
                    throw new RuntimeException('STARTTLS konnte nicht aktiviert werden.');
                }
                $this->command('EHLO ' . $ehloName, [250]);
            }

            if ($this->auth) {
                $this->authenticate();
            }

            $this->command('MAIL FROM:<' . $this->sanitizeAddress($fromEmail) . '>', [250]);
            $this->command('RCPT TO:<' . $this->sanitizeAddress($to) . '>', [250, 251]);
            $this->command('DATA', [354]);

            $rawMessage = $this->buildMessage($fromEmail, $fromName, $to, $subject, $textBody, $htmlBody, $replyTo);
            $this->write($this->dotStuff($rawMessage) . "\r\n.\r\n");
            $this->expect(250);

            $this->command('QUIT', [221]);
            $this->disconnect();
            return true;
        } catch (Throwable $e) {
            error_log('SMTP Mailer Fehler: ' . $e->getMessage());
            $this->disconnect();
            return false;
        }
    }

    private function connect(): void
    {
        $transport = ($this->secure === 'ssl') ? 'ssl://' : '';
        $remote = $transport . $this->host . ':' . $this->port;

        $errno = 0;
        $errstr = '';
        $this->socket = @stream_socket_client($remote, $errno, $errstr, $this->timeout, STREAM_CLIENT_CONNECT);
        if (!is_resource($this->socket)) {
            throw new RuntimeException('SMTP Verbindung fehlgeschlagen: ' . $errstr . ' (' . $errno . ')');
        }

        stream_set_timeout($this->socket, $this->timeout);
    }

    private function authenticate(): void
    {
        if ($this->username === '' || $this->password === '') {
            throw new RuntimeException('SMTP AUTH aktiv, aber Username/Passwort fehlen.');
        }

        $this->command('AUTH LOGIN', [334]);
        $this->command(base64_encode($this->username), [334]);
        $this->command(base64_encode($this->password), [235]);
    }

    private function buildMessage(
        string $fromEmail,
        string $fromName,
        string $to,
        string $subject,
        string $textBody,
        string $htmlBody,
        string $replyTo
    ): string {
        $subjectEnc = '=?UTF-8?B?' . base64_encode($subject) . '?=';
        $fromNameEnc = '=?UTF-8?B?' . base64_encode($fromName) . '?=';

        $headers = [];
        $headers[] = 'Date: ' . gmdate('D, d M Y H:i:s O');
        $headers[] = 'From: ' . $fromNameEnc . ' <' . $this->sanitizeAddress($fromEmail) . '>';
        $headers[] = 'To: <' . $this->sanitizeAddress($to) . '>';
        if ($replyTo !== '') {
            $headers[] = 'Reply-To: <' . $this->sanitizeAddress($replyTo) . '>';
        }
        $headers[] = 'Subject: ' . $subjectEnc;
        $headers[] = 'MIME-Version: 1.0';
        $headers[] = 'Message-ID: <' . bin2hex(random_bytes(10)) . '@' . $this->resolveMessageIdDomain($fromEmail) . '>';
        $headers[] = 'X-Mailer: ImmobilienGhumman-SocketMailer/1.0';

        if ($htmlBody !== '') {
            $boundary = '=_ghumman_' . bin2hex(random_bytes(8));
            $headers[] = 'Content-Type: multipart/alternative; boundary="' . $boundary . '"';

            $body = [];
            $body[] = '--' . $boundary;
            $body[] = 'Content-Type: text/plain; charset=UTF-8';
            $body[] = 'Content-Transfer-Encoding: 8bit';
            $body[] = '';
            $body[] = $this->normalizeLineEndings($textBody);
            $body[] = '';
            $body[] = '--' . $boundary;
            $body[] = 'Content-Type: text/html; charset=UTF-8';
            $body[] = 'Content-Transfer-Encoding: 8bit';
            $body[] = '';
            $body[] = $this->normalizeLineEndings($htmlBody);
            $body[] = '';
            $body[] = '--' . $boundary . '--';

            return implode("\r\n", $headers) . "\r\n\r\n" . implode("\r\n", $body);
        }

        $headers[] = 'Content-Type: text/plain; charset=UTF-8';
        $headers[] = 'Content-Transfer-Encoding: 8bit';

        return implode("\r\n", $headers) . "\r\n\r\n" . $this->normalizeLineEndings($textBody);
    }

    private function command(string $command, array $expectedCodes): void
    {
        $this->write($command . "\r\n");
        $this->expectMany($expectedCodes, $command);
    }

    private function expect(int $expectedCode): void
    {
        $this->expectMany([$expectedCode], '');
    }

    private function expectMany(array $expectedCodes, string $context): void
    {
        $response = $this->readResponse();
        $code = (int)substr($response, 0, 3);
        if (!in_array($code, $expectedCodes, true)) {
            throw new RuntimeException('SMTP unerwartete Antwort' . ($context !== '' ? ' bei ' . $context : '') . ': ' . trim($response));
        }
    }

    private function readResponse(): string
    {
        if (!is_resource($this->socket)) {
            throw new RuntimeException('SMTP Socket nicht verfuegbar.');
        }

        $response = '';
        while (($line = fgets($this->socket, 515)) !== false) {
            $response .= $line;
            // Multi-line SMTP response: continue while status code is followed by '-'.
            if (strlen($line) >= 4 && $line[3] !== '-') {
                break;
            }
        }

        if ($response === '') {
            throw new RuntimeException('Leere SMTP Antwort vom Server.');
        }

        return $response;
    }

    private function write(string $data): void
    {
        if (!is_resource($this->socket)) {
            throw new RuntimeException('SMTP Socket nicht geoeffnet.');
        }

        $written = fwrite($this->socket, $data);
        if ($written === false || $written < strlen($data)) {
            throw new RuntimeException('SMTP write fehlgeschlagen.');
        }
    }

    private function dotStuff(string $data): string
    {
        $normalized = $this->normalizeLineEndings($data);
        return preg_replace('/(^|\r\n)\./', '$1..', $normalized) ?? $normalized;
    }

    private function normalizeLineEndings(string $value): string
    {
        return preg_replace('/\r\n|\r|\n/', "\r\n", $value) ?? $value;
    }

    private function sanitizeAddress(string $email): string
    {
        return str_replace(["\r", "\n", '<', '>'], '', trim($email));
    }

    private function resolveEhloName(string $fromEmail): string
    {
        $domain = $this->extractDomain($fromEmail);
        if ($domain !== '') {
            return $domain;
        }

        $siteUrl = defined('SITE_URL') ? (string)SITE_URL : '';
        $siteHost = parse_url($siteUrl, PHP_URL_HOST);
        if (is_string($siteHost) && trim($siteHost) !== '') {
            return trim($siteHost);
        }

        return 'immobilienghumman.de';
    }

    private function resolveMessageIdDomain(string $fromEmail): string
    {
        $domain = $this->extractDomain($fromEmail);
        if ($domain !== '') {
            return $domain;
        }

        return $this->resolveEhloName($fromEmail);
    }

    private function extractDomain(string $email): string
    {
        $email = $this->sanitizeAddress($email);
        if ($email === '' || strpos($email, '@') === false) {
            return '';
        }

        $domain = strtolower(trim((string)substr(strrchr($email, '@') ?: '', 1)));
        if ($domain === '' || strpos($domain, ' ') !== false) {
            return '';
        }

        return $domain;
    }

    private function disconnect(): void
    {
        if (is_resource($this->socket)) {
            fclose($this->socket);
        }
        $this->socket = null;
    }
}
