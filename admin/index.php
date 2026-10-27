<?php

/**
 * admin/index.php – Dashboard
 */
require_once __DIR__ . '/_auth_check.php';

$pdo = db();

// Statistiken in einer Abfrage zusammenfassen
$stats = $pdo->query('
    SELECT
        COUNT(*) AS total_props,
        SUM(status = "available") AS available_props,
        SUM(status = "sold") AS sold_props,
        SUM(status = "rented") AS rented_props,
        SUM(featured = 1) AS featured_props
    FROM properties
')->fetch();

$inquiryStats = $pdo->query('
    SELECT
        COUNT(*) AS total_inq,
        SUM(status = "new") AS new_inq,
        SUM(status = "replied") AS replied_inq
    FROM contact_inquiries
')->fetch();

// Neueste Anfragen
$recentInq = $pdo->query(
    'SELECT id, name, email, message, status, created_at FROM contact_inquiries ORDER BY created_at DESC LIMIT 8'
)->fetchAll();

// Neueste Immobilien
$recentProps = $pdo->query(
    'SELECT id, title, type, offer_type, price, status, created_at FROM properties ORDER BY created_at DESC LIMIT 5'
)->fetchAll();

$propCount    = (int)($stats['total_props'] ?? 0);
$availCount   = (int)($stats['available_props'] ?? 0);
$newContacts  = (int)($inquiryStats['new_inq'] ?? 0);
$contactCount = (int)($inquiryStats['total_inq'] ?? 0);
?>
<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard – <?= htmlspecialchars(SITE_NAME) ?></title>
    <meta name="robots" content="noindex, nofollow">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" crossorigin="anonymous">
    <style>
        *,
        *::before,
        *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        :root {
            --navy: #1a3a5c;
            --navy2: #142d48;
            --accent: #2196f3;
            --green: #27ae60;
            --orange: #e67e22;
            --red: #e74c3c;
            --bg: #f4f6fa;
            --white: #ffffff;
            --border: #e8ecf0;
            --text: #1e293b;
            --muted: #64748b;
            --sidebar-w: 240px;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: var(--bg);
            color: var(--text);
            display: flex;
            min-height: 100vh;
        }

        /* ── Sidebar ── */
        .sidebar {
            width: var(--sidebar-w);
            background: var(--navy);
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            z-index: 100;
            overflow-y: auto;
        }

        .sidebar-logo {
            padding: 1.5rem 1.25rem;
            border-bottom: 1px solid rgba(255, 255, 255, .1);
            text-align: center;
        }

        .sidebar-logo img {
            max-width: 110px;
        }

        .sidebar-logo .site-name {
            color: rgba(255, 255, 255, .7);
            font-size: .7rem;
            letter-spacing: .05em;
            text-transform: uppercase;
            margin-top: .5rem;
        }

        .sidebar nav {
            padding: 1rem 0;
            flex: 1;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: .75rem;
            padding: .8rem 1.25rem;
            color: rgba(255, 255, 255, .75);
            text-decoration: none;
            font-size: .88rem;
            font-weight: 500;
            border-left: 3px solid transparent;
            transition: all .15s;
        }

        .nav-item:hover {
            color: #fff;
            background: rgba(255, 255, 255, .08);
        }

        .nav-item.active {
            color: #fff;
            background: rgba(255, 255, 255, .12);
            border-left-color: var(--accent);
        }

        .nav-item .fa {
            width: 18px;
            text-align: center;
            font-size: .9rem;
        }

        .nav-badge {
            margin-left: auto;
            background: var(--red);
            color: #fff;
            border-radius: 10px;
            padding: 0 7px;
            font-size: .7rem;
            font-weight: 700;
            line-height: 1.6;
        }

        .nav-section-label {
            padding: 1.2rem 1.25rem .4rem;
            font-size: .68rem;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: rgba(255, 255, 255, .35);
        }

        .sidebar-footer {
            border-top: 1px solid rgba(255, 255, 255, .1);
            padding: 1rem;
        }

        .sidebar-footer .nav-item {
            border-radius: 8px;
        }

        /* ── Main Content ── */
        .main {
            margin-left: var(--sidebar-w);
            flex: 1;
            min-width: 0;
        }

        /* ── Top Bar ── */
        .topbar {
            background: var(--white);
            border-bottom: 1px solid var(--border);
            padding: 1rem 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 50;
        }

        .topbar-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--navy);
        }

        .topbar-meta {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .topbar-user {
            display: flex;
            align-items: center;
            gap: .5rem;
            font-size: .875rem;
            color: var(--muted);
        }

        .topbar-user .avatar {
            width: 34px;
            height: 34px;
            background: var(--navy);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: .8rem;
            font-weight: 700;
        }

        .topbar-date {
            font-size: .8rem;
            color: var(--muted);
        }

        /* ── Content Area ── */
        .content {
            padding: 2rem;
        }

        /* ── Stat Cards ── */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.25rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: var(--white);
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 1px 4px rgba(0, 0, 0, .06);
            border: 1px solid var(--border);
            display: flex;
            align-items: flex-start;
            gap: 1rem;
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
            flex-shrink: 0;
        }

        .stat-icon.blue {
            background: #e8f4fd;
            color: #1a73e8;
        }

        .stat-icon.green {
            background: #e8f5e9;
            color: var(--green);
        }

        .stat-icon.orange {
            background: #fff3e0;
            color: var(--orange);
        }

        .stat-icon.red {
            background: #fde8e8;
            color: var(--red);
        }

        .stat-icon.navy {
            background: #e8edf5;
            color: var(--navy);
        }

        .stat-num {
            font-size: 1.9rem;
            font-weight: 800;
            color: var(--navy);
            line-height: 1;
        }

        .stat-lbl {
            font-size: .8rem;
            color: var(--muted);
            margin-top: .25rem;
        }

        /* ── Quick Actions ── */
        .quick-actions {
            display: flex;
            gap: .75rem;
            margin-bottom: 2rem;
            flex-wrap: wrap;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: .5rem;
            padding: .65rem 1.25rem;
            border-radius: 8px;
            font-size: .875rem;
            font-weight: 600;
            text-decoration: none;
            border: 1px solid transparent;
            cursor: pointer;
            transition: all .15s;
        }

        .btn-primary {
            background: var(--navy);
            color: #fff;
        }

        .btn-primary:hover {
            background: var(--navy2);
        }

        .btn-outline {
            background: var(--white);
            color: var(--navy);
            border-color: var(--border);
        }

        .btn-outline:hover {
            border-color: var(--navy);
            background: #f8f9ff;
        }

        .btn-green {
            background: var(--green);
            color: #fff;
        }

        .btn-green:hover {
            background: #229954;
        }

        /* ── Dual Grid ── */
        .two-col {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
        }

        @media (max-width: 900px) {
            .two-col {
                grid-template-columns: 1fr;
            }
        }

        /* ── Cards ── */
        .card {
            background: var(--white);
            border-radius: 12px;
            border: 1px solid var(--border);
            box-shadow: 0 1px 4px rgba(0, 0, 0, .06);
            overflow: hidden;
        }

        .card-header {
            padding: 1.1rem 1.5rem;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .card-header h2 {
            font-size: 1rem;
            font-weight: 700;
            color: var(--navy);
        }

        .card-header a {
            font-size: .8rem;
            color: var(--accent);
            text-decoration: none;
        }

        .card-header a:hover {
            text-decoration: underline;
        }

        .card-body {
            padding: 0;
        }

        /* ── Table ── */
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: .855rem;
        }

        th {
            padding: .75rem 1.25rem;
            font-size: .75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .04em;
            color: var(--muted);
            background: #f8fafc;
            border-bottom: 1px solid var(--border);
            text-align: left;
        }

        td {
            padding: .85rem 1.25rem;
            border-bottom: 1px solid var(--border);
            vertical-align: middle;
        }

        tr:last-child td {
            border-bottom: none;
        }

        tr:hover td {
            background: #fafbff;
        }

        /* ── Badge ── */
        .badge {
            display: inline-flex;
            align-items: center;
            padding: .25rem .6rem;
            border-radius: 20px;
            font-size: .72rem;
            font-weight: 700;
            letter-spacing: .02em;
        }

        .badge-new {
            background: #dbeafe;
            color: #1d4ed8;
        }

        .badge-read {
            background: #f1f5f9;
            color: #64748b;
        }

        .badge-replied {
            background: #dcfce7;
            color: #166534;
        }

        .badge-available {
            background: #dcfce7;
            color: #166534;
        }

        .badge-sold {
            background: #fef9c3;
            color: #854d0e;
        }

        .badge-rented {
            background: #f1f5f9;
            color: #475569;
        }

        /* ── Empty State ── */
        .empty {
            text-align: center;
            padding: 3rem 1rem;
            color: var(--muted);
        }

        .empty i {
            font-size: 2rem;
            margin-bottom: .75rem;
            opacity: .4;
        }

        /* ── Status dot ── */
        .dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            display: inline-block;
            margin-right: .4rem;
        }

        .dot-green {
            background: var(--green);
        }

        .dot-orange {
            background: var(--orange);
        }

        .dot-gray {
            background: #94a3b8;
        }

        /* ── Truncate ── */
        .truncate {
            max-width: 240px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* ── Link in table ── */
        .tbl-link {
            color: var(--accent);
            text-decoration: none;
            font-weight: 500;
        }

        .tbl-link:hover {
            text-decoration: underline;
        }

        @media (max-width: 600px) {
            :root {
                --sidebar-w: 0px;
            }

            .sidebar {
                display: none;
            }

            .main {
                margin-left: 0;
            }

            .content {
                padding: 1rem;
            }
        }
    </style>
</head>

<body>

    <!-- ══ Sidebar ══ -->
    <aside class="sidebar">
        <div class="sidebar-logo">
            <img src="<?= SITE_LOGO ?>" alt="<?= htmlspecialchars(SITE_NAME) ?>">
            <div class="site-name">Admin-Bereich</div>
        </div>
        <nav>
            <div class="nav-section-label">Hauptmenü</div>
            <a href="<?= BASE ?>/admin/index.php" class="nav-item active">
                <i class="fa fa-gauge nav-fa"></i> Dashboard
            </a>
            <a href="<?= BASE ?>/admin/properties.php" class="nav-item">
                <i class="fa fa-house nav-fa"></i> Immobilien
                <span style="margin-left:auto;font-size:.75rem;color:rgba(255,255,255,.4)"><?= $propCount ?></span>
            </a>
            <a href="<?= BASE ?>/admin/inquiries.php" class="nav-item">
                <i class="fa fa-envelope nav-fa"></i> Anfragen
                <?php if ($newContacts > 0): ?>
                    <span class="nav-badge"><?= $newContacts ?></span>
                <?php endif; ?>
            </a>
            <div class="nav-section-label" style="margin-top:.5rem">Einstellungen</div>
            <a href="<?= BASE ?>/admin/settings.php" class="nav-item">
                <i class="fa fa-gear nav-fa"></i> Einstellungen
            </a>
            <a href="<?= BASE ?>/" target="_blank" rel="noopener" class="nav-item">
                <i class="fa fa-arrow-up-right-from-square nav-fa"></i> Website öffnen
            </a>
        </nav>
        <div class="sidebar-footer">
            <a href="<?= BASE ?>/admin/logout.php" class="nav-item">
                <i class="fa fa-right-from-bracket nav-fa"></i> Abmelden
            </a>
        </div>
    </aside>

    <!-- ══ Main ══ -->
    <div class="main">
        <!-- Top Bar -->
        <header class="topbar">
            <div class="topbar-title">Dashboard</div>
            <div class="topbar-meta">
                <span class="topbar-date"><?= date('d. F Y') ?></span>
                <div class="topbar-user">
                    <div class="avatar"><?= strtoupper(substr(htmlspecialchars($admin['username']), 0, 1)) ?></div>
                    <span><?= htmlspecialchars($admin['username']) ?></span>
                </div>
            </div>
        </header>

        <div class="content">
            <!-- Stat Cards -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon blue"><i class="fa fa-house"></i></div>
                    <div>
                        <div class="stat-num"><?= $propCount ?></div>
                        <div class="stat-lbl">Immobilien gesamt</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon green"><i class="fa fa-circle-check"></i></div>
                    <div>
                        <div class="stat-num"><?= $availCount ?></div>
                        <div class="stat-lbl">Verfügbar</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon <?= $newContacts > 0 ? 'orange' : 'navy' ?>">
                        <i class="fa fa-envelope"></i>
                    </div>
                    <div>
                        <div class="stat-num"><?= $newContacts ?></div>
                        <div class="stat-lbl">Neue Anfragen</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon navy"><i class="fa fa-inbox"></i></div>
                    <div>
                        <div class="stat-num"><?= $contactCount ?></div>
                        <div class="stat-lbl">Anfragen gesamt</div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="quick-actions">
                <a href="<?= BASE ?>/admin/properties.php?action=new" class="btn btn-primary">
                    <i class="fa fa-plus"></i> Neue Immobilie
                </a>
                <a href="<?= BASE ?>/admin/inquiries.php" class="btn btn-outline">
                    <i class="fa fa-envelope"></i> Alle Anfragen
                </a>
                <a href="<?= BASE ?>/admin/properties.php" class="btn btn-outline">
                    <i class="fa fa-list"></i> Alle Immobilien
                </a>
                <a href="<?= BASE ?>/" target="_blank" rel="noopener" class="btn btn-outline">
                    <i class="fa fa-eye"></i> Website anzeigen
                </a>
            </div>

            <!-- Two-col Grid -->
            <div class="two-col">
                <!-- Recent Inquiries -->
                <div class="card">
                    <div class="card-header">
                        <h2><i class="fa fa-envelope" style="color:var(--accent);margin-right:.4rem"></i> Neueste Anfragen</h2>
                        <a href="<?= BASE ?>/admin/inquiries.php">Alle anzeigen &rarr;</a>
                    </div>
                    <div class="card-body">
                        <?php if ($recentInq): ?>
                            <table>
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Nachricht</th>
                                        <th>Status</th>
                                        <th>Datum</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recentInq as $inq): ?>
                                        <tr>
                                            <td>
                                                <a href="<?= BASE ?>/admin/inquiries.php?id=<?= $inq['id'] ?>" class="tbl-link">
                                                    <?= htmlspecialchars($inq['name']) ?>
                                                </a>
                                            </td>
                                            <td class="truncate" title="<?= htmlspecialchars($inq['message']) ?>">
                                                <?= htmlspecialchars(mb_substr($inq['message'], 0, 50)) ?>…
                                            </td>
                                            <td>
                                                <?php
                                                $badge = match ($inq['status']) {
                                                    'new'     => ['badge-new',     'Neu'],
                                                    'replied' => ['badge-replied', 'Beantwortet'],
                                                    default   => ['badge-read',    'Gelesen'],
                                                };
                                                ?>
                                                <span class="badge <?= $badge[0] ?>"><?= $badge[1] ?></span>
                                            </td>
                                            <td style="white-space:nowrap;color:var(--muted);font-size:.8rem">
                                                <?= date('d.m.Y', strtotime($inq['created_at'])) ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <div class="empty"><i class="fa fa-inbox"></i><br>Noch keine Anfragen</div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Recent Properties -->
                <div class="card">
                    <div class="card-header">
                        <h2><i class="fa fa-house" style="color:var(--green);margin-right:.4rem"></i> Zuletzt bearbeitet</h2>
                        <a href="<?= BASE ?>/admin/properties.php">Alle anzeigen &rarr;</a>
                    </div>
                    <div class="card-body">
                        <?php if ($recentProps): ?>
                            <table>
                                <thead>
                                    <tr>
                                        <th>Titel</th>
                                        <th>Typ</th>
                                        <th>Preis</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recentProps as $p): ?>
                                        <tr>
                                            <td>
                                                <a href="<?= BASE ?>/admin/properties.php?action=edit&id=<?= $p['id'] ?>" class="tbl-link truncate" style="display:block;max-width:160px">
                                                    <?= htmlspecialchars($p['title']) ?>
                                                </a>
                                            </td>
                                            <td style="font-size:.8rem;color:var(--muted)"><?= htmlspecialchars($p['type']) ?></td>
                                            <td style="white-space:nowrap;font-weight:600">
                                                <?= number_format((float)$p['price'], 0, ',', '.') ?> €
                                            </td>
                                            <td>
                                                <?php
                                                $pbadge = match ($p['status']) {
                                                    'available' => ['badge-available', 'Verfügbar'],
                                                    'sold'      => ['badge-sold',      'Verkauft'],
                                                    'rented'    => ['badge-rented',    'Vermietet'],
                                                    default     => ['badge-read',      ucfirst($p['status'])],
                                                };
                                                ?>
                                                <span class="badge <?= $pbadge[0] ?>"><?= $pbadge[1] ?></span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <div class="empty"><i class="fa fa-house"></i><br>Noch keine Immobilien</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div><!-- /two-col -->
        </div><!-- /content -->
    </div><!-- /main -->

</body>

</html>