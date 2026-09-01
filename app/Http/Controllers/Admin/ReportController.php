<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Award;
use App\Models\Staff;
use App\Models\Project;
use App\Models\Competition;
use App\Models\Faculty;
use App\Exports\AwardsReportExport;
use App\Exports\AwardsTemplateExport;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    /**
     * Display the reports dashboard.
     */
    public function index(Request $request)
    {
        // Get the most recent year with awards
        $latestYear = Award::whereNotNull('award_date')
            ->orderBy('award_date', 'desc')
            ->value(DB::raw('YEAR(award_date)'));

        $targetYear = $latestYear ?: date('Y');

        // Summary statistics
        $summary = [
            'total_awards' => Award::count(),
            'total_staff' => Staff::count(),
            'total_projects' => Project::count(),
            'total_events' => Competition::count(),
            'total_faculties' => Faculty::count(),
            'this_year_awards' => Award::whereYear('award_date', $targetYear)->count(),
            'last_year_awards' => Award::whereYear('award_date', $targetYear - 1)->count(),
        ];

        // Recent activity
        $recentActivity = Award::with(['staff', 'project', 'event'])
            ->orderBy('award_date', 'desc')
            ->limit(10)
            ->get();

        // Top Performing Faculties – using withCount (cleaner and reliable)
        $topFaculties = Faculty::withCount(['awards' => function ($q) use ($targetYear) {
            $q->whereYear('award_date', $targetYear);
        }])
        ->having('awards_count', '>', 0)
        ->orderBy('awards_count', 'desc')
        ->limit(5)
        ->get();

        // Monthly awards trend – ensure we get all months (including zeros)
        $rawMonthly = Award::whereYear('award_date', $targetYear)
            ->select(DB::raw('MONTH(award_date) as month'), DB::raw('count(*) as count'))
            ->groupBy(DB::raw('MONTH(award_date)'))
            ->orderBy('month')
            ->get()
            ->keyBy('month');

        $monthlyTrend = collect(range(1, 12))->map(function ($month) use ($rawMonthly) {
            return (object)[
                'month' => $month,
                'count' => $rawMonthly->get($month)->count ?? 0,
            ];
        });

        // Detailed award records by faculty - check award_name field for award types
        $detailedRecords = Faculty::select(
            'faculties.id',
            'faculties.faculty_code',
            DB::raw('COUNT(CASE WHEN LOWER(TRIM(awards.award_name)) LIKE "%gold%" THEN 1 END) as gold_count'),
            DB::raw('COUNT(CASE WHEN LOWER(TRIM(awards.award_name)) LIKE "%silver%" THEN 1 END) as silver_count'),
            DB::raw('COUNT(CASE WHEN LOWER(TRIM(awards.award_name)) LIKE "%bronze%" THEN 1 END) as bronze_count'),
            DB::raw('COUNT(CASE WHEN LOWER(TRIM(awards.award_name)) LIKE "%special%" THEN 1 END) as special_count'),
            DB::raw('COUNT(CASE WHEN LOWER(TRIM(awards.award_name)) LIKE "%platinum%" THEN 1 END) as platinum_count'),
            DB::raw('COUNT(awards.id) as total_count'),
            DB::raw('GROUP_CONCAT(DISTINCT awards.award_name) as sample_award_names')
        )
        ->leftJoin('staff', 'faculties.id', '=', 'staff.faculty_id')
        ->leftJoin('awards', 'staff.id', '=', 'awards.staff_id')
        ->whereYear('awards.award_date', $targetYear)
        ->groupBy('faculties.id', 'faculties.faculty_code')
        ->orderBy('total_count', 'desc')
        ->get();

        return view('admin.awards.reports.index', compact(
            'summary',
            'recentActivity',
            'topFaculties',
            'monthlyTrend',
            'detailedRecords',
            'targetYear'
        ));
    }

    /**
     * Generate faculty performance report with enhanced analytics.
     */
    public function facultyPerformance(Request $request)
    {
        $query = Faculty::withCount(['staff', 'awards']);

        // Filter by year if provided
        if ($request->has('year') && $request->year) {
            $year = $request->year;
            $query->withCount([
                'awards' => function ($q) use ($year) {
                    $q->whereYear('award_date', $year);
                }
            ]);
        }

        // Filter by faculty if provided
        if ($request->has('faculty_id') && $request->faculty_id) {
            $query->where('id', $request->faculty_id);
        }

        $faculties = $query->orderBy('awards_count', 'desc')->get();

        // Calculate detailed statistics for each faculty
        $facultyStats = $faculties->map(function ($faculty) use ($request) {
            $awardsQuery = $faculty->awards();

            if ($request->has('year') && $request->year) {
                $awardsQuery->whereYear('award_date', $request->year);
            }

            $awards = $awardsQuery->get();

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

        // Get available years for filtering
        $availableYears = Award::selectRaw('YEAR(award_date) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');

        return view('admin.reports.faculty-performance', compact('facultyStats', 'availableYears'));
    }

    /**
     * Generate comprehensive award statistics report.
     */
    public function awardStatistics(Request $request)
    {
        $year = $request->get('year', date('Y'));

        // Basic award statistics
        $stats = [
            'total_awards' => Award::whereYear('award_date', $year)->count(),
            'gold_awards' => Award::whereYear('award_date', $year)->where('award_level', 'Gold')->count(),
            'silver_awards' => Award::whereYear('award_date', $year)->where('award_level', 'Silver')->count(),
            'bronze_awards' => Award::whereYear('award_date', $year)->where('award_level', 'Bronze')->count(),
        ];

        // Award level distribution
        $levelDistribution = Award::whereYear('award_date', $year)
            ->select('award_level', DB::raw('count(*) as count'))
            ->groupBy('award_level')
            ->orderBy('count', 'desc')
            ->get();

        // Award type distribution
        $typeDistribution = Award::whereYear('award_date', $year)
            ->select('award_type', DB::raw('count(*) as count'))
            ->groupBy('award_type')
            ->orderBy('count', 'desc')
            ->get();

        // Monthly distribution with trend analysis
        $monthlyDistribution = Award::whereYear('award_date', $year)
            ->select(
                DB::raw('MONTH(award_date) as month'),
                DB::raw('count(*) as count')
            )
            ->groupBy(DB::raw('MONTH(award_date)'))
            ->orderBy('month')
            ->get();

        // Fill missing months with zero
        $monthlyData = [];
        for ($i = 1; $i <= 12; $i++) {
            $monthData = $monthlyDistribution->where('month', $i)->first();
            $monthlyData[] = [
                'month' => $i,
                'month_name' => date('F', mktime(0, 0, 0, $i, 1)),
                'count' => $monthData ? $monthData->count : 0,
            ];
        }

        // Top performers with detailed metrics
        $topStaff = Staff::with('faculty')
            ->withCount([
                'awards' => function ($q) use ($year) {
                    $q->whereYear('award_date', $year);
                }
            ])
            ->orderBy('awards_count', 'desc')
            ->limit(10)
            ->get();

        $topProjects = Project::withCount([
            'awards' => function ($q) use ($year) {
                $q->whereYear('award_date', $year);
            }
        ])
            ->orderBy('awards_count', 'desc')
            ->limit(10)
            ->get();

        $topEvents = Event::withCount([
            'awards' => function ($q) use ($year) {
                $q->whereYear('award_date', $year);
            }
        ])
            ->orderBy('awards_count', 'desc')
            ->limit(10)
            ->get();

        // Get available years
        $availableYears = Award::selectRaw('YEAR(award_date) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');

        return view('admin.reports.award-statistics', compact(
            'stats',
            'levelDistribution',
            'typeDistribution',
            'monthlyData',
            'topStaff',
            'topProjects',
            'topEvents',
            'availableYears',
            'year'
        ));
    }

    /**
     * Generate detailed staff performance report.
     */
    public function staffPerformance(Request $request)
    {
        $query = Staff::with('faculty')->withCount('awards');

        // Filters
        if ($request->has('faculty_id') && $request->faculty_id) {
            $query->where('faculty_id', $request->faculty_id);
        }

        if ($request->has('year') && $request->year) {
            $query->withCount([
                'awards' => function ($q) use ($request) {
                    $q->whereYear('award_date', $request->year);
                }
            ]);
        }

        if ($request->has('min_awards') && $request->min_awards) {
            $query->having('awards_count', '>=', $request->min_awards);
        }

        if ($request->has('award_level') && $request->award_level) {
            $query->whereHas('awards', function ($q) use ($request) {
                $q->where('award_level', $request->award_level);
                if ($request->has('year') && $request->year) {
                    $q->whereYear('award_date', $request->year);
                }
            });
        }

        $staff = $query->orderBy('awards_count', 'desc')->paginate(20);

        // Get detailed statistics for each staff member
        $staffStats = collect($staff->items())->map(function ($staffMember) use ($request) {
            $awardsQuery = $staffMember->awards();

            if ($request->has('year') && $request->year) {
                $awardsQuery->whereYear('award_date', $request->year);
            }

            $awards = $awardsQuery->get();

            return [
                'staff' => $staffMember,
                'total_awards' => $awards->count(),
                'gold_awards' => $awards->where('award_level', 'Gold')->count(),
                'silver_awards' => $awards->where('award_level', 'Silver')->count(),
                'bronze_awards' => $awards->where('award_level', 'Bronze')->count(),
                'unique_projects' => $awards->pluck('project_id')->unique()->count(),
                'highest_award' => optional($awards->sortByDesc('award_date')->first())->award_name,
                'recent_award' => optional($awards->sortByDesc('award_date')->first())->award_date ? Carbon::parse($awards->sortByDesc('award_date')->first()->award_date)->format('M Y') : null,
                'first_award' => optional($awards->sortBy('award_date')->first())->award_date ? Carbon::parse($awards->sortBy('award_date')->first()->award_date)->format('M Y') : null,
                'award_span_months' => $awards->count() > 1 ?
                    Carbon::parse($awards->sortByDesc('award_date')->first()->award_date)->diffInMonths(
                        Carbon::parse($awards->sortBy('award_date')->first()->award_date)
                    ) : 0,
            ];
        });

        // Get filter options
        $faculties = Faculty::orderBy('faculty_name')->get();
        $availableYears = Award::selectRaw('YEAR(award_date) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');

        return view('admin.reports.staff-performance', compact(
            'staff',
            'staffStats',
            'faculties',
            'availableYears'
        ));
    }

    /**
     * Generate event participation report with detailed analytics.
     */
    public function eventParticipation(Request $request)
    {
        $query = Event::withCount(['awards', 'staff as unique_participants', 'projects as unique_projects']);

        // Filters
        if ($request->has('year') && $request->year) {
            $query->whereYear('start_date', $request->year);
        }

        if ($request->has('level') && $request->level) {
            $query->where('national_level', $request->level);
        }

        if ($request->has('min_participants') && $request->min_participants) {
            $query->having('unique_participants', '>=', $request->min_participants);
        }

        $events = $query->orderBy('start_date', 'desc')->paginate(15);

        // Get detailed statistics for each event
        $eventStats = collect($events->items())->map(function ($event) {
            $awards = $event->awards;
            $start = $event->start_date ? Carbon::parse($event->start_date) : null;
            $end = $event->end_date ? Carbon::parse($event->end_date) : null;
            $duration = ($start && $end) ? $start->diffInDays($end) + 1 : 0;

            return [
                'event' => $event,
                'total_awards' => $awards->count(),
                'unique_participants' => $event->unique_participants,
                'unique_projects' => $event->unique_projects,
                'gold_awards' => $awards->where('award_level', 'Gold')->count(),
                'silver_awards' => $awards->where('award_level', 'Silver')->count(),
                'bronze_awards' => $awards->where('award_level', 'Bronze')->count(),
                'faculties_involved' => $awards->pluck('staff.faculty_id')->unique()->count(),
                'duration_days' => $duration,
                'awards_per_day' => $duration > 0 ? round($awards->count() / $duration, 2) : $awards->count(),
                'participation_efficiency' => $event->unique_participants > 0 ? round(($awards->count() / $event->unique_participants) * 100, 2) : 0,
            ];
        });

        // Get filter options
        $availableYears = Event::selectRaw('YEAR(start_date) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');

        $eventLevels = Event::select('national_level')
            ->distinct()
            ->orderBy('national_level')
            ->pluck('national_level');

        return view('admin.reports.event-participation', compact(
            'events',
            'eventStats',
            'availableYears',
            'eventLevels'
        ));
    }

    /**
     * Export report data to Excel (XLSX).
     */
    public function exportExcel(Request $request)
    {
        $type = $request->get('type', 'summary');
        $year = $request->get('year', date('Y'));
        $filters = $request->except(['type', 'year']);

        // Use template export for template type
        if ($type === 'template') {
            $templateType = $request->get('template_type', 'Template_RD8_1_New'); // Default to Template_RD8_1_New
            $filename = "{$templateType}_{$year}_" . date('Y-m-d') . ".xlsx";
            return Excel::download(
                new AwardsTemplateExport($year, $filters, $templateType),
                $filename
            );
        }

        $filename = "report_{$type}_{$year}_" . date('Y-m-d') . ".xlsx";

        return Excel::download(
            new AwardsReportExport($type, $year, $filters),
            $filename
        );
    }

    /**
     * Export report data to CSV.
     */
    public function exportCsv(Request $request)
    {
        $type = $request->get('type', 'summary');
        $year = $request->get('year', date('Y'));

        $filename = "report_{$type}_{$year}_" . date('Y-m-d') . ".csv";

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($type, $year, $request) {
            $file = fopen('php://output', 'w');

            switch ($type) {
                case 'faculty_performance':
                    $this->exportFacultyPerformance($file, $year, $request);
                    break;
                case 'award_statistics':
                    $this->exportAwardStatistics($file, $year, $request);
                    break;
                case 'staff_performance':
                    $this->exportStaffPerformance($file, $year, $request);
                    break;
                case 'event_participation':
                    $this->exportEventParticipation($file, $year, $request);
                    break;
                default:
                    $this->exportSummary($file, $year, $request);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export faculty performance data to CSV.
     */
    private function exportFacultyPerformance($file, $year, $request)
    {
        fputcsv($file, [
            'Faculty Code',
            'Faculty Name',
            'Total Staff',
            'Total Awards',
            'Gold Awards',
            'Silver Awards',
            'Bronze Awards',
            'Unique Staff Participated',
            'Unique Projects',
            'Awards per Staff',
            'Participation Rate (%)'
        ]);

        $faculties = Faculty::withCount([
            'staff',
            'awards' => function ($q) use ($year) {
                $q->whereYear('award_date', $year);
            }
        ])
            ->orderBy('awards_count', 'desc')
            ->get();

        foreach ($faculties as $faculty) {
            $awards = $faculty->awards()->whereYear('award_date', $year)->get();

            fputcsv($file, [
                $faculty->faculty_code,
                $faculty->faculty_name,
                $faculty->staff_count,
                $awards->count(),
                $awards->where('award_level', 'Gold')->count(),
                $awards->where('award_level', 'Silver')->count(),
                $awards->where('award_level', 'Bronze')->count(),
                $awards->pluck('staff_id')->unique()->count(),
                $awards->pluck('project_id')->unique()->count(),
                $faculty->staff_count > 0 ? round($awards->count() / $faculty->staff_count, 2) : 0,
                $faculty->staff_count > 0 ? round(($awards->pluck('staff_id')->unique()->count() / $faculty->staff_count) * 100, 2) : 0,
            ]);
        }
    }

    /**
     * Export award statistics data to CSV.
     */
    private function exportAwardStatistics($file, $year, $request)
    {
        fputcsv($file, [
            'Award Level',
            'Count',
            'Percentage'
        ]);

        $levels = Award::whereYear('award_date', $year)
            ->select('award_level', DB::raw('count(*) as count'))
            ->groupBy('award_level')
            ->orderBy('count', 'desc')
            ->get();

        $totalAwards = Award::whereYear('award_date', $year)->count();

        foreach ($levels as $level) {
            fputcsv($file, [
                $level->award_level,
                $level->count,
                $totalAwards > 0 ? round(($level->count / $totalAwards) * 100, 2) : 0,
            ]);
        }
    }

    /**
     * Export staff performance data to CSV.
     */
    private function exportStaffPerformance($file, $year, $request)
    {
        fputcsv($file, [
            'Staff ID',
            'Staff Name',
            'Faculty',
            'Total Awards',
            'Gold Awards',
            'Silver Awards',
            'Bronze Awards',
            'Unique Projects',
            'Latest Award',
            'Recent Award'
        ]);

        $query = Staff::with('faculty');

        if ($request->has('faculty_id') && $request->faculty_id) {
            $query->where('faculty_id', $request->faculty_id);
        }

        $staff = $query->get();

        foreach ($staff as $staffMember) {
            $awards = $staffMember->awards()->whereYear('award_date', $year)->get();

            fputcsv($file, [
                $staffMember->staff_id,
                $staffMember->staff_name,
                $staffMember->faculty->faculty_name ?? 'Unknown',
                $awards->count(),
                $awards->where('award_level', 'Gold')->count(),
                $awards->where('award_level', 'Silver')->count(),
                $awards->where('award_level', 'Bronze')->count(),
                $awards->pluck('project_id')->unique()->count(),
                $awards->sortByDesc('award_date')->first()?->award_name ?? 'None',
                ($awards->sortByDesc('award_date')->first() && $awards->sortByDesc('award_date')->first()->award_date) ? Carbon::parse($awards->sortByDesc('award_date')->first()->award_date)->format('M Y') : 'Never',
            ]);
        }
    }

    /**
     * Export event participation data to CSV.
     */
    private function exportEventParticipation($file, $year, $request)
    {
        fputcsv($file, [
            'Event Name',
            'Location',
            'Start Date',
            'End Date',
            'Duration (Days)',
            'Total Awards',
            'Unique Participants',
            'Unique Projects',
            'Gold Awards',
            'Silver Awards',
            'Bronze Awards',
            'Faculties Involved',
            'Awards per Day',
            'Participation Efficiency (%)'
        ]);

        $query = Event::withCount(['awards', 'staff as unique_participants', 'projects as unique_projects']);

        if ($request->has('year') && $request->year) {
            $query->whereYear('start_date', $request->year);
        }

        $events = $query->orderBy('start_date', 'desc')->get();

        foreach ($events as $event) {
            $awards = $event->awards;

            $start = $event->start_date ? Carbon::parse($event->start_date) : null;
            $end = $event->end_date ? Carbon::parse($event->end_date) : null;
            $duration = ($start && $end) ? $start->diffInDays($end) + 1 : 0;

            fputcsv($file, [
                $event->event_name,
                $event->exhibition_place ?? 'N/A',
                $start ? $start->format('Y-m-d') : 'N/A',
                $end ? $end->format('Y-m-d') : 'N/A',
                $duration,
                $awards->count(),
                $event->unique_participants,
                $event->unique_projects,
                $awards->where('award_level', 'Gold')->count(),
                $awards->where('award_level', 'Silver')->count(),
                $awards->where('award_level', 'Bronze')->count(),
                $awards->pluck('staff.faculty_id')->unique()->count(),
                $duration > 0 ? round($awards->count() / $duration, 2) : $awards->count(),
                $event->unique_participants > 0 ? round(($awards->count() / $event->unique_participants) * 100, 2) : 0,
            ]);
        }
    }

    /**
     * Export summary data to CSV.
     */
    private function exportSummary($file, $year, $request)
    {
        fputcsv($file, [
            'Metric',
            'Value',
            'Description'
        ]);

        $summary = [
            'total_awards' => Award::count(),
            'total_staff' => Staff::count(),
            'total_projects' => Project::count(),
            'total_events' => Event::count(),
            'total_faculties' => Faculty::count(),
            'this_year_awards' => Award::whereYear('award_date', date('Y'))->count(),
            'last_year_awards' => Award::whereYear('award_date', date('Y') - 1)->count(),
        ];

        fputcsv($file, ['Total Awards', $summary['total_awards'], 'All time total number of awards']);
        fputcsv($file, ['Total Staff', $summary['total_staff'], 'Total number of staff members']);
        fputcsv($file, ['Total Projects', $summary['total_projects'], 'Total number of projects']);
        fputcsv($file, ['Total Events', $summary['total_events'], 'Total number of events']);
        fputcsv($file, ['Total Faculties', $summary['total_faculties'], 'Total number of faculties']);
        fputcsv($file, ['This Year Awards', $summary['this_year_awards'], 'Awards received in ' . date('Y')]);
        fputcsv($file, ['Last Year Awards', $summary['last_year_awards'], 'Awards received in ' . (date('Y') - 1)]);

        $growthRate = $summary['last_year_awards'] > 0 ?
            round((($summary['this_year_awards'] - $summary['last_year_awards']) / $summary['last_year_awards']) * 100, 1) : 0;
        fputcsv($file, ['Growth Rate', $growthRate . '%', 'Year-over-year growth rate']);
    }

    /**
     * API endpoint for chart data.
     */
    public function getChartData(Request $request)
    {
        $type = $request->get('type');
        $year = $request->get('year', date('Y'));

        switch ($type) {
            case 'monthly_awards':
                $data = Award::whereYear('award_date', $year)
                    ->select(DB::raw('MONTH(award_date) as month'), DB::raw('count(*) as count'))
                    ->groupBy(DB::raw('MONTH(award_date)'))
                    ->orderBy('month')
                    ->get();
                break;

            case 'level_distribution':
                $data = Award::whereYear('award_date', $year)
                    ->select('award_level', DB::raw('count(*) as count'))
                    ->groupBy('award_level')
                    ->orderBy('count', 'desc')
                    ->get();
                break;

            case 'faculty_performance':
                $data = Faculty::withCount([
                    'awards' => function ($q) use ($year) {
                        $q->whereYear('award_date', $year);
                    }
                ])
                    ->orderBy('awards_count', 'desc')
                    ->limit(10)
                    ->get();
                break;

            case 'award_trends':
                $data = [];
                for ($i = 0; $i < 12; $i++) {
                    $month = $i + 1;
                    $data[] = [
                        'month' => $month,
                        'month_name' => date('F', mktime(0, 0, 0, $month, 1)),
                        'count' => Award::whereYear('award_date', $year)
                            ->whereMonth('award_date', $month)
                            ->count(),
                        'gold' => Award::whereYear('award_date', $year)
                            ->whereMonth('award_date', $month)
                            ->where('award_level', 'Gold')
                            ->count(),
                        'silver' => Award::whereYear('award_date', $year)
                            ->whereMonth('award_date', $month)
                            ->where('award_level', 'Silver')
                            ->count(),
                        'bronze' => Award::whereYear('award_date', $year)
                            ->whereMonth('award_date', $month)
                            ->where('award_level', 'Bronze')
                            ->count(),
                    ];
                }
                break;

            default:
                $data = [];
        }

        return response()->json($data);
    }
}
