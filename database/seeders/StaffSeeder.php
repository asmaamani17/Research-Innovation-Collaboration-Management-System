<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StaffSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing data
        DB::table('staff')->delete();
        
        // Get faculty codes mapping
        $facultyMap = DB::table('faculties')->pluck('id', 'faculty_code')->toArray();
        
        $staff = [
            [
                'staff_id' => '01300',
                'staff_name' => 'NIK MOHD ZARIFIE BIN HASHIM',
                'faculty_id' => $facultyMap['FTKEK'] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'staff_id' => '02610',
                'staff_name' => 'MOHD FAUZI BIN MAMAT',
                'faculty_id' => $facultyMap['FTKIP'] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'staff_id' => '2719',
                'staff_name' => 'NOORREZAM BIN YUSOP',
                'faculty_id' => $facultyMap['FTMK'] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'staff_id' => '01678',
                'staff_name' => 'UMMI RABAAH BINTI HASHIM',
                'faculty_id' => $facultyMap['FTMK'] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'staff_id' => '1137',
                'staff_name' => 'SAZILAH BINTI SALAM',
                'faculty_id' => $facultyMap['FTMK'] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'staff_id' => '02777',
                'staff_name' => 'MUHAMMAD SHAHKHIR BIN MOZAMIR',
                'faculty_id' => $facultyMap['FTMK'] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'staff_id' => '2725',
                'staff_name' => 'FATHIN NABILLA BINTI MD LEZA',
                'faculty_id' => $facultyMap['FTMK'] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'staff_id' => '02638',
                'staff_name' => 'WAN MOHD YA\'AKOB BIN WAN BEJURI',
                'faculty_id' => $facultyMap['FAIX'] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'staff_id' => '2592',
                'staff_name' => 'NUR ZAREEN BINTI ZULKARNAIN',
                'faculty_id' => $facultyMap['FAIX'] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'staff_id' => '2638',
                'staff_name' => 'WAN MOHD YA\'AKOB BIN WAN BEJURI',
                'faculty_id' => $facultyMap['FAIX'] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'staff_id' => '2195',
                'staff_name' => 'DAYANASARI BINTI ABDUL HADI',
                'faculty_id' => $facultyMap['FTKEK'] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'staff_id' => '01043',
                'staff_name' => 'ZURAINI BINTI OTHMAN',
                'faculty_id' => $facultyMap['FTMK'] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($staff as $staffMember) {
            DB::table('staff')->insert($staffMember);
        }
    }
}
