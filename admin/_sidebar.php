<?php

/**
 * admin/_sidebar.php
 * Wiederverwendbare Sidebar für alle Admin-Seiten.
 *
 * Erwartet:
 *   $activePage – string: 'dashboard'|'properties'|'inquiries'|'settings'
 *   $newContacts – int: Anzahl neuer Anfragen (für Badge)
 */
$activePage  = $activePage  ?? 'dashboard';
$newContacts = $newContacts ?? 0;
?>
<style>
    /* ── Admin Shared Styles ── */
    *,
    *::before,
    *::after {
        box-sizing: border-box;
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
        margin: 0;
        display: flex;
        min-height: 100vh;
    }

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

    .main {
        margin-left: var(--sidebar-w);
        flex: 1;
        min-width: 0;
    }

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
        font-size: 1.15rem;
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

    .content {
        padding: 2rem;
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
    }

    .btn-danger {
        background: var(--red);
        color: #fff;
    }

    .btn-danger:hover {
        opacity: .9;
    }

    .btn-sm {
        padding: .4rem .8rem;
        font-size: .8rem;
    }

    .card {
        background: var(--white);
        border-radius: 12px;
        border: 1px solid var(--border);
        box-shadow: 0 1px 4px rgba(0, 0, 0, .06);
        overflow: hidden;
        margin-bottom: 1.5rem;
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
        margin: 0;
    }

    .alert {
        padding: .85rem 1.25rem;
        border-radius: 8px;
        margin-bottom: 1.5rem;
        font-size: .875rem;
    }

    .alert-success {
        background: #dcfce7;
        color: #166534;
        border: 1px solid #bbf7d0;
    }

    .alert-error {
        background: #fee2e2;
        color: #991b1b;
        border: 1px solid #fecaca;
    }

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

    .badge {
        display: inline-flex;
        align-items: center;
        padding: .25rem .6rem;
        border-radius: 20px;
        font-size: .72rem;
        font-weight: 700;
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

    .tbl-link {
        color: var(--accent);
        text-decoration: none;
        font-weight: 500;
    }

    .tbl-link:hover {
        text-decoration: underline;
    }

    .empty {
        text-align: center;
        padding: 3rem 1rem;
        color: var(--muted);
    }

    .form-group {
        margin-bottom: 1.25rem;
    }

    label {
        display: block;
        font-size: .85rem;
        font-weight: 600;
        color: var(--text);
        margin-bottom: .4rem;
    }

    input[type=text],
    input[type=email],
    input[type=number],
    input[type=password],
    input[type=url],
    textarea,
    select {
        width: 100%;
        padding: .65rem .9rem;
        border: 1px solid var(--border);
        border-radius: 8px;
        font-size: .9rem;
        font-family: inherit;
        background: var(--white);
        color: var(--text);
        transition: border-color .15s;
    }

    input:focus,
    textarea:focus,
    select:focus {
        outline: none;
        border-color: var(--accent);
        box-shadow: 0 0 0 3px rgba(33, 150, 243, .12);
    }

    textarea {
        resize: vertical;
        min-height: 100px;
    }

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
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

        .form-row {
            grid-template-columns: 1fr;
        }
    }
</style>

<aside class="sidebar">
    <div class="sidebar-logo">
        <img src="<?= SITE_LOGO ?>" alt="<?= htmlspecialchars(SITE_NAME) ?>">
        <div class="site-name">Admin-Bereich</div>
    </div>
    <nav>
        <div class="nav-section-label">Hauptmenü</div>
        <a href="<?= BASE ?>/admin/index.php" class="nav-item <?= $activePage === 'dashboard' ? 'active' : '' ?>">
            <i class="fa fa-gauge"></i> Dashboard
        </a>
        <a href="<?= BASE ?>/admin/properties.php" class="nav-item <?= $activePage === 'properties' ? 'active' : '' ?>">
            <i class="fa fa-house"></i> Immobilien
        </a>
        <a href="<?= BASE ?>/admin/inquiries.php" class="nav-item <?= $activePage === 'inquiries' ? 'active' : '' ?>">
            <i class="fa fa-envelope"></i> Anfragen
            <?php if ($newContacts > 0): ?>
                <span class="nav-badge"><?= (int)$newContacts ?></span>
            <?php endif; ?>
        </a>
        <a href="<?= BASE ?>/admin/subscribers.php" class="nav-item <?= $activePage === 'subscribers' ? 'active' : '' ?>">
            <i class="fa fa-bell"></i> Abonnenten
        </a>
        <div class="nav-section-label" style="margin-top:.5rem">Einstellungen</div>
        <a href="<?= BASE ?>/admin/settings.php" class="nav-item <?= $activePage === 'settings' ? 'active' : '' ?>">
            <i class="fa fa-gear"></i> Einstellungen
        </a>
        <a href="<?= BASE ?>/" target="_blank" rel="noopener" class="nav-item">
            <i class="fa fa-arrow-up-right-from-square"></i> Website öffnen
        </a>
    </nav>
    <div class="sidebar-footer">
        <a href="<?= BASE ?>/admin/logout.php" class="nav-item">
            <i class="fa fa-right-from-bracket"></i> Abmelden
        </a>
    </div>
</aside>