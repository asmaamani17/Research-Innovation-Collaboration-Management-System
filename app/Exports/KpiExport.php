<?php

namespace App\Exports;

use App\Models\KpiStrategy;
use App\Models\KpiRecord;
use App\Models\KpiYear;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class KpiExport implements FromView, WithTitle, WithStyles, WithColumnWidths
{
    protected $year;

    public function __construct(int $year)
    {
        $this->year = $year;
    }

    public function view(): View
    {
        $strategies = KpiStrategy::ordered()->with([
            'kpiRecords.kpiYears' => function ($query) {
                $query->where('target_year', $this->year)->with('phases');
            }
        ])->get();

        return view('admin.kpi.exports.kpi', [
            'strategies' => $strategies,
            'year' => $this->year,
            'generated_at' => now()->format('Y-m-d H:i:s')
        ]);
    }

    public function title(): string
    {
        return 'KPI Data Strategi ' . $this->year;
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 14]],
            'A1:M1' => ['fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E3F2FD']]],
            'A2:M2' => [
                'font' => ['bold' => true],
                'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F5F5F5']],
            ],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 15,
            'B' => 25,
            'C' => 15,
            'D' => 40,
            'E' => 15,
            'F' => 15,
            'G' => 15,
            'H' => 15,
            'I' => 15,
            'J' => 15,
            'K' => 15,
            'L' => 15,
            'M' => 40,
        ];
    }
}
