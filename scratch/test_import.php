<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Award;
use App\Models\Competition;
use App\Models\Project;
use App\Models\Staff;
use App\Http\Controllers\Admin\ImportController;

echo "=== STARTING IMPORT SAFETY TEST ===\n";
Award::whereNotNull('id')->delete();

$dataset1 = [
    ['Id_Recognation','ProjectID','Person_RefNo','PersonTypeID','FullName'],
    ['1','369','369','1','MOHD SHAHRIL IZUAN BIN MOHD ZIN'],
    ['40','PJP/2025/FTKEK/PERSPEKTIF/SA0115','1479','1','FARA ASHIKIN BINTI ALI'],
    ['43','FRGS-EC/1/2024/FTKIP/F00601','2610','1','MOHD FAUZI BIN MAMAT'],
    ['52','TIADA','1489','1','MOHD YUHAZRI BIN YAAKOB'],
];

$dataset2 = [
    ['Id_Recognation','ProjectID','ProjectTitle','ExhibitionName','ExhibitionLevelID','ExhibitionResultID','ExhibitionPlace','StartDate','EndDate','Amount','Invention_Award'],
    ['1','369','DESIGN AND ANALYSIS OF IOT-BASED SYSTEM FOR CONDITION MONITORING','INNOTEK, FKEKK','4','5','FTKEK','2026-01-16','2026-01-16','RM0','2'],
    ['40','PJP/2025/FTKEK/PERSPEKTIF/SA0115','DEVELOPMENT OF IOT BASED EMERGENCY VEHICLE ALERT SYSTEM','IIDEX','4','3','FTK','2026-01-14','2026-01-14','RM0','2'],
    ['43','FRGS-EC/1/2024/FTKIP/F00601','ANTIBACTERIAL SELF-HEALING COATING','MTE','1','1','MALAYSIA TECHNOLOGY EXPO 2026','2026-04-09','2026-04-11','RM0','2'],
    ['52','TIADA','GREENCORE COMPOSITE: LOW CARBON KENAF THERMOSET','INFINEON WEEK','4','9','UTEM','2026-06-12','2026-06-12','RM0','1'],
];

$controller = new ImportController();

// Use Reflection to invoke private methods for testing
$reflector = new ReflectionClass(ImportController::class);
$m1 = $reflector->getMethod('importTemplateRD8_1_New');
$m1->setAccessible(true);
$m2 = $reflector->getMethod('importMyRA');
$m2->setAccessible(true);

$res1 = $m1->invoke($controller, array_slice($dataset1, 1));
echo "Dataset 1 imported records: {$res1}\n";

$res2 = $m2->invoke($controller, array_slice($dataset2, 1));
echo "Dataset 2 imported & merged records: {$res2}\n";

$awards2026 = Award::with(['staff', 'project', 'event'])->whereYear('award_date', 2026)->get();
echo "Total Awards created for 2026: " . $awards2026->count() . "\n";

foreach ($awards2026 as $a) {
    echo "- Award ID #{$a->id}: '{$a->award_name}' | Project: '{$a->project?->project_id}' | Staff: '{$a->staff?->staff_name}' | Event: '{$a->event?->event_name}' | Level: {$a->award_level} | Date: {$a->award_date}\n";
}

echo "=== TEST COMPLETED SUCCESSFULLY ===\n";
