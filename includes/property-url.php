<?php

function property_legacy_number_id(string $number): ?int
{
    if ($number === '' || !ctype_digit($number)) return null;
    $number = ltrim($number, '0');
    $value = filter_var($number, FILTER_VALIDATE_INT);
    if ($value === false || $value < 1) return null;
    return $value >= 1111 ? $value - 1110 : $value;
}

function property_slugify(string $text): string
{
    $text = mb_strtolower($text, 'UTF-8');
    $text = str_replace(['ä', 'ö', 'ü', 'ß'], ['ae', 'oe', 'ue', 'ss'], $text);
    return trim((string)preg_replace('/[^a-z0-9]+/', '-', $text), '-');
}

function property_unique_slug(PDO $pdo, string $title, ?int $excludeId = null, bool $hasExtraData = true): string
{
    $base = rtrim(substr(property_slugify($title), 0, 255), '-') ?: 'immobilie';
    $reserved = [];
    $columns = $hasExtraData ? 'id, slug, extra_data' : 'id, slug';
    foreach ($pdo->query("SELECT $columns FROM properties")->fetchAll(PDO::FETCH_ASSOC) as $row) {
        if ($excludeId !== null && (int)$row['id'] === $excludeId) continue;
        $reserved[strtolower((string)$row['slug'])] = true;
        $extra = json_decode((string)($row['extra_data'] ?? '{}'), true);
        foreach (is_array($extra['legacy_slugs'] ?? null) ? $extra['legacy_slugs'] : [] as $alias) {
            if (is_string($alias)) $reserved[strtolower($alias)] = true;
        }
    }
    $candidate = $base;
    $suffix = 'a';
    while (isset($reserved[strtolower($candidate)])) {
        $tail = '-' . $suffix++;
        $candidate = rtrim(substr($base, 0, 255 - strlen($tail)), '-') . $tail;
    }
    return $candidate;
}

function property_migrate_slugs(PDO $pdo): void
{
    $ownsTransaction = !$pdo->inTransaction();
    if ($ownsTransaction) {
        $pdo->beginTransaction();
    }

    try {
        $rows = $pdo->query('SELECT id, slug, title, extra_data FROM properties ORDER BY id')->fetchAll(PDO::FETCH_ASSOC);
        if (!$rows) {
            if ($ownsTransaction) {
                $pdo->commit();
            }
            return;
        }

        $update = $pdo->prepare('UPDATE properties SET slug = ?, extra_data = ? WHERE id = ?');
        foreach ($rows as $row) {
            $oldSlug = (string)($row['slug'] ?? '');
            $slug = property_unique_slug($pdo, (string)($row['title'] ?? ''), (int)$row['id']);
            if ($slug === $oldSlug) {
                continue;
            }

            $extraData = json_decode((string)($row['extra_data'] ?? '{}'), true);
            if (!is_array($extraData)) {
                throw new RuntimeException('Immobilien-Slugs konnten wegen ungültiger Zusatzdaten nicht aktualisiert werden.');
            }
            $legacySlugs = array_filter(
                is_array($extraData['legacy_slugs'] ?? null) ? $extraData['legacy_slugs'] : [],
                static fn($value): bool => is_string($value) && $value !== '' && $value !== $slug
            );
            if ($oldSlug !== '') {
                $legacySlugs[] = $oldSlug;
            }
            $extraData['legacy_slugs'] = array_values(array_unique($legacySlugs));
            $encodedExtraData = json_encode($extraData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            if (!is_string($encodedExtraData)) {
                throw new RuntimeException('Immobilien-Slugs konnten wegen ungültiger Zusatzdaten nicht aktualisiert werden.');
            }
            $update->execute([$slug, $encodedExtraData, $row['id']]);
        }

        if ($ownsTransaction) {
            $pdo->commit();
        }
    } catch (Throwable $e) {
        if ($ownsTransaction && $pdo->inTransaction()) {
            $pdo->rollBack();
        }
        throw $e;
    }
}
