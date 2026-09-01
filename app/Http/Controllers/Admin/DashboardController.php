<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Award;
use App\Models\Staff;
use App\Models\Faculty;
use App\Models\Project;
use App\Models\Event;
use App\Models\Competition;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard.
     */
    public function index()
    {
        $currentYear = now()->year;
        $lastYear = $currentYear - 1;

        $stats = [
            'total_awards' => Award::count(),
            'total_staff' => Staff::count(),
            'total_projects' => Project::count(),
            'total_events' => Competition::count(),
            'total_faculties' => Faculty::count(),
            'this_year_awards' => Award::whereYear('award_date', $currentYear)->count(),
            'last_year_awards' => Award::whereYear('award_date', $lastYear)->count(),
            'current_year' => $currentYear,
        ];

        $stats['award_growth_percent'] = $stats['last_year_awards'] > 0
            ? round((($stats['this_year_awards'] - $stats['last_year_awards']) / $stats['last_year_awards']) * 100, 1)
            : null;

        $recent_awards = Award::with(['staff.faculty', 'project', 'event'])
            ->orderByDesc('award_date')
            ->orderByDesc('created_at')
            ->limit(8)
            ->get();

        $top_faculties = Faculty::withCount(['staff', 'awards'])
            ->orderByDesc('awards_count')
            ->get();

        $max_faculty_awards = max((int) $top_faculties->max('awards_count'), 1);

        $nationality_stats = Award::query()
            ->leftJoin('competitions', 'awards.competition_id', '=', 'competitions.id')
            ->selectRaw("COALESCE(competitions.exhibition_level, 'Unknown') as level, COUNT(*) as total")
            ->groupBy('competitions.exhibition_level')
            ->orderByDesc('total')
            ->get()
            ->map(function ($row) use ($stats) {
                $row->percentage = $stats['total_awards'] > 0
                    ? round(($row->total / $stats['total_awards']) * 100, 1)
                    : 0;

                return $row;
            });

        $levelColors = [
            'INTERNATIONAL' => '#184290',
            'NATIONAL' => '#10B981',
            'UNKNOWN' => '#6B7280',
        ];

        $chartOffset = 0;
        $nationality_stats = $nationality_stats->map(function ($row) use ($levelColors, &$chartOffset) {
            $levelKey = strtoupper($row->level ?? 'Unknown');
            $row->label = ucwords(strtolower($row->level ?? 'Unknown'));
            $row->color = $levelColors[$levelKey] ?? '#6B7280';
            $row->dash = max((float) $row->percentage, 0);
            $row->dash_offset = $chartOffset;
            $chartOffset += $row->dash;

            return $row;
        });

        return view('admin.awards.dashboard', compact(
            'stats',
            'recent_awards',
            'top_faculties',
            'max_faculty_awards',
            'nationality_stats'
        ));
    }
}
