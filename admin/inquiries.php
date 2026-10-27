<?php

/**
 * admin/inquiries.php – Kontaktanfragen verwalten
 */
require_once __DIR__ . '/_auth_check.php';

$pdo = db();
$id  = isset($_GET['id']) ? (int)$_GET['id'] : null;
$statusFilter = trim((string)($_GET['status'] ?? ''));
$searchQuery = trim((string)($_GET['q'] ?? ''));

function extract_subject_from_message(string $message): string
{
    if (preg_match('/^Betreff:\s*(.+)$/mi', $message, $m)) {
        return trim($m[1]);
    }
    if (preg_match('/^Anfrage über Formular:\s*(.+)$/mi', $message, $m)) {
        return trim($m[1]);
    }
    return 'Kontaktanfrage';
}

function extract_property_ref_from_message(string $message): string
{
    if (preg_match('/^Immobilie:\s*(.+)$/mi', $message, $m)) {
        return trim($m[1]);
    }
    return '';
}

// ── CSV-Export ────────────────────────────────────────────────
if (isset($_GET['export']) && $_GET['export'] === 'inquiries') {
    if (!csrf_verify($_GET['token'] ?? '')) {
        die('Ungültiges Sicherheitstoken.');
    }
    $rows = $pdo->query(
        'SELECT id, name, email, phone, message, status, created_at FROM contact_inquiries ORDER BY created_at DESC'
    )->fetchAll(PDO::FETCH_ASSOC);
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="anfragen-' . date('Y-m-d') . '.csv"');
    $out = fopen('php://output', 'w');
    fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF)); // UTF-8 BOM für Excel
    fputcsv($out, ['ID', 'Name', 'E-Mail', 'Telefon', 'Nachricht', 'Status', 'Datum'], ';');
    foreach ($rows as $r) {
        fputcsv($out, array_values($r), ';');
    }
    fclose($out);
    exit;
}

// ── Als gelesen markieren ─────────────────────────────────────
if ($id && isset($_GET['mark_read'])) {
    if (!csrf_verify($_GET['token'] ?? '')) {
        die('Ungültiges Sicherheitstoken.');
    }
    $pdo->prepare("UPDATE contact_inquiries SET status='read' WHERE id=?")->execute([$id]);
    header('Location: ' . BASE . '/admin/inquiries.php?id=' . $id . '&read=1');
    exit;
}

if ($id && isset($_GET['delete'])) {
    if (!csrf_verify($_GET['token'] ?? '')) {
        die('Ungültiges Sicherheitstoken.');
    }
    $pdo->prepare('DELETE FROM contact_inquiries WHERE id = ?')->execute([$id]);
    header('Location: ' . BASE . '/admin/inquiries.php?deleted=1');
    exit;
}

// ── E-Mail-Antwort senden ─────────────────────────────────────
$replyError = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_reply'])) {
    if (!csrf_verify($_POST['csrf_token'] ?? '')) {
        $replyError = 'Ungültiges Sicherheitstoken.';
    } else {
        $replyId   = (int)($_POST['reply_id'] ?? 0);
        $replyText = trim($_POST['reply_text'] ?? '');
        $stmt = $pdo->prepare('SELECT * FROM contact_inquiries WHERE id = ?');
        $stmt->execute([$replyId]);
        $inqRow = $stmt->fetch();
        if ($inqRow && $replyText) {
            $subject = 'Re: Ihre Anfrage bei ' . SITE_NAME;
            $body    = $replyText
                . "\n\n" . str_repeat('-', 40)
                . "\nIhre ursprüngliche Nachricht:\n" . $inqRow['message']
                . "\n\n-- \n" . SITE_NAME . "\n" . SITE_PHONE . "\n" . SITE_EMAIL;
            $ok = app_mail_text($inqRow['email'], $subject, $body, MAIL_FROM);
            if ($ok) {
                $pdo->prepare("UPDATE contact_inquiries SET status='replied' WHERE id=?")->execute([$replyId]);
                header('Location: ' . BASE . '/admin/inquiries.php?id=' . $replyId . '&replied=1');
                exit;
            } else {
                $replyError = 'E-Mail konnte nicht gesendet werden. SMTP/Socket-Mailer-Konfiguration unter Einstellungen prüfen.';
                $id = $replyId;
            }
        } else {
            $replyError = 'Antworttext darf nicht leer sein.';
            $id = $replyId;
        }
    }
}

