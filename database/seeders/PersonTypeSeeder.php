<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PersonTypeSeeder extends Seeder
{
    public function run(): void
    {
        $personTypes = [
            ['PersonTypeID' => 1, 'PersonDesc' => 'Staf'],
            ['PersonTypeID' => 2, 'PersonDesc' => 'Student'],
            ['PersonTypeID' => 3, 'PersonDesc' => 'External'],
            ['PersonTypeID' => 4, 'PersonDesc' => 'Post-Doct'],
            ['PersonTypeID' => 5, 'PersonDesc' => 'Researcher Assistant'],
            ['PersonTypeID' => 999, 'PersonDesc' => 'Other'],
        ];

        foreach ($personTypes as $type) {
            DB::table('person_types')->updateOrInsert(
                ['PersonTypeID' => $type['PersonTypeID']],
                ['PersonDesc' => $type['PersonDesc']]
            );
        }
    }
}
