<?php

/**
 * ratgeber-artikel.php
 * Rendert /ratgeber/<slug> aus includes/ratgeber-articles/<slug>.php
 * (Routing per .htaccess: ^ratgeber/<slug> -> ratgeber-artikel.php?slug=<slug>)
 */
require_once __DIR__ . '/includes/config.php';

$slug = (string)($_GET['slug'] ?? '');
$file = __DIR__ . '/includes/ratgeber-articles/' . $slug . '.php';

if (!preg_match('/^[a-z0-9-]+$/', $slug) || !is_file($file)) {
    http_response_code(404);
    require __DIR__ . '/404.php';
    exit;
}

$a = require $file;

$base      = rtrim(SITE_URL, '/');
$url       = $base . '/ratgeber/' . $slug;
$updated   = $a['updated'];
$ldFlags   = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG;

$faqLd = [];
foreach ($a['faq'] as $f) {
    $faqLd[] = [
        '@type' => 'Question',
        'name'  => $f[0],
        'acceptedAnswer' => ['@type' => 'Answer', 'text' => strip_tags($f[1])],
    ];
}

$ld = [
    '@context' => 'https://schema.org',
    '@graph'   => [
        [
            '@type'            => 'Article',
            'headline'         => $a['title'],
            'description'      => $a['description'],
            'inLanguage'       => 'de-DE',
            'datePublished'    => $a['published'] ?? $updated,
            'dateModified'     => $updated,
            'mainEntityOfPage' => $url,
            'author'           => ['@type' => 'Organization', 'name' => 'Immobilien Ghumman', 'url' => $base],
            'publisher'        => ['@type' => 'Organization', 'name' => 'Immobilien Ghumman', 'url' => $base, 'logo' => ['@type' => 'ImageObject', 'url' => SITE_LOGO]],
        ],
        [
            '@type' => 'BreadcrumbList',
            'itemListElement' => [
                ['@type' => 'ListItem', 'position' => 1, 'name' => 'Startseite', 'item' => $base . '/'],
                ['@type' => 'ListItem', 'position' => 2, 'name' => 'Ratgeber', 'item' => $base . '/ratgeber'],
                ['@type' => 'ListItem', 'position' => 3, 'name' => $a['title'], 'item' => $url],
            ],
        ],
        ['@type' => 'FAQPage', 'mainEntity' => $faqLd],
    ],
];

$pageTitle       = $a['title'] . ' | Immobilien Ghumman';
$pageDescription = $a['description'];
$pageKeywords    = $a['keywords'];
$activeNav       = 'ratgeber';
$ogUrl           = $url;
$extraHead       = '<script type="application/ld+json">' . json_encode($ld, $ldFlags) . '</script>
<style>
.ratgeber-article{max-width:820px;margin:0 auto;padding:2.5rem 1.25rem 3rem;line-height:1.75;color:var(--premium-text,#1f2937)}
.ratgeber-article h2{font-size:1.5rem;margin:2.25rem 0 .75rem}
.ratgeber-article p,.ratgeber-article li{font-size:1.02rem}
.ratgeber-article ul,.ratgeber-article ol{padding-left:1.4rem;margin:.5rem 0 1rem}
.ratgeber-article .ra-lead{font-size:1.15rem;color:var(--premium-text-lt,#4b5563)}
.ratgeber-article .ra-meta{font-size:.85rem;color:var(--premium-text-lt,#6b7280);margin-bottom:1.5rem}
.ratgeber-article .ra-note{background:var(--premium-offwhite,#f8fafc);border:1px solid var(--premium-border,#e5e7eb);border-radius:10px;padding:1rem 1.25rem;margin:1.5rem 0;font-size:.92rem}
.ratgeber-article details{border:1px solid var(--premium-border,#e5e7eb);border-radius:10px;padding:.9rem 1.1rem;margin:.6rem 0}
.ratgeber-article summary{font-weight:700;cursor:pointer}
.ratgeber-article table{width:100%;border-collapse:collapse;margin:1rem 0;font-size:.95rem}
.ratgeber-article th,.ratgeber-article td{border:1px solid var(--premium-border,#e5e7eb);padding:.55rem .75rem;text-align:left}
.ratgeber-article .ra-cta{margin-top:2.5rem;padding:1.5rem;border-radius:12px;background:var(--premium-offwhite,#f1f5f9);text-align:center}
.ratgeber-article .ra-related a{display:block;margin:.35rem 0}
</style>';

require_once __DIR__ . '/includes/header.php';
?>

<section class="page-hero">
    <div class="page-hero-inner">
        <nav class="page-hero-breadcrumb" aria-label="Breadcrumb">
            <a href="<?= BASE ?>/">Startseite</a>
            <span>/</span>
            <a href="<?= BASE ?>/ratgeber">Ratgeber</a>
            <span>/</span>
            <span><?= htmlspecialchars($a['kategorie'], ENT_QUOTES) ?></span>
        </nav>
        <h1><?= htmlspecialchars($a['title'], ENT_QUOTES) ?></h1>
    </div>
</section>

<article class="ratgeber-article">
    <p class="ra-meta">
        Von Immobilien Ghumman · Kategorie: <?= htmlspecialchars($a['kategorie'], ENT_QUOTES) ?> ·
        ca. <?= (int)$a['lese_min'] ?> Min. Lesezeit ·
        Stand: <time datetime="<?= htmlspecialchars($updated, ENT_QUOTES) ?>"><?= date('d.m.Y', strtotime($updated)) ?></time>
    </p>
    <p class="ra-lead"><?= $a['intro'] ?></p>

    <?php foreach ($a['sections'] as $s): ?>
        <h2><?= htmlspecialchars($s[0], ENT_QUOTES) ?></h2>
        <?= $s[1] ?>
    <?php endforeach; ?>

    <h2>Häufige Fragen</h2>
    <?php foreach ($a['faq'] as $f): ?>
        <details>
            <summary><?= htmlspecialchars($f[0], ENT_QUOTES) ?></summary>
            <p><?= $f[1] ?></p>
        </details>
    <?php endforeach; ?>

    <p class="ra-note">
        Hinweis: Dieser Ratgeber bietet eine allgemeine Orientierung und ersetzt keine Rechts-, Steuer- oder Finanzberatung im Einzelfall.
        Gesetze, Fristen und Zinssätze ändern sich – prüfen Sie die aktuellen Werte vor einer Entscheidung.
    </p>

    <div class="ra-cta">
        <h2 style="margin-top:0"><?= htmlspecialchars($a['cta'][0], ENT_QUOTES) ?></h2>
        <p><?= htmlspecialchars($a['cta'][1], ENT_QUOTES) ?></p>
        <a class="btn-hero-primary" href="<?= BASE ?><?= htmlspecialchars($a['cta'][3], ENT_QUOTES) ?>"><?= htmlspecialchars($a['cta'][2], ENT_QUOTES) ?></a>
        <a class="btn-hero-secondary" href="https://wa.me/4916098787878" rel="noopener noreferrer" target="_blank">WhatsApp schreiben</a>
    </div>

    <?php if (!empty($a['related'])): ?>
        <h2>Weiterlesen</h2>
        <div class="ra-related">
            <?php foreach ($a['related'] as $rel):
                $relFile = __DIR__ . '/includes/ratgeber-articles/' . $rel . '.php';
                if (!is_file($relFile)) continue;
                $relData = require $relFile; ?>
                <a href="<?= BASE ?>/ratgeber/<?= htmlspecialchars($rel, ENT_QUOTES) ?>"><?= htmlspecialchars($relData['title'], ENT_QUOTES) ?></a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</article>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
