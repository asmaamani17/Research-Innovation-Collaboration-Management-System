<?php

namespace App\Exports;

use App\Models\Award;
use App\Models\Staff;
use App\Models\Faculty;
use App\Models\Event;
use App\Models\Project;
use Illuminate\Contracts\View\View;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AwardsReportExport implements FromView, WithTitle, WithStyles, WithColumnWidths
{
    protected $type;
    protected $year;
    protected $filters;

    public function __construct(string $type, int $year, array $filters = [])
    {
        $this->type = $type;
        $this->year = $year;
        $this->filters = $filters;
    }

    public function view(): View
    {
        switch ($this->type) {
            case 'faculty_performance':
                return view('admin.awards.exports.reports.faculty-performance', $this->getFacultyData());
            case 'award_statistics':
                return view('admin.awards.exports.reports.award-statistics', $this->getAwardStatistics());
            case 'staff_performance':
                return view('admin.awards.exports.reports.staff-performance', $this->getStaffData());
            case 'event_participation':
                return view('admin.awards.exports.reports.event-participation', $this->getEventData());
            case 'comprehensive':
                return view('admin.awards.exports.reports.comprehensive', $this->getComprehensiveData());
            default:
                return view('admin.awards.exports.reports.summary', $this->getSummaryData());
        }
    }

    public function title(): string
    {
        $titles = [
            'faculty_performance' => 'Faculty Performance Report',
            'award_statistics' => 'Award Statistics Report',
            'staff_performance' => 'Staff Performance Report',
            'event_participation' => 'Event Participation Report',
            'comprehensive' => 'Comprehensive Report',
            'summary' => 'Summary Report'
        ];

        return $titles[$this->type] ?? 'Report';
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 14]],
            'A1:Z1' => ['fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E3F2FD']]],
            'A2:Z2' => [
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
            'D' => 15,
            'E' => 15,
            'F' => 15,
            'G' => 15,
            'H' => 20,
            'I' => 20,
            'J' => 20,
        ];
    }

    private function getFacultyData(): array
    {
        $faculties = Faculty::withCount([
            'staff',
            'awards' => function ($q) {
                $q->whereYear('award_date', $this->year);
            }
        ])
            ->orderBy('awards_count', 'desc')
            ->get();

        $facultyStats = $faculties->map(function ($faculty) {
            $awards = $faculty->awards()->whereYear('award_date', $this->year)->get();

            return [
                'faculty' => $faculty,
                'total_awards' => $awards->count(),
                'gold_awards' => $awards->where('award_level', 'Gold')->count(),
                'silver_awards' => $awards->where('award_level', 'Silver')->count(),
                'bronze_awards' => $awards->where('award_level', 'Bronze')->count(),
                'unique_staff' => $awards->pluck('staff_id')->unique()->count(),
                'unique_projects' => $awards->pluck('project_id')->unique()->count(),
                'awards_per_staff' => $faculty->staff_count > 0 ? round($awards->count() / $faculty->staff_count, 2) : 0,
                'participation_rate' => $faculty->staff_count > 0 ? round(($awards->pluck('staff_id')->unique()->count() / $faculty->staff_count) * 100, 2) : 0,
            ];
        });

        return [
            'facultyStats' => $facultyStats,
            'year' => $this->year,
            'generated_at' => now()->format('Y-m-d H:i:s')
        ];
    }

    private function getAwardStatistics(): array
    {
        $stats = [
            'total_awards' => Award::whereYear('award_date', $this->year)->count(),
            'gold_awards' => Award::whereYear('award_date', $this->year)->where('award_level', 'Gold')->count(),
            'silver_awards' => Award::whereYear('award_date', $this->year)->where('award_level', 'Silver')->count(),
            'bronze_awards' => Award::whereYear('award_date', $this->year)->where('award_level', 'Bronze')->count(),
        ];

        $levelDistribution = Award::whereYear('award_date', $this->year)
            ->select('award_level', \Illuminate\Support\Facades\DB::raw('count(*) as count'))
            ->groupBy('award_level')
            ->orderBy('count', 'desc')
            ->get();

        $typeDistribution = Award::whereYear('award_date', $this->year)
            ->select('award_type', \Illuminate\Support\Facades\DB::raw('count(*) as count'))
            ->groupBy('award_type')
            ->orderBy('count', 'desc')
            ->get();

        return [
            'stats' => $stats,
            'levelDistribution' => $levelDistribution,
            'typeDistribution' => $typeDistribution,
            'year' => $this->year,
            'generated_at' => now()->format('Y-m-d H:i:s')
        ];
    }

    private function getStaffData(): array
    {
        $query = Staff::with('faculty')->withCount('awards');

        if (isset($this->filters['faculty_id']) && $this->filters['faculty_id']) {
            $query->where('faculty_id', $this->filters['faculty_id']);
        }

        $query->withCount([
            'awards' => function ($q) {
                $q->whereYear('award_date', $this->year);
            }
        ]);

        if (isset($this->filters['min_awards']) && $this->filters['min_awards']) {
            $query->having('awards_count', '>=', $this->filters['min_awards']);
        }

        $staff = $query->orderBy('awards_count', 'desc')->get();

        $staffStats = $staff->map(function ($staffMember) {
            $awards = $staffMember->awards()->whereYear('award_date', $this->year)->get();

            return [
                'staff' => $staffMember,
                'total_awards' => $awards->count(),
                'gold_awards' => $awards->where('award_level', 'Gold')->count(),
                'silver_awards' => $awards->where('award_level', 'Silver')->count(),
                'bronze_awards' => $awards->where('award_level', 'Bronze')->count(),
                'unique_projects' => $awards->pluck('project_id')->unique()->count(),
                'highest_award' => $awards->sortByDesc('award_date')->first()?->award_name,
                'recent_award' => ($awards->sortByDesc('award_date')->first() && $awards->sortByDesc('award_date')->first()->award_date) ? Carbon::parse($awards->sortByDesc('award_date')->first()->award_date)->format('M Y') : null,
            ];
        });

        return [
            'staffStats' => $staffStats,
            'year' => $this->year,
            'generated_at' => now()->format('Y-m-d H:i:s')
        ];
    }

    private function getEventData(): array
    {
        $query = Event::withCount(['awards', 'staff as unique_participants', 'projects as unique_projects']);

        if (isset($this->filters['year']) && $this->filters['year']) {
            $query->whereYear('start_date', $this->filters['year']);
        }

        if (isset($this->filters['level']) && $this->filters['level']) {
            $query->where('national_level', $this->filters['level']);
        }

        $events = $query->orderBy('start_date', 'desc')->get();

        $eventStats = $events->map(function ($event) {
            $awards = $event->awards;

            return [
                'event' => $event,
                'total_awards' => $awards->count(),
                'unique_participants' => $event->unique_participants,
                'unique_projects' => $event->unique_projects,
                'gold_awards' => $awards->where('award_level', 'Gold')->count(),
                'silver_awards' => $awards->where('award_level', 'Silver')->count(),
                'bronze_awards' => $awards->where('award_level', 'Bronze')->count(),
                'faculties_involved' => $awards->pluck('staff.faculty_id')->unique()->count(),
                'duration_days' => ($event->start_date && $event->end_date) ? Carbon::parse($event->start_date)->diffInDays(Carbon::parse($event->end_date)) + 1 : 0,
                'participation_efficiency' => $event->unique_participants > 0 ?
                    round(($awards->count() / $event->unique_participants) * 100, 2) : 0,
            ];
        });

        return [
            'eventStats' => $eventStats,
            'year' => $this->year,
            'generated_at' => now()->format('Y-m-d H:i:s')
        ];
    }

    private function getComprehensiveData(): array
    {
        // Executive Summary
        $executiveSummary = [
            'total_awards' => Award::whereYear('award_date', $this->year)->count(),
            'total_staff_participated' => Award::whereYear('award_date', $this->year)
                ->distinct('staff_id')->count('staff_id'),
            'total_projects_featured' => Award::whereYear('award_date', $this->year)
                ->distinct('project_id')->count('project_id'),
            'total_events_held' => Event::whereYear('start_date', $this->year)->count(),
            'total_faculties_involved' => Award::whereYear('award_date', $this->year)
                ->join('staff', 'awards.staff_id', '=', 'staff.id')
                ->distinct('staff.faculty_id')->count('staff.faculty_id'),
        ];

        // Performance by Faculty
        $facultyPerformance = Faculty::with([
            'awards' => function ($q) {
                $q->whereYear('award_date', $this->year);
            }
        ])->get()->map(function ($faculty) {
            $awards = $faculty->awards;
            return [
                'faculty_name' => $faculty->faculty_name,
                'total_awards' => $awards->count(),
                'gold_awards' => $awards->where('award_level', 'Gold')->count(),
                'staff_participated' => $awards->pluck('staff_id')->unique()->count(),
            ];
        })->sortByDesc('total_awards')->values();

        return [
            'executiveSummary' => $executiveSummary,
            'facultyPerformance' => $facultyPerformance,
            'year' => $this->year,
            'generated_at' => now()->format('Y-m-d H:i:s')
        ];
    }

    private function getSummaryData(): array
    {
        $summary = [
            'total_awards' => Award::count(),
            'total_staff' => Staff::count(),
            'total_projects' => Project::count(),
            'total_events' => Event::count(),
            'total_faculties' => Faculty::count(),
            'this_year_awards' => Award::whereYear('award_date', date('Y'))->count(),
            'last_year_awards' => Award::whereYear('award_date', date('Y') - 1)->count(),
        ];

        return [
            'summary' => $summary,
            'year' => $this->year,
            'generated_at' => now()->format('Y-m-d H:i:s')
        ];
    }
}
