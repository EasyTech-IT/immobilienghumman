<?php

/**
 * admin/subscribers.php – Abonnenten (Double-Opt-In) verwalten
 */
require_once __DIR__ . '/_auth_check.php';
require_once __DIR__ . '/../includes/subscribers.php';

$pdo = db();
subscribers_ensure_tables($pdo);

if (isset($_GET['export']) && $_GET['export'] === 'subscribers') {
    if (!csrf_verify($_GET['token'] ?? '')) {
        die('Ungültiges Sicherheitstoken.');
    }
    $rows = $pdo->query(
        'SELECT id, email, name, status, source, consent_at, confirmed_at, unsubscribed_at FROM subscribers ORDER BY id DESC'
    )->fetchAll(PDO::FETCH_ASSOC);
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="abonnenten-' . date('Y-m-d') . '.csv"');
    $out = fopen('php://output', 'w');
    fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF));
    fputcsv($out, ['ID', 'E-Mail', 'Name', 'Status', 'Quelle', 'Einwilligung', 'Bestätigt', 'Abgemeldet'], ';');
    foreach ($rows as $r) {
        fputcsv($out, array_values($r), ';');
    }
    fclose($out);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify($_POST['csrf_token'] ?? '')) {
        die('Ungültiges Sicherheitstoken.');
    }
    $delId = (int)($_POST['delete_id'] ?? 0);
    if ($delId > 0) {
        $pdo->prepare('DELETE FROM property_notifications WHERE subscriber_id = ?')->execute([$delId]);
        $pdo->prepare('DELETE FROM subscribers WHERE id = ?')->execute([$delId]);
    }
    header('Location: ' . BASE . '/admin/subscribers.php?deleted=1');
    exit;
}

$counts = ['confirmed' => 0, 'pending' => 0, 'unsubscribed' => 0];
foreach ($pdo->query('SELECT status, COUNT(*) c FROM subscribers GROUP BY status')->fetchAll() as $r) {
    $counts[$r['status']] = (int)$r['c'];
}
$subs = $pdo->query('SELECT * FROM subscribers ORDER BY id DESC LIMIT 500')->fetchAll();
$labels = ['confirmed' => 'Bestätigt', 'pending' => 'Ausstehend', 'unsubscribed' => 'Abgemeldet'];
?>
<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Abonnenten – Admin</title>
    <meta name="robots" content="noindex, nofollow">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" crossorigin="anonymous">
</head>

<body>
    <?php
    $activePage  = 'subscribers';
    $newContacts = (int)$pdo->query("SELECT COUNT(*) FROM contact_inquiries WHERE status='new'")->fetchColumn();
    require __DIR__ . '/_sidebar.php';
    ?>
    <div class="main">
        <header class="topbar">
            <div class="topbar-title"><i class="fa fa-bell" style="margin-right:.4rem"></i> Abonnenten</div>
            <div class="topbar-meta">
                <div class="topbar-user">
                    <div class="avatar"><?= strtoupper(substr(htmlspecialchars($admin['username']), 0, 1)) ?></div>
                    <span><?= htmlspecialchars($admin['username']) ?></span>
                </div>
            </div>
        </header>
        <div class="content">
            <div class="toolbar">
                <div style="font-weight:700;color:#1a3a5c;">
                    <?= $counts['confirmed'] ?> bestätigt · <?= $counts['pending'] ?> ausstehend · <?= $counts['unsubscribed'] ?> abgemeldet
                </div>
                <a href="?export=subscribers&token=<?= urlencode(CSRF_TOKEN) ?>" class="btn btn-sm btn-outline"><i class="fa fa-download"></i> CSV-Export</a>
            </div>
            <?php if (isset($_GET['deleted'])): ?>
                <div class="alert alert-success">Abonnent gelöscht.</div>
            <?php endif; ?>
            <div class="alert alert-info">Benachrichtigungen erhalten nur <strong>bestätigte</strong> Abonnenten (Double-Opt-In). Versand erfolgt beim Speichern einer verfügbaren Immobilie, wenn „Abonnenten benachrichtigen“ aktiv ist.</div>
            <div class="card">
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>E-Mail</th>
                            <th>Name</th>
                            <th>Status</th>
                            <th>Quelle</th>
                            <th>Einwilligung</th>
                            <th>Bestätigt</th>
                            <th>Aktion</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($subs as $s): ?>
                            <tr>
                                <td><?= (int)$s['id'] ?></td>
                                <td><?= htmlspecialchars($s['email']) ?></td>
                                <td><?= htmlspecialchars((string)$s['name']) ?></td>
                                <td><?= htmlspecialchars($labels[$s['status']] ?? $s['status']) ?></td>
                                <td><?= htmlspecialchars((string)$s['source']) ?></td>
                                <td><?= $s['consent_at'] ? date('d.m.Y H:i', strtotime($s['consent_at'])) : '—' ?></td>
                                <td><?= $s['confirmed_at'] ? date('d.m.Y H:i', strtotime($s['confirmed_at'])) : '—' ?></td>
                                <td>
                                    <form method="post" onsubmit="return confirm('Abonnent wirklich löschen?')" style="margin:0">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="delete_id" value="<?= (int)$s['id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-danger">Löschen</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($subs)): ?>
                            <tr>
                                <td colspan="8" style="text-align:center;color:#999;padding:2rem;">Noch keine Abonnenten.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>

</html>
