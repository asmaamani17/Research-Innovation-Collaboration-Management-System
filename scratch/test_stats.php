<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Award;
use App\Models\Staff;
use App\Models\Faculty;

foreach ([null, 2025, 2026] as $yr) {
    $q = Award::query();
    if ($yr) $q->whereYear('award_date', $yr);
    $facIds = $q->leftJoin('staff', 'awards.staff_id', '=', 'staff.id')->pluck('staff.faculty_id')->unique();
    echo "Year " . ($yr ?: 'ALL') . " faculty IDs: " . json_encode($facIds->values()->toArray()) . PHP_EOL;
}
