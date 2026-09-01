<?php
require __DIR__ . '/../vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as Capsule;

$capsule = new Capsule;
$capsule->addConnection([
    'driver' => 'mysql',
    'host' => '127.0.0.1',
    'database' => 'penyelidikan_database',
    'username' => 'root',
    'password' => '',
    'port' => 3307,
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
]);
$capsule->setAsGlobal();
$connection = $capsule->getConnection();

$rows = $connection->select("SELECT id,name,ic,email,role_id,status,created_at,updated_at FROM users WHERE ic='910101010101'");

echo json_encode($rows, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . PHP_EOL;
