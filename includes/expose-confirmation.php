<?php

require_once __DIR__ . '/expose-pdf.php';

function expose_automatic_ensure_tables(PDO $pdo): void
{
    expose_requests_ensure_table($pdo);
    $pdo->exec("CREATE TABLE IF NOT EXISTS expose_confirmations (
        inquiry_id INT UNSIGNED PRIMARY KEY,
        token_hash CHAR(64) NOT NULL UNIQUE,
        expires_at BIGINT NOT NULL,
        used_at BIGINT NULL,
        FOREIGN KEY (inquiry_id) REFERENCES expose_requests(inquiry_id) ON DELETE CASCADE
    ) ENGINE=InnoDB");
    $pdo->exec("CREATE TABLE IF NOT EXISTS expose_approvals (
        property_id INT UNSIGNED PRIMARY KEY,
        property_hash CHAR(64) NOT NULL
    ) ENGINE=InnoDB");
}

function expose_property_hash(array $property): string
{
    unset($property['updated_at']);
    ksort($property);
    $templateHash = hash_file('sha256', __DIR__ . '/expose-pdf.php');
    $logoHash = hash_file('sha256', __DIR__ . '/../assets/expose-logo.png');
    if ($templateHash === false || $logoHash === false) throw new RuntimeException('Exposé-Vorlage oder Logo ist nicht verfügbar.');
    return hash('sha256', json_encode([$property, $templateHash, $logoHash], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR));
}

function expose_property_is_approved(PDO $pdo, array $property): bool
{
    $stmt = $pdo->prepare('SELECT property_hash FROM expose_approvals WHERE property_id = ?');
    $stmt->execute([(int)$property['id']]);
    $hash = $stmt->fetchColumn();
    return is_string($hash) && hash_equals($hash, expose_property_hash($property));
}

function expose_store_pending_confirmation(PDO $pdo, array $fields, string $token): int
{
    if (!preg_match('/^[a-f0-9]{64}$/', $token)) throw new InvalidArgumentException('Ungültiger Bestätigungscode.');
    $pdo->beginTransaction();
    try {
        $stmt = $pdo->prepare('INSERT INTO contact_inquiries (name, email, phone, message, property_id, ip_address, created_at) VALUES (?, ?, ?, ?, ?, ?, CURRENT_TIMESTAMP)');
        $stmt->execute($fields);
        $id = (int)$pdo->lastInsertId();
        $stmt = $pdo->prepare("INSERT INTO expose_requests (inquiry_id, pdf, delivery_status) VALUES (?, ?, 'pending_email')");
        $stmt->bindValue(1, $id, PDO::PARAM_INT);
        $stmt->bindValue(2, '', PDO::PARAM_LOB);
        $stmt->execute();
        $stmt = $pdo->prepare('INSERT INTO expose_confirmations (inquiry_id, token_hash, expires_at) VALUES (?, ?, ?)');
        $stmt->execute([$id, hash('sha256', $token), time() + 48 * 3600]);
        $pdo->commit();
        return $id;
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        throw $e;
    }
}

function expose_confirm_request(PDO $pdo, string $token): ?array
{
    if (!preg_match('/^[a-f0-9]{64}$/', $token)) return null;
    $pdo->beginTransaction();
    try {
        $stmt = $pdo->prepare('SELECT c.inquiry_id, i.name, i.email, i.phone, i.property_id FROM expose_confirmations c JOIN contact_inquiries i ON i.id = c.inquiry_id WHERE c.token_hash = ? AND c.used_at IS NULL AND c.expires_at >= ?');
        $stmt->execute([hash('sha256', $token), time()]);
        $request = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$request) {
            $pdo->rollBack();
            return null;
        }
        $stmt = $pdo->prepare('SELECT * FROM properties WHERE id = ?');
        $stmt->execute([$request['property_id']]);
        $property = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$property || ($property['status'] ?? '') !== 'available' || !expose_property_is_approved($pdo, $property)) {
            throw new RuntimeException('Das Exposé ist nicht mehr für den automatischen Versand freigegeben.');
        }
        $stmt = $pdo->prepare('UPDATE expose_confirmations SET used_at = ? WHERE inquiry_id = ? AND token_hash = ? AND used_at IS NULL AND expires_at >= ?');
        $now = time();
        $stmt->execute([$now, $request['inquiry_id'], hash('sha256', $token), $now]);
        if ($stmt->rowCount() !== 1) {
            $pdo->rollBack();
            return null;
        }

        $stmt = $pdo->prepare("UPDATE expose_requests SET delivery_status = 'new' WHERE inquiry_id = ? AND delivery_status = 'pending_email'");
        $stmt->execute([$request['inquiry_id']]);
        if ($stmt->rowCount() !== 1) throw new RuntimeException('Exposé-Anfrage hat einen ungültigen Status.');
        $pdo->commit();
        $request['property'] = $property;
        return $request;
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        throw $e;
    }
}

function expose_send_confirmation(string $email, string $name, string $propertyTitle, string $token): bool
{
    $url = rtrim(SITE_URL, '/') . '/expose-confirm?t=' . $token;
    $body = "Guten Tag $name,\n\nvielen Dank für Ihr Interesse an $propertyTitle.\n\nBitte bestätigen Sie Ihre E-Mail-Adresse über den folgenden Link. Anschließend senden wir Ihnen das Exposé zu:\n$url\n\nDer Link ist 48 Stunden gültig. Falls Sie keine Anfrage gestellt haben, ignorieren Sie diese Nachricht.\n\n" . SITE_NAME;
    return app_mail_text($email, 'Bitte bestätigen Sie Ihre Exposé-Anfrage – ' . SITE_NAME, $body);
}
