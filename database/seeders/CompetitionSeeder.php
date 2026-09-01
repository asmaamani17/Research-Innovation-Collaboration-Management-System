<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CompetitionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing data
        DB::table('competitions')->delete();
        
        $competitions = [
            [
                'event_name' => 'THE 2ND ARTIFICIAL INTELLIGENCE APPLICATION CHALLENGE BY CHINA-ASEAN INFORMATION HARBOR ELECTRONIC INFORMATION TALENT DEVELOPMENT AND TECHNOLOGY INNOVATION ALLIANCE',
                'organizer' => 'THE 2ND ARTIFICIAL INTELLIGENCE APPLICATION CHALLENGE BY CHINA-ASEAN INFORMATION HARBOR ELECTRONIC INFORMATION TALENT DEVELOPMENT AND TECHNOLOGY INNOVATION ALLIANCE',
                'exhibition_level' => 'International',
                'exhibition_place' => 'ONLINE',
                'start_date' => '2025-07-01',
                'end_date' => '2025-07-01',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'event_name' => '4TH INTERNATIONAL RESEARCH & INNOVATIVE TECHNOLOGY COMPETITION 2025 (I-RITEC2025)',
                'organizer' => '4TH INTERNATIONAL RESEARCH & INNOVATIVE TECHNOLOGY COMPETITION 2025 (I-RITEC2025)',
                'exhibition_level' => 'International',
                'exhibition_place' => '4TH INTERNATIONAL RESEARCH & INNOVATIVE TECHNOLOGY COMPETITION 2025 (I-RITEC2025)',
                'start_date' => '2025-07-10',
                'end_date' => '2025-09-25',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'event_name' => 'PERTANDINGAN INOVASI ALAM MELAYU ANTARABANGSA 2025 (IMAN25)',
                'organizer' => 'PERTANDINGAN INOVASI ALAM MELAYU ANTARABANGSA 2025 (IMAN25)',
                'exhibition_level' => 'International',
                'exhibition_place' => 'PERTANDINGAN INOVASI ALAM MELAYU ANTARABANGSA 2025 (IMAN25)',
                'start_date' => '2025-07-15',
                'end_date' => '2025-11-01',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'event_name' => 'INTERNATIONAL SCIENCE AND SOCIAL SCIENCE INNOVATION COMPETITION (ISIC VII 2025) -ONLINE',
                'organizer' => 'INTERNATIONAL SCIENCE AND SOCIAL SCIENCE INNOVATION COMPETITION (ISIC VII 2025) -ONLINE',
                'exhibition_level' => 'International',
                'exhibition_place' => 'INTERNATIONAL SCIENCE AND SOCIAL SCIENCE INNOVATION COMPETITION (ISIC VII 2025) -ONLINE',
                'start_date' => '2025-07-31',
                'end_date' => '2025-07-31',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'event_name' => '2ND INTERNATIONAL DEVELOPMENT, RESEARCH AND INNOVATION EXHIBITION (IDRIVE\'25)',
                'organizer' => '2ND INTERNATIONAL DEVELOPMENT, RESEARCH AND INNOVATION EXHIBITION (IDRIVE\'25)',
                'exhibition_level' => 'International',
                'exhibition_place' => 'ONLINE',
                'start_date' => '2025-08-11',
                'end_date' => '2025-08-11',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'event_name' => 'EKSPO PENYELIDIKAN ANTARABANGSA',
                'organizer' => 'EKSPO PENYELIDIKAN ANTARABANGSA',
                'exhibition_level' => 'International',
                'exhibition_place' => 'UNIVERSITI TUN HUSSEIN ONN MALAYSIA',
                'start_date' => '2025-08-11',
                'end_date' => '2025-08-11',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'event_name' => 'KUALA LUMPUR INTERNATIONAL INVENTION & INNOVATION SYMPOSIUM 2025, UNIVERSITI TEKNOLOGI MARA (UITM) KELANTAN BRANCH',
                'organizer' => 'KUALA LUMPUR INTERNATIONAL INVENTION & INNOVATION SYMPOSIUM 2025, UNIVERSITI TEKNOLOGI MARA (UITM) KELANTAN BRANCH',
                'exhibition_level' => 'International',
                'exhibition_place' => 'ONLINE',
                'start_date' => '2025-08-25',
                'end_date' => '2025-08-25',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'event_name' => 'HARI INOVASI NEGERI MELAKA',
                'organizer' => 'HARI INOVASI NEGERI MELAKA',
                'exhibition_level' => 'National',
                'exhibition_place' => 'DEWAN LESTARI UNIKL MICET',
                'start_date' => '2025-09-25',
                'end_date' => '2025-09-25',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'event_name' => 'WORLD INOVATION IN STEM COMPETITION 2025',
                'organizer' => 'WORLD INOVATION IN STEM COMPETITION 2025',
                'exhibition_level' => 'International',
                'exhibition_place' => 'WORLD INOVATION IN STEM COMPETITION 2025',
                'start_date' => '2025-09-27',
                'end_date' => '2025-09-27',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'event_name' => 'RBN-AUTOMOBILE ROBONEO 2025',
                'organizer' => 'RBN-AUTOMOBILE ROBONEO 2025',
                'exhibition_level' => 'National',
                'exhibition_place' => 'PAPAR, SABAH',
                'start_date' => '2025-10-04',
                'end_date' => '2025-10-04',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'event_name' => 'INTERNATIONAL TECHNOLOGY TELENT COMPETITION',
                'organizer' => 'INTERNATIONAL TECHNOLOGY TELENT COMPETITION',
                'exhibition_level' => 'International',
                'exhibition_place' => 'UiTM, SEGAMAT, JOHOR',
                'start_date' => '2025-10-09',
                'end_date' => '2025-10-09',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'event_name' => 'XCIPTA 2025',
                'organizer' => 'XCIPTA 2025',
                'exhibition_level' => 'National',
                'exhibition_place' => 'ONLINE, UNIMAP',
                'start_date' => '2025-10-10',
                'end_date' => '2025-10-10',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'event_name' => 'PERTANDINGAN INOVASI IPTA/S',
                'organizer' => 'PERTANDINGAN INOVASI IPTA/S',
                'exhibition_level' => 'National',
                'exhibition_place' => 'PERSADA JOHOR INTERNATIONAL CONVENTION CENTRE',
                'start_date' => '2025-10-07',
                'end_date' => '2025-10-08',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($competitions as $competition) {
            DB::table('competitions')->insert($competition);
        }
    }
}
