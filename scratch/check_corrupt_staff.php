<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Staff;
use Illuminate\Support\Facades\DB;

$corruptIds = Staff::whereRaw('LENGTH(staff_id) > 15')->pluck('id');

echo "Corrupt staff IDs count: " . $corruptIds->count() . "\n";
echo "Linked in ip_staff: " . DB::table('ip_staff')->whereIn('staff_id', $corruptIds)->count() . "\n";
echo "Linked in awards: " . DB::table('awards')->whereIn('staff_id', $corruptIds)->count() . "\n";
