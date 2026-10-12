<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ExhibitionResultSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $exhibitionResults = [
            ['ExhibitionResultID' => 1, 'Description' => 'Emas dan Setara'],
            ['ExhibitionResultID' => 2, 'Description' => 'Emas dan Anugerah Tambahan'],
            ['ExhibitionResultID' => 3, 'Description' => 'Perak dan Setara'],
            ['ExhibitionResultID' => 4, 'Description' => 'Perak dan Anugerah Tambahan'],
            ['ExhibitionResultID' => 5, 'Description' => 'Gangsa dan Setara'],
            ['ExhibitionResultID' => 6, 'Description' => 'Sijil penghargaan'],
            ['ExhibitionResultID' => 7, 'Description' => 'Platinum dan Setara'],
            ['ExhibitionResultID' => 8, 'Description' => 'Anugerah Tambahan sahaja'],
            ['ExhibitionResultID' => 9, 'Description' => 'Anugerah Khas'],
            ['ExhibitionResultID' => 999, 'Description' => 'Lain-lain'],
        ];

        foreach ($exhibitionResults as $result) {
            DB::table('exhibition_results')->updateOrInsert(
                ['ExhibitionResultID' => $result['ExhibitionResultID']],
                ['Description' => $result['Description']]
            );
        }
    }
}
