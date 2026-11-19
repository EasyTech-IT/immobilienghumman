<?php
require_once __DIR__ . '/_auth_check.php';
require_once __DIR__ . '/../includes/expose-confirmation.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !csrf_verify($_POST['csrf_token'] ?? '')) {
    http_response_code(403);
    exit('Ungültige Anfrage.');
}
$id = (int)($_POST['property_id'] ?? 0);
try {
    $pdo = db();
    expose_automatic_ensure_tables($pdo);
    $stmt = $pdo->prepare('SELECT * FROM properties WHERE id = ?');
    $stmt->execute([$id]);
    $property = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$property || ($property['status'] ?? '') !== 'available') {
        http_response_code(400);
        exit('Nur verfügbare Immobilien können freigegeben werden.');
    }
    $pdf = expose_pdf_render($property);
    if (strncmp($pdf, '%PDF-', 5) !== 0) throw new RuntimeException('Exposé konnte nicht geprüft werden.');
    $pdo->beginTransaction();
    $pdo->prepare('DELETE FROM expose_approvals WHERE property_id = ?')->execute([$id]);
    $pdo->prepare('INSERT INTO expose_approvals (property_id, property_hash) VALUES (?, ?)')->execute([$id, expose_property_hash($property)]);
    $pdo->commit();
    header('Location: ' . BASE . '/admin/properties.php?action=edit&id=' . $id . '&expose_approved=1');
} catch (Throwable $e) {
    if (isset($pdo) && $pdo->inTransaction()) $pdo->rollBack();
    error_log('Exposé-Freigabe fehlgeschlagen: ' . $e->getMessage());
    http_response_code(503);
    exit('Das Exposé konnte nicht freigegeben werden. Bitte prüfen Sie Bilder und Unterlagen und versuchen Sie es erneut.');
}
