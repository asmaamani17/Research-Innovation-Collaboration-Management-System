<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Award;
use App\Models\Staff;
use App\Models\Project;
use App\Models\Competition;
use App\Http\Controllers\Admin\ImportController;

echo "=== TESTING REVERSE IMPORT ORDER (RD8_New FIRST, THEN RD8_1) ===\n";

// Clear 2026 awards
Award::whereYear('award_date', 2026)->orWhere('award_name', 'like', 'Imported Award%')->delete();

$testFlow = file_get_contents(__DIR__ . '/test_import_flow.php');
preg_match('/\$raw1 = <<<\'CSV\'\n(.*?)\nCSV;/s', $testFlow, $m1);
preg_match('/\$raw2 = <<<\'CSV\'\n(.*?)\nCSV;/s', $testFlow, $m2);

$lines1 = array_map('str_getcsv', explode("\n", trim($m1[1])));
$lines2 = array_map('str_getcsv', explode("\n", trim($m2[1])));

array_shift($lines1);
array_shift($lines2);

$controller = new ImportController();
$reflector = new ReflectionClass(ImportController::class);

$mImport1 = $reflector->getMethod('importTemplateRD8_1_New');
$mImport1->setAccessible(true);
$mImport2 = $reflector->getMethod('importTemplateRD8_New');
$mImport2->setAccessible(true);

// RD8_New FIRST
$imported2 = $mImport2->invoke($controller, $lines2);
echo "Imported RD8_New: {$imported2}\n";

// RD8_1 SECOND
$imported1 = $mImport1->invoke($controller, $lines1);
echo "Imported RD8_1: {$imported1}\n";

$awards = Award::all();
$awards2026 = Award::whereYear('award_date', 2026)->get();

echo "Total 2026 awards: " . $awards2026->count() . "\n";
$missingStaff = Award::whereYear('award_date', 2026)->whereNull('staff_id')->count();
$missingComp = Award::whereYear('award_date', 2026)->whereNull('competition_id')->count();
$missingResult = Award::whereYear('award_date', 2026)->whereNull('exhibition_result_id')->count();

echo "Missing staff: {$missingStaff}\n";
echo "Missing competition: {$missingComp}\n";
echo "Missing exhibition result: {$missingResult}\n";

$dist = Award::whereYear('award_date', 2026)
    ->selectRaw('award_name, COUNT(*) as count')
    ->groupBy('award_name')
    ->pluck('count', 'award_name');

echo "Distribution: " . json_encode($dist) . "\n";
