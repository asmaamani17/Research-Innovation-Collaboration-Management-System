<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Award;
use App\Models\Staff;
use App\Models\Project;
use App\Models\Competition;

echo "=== CURRENT DATABASE STATUS ===\n";
echo "Total Awards: " . Award::count() . "\n";
echo "2025 Awards: " . Award::whereYear('award_date', 2025)->count() . "\n";
echo "2026 Awards: " . Award::whereYear('award_date', 2026)->count() . "\n";

$corruptStaff = Staff::whereRaw('LENGTH(staff_id) > 15')->get();
echo "Corrupt Staff (project titles as staff_id): " . $corruptStaff->count() . "\n";

$importedAwards = Award::where('award_name', 'like', 'Imported Award%')->get();
echo "Awards with 'Imported Award #X': " . $importedAwards->count() . "\n";

$nullCompetitionAwards = Award::whereNull('competition_id')->get();
echo "Awards with NULL competition_id: " . $nullCompetitionAwards->count() . "\n";

$nullResultAwards = Award::whereNull('exhibition_result_id')->get();
echo "Awards with NULL exhibition_result_id: " . $nullResultAwards->count() . "\n";