$where = [];
$params = [];
if ($statusFilter !== '') {
    $where[] = 'status = ?';
    $params[] = $statusFilter;
}
if ($searchQuery !== '') {
    $where[] = '(name LIKE ? OR email LIKE ? OR phone LIKE ? OR message LIKE ?)';
    $like = '%' . $searchQuery . '%';
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
}
$sql = 'SELECT * FROM contact_inquiries';
if (!empty($where)) {
    $sql .= ' WHERE ' . implode(' AND ', $where);
}
$sql .= ' ORDER BY created_at DESC LIMIT 300';
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$inquiries = $stmt->fetchAll();
$detail    = null;
if ($id) {
    $stmt = $pdo->prepare('SELECT * FROM contact_inquiries WHERE id = ?');
    $stmt->execute([$id]);
    $detail = $stmt->fetch() ?: null;
}
?>
<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Anfragen – Admin</title>
    <meta name="robots" content="noindex, nofollow">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" crossorigin="anonymous">
    <style>
        .detail-grid {
            display: grid;
            grid-template-columns: max-content 1fr;
            gap: .4rem 1rem;
            font-size: .9rem;
        }

        .detail-grid dt {
            font-weight: 600;
            color: #555;
        }

        .detail-grid dd {
            margin: 0;
        }

        .msg-box {
            background: #f5f5f5;
            border-radius: 8px;
            padding: 1rem;
            white-space: pre-wrap;
            font-size: .9rem;
            line-height: 1.6;
        }

        .btn-success {
            background: #16a34a;
            color: #fff;
        }

        .btn-success:hover {
            background: #15803d;
        }

        .badge.replied {
            background: #d1fae5;
            color: #065f46;
        }

        .reply-form-wrap {
            margin-top: 1.5rem;
            padding-top: 1.25rem;
            border-top: 2px solid #e8f0fb;
        }

        .reply-form-wrap h3 {
            margin: 0 0 .85rem;
            font-size: .95rem;
            color: #1a3a5c;
        }

        .reply-field label {
            display: block;
            font-size: .78rem;
            font-weight: 700;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: .06em;
            margin-bottom: 4px;
        }

        .toolbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.25rem;
            flex-wrap: wrap;
            gap: .5rem;
        }

        .filter-pills {
            display: flex;
            gap: .4rem;
            flex-wrap: wrap;
        }

        .filter-pills a {
            padding: .3rem .75rem;
            border-radius: 20px;
            font-size: .8rem;
            font-weight: 600;
            text-decoration: none;
            background: #e5e7eb;
            color: #374151;
            transition: background .15s;
        }

        .filter-pills a.active,
        .filter-pills a:hover {
            background: #1a3a5c;
            color: #fff;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: .88rem;
        }

        th {
            text-align: left;
            padding: .65rem 1rem;
            border-bottom: 1px solid #e8ecf0;
            color: #64748b;
            font-size: .75rem;
            font-weight: 700;
            text-transform: uppercase;
            background: #f8fafc;
        }

        td {
            padding: .75rem 1rem;
            border-bottom: 1px solid #f0f0f0;
            vertical-align: middle;
        }

        tr:hover td {
            background: #fafbff;
        }

        .card-inner {
            padding: 1.5rem;
        }
    </style>
</head>

