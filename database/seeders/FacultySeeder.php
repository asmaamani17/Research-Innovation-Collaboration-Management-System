<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FacultySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing data
        DB::table('faculties')->delete();
        
        $faculties = [
            [
                'faculty_code' => 'FTMK',
                'faculty_name' => 'FAKULTI TEKNOLOGI MAKLUMAT DAN KOMUNIKASI (FTMK)',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'faculty_code' => 'FTKE',
                'faculty_name' => 'FAKULTI TEKNOLOGI DAN KEJURUTERAAN ELEKTRIK (FTKE)',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'faculty_code' => 'FTKEK',
                'faculty_name' => 'FAKULTI TEKNOLOGI DAN KEJURUTERAAN ELEKTRONIK DAN KOMPUTER (FTKEK)',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'faculty_code' => 'FTKM',
                'faculty_name' => 'FAKULTI TEKNOLOGI DAN KEJURUTERAAN MEKANIKAL (FTKM)',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'faculty_code' => 'FTKIP',
                'faculty_name' => 'FAKULTI TEKNOLOGI DAN KEJURUTERAAN INDUSTRI DAN PEMBUATAN (FTKIP)',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'faculty_code' => 'FPTT',
                'faculty_name' => 'FAKULTI PENGURUSAN TEKNOLOGI DAN TEKNOUSAHAWANAN (FPTT)',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'faculty_code' => 'FAIX',
                'faculty_name' => 'FAKULTI KECERDASAN BUATAN DAN TRANSFORMASI DIGITAL (FAIX)',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($faculties as $faculty) {
            DB::table('faculties')->insert($faculty);
        }
    }
}