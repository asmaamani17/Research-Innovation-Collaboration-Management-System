<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing data
        DB::table('modules')->delete();
        
        $modules = [
            [
                'module_name' => 'KPI',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'module_name' => 'Awards',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'module_name' => 'Products',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'module_name' => 'Intellectual Property',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'module_name' => 'Community',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'module_name' => 'STEM',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($modules as $module) {
            DB::table('modules')->insert($module);
        }
    }
}
