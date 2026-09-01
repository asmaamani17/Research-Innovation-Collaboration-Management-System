<?php

namespace App\Exports;

use App\Models\Award;
use App\Models\Event;
use Illuminate\Contracts\View\View;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AwardsTemplateExport implements FromView, WithTitle, WithStyles, WithColumnWidths
{
    protected $year;
    protected $filters;
    protected $templateType;

    public function __construct(int $year = null, array $filters = [], string $templateType = 'myRA')
    {
        $this->year = $year ?? 2025;
        $this->filters = $filters;
        $this->templateType = $templateType;
    }

    public function view(): View
    {
        return view('admin.awards.exports.reports.awards-template', array_merge($this->getTemplateData(), ['template_type' => $this->templateType]));
    }

    public function title(): string
    {
        return 'Awards Report ' . $this->year;
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 11]],
            'A1:N1' => ['fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '4472C4']]],
            'A1:N1' => ['font' => ['color' => ['rgb' => 'FFFFFF']]],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,   // NO
            'B' => 15,  // PROJECT ID
            'C' => 15,  // STAFF ID / GRANT NO
            'D' => 25,  // STAFF NAME
            'E' => 20,  // FACULTY
            'F' => 20,  // NAME OF AWARD
            'G' => 15,  // LEVEL OF AWARD / ORGANIZER
            'H' => 15,  // TYPE OF AWARD / EXIBITION LEVEL
            'I' => 20,  // ORGANIZER / TITLE OF INVENTION
            'J' => 15,  // EXIBITION LEVEL / EVENT
            'K' => 30,  // TITLE OF INVENTION / EXIBITION PLACE
            'L' => 25,  // EVENT / START DATE
            'M' => 15,  // DATE AWARDS / END DATE
            'N' => 15,  // LINK TO EVIDENCE / AMOUNT
        ];
    }

    private function getTemplateData(): array
    {
        // Get all awards for the specified year with all related data
        $query = Award::with(['staff.faculty', 'project', 'event'])
            ->whereYear('award_date', $this->year);

        // Apply filters if provided
        if (isset($this->filters['faculty']) && $this->filters['faculty']) {
            $query->whereHas('staff', function ($q) {
                $q->where('faculty_id', $this->filters['faculty']);
            });
        }

        if (isset($this->filters['level']) && $this->filters['level']) {
            $query->whereHas('event', function ($q) {
                $q->where('national_level', $this->filters['level']);
            });
        }

        $awards = $query->get();

        // If no awards found for the year, get all awards
        if ($awards->count() === 0) {
            $query = Award::with(['staff.faculty', 'project', 'event']);
            
            if (isset($this->filters['faculty']) && $this->filters['faculty']) {
                $query->whereHas('staff', function ($q) {
                    $q->where('faculty_id', $this->filters['faculty']);
                });
            }

            if (isset($this->filters['level']) && $this->filters['level']) {
                $query->whereHas('event', function ($q) {
                    $q->where('national_level', $this->filters['level']);
                });
            }

            $awards = $query->get();
        }

        // Debug: Log the count and first award to check data
        \Log::info('Awards Template Export', [
            'year' => $this->year,
            'count' => $awards->count(),
            'first_award' => $awards->first(),
        ]);

        return [
            'awards' => $awards,
            'year' => $this->year
        ];
    }

    private function determineAwardLevel($award): string
    {
        // Determine if award is INDIVIDUAL or INSTITUTIONAL based on staff/project relationship
        // For now, we'll assume awards with staff are INDIVIDUAL
        return 'INDIVIDUAL';
    }

    private function determineAwardType($award): string
    {
        // Map award_type to template categories
        $typeMapping = [
            'Award' => 'AWARD',
            'Recognition' => 'RECOGNITION',
            'Stewardship' => 'STEWARDSHIP',
            'Exhibition' => 'EXHIBITION',
            'Other Research Awards' => 'OTHER RESEARCH AWARDS',
            'Clarivate Highly Award' => 'CLARIVATE HIGHLY AWARD'
        ];

        return $typeMapping[$award->award_type] ?? 'OTHER RESEARCH AWARDS';
    }

    private function determineAwardScope($award): string
    {
        // Determine if award is NATIONAL or INTERNATIONAL based on event
        if ($award->event && $award->event->national_level) {
            $level = strtoupper($award->event->national_level);
            return in_array($level, ['NATIONAL', 'INTERNATIONAL']) ? $level : 'NATIONAL';
        }

        return 'NATIONAL';
    }
}
