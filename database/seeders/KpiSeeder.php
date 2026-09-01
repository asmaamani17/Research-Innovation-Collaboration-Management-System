<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\KpiStrategy;
use App\Models\KpiRecord;
use App\Models\KpiYear;
use App\Models\KpiPhase;

class KpiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing KPI data
        DB::table('kpi_phases')->delete();
        DB::table('kpi_years')->delete();
        DB::table('kpi_records')->delete();
        DB::table('kpi_strategies')->delete();

        $csvPath = database_path('seeders/KPI_Data_Strategi.csv');
        
        if (!file_exists($csvPath)) {
            $this->command->warn('CSV file not found at: ' . $csvPath);
            return;
        }

        // Use fgetcsv so multiline quoted fields (achievement notes) stay intact
        $handle = fopen($csvPath, 'r');
        if ($handle === false) {
            $this->command->warn('Unable to open CSV file at: ' . $csvPath);
            return;
        }

        $csvData = [];
        while (($row = fgetcsv($handle)) !== false) {
            $csvData[] = $row;
        }
        fclose($handle);

        $currentStrategy = null;
        $strategyOrder = 1;

        // Parse CSV data (skip title + header rows)
        for ($i = 2; $i < count($csvData); $i++) {
            $row = $csvData[$i];
            
            // Skip empty rows
            if (empty(array_filter($row, fn ($value) => trim((string) $value) !== ''))) {
                continue;
            }

            // Check if this is a strategy header row
            if (isset($row[0]) && preg_match('/^STRATEGI\s+(\d+\.\d+):\s*(.+)$/', $row[0], $matches)) {
                $strategyCode = $matches[1];
                $strategyName = $matches[2];
                
                // Create or get strategy
                $strategy = KpiStrategy::firstOrCreate(
                    ['strategy_code' => $strategyCode],
                    [
                        'strategy_name' => $strategyName,
                        'display_order' => $strategyOrder++,
                    ]
                );
                $currentStrategy = $strategy;
                continue;
            }

            // Skip if no strategy is set
            if (!$currentStrategy) {
                continue;
            }

            // Parse KPI row - check if row has enough columns
            if (!isset($row[0])) {
                continue;
            }

            $initiative = isset($row[1]) ? trim($row[1]) : '';
            $performanceIndicator = isset($row[2]) ? trim($row[2]) : '';
            
            // Extract actual KPI code from performance indicator (e.g., "2.2.1.1 Bilangan paten" -> "2.2.1.1")
            $kpiCode = null;
            if (preg_match('/^(\d+\.\d+\.\d+(?:\.\d+)?)\b/', $performanceIndicator, $matches)) {
                $kpiCode = $matches[1];
            }
            
            // Skip invalid / orphaned fragments from broken multiline CSV rows
            if (
                empty($kpiCode)
                || !str_starts_with($kpiCode, $currentStrategy->strategy_code . '.')
            ) {
                continue;
            }
            
            $actionPlan = isset($row[38]) ? trim($row[38]) : ''; // Last column is action plan

            // Create KPI record
            $kpiRecord = KpiRecord::create([
                'strategy_id' => $currentStrategy->id,
                'kpi_code' => $kpiCode,
                'initiative' => $initiative,
                'performance_indicator' => $performanceIndicator,
                'action_plan' => $actionPlan,
            ]);

            // Process years (2026-2030)
            $years = [2026, 2027, 2028, 2029, 2030];
            $baseColumns = [3, 10, 17, 24, 31]; // Starting column index for each year

            foreach ($years as $index => $year) {
                $baseCol = $baseColumns[$index];
                
                $targetValue = isset($row[$baseCol]) ? $this->parseValue($row[$baseCol]) : null;
                $phase1 = isset($row[$baseCol + 1]) ? $this->parseValue($row[$baseCol + 1]) : null;
                $phase2 = isset($row[$baseCol + 2]) ? $this->parseValue($row[$baseCol + 2]) : null;
                $phase3 = isset($row[$baseCol + 3]) ? $this->parseValue($row[$baseCol + 3]) : null;
                $phase4 = isset($row[$baseCol + 4]) ? $this->parseValue($row[$baseCol + 4]) : null;
                $percentage = isset($row[$baseCol + 5]) ? $this->parsePercentage($row[$baseCol + 5]) : 0.00;
                $achievementInfo = isset($row[$baseCol + 6]) ? trim($row[$baseCol + 6]) : '';

                // Create KPI year record
                $kpiYear = KpiYear::create([
                    'kpi_id' => $kpiRecord->id,
                    'target_year' => $year,
                    'target_value' => $targetValue,
                    'achievement_percentage' => $percentage,
                    'achievement_information' => $achievementInfo,
                ]);

                // Create phase records
                KpiPhase::create([
                    'kpi_year_id' => $kpiYear->id,
                    'phase' => 'Phase 1',
                    'achievement' => $phase1,
                ]);

                KpiPhase::create([
                    'kpi_year_id' => $kpiYear->id,
                    'phase' => 'Phase 2',
                    'achievement' => $phase2,
                ]);

                KpiPhase::create([
                    'kpi_year_id' => $kpiYear->id,
                    'phase' => 'Phase 3',
                    'achievement' => $phase3,
                ]);

                KpiPhase::create([
                    'kpi_year_id' => $kpiYear->id,
                    'phase' => 'Phase 4',
                    'achievement' => $phase4,
                ]);
            }
        }

        $this->command->info('KPI data seeded successfully.');
    }

    /**
     * Parse value from CSV (handle RM format and numbers)
     */
    private function parseValue($value)
    {
        if ($value === null || trim((string) $value) === '') {
            return null;
        }
        
        // Remove RM, commas, and spaces
        $cleaned = str_replace(['RM', ',', ' '], '', (string) $value);
        $cleaned = trim($cleaned);
        
        // Keep numeric zero (empty("0") is true in PHP)
        if ($cleaned === '' || $cleaned === '-' || strcasecmp($cleaned, 'null') === 0) {
            return null;
        }
        
        // Check if it's a number
        if (is_numeric($cleaned)) {
            return $cleaned;
        }
        
        return $value; // Return as string if not numeric
    }

    /**
     * Parse percentage from CSV
     */
    private function parsePercentage($value)
    {
        if (empty($value)) return 0.00;
        
        // Remove % and spaces
        $cleaned = str_replace(['%', ' '], '', $value);
        $cleaned = trim($cleaned);
        
        if (empty($cleaned)) return 0.00;
        
        // Extract number before decimal
        if (preg_match('/([\d.]+)/', $cleaned, $matches)) {
            return floatval($matches[1]);
        }
        
        return 0.00;
    }
}
