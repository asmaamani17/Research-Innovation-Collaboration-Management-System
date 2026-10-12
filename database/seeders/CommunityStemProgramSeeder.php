<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CommunityStemProgramSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing data
        DB::table('community_stem_beneficiaries')->delete();
        DB::table('community_stem_programs')->delete();

        // Get existing staff IDs and project IDs
        $staffIds = DB::table('staff')->pluck('staff_id')->toArray();
        $projectIds = DB::table('projects')->pluck('project_id')->toArray();

        // Read CSV file from local storage
        $csvPath = storage_path('app/import/borang_a.csv');
        
        if (!file_exists($csvPath)) {
            $this->command->error('CSV file not found: ' . $csvPath);
            return;
        }

        $file = fopen($csvPath, 'r');
        
        // Skip header rows (3 rows)
        fgetcsv($file);
        fgetcsv($file);
        fgetcsv($file);

        $programs = [];
        $beneficiaries = [];
        $projectIndex = 0;

        while (($row = fgetcsv($file)) !== false) {
            // Skip empty rows
            if (empty($row[0])) {
                continue;
            }

            // Parse CSV columns
            $timestamp = $row[0];
            $staffName = $row[2];
            $staffId = $row[3];
            $faculty = trim($row[5]);
            $communityIndustryName = trim($row[6]);
            $programName = trim($row[7]);
            $kategori = trim($row[8]);
            $jenisProgram = trim($row[9]);
            $organiser = trim($row[10]);
            $sponsorshipValue = trim($row[11]);
            $startDate = $row[12];
            $endDate = $row[13];
            $linkBorangA = trim($row[14]);
            $linkKelulusan = trim($row[15]);

            // Map classification
            $classificationMap = [
                'Komuniti' => 'Community',
                'STEM' => 'STEM',
                'Industri' => 'Industri',
            ];
            $classification = $classificationMap[$jenisProgram] ?? 'Community';

            // Parse dates
            $startDateParsed = $this->parseDate($startDate);
            $endDateParsed = $this->parseDate($endDate);
            $submittedAt = $this->parseTimestamp($timestamp);

            $programData = [
                'project_id' => $projectIds[$projectIndex % count($projectIds)] ?? null,
                'staff_id' => $staffId,
                'faculty' => $faculty,
                'program' => $programName,
                'kategori' => $kategori,
                'classification' => $classification,
                'start_date' => $startDateParsed,
                'end_date' => $endDateParsed,
                'submitted_at' => $submittedAt,
                'status' => 'New',
                'community_industry_name' => $communityIndustryName ?: null,
                'organiser' => $organiser ?: null,
                'sponsorship_value' => $sponsorshipValue ?: null,
                'link_borang_a' => $linkBorangA ?: null,
                'link_kelulusan_kertas_kerja' => $linkKelulusan ?: null,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $programs[] = $programData;
            $projectIndex++;
        }

        fclose($file);

        // Insert programs
        $programIds = [];
        foreach ($programs as $program) {
            $programId = DB::table('community_stem_programs')->insertGetId($program);
            $programIds[] = $programId;
        }

        // Add beneficiaries for programs that have community/industry names
        foreach ($programs as $index => $program) {
            if (!empty($program['community_industry_name'])) {
                // Split by comma for multiple beneficiaries
                $beneficiaryNames = explode(',', $program['community_industry_name']);
                foreach ($beneficiaryNames as $name) {
                    $name = trim($name);
                    if (!empty($name)) {
                        $beneficiaries[] = [
                            'community_stem_program_id' => $programIds[$index],
                            'name' => $name,
                        ];
                    }
                }
            }
        }

        // Insert beneficiaries
        foreach ($beneficiaries as $beneficiary) {
            DB::table('community_stem_beneficiaries')->insert($beneficiary);
        }

        $this->command->info('Seeded ' . count($programs) . ' programs and ' . count($beneficiaries) . ' beneficiaries.');
    }

    private function parseDate(string $date): ?string
    {
        if (empty($date)) {
            return null;
        }
        
        // Try parsing various date formats
        $formats = ['m/d/Y', 'd/m/Y', 'Y-m-d'];
        foreach ($formats as $format) {
            $parsed = \DateTime::createFromFormat($format, $date);
            if ($parsed !== false) {
                return $parsed->format('Y-m-d');
            }
        }
        
        return null;
    }

    private function parseTimestamp(string $timestamp): ?string
    {
        if (empty($timestamp)) {
            return now()->format('Y-m-d H:i:s');
        }
        
        // Try parsing timestamp format: M/d/Y H:i:s
        $parsed = \DateTime::createFromFormat('m/d/Y H:i:s', $timestamp);
        if ($parsed !== false) {
            return $parsed->format('Y-m-d H:i:s');
        }
        
        return now()->format('Y-m-d H:i:s');
    }
}
