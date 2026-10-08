<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class TrlProductsSeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('seeders/trl_products_seed.sql');
        if (! is_file($path)) {
            throw new RuntimeException("TRL product seed SQL was not found at {$path}.");
        }

        $sql = file_get_contents($path);
        if ($sql === false) {
            throw new RuntimeException("Unable to read TRL product seed SQL at {$path}.");
        }

        DB::unprepared($sql);
    }
}
