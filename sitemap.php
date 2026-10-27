<?php

/**
 * sitemap.php – Dynamische XML-Sitemap
 * Wird per .htaccess als /sitemap.xml ausgeliefert.
 *
 * Enthält:
 *  – Alle statischen Seiten mit priorisierten Crawl-Hinweisen
 *  – Alle verfügbaren Immobilien aus der Datenbank (status='available')
 */
ob_start();
require_once __DIR__ . '/includes/config.php';
$bootstrapOutput = ob_get_clean();
if ($bootstrapOutput !== '') {
    error_log('Sitemap bootstrap output suppressed.');
}

// Kein HTML-Caching
header('Content-Type: application/xml; charset=utf-8');
header('Cache-Control: public, max-age=3600');
header('X-Robots-Tag: noindex'); // Sitemap selbst nicht indexieren

$base = rtrim(SITE_URL, '/');
$now  = date('Y-m-d');

// ── Statische Seiten ──────────────────────────────────────────────────────────
$staticPages = [
    // Startseite
    ['loc' => '/',                                         'priority' => '1.00', 'freq' => 'weekly',  'lastmod' => $now],
    // Kernleistungen
    ['loc' => '/immobilien-angebote',                  'priority' => '0.90', 'freq' => 'daily',   'lastmod' => $now],
    ['loc' => '/immobilienbewertung',                  'priority' => '0.85', 'freq' => 'monthly', 'lastmod' => $now],
    ['loc' => '/analyse-beratung',                     'priority' => '0.80', 'freq' => 'monthly', 'lastmod' => $now],
    ['loc' => '/finanzierung',                         'priority' => '0.75', 'freq' => 'monthly', 'lastmod' => $now],
    ['loc' => '/kapitalanlage',                        'priority' => '0.75', 'freq' => 'monthly', 'lastmod' => $now],
    ['loc' => '/hausverwaltung',                       'priority' => '0.75', 'freq' => 'monthly', 'lastmod' => $now],
    ['loc' => '/ratgeber',                             'priority' => '0.70', 'freq' => 'monthly', 'lastmod' => $now],
    // Ratgeber-Artikel (aus includes/ratgeber-articles/)
];
foreach (glob(__DIR__ . '/includes/ratgeber-articles/*.php') ?: [] as $articleFile) {
    $staticPages[] = [
        'loc'      => '/ratgeber/' . basename($articleFile, '.php'),
        'priority' => '0.60',
        'freq'     => 'monthly',
        'lastmod'  => date('Y-m-d', filemtime($articleFile)),
    ];
}
$staticPages = array_merge($staticPages, [    // Rechtliches (niedrige Priorität)
    ['loc' => '/impressum',                            'priority' => '0.30', 'freq' => 'yearly',  'lastmod' => $now],
    ['loc' => '/datenschutz',                          'priority' => '0.30', 'freq' => 'yearly',  'lastmod' => $now],
]);

// lastmod aus der Änderungszeit der jeweiligen Seitendatei (statt immer "heute")
foreach ($staticPages as &$sp) {
    $file = __DIR__ . ($sp['loc'] === '/' ? '/index.php' : $sp['loc'] . '.php');
    if (is_file($file)) {
        $sp['lastmod'] = date('Y-m-d', filemtime($file));
    }
}
unset($sp);

// ── Immobilien aus Datenbank ──────────────────────────────────────────────────
$properties = [];
try {
    $port = defined('DB_PORT') ? DB_PORT : 3306;
    $charset = defined('DB_CHARSET') ? DB_CHARSET : 'utf8mb4';
    $dsn = 'mysql:host=' . DB_HOST . ';port=' . $port . ';dbname=' . DB_NAME . ';charset=' . $charset;
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);

    $properties = $pdo->query(
        "SELECT slug, updated_at
           FROM properties
          WHERE status = 'available'
          ORDER BY updated_at DESC"
    )->fetchAll();
} catch (Throwable $e) {
    error_log('Sitemap properties query failed: ' . $e->getMessage());
    $properties = [];
}

// ── XML ausgeben ──────────────────────────────────────────────────────────────
echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
    xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9
                            http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">
    <?php foreach ($staticPages as $p): ?>
        <url>
            <loc><?= htmlspecialchars($base . $p['loc'], ENT_XML1) ?></loc>
            <lastmod><?= $p['lastmod'] ?></lastmod>
            <changefreq><?= $p['freq'] ?></changefreq>
            <priority><?= $p['priority'] ?></priority>
        </url>
    <?php endforeach; ?>
    <?php foreach ($properties as $prop): ?>
        <url>
            <loc><?= htmlspecialchars($base . '/immobilie/' . rawurlencode($prop['slug']), ENT_XML1) ?></loc>
            <lastmod><?= date('Y-m-d', strtotime($prop['updated_at'])) ?></lastmod>
            <changefreq>weekly</changefreq>
            <priority>0.80</priority>
        </url>
    <?php endforeach; ?>
</urlset>