<?php
require_once __DIR__ . '/_auth_check.php';

$inquiryId = (int)($_GET['inquiry_id'] ?? 0);
$propertyId = (int)($_GET['property_id'] ?? 0);
if (($inquiryId > 0) === ($propertyId > 0)) {
    http_response_code(400);
    exit('Bitte eine Anfrage oder Immobilie auswählen.');
}

try {
    if ($inquiryId > 0) {
        $stmt = db()->prepare('SELECT pdf FROM expose_requests WHERE inquiry_id = ?');
        $stmt->execute([$inquiryId]);
        $pdf = $stmt->fetchColumn();
        if (is_resource($pdf)) $pdf = stream_get_contents($pdf);
    } else {
        $stmt = db()->prepare('SELECT * FROM properties WHERE id = ?');
        $stmt->execute([$propertyId]);
        $property = $stmt->fetch();
        $pdf = false;
        if ($property) {
            require_once __DIR__ . '/../includes/expose-pdf.php';
            $pdf = expose_pdf_render($property);
        }
    }
    if (!is_string($pdf) || $pdf === '') {
        http_response_code(404);
        exit('Exposé nicht gefunden.');
    }
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="expose.pdf"');
    header('Content-Length: ' . strlen($pdf));
    header('Cache-Control: private, no-store');
    header('X-Content-Type-Options: nosniff');
    echo $pdf;
} catch (Throwable $e) {
    error_log('Exposé-Download fehlgeschlagen: ' . $e->getMessage());
    http_response_code(503);
    exit('Das Exposé ist derzeit nicht verfügbar.');
}
