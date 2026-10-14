<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ExhibitionLevelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $exhibitionLevels = [
            ['ExhibitionLevelID' => 1, 'Description' => 'Antarabangsa'],
            ['ExhibitionLevelID' => 2, 'Description' => 'Kebangsaan'],
            ['ExhibitionLevelID' => 4, 'Description' => 'Universiti'],
        ];

        foreach ($exhibitionLevels as $level) {
            DB::table('exhibition_levels')->updateOrInsert(
                ['ExhibitionLevelID' => $level['ExhibitionLevelID']],
                ['Description' => $level['Description']]
            );
        }
    }
}
