<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Exports\AwardsTemplateExport;

$export1 = new AwardsTemplateExport(2026, [], 'Template_RD8_1_New');
$view1 = $export1->view();
$data1 = $view1->getData();
echo "Template_RD8_1_New rows: " . count($data1['awards']) . "\n";

$export2 = new AwardsTemplateExport(2026, [], 'Template_RD8_New');
$view2 = $export2->view();
$data2 = $view2->getData();
echo "Template_RD8_New rows: " . count($data2['awards']) . "\n";

$firstAward = $data2['awards']->first();
echo "Sample Award in Export: \n";
echo "- Project ID: " . ($firstAward->project->project_id ?? 'TIADA') . "\n";
echo "- Project Title: " . ($firstAward->project->project_title ?? 'N/A') . "\n";
echo "- Competition: " . ($firstAward->event->event_name ?? 'N/A') . "\n";
echo "- Result ID: " . ($firstAward->exhibition_result_id ?? 'N/A') . "\n";
echo "- Level: " . ($firstAward->event->exhibition_level_id ?? 'N/A') . "\n";
echo "- Staff: " . ($firstAward->staff->staff_name ?? 'N/A') . "\n";
echo "- Award Name: " . $firstAward->award_name . "\n";
