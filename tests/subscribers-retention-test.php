<?php

require_once __DIR__ . '/../includes/subscribers.php';

final class RecordingPDO extends PDO
{
    public array $statements = [];

    public function __construct()
    {
    }

    public function exec(string $statement): int|false
    {
        $this->statements[] = $statement;
        return str_starts_with($statement, 'DELETE FROM subscribers') ? 3 : 0;
    }
}

function expect_retention(bool $condition, string $message): void
{
    if (!$condition) {
        fwrite(STDERR, $message . PHP_EOL);
        exit(1);
    }
}

$pdo = new RecordingPDO();
$deleted = subscribers_cleanup_expired($pdo);
$deleteStatements = array_values(array_filter(
    $pdo->statements,
    static fn(string $statement): bool => str_starts_with($statement, 'DELETE ')
));

expect_retention($deleted === 3, 'Cleanup should return the deleted subscriber count.');
expect_retention(count($deleteStatements) === 2, 'Cleanup must remove notification rows and expired subscribers.');
foreach ($deleteStatements as $statement) {
    expect_retention(str_contains($statement, "status = 'pending'"), 'Pending signups must be included.');
    expect_retention(str_contains($statement, "status = 'unsubscribed'"), 'Unsubscribed records must be included.');
    expect_retention(str_contains($statement, 'INTERVAL 3 MONTH'), 'The retention period must be three months.');
    expect_retention(!str_contains($statement, "status = 'confirmed'"), 'Confirmed subscribers must be retained.');
}

echo "Subscriber retention tests passed.\n";
