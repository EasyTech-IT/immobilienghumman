<?php
require_once __DIR__ . '/../includes/analyse-fragen.php';

function expect_analysis(bool $condition, string $message): void
{
    if (!$condition) {
        fwrite(STDERR, $message . PHP_EOL);
        exit(1);
    }
}

$flows = analyse_flows();
foreach ($flows as $type => $flow) {
    expect_analysis(count($flow['questions']) === 12, $type . ': expected 12 questions');
    $answers = [];
    foreach ($flow['questions'] as $question) {
        expect_analysis(!empty($question['options']), $type . ': question requires typing');
        foreach ($question['options'] as $option) {
            expect_analysis(analyse_option_icon($question['name'], $option) !== '', $type . ': missing option icon');
        }
        $answers['analyse_' . $question['name']] = !empty($question['multiple']) ? [$question['options'][0]] : $question['options'][0];
    }
    expect_analysis(analyse_validate_answers($type, $answers) === [], $type . ': valid answers rejected');
    $first = $flow['questions'][0];
    unset($answers['analyse_' . $first['name']]);
    expect_analysis(analyse_validate_answers($type, $answers) !== [], $type . ': missing answer accepted');
    $answers['analyse_' . $first['name']] = 'invalid option';
    expect_analysis(analyse_validate_answers($type, $answers) !== [], $type . ': invalid option accepted');
}

$finance = [];
foreach ($flows['analyse_finanzierung']['questions'] as $question) {
    $finance['analyse_' . $question['name']] = $question['options'][0];
}
expect_analysis(isset($finance['analyse_monatsrate'], $finance['analyse_zinsbindung']), 'Mortgage-specific questions missing');
expect_analysis(!isset($finance['analyse_zins'], $finance['analyse_laufzeit']), 'Outdated financing questions remain');
unset($finance['analyse_zinsbindung']);
expect_analysis(analyse_validate_answers('analyse_finanzierung', $finance) !== [], 'Missing fixed-rate term accepted');
$finance['analyse_zinsbindung'] = 'Noch offen';
$finance['analyse_kaufpreis'] = 'Unter 100.000 €';
$finance['analyse_eigenkapital'] = '100.000–250.000 €';
expect_analysis(analyse_validate_answers('analyse_finanzierung', $finance) !== [], 'Equity definitely above budget accepted');
$finance['analyse_eigenkapital'] = '50.000–100.000 €';
expect_analysis(analyse_validate_answers('analyse_finanzierung', $finance) === [], 'Overlapping ranges rejected');
$finance['analyse_kaufpreis'] = 'Noch offen';
expect_analysis(analyse_validate_answers('analyse_finanzierung', $finance) === [], 'Unknown budget rejected');

$management = [];
foreach ($flows['analyse_hausverwaltung']['questions'] as $question) {
    $management['analyse_' . $question['name']] = !empty($question['multiple']) ? [$question['options'][0]] : $question['options'][0];
}
$management['analyse_kontaktwunsch'] = 'Telefonisch';
expect_analysis(analyse_validate_answers('analyse_hausverwaltung', $management) !== [], 'Phone request without phone accepted');
$management['phone'] = '0123456789';
expect_analysis(analyse_validate_answers('analyse_hausverwaltung', $management) === [], 'Phone request with phone rejected');
$management['analyse_bedarf'] = ['Abrechnungen', 'Instandhaltung'];
expect_analysis(analyse_validate_answers('analyse_hausverwaltung', $management) === [], 'Multiple valid answers rejected');
$management['analyse_bedarf'] = ['Abrechnungen', 'Noch offen'];
expect_analysis(analyse_validate_answers('analyse_hausverwaltung', $management) !== [], 'Exclusive option combined with another answer');
$management['analyse_bedarf'] = ['Abrechnungen', 'invalid option'];
expect_analysis(analyse_validate_answers('analyse_hausverwaltung', $management) !== [], 'Invalid multiple answer accepted');
$management['analyse_bedarf'] = [];
expect_analysis(analyse_validate_answers('analyse_hausverwaltung', $management) !== [], 'Empty multiple answer accepted');

$calls = [];
$delivery = analyse_send_notifications(
    'internal@example.invalid',
    'sender@example.invalid',
    static function ($to) use (&$calls) {
        $calls[] = $to;
        return $to !== 'internal@example.invalid';
    },
    static function ($to) use (&$calls) {
        $calls[] = $to;
        return true;
    }
);
expect_analysis($calls === ['internal@example.invalid', 'info@easytechit.de', 'sender@example.invalid'], 'Unexpected recipients or missing delivery attempt');
expect_analysis($delivery === ['admin' => false, 'partner' => true, 'confirmation' => true], 'Delivery failure not surfaced');
echo "Analyse tests passed.\n";
