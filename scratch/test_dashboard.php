<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Http\Controllers\Admin\AwardController;
use Illuminate\Http\Request;

$controller = new AwardController();

$req2026 = Request::create('/api/awards/chart-data', 'GET', ['year' => 2026, 'type' => 'levels']);
$res2026 = $controller->chartData($req2026);

echo "2026 Levels Chart Data:\n";
echo json_encode($res2026) . "\n";

$reqAll = Request::create('/api/awards/chart-data', 'GET', ['type' => 'levels']);
$resAll = $controller->chartData($reqAll);

echo "All Years Levels Chart Data:\n";
echo json_encode($resAll) . "\n";