<body>
    <?php
    $activePage  = 'inquiries';
    $newContacts = (int)$pdo->query("SELECT COUNT(*) FROM contact_inquiries WHERE status='new'")->fetchColumn();
    require __DIR__ . '/_sidebar.php';
    ?>
    <div class="main">
        <header class="topbar">
            <div class="topbar-title"><i class="fa fa-envelope" style="margin-right:.4rem"></i> Anfragen</div>
            <div class="topbar-meta">
                <div class="topbar-user">
                    <div class="avatar"><?= strtoupper(substr(htmlspecialchars($admin['username']), 0, 1)) ?></div>
                    <span><?= htmlspecialchars($admin['username']) ?></span>
                </div>
            </div>
        </header>
        <div class="content">
            <div class="toolbar">
                <div style="font-size:1rem;font-weight:700;color:#1a3a5c;">
                    Anfragen <span style="font-weight:400;color:#6b7280;">(<?= count($inquiries) ?>)</span>
                </div>
                <div style="display:flex;gap:.5rem;">
                    <?php
                    $statusFilter = $_GET['status'] ?? '';
                    $countNew  = count(array_filter($inquiries, fn($q) => $q['status'] === 'new'));
                    ?>
                    <div class="filter-pills">
                        <a href="?" class="<?= !$statusFilter ? 'active' : '' ?>">Alle</a>
                        <a href="?status=new&q=<?= urlencode($searchQuery) ?>" class="<?= $statusFilter === 'new' ? 'active' : '' ?>">Neu <?= $countNew ? "($countNew)" : '' ?></a>
                        <a href="?status=read&q=<?= urlencode($searchQuery) ?>" class="<?= $statusFilter === 'read' ? 'active' : '' ?>">Gelesen</a>
                        <a href="?status=replied&q=<?= urlencode($searchQuery) ?>" class="<?= $statusFilter === 'replied' ? 'active' : '' ?>">Beantwortet</a>
                    </div>
                    <form method="get" style="display:flex;gap:.45rem;align-items:center;">
                        <?php if ($statusFilter !== ''): ?>
                            <input type="hidden" name="status" value="<?= htmlspecialchars($statusFilter) ?>">
                        <?php endif; ?>
                        <input type="text" name="q" value="<?= htmlspecialchars($searchQuery) ?>" placeholder="Suche Name, E-Mail, Telefon, Text" style="padding:.4rem .55rem;border:1px solid #d5dbe3;border-radius:6px;min-width:260px;">
                        <button type="submit" class="btn btn-sm btn-secondary">Suchen</button>
                    </form>
                    <a href="?export=inquiries&token=<?= urlencode(CSRF_TOKEN) ?>" class="btn btn-sm btn-outline" title="Als CSV herunterladen">
                        <i class="fa fa-download"></i> CSV-Export
                    </a>
                </div>
            </div>
            <?php if (isset($_GET['read'])): ?>
                <div class="alert alert-info">Als gelesen markiert.</div>
            <?php endif; ?>
            <?php if (isset($_GET['deleted'])): ?>
                <div class="alert alert-success">Anfrage gelöscht.</div>
            <?php endif; ?>

            <?php if ($detail): ?>
                <div class="card">
                    <div style="display:flex;justify-content:space-between;margin-bottom:1rem;">
                        <h2 style="margin:0;font-size:1rem;">Anfrage #<?= $detail['id'] ?></h2>
                        <div style="display:flex;gap:.5rem;">
                            <?php if ($detail['status'] === 'new'): ?>
                                <a href="?id=<?= $detail['id'] ?>&mark_read=1&token=<?= urlencode(CSRF_TOKEN) ?>" class="btn btn-sm btn-primary">✓ Als gelesen markieren</a>
                            <?php endif; ?>
                            <a href="?id=<?= $detail['id'] ?>&delete=1&token=<?= urlencode(CSRF_TOKEN) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Anfrage wirklich löschen?')">Löschen</a>
                            <a href="<?= BASE ?>/admin/inquiries.php" class="btn btn-sm btn-secondary">← Zurück</a>
                        </div>
                    </div>
                    <dl class="detail-grid">
                        <dt>Name</dt>
                        <dd><?= htmlspecialchars($detail['name']) ?></dd>
                        <dt>E-Mail</dt>
                        <dd><a href="mailto:<?= htmlspecialchars($detail['email']) ?>"><?= htmlspecialchars($detail['email']) ?></a></dd>
                        <dt>Telefon</dt>
                        <dd><?= htmlspecialchars($detail['phone'] ?: '—') ?></dd>
                        <dt>Immobilie</dt>
                        <dd><?= $detail['property_id'] ? '#' . $detail['property_id'] : '—' ?></dd>
                        <dt>Datum</dt>
                        <dd><?= date('d.m.Y H:i', strtotime($detail['created_at'])) ?></dd>
                        <dt>Status</dt>
                        <dd><span class="badge <?= $detail['status'] ?>"><?php
                                                                            echo match ($detail['status']) {
                                                                                'new'     => 'Neu',
                                                                                'replied' => '&#10003; Beantwortet',
                                                                                default   => 'Gelesen',
                                                                            };
                                                                            ?></span></dd>
                    </dl>
                    <h3 style="margin:1.2rem 0 .5rem;font-size:.95rem;">Nachricht:</h3>
                    <div class="msg-box"><?= htmlspecialchars($detail['message']) ?></div>

                    <!-- ── Reply-Formular ── -->
                    <div class="reply-form-wrap">
                        <h3>&#9993; Antwort per E-Mail senden</h3>

                        <?php if (isset($_GET['replied'])): ?>
                            <div class="alert alert-success">&#10003; Antwort erfolgreich gesendet &ndash; Anfrage als &bdquo;Beantwortet&ldquo; markiert.</div>
                        <?php endif; ?>
                        <?php if ($replyError): ?>
                            <div class="alert alert-error">&#9888; <?= htmlspecialchars($replyError) ?></div>
                        <?php endif; ?>

                        <form method="POST" autocomplete="off">
                            <?= csrf_field() ?>
                            <input type="hidden" name="send_reply" value="1">
                            <input type="hidden" name="reply_id" value="<?= $detail['id'] ?>">

                            <div class="reply-row">
                                <div class="reply-field">
                                    <label>An</label>
                                    <input type="text" readonly value="<?= htmlspecialchars($detail['name']) ?> &lt;<?= htmlspecialchars($detail['email']) ?>&gt;">
                                </div>
                                <div class="reply-field">
                                    <label>Betreff</label>
                                    <input type="text" readonly value="Re: Ihre Anfrage bei <?= htmlspecialchars(SITE_NAME) ?>">
                                </div>
                            </div>

                            <div class="reply-field" style="margin-bottom:.75rem;">
                                <label>Ihre Antwort</label>
                                <textarea name="reply_text" required rows="7"
                                    placeholder="Sehr geehrte(r) <?= htmlspecialchars($detail['name']) ?>,..?"></textarea>
                            </div>

                            <div style="display:flex;gap:.5rem;align-items:center;flex-wrap:wrap;">
                                <button type="submit" class="btn btn-primary">&#9993; Antwort senden</button>
                                <?php if (!empty($detail['phone'])): ?>
                                    <a href="tel:<?= htmlspecialchars($detail['phone']) ?>" class="btn btn-secondary">&#128222; <?= htmlspecialchars($detail['phone']) ?></a>
                                <?php endif; ?>
                                <a href="mailto:<?= htmlspecialchars($detail['email']) ?>" class="btn btn-secondary" style="margin-left:auto;">Nur mailto: öffnen</a>
                            </div>
                        </form>
                    </div>
                </div>
            <?php endif; ?>

            <div class="card">
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>E-Mail</th>
                            <th>Telefon</th>
                            <th>Betreff</th>
                            <th>Immobilie</th>
                            <th>Nachricht</th>
                            <th>Status</th>
                            <th>Datum</th>
                            <th>Aktion</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($inquiries as $inq): ?>
                            <tr style="<?= $inq['status'] === 'new' ? 'font-weight:600;' : '' ?>">
                                <td><?= $inq['id'] ?></td>
                                <td><?= htmlspecialchars($inq['name']) ?></td>
                                <td><a href="mailto:<?= htmlspecialchars($inq['email']) ?>"><?= htmlspecialchars($inq['email']) ?></a></td>
                                <td><?= htmlspecialchars($inq['phone'] ?: '—') ?></td>
                                <td><?= htmlspecialchars(extract_subject_from_message((string)$inq['message'])) ?></td>
                                <td>
                                    <?php
                                    $propertyText = extract_property_ref_from_message((string)$inq['message']);
                                    if ($propertyText !== '') {
                                        echo htmlspecialchars($propertyText);
                                    } elseif (!empty($inq['property_id'])) {
                                        echo '# ' . (int)$inq['property_id'];
                                    } else {
                                        echo '—';
                                    }
                                    ?>
                                </td>
                                <td><?= htmlspecialchars(mb_substr($inq['message'], 0, 55)) ?>…</td>
                                <td><span class="badge <?= $inq['status'] ?>"><?php
                                                                                echo match ($inq['status']) {
                                                                                    'new'     => 'Neu',
                                                                                    'replied' => '&#10003; Beantwortet',
                                                                                    default   => 'Gelesen',
                                                                                };
                                                                                ?></span></td>
                                <td><?= date('d.m.Y H:i', strtotime($inq['created_at'])) ?></td>
                                <td style="display:flex;gap:.4rem;flex-wrap:wrap;">
                                    <a href="?id=<?= $inq['id'] ?>" class="btn btn-sm btn-secondary">Details</a>
                                    <a href="?id=<?= $inq['id'] ?>&delete=1&token=<?= urlencode(CSRF_TOKEN) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Anfrage wirklich löschen?')">Löschen</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($inquiries)): ?>
                            <tr>
                                <td colspan="10" style="text-align:center;color:#999;padding:2rem;">Keine Anfragen vorhanden.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div><!-- /content -->
    </div><!-- /main -->
</body>

</html>