<?php

namespace App\Http\Controllers;

use App\Models\Award;
use App\Models\Project;
use App\Models\Staff;
use App\Models\TrlProduct;
use App\Models\KpiStrategy;
use App\Models\KpiYear;
use App\Models\Competition;
use App\Models\Faculty;
use App\Models\IntellectualProperty;
use App\Models\CommunityStemProgram;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SuperAdminController extends Controller
{
    public function dashboard(Request $request)
    {
        // Set workspace to superadmin when accessing this dashboard
        session(['workspace' => 'superadmin']);

        // Active filters
        $selectedYear = $request->get('year');
        $selectedFaculty = $request->get('faculty_id') ?? $request->get('faculty');
        $selectedModule = $request->get('module');

        // Extract available distinct years across all modules
        $yearsAwards = Award::whereNotNull('award_date')->selectRaw('YEAR(award_date) as yr')->pluck('yr');
        $yearsIp = IntellectualProperty::whereNotNull('tarikh_difailkan')->selectRaw('YEAR(tarikh_difailkan) as yr')->pluck('yr');
        $yearsStem = CommunityStemProgram::whereNotNull('start_date')->selectRaw('YEAR(start_date) as yr')->pluck('yr');
        $yearsTrl = TrlProduct::whereNotNull('product_year')->selectRaw('product_year as yr')->pluck('yr');
        $yearsKpi = KpiYear::selectRaw('target_year as yr')->pluck('yr');

        $availableYears = $yearsAwards->concat($yearsIp)->concat($yearsStem)->concat($yearsTrl)->concat($yearsKpi)
            ->filter()
            ->map(fn($y) => (int)$y)
            ->unique()
            ->sortDesc()
            ->values()
            ->toArray();

        if (empty($availableYears)) {
            $availableYears = [(int)date('Y')];
        }

        // Available faculties
        $faculties = Faculty::orderBy('faculty_name')->get();

        // 1. AWARDS MODULE
        $awardsQuery = Award::with(['staff.faculty', 'event', 'project']);
        if ($selectedYear) {
            $awardsQuery->whereYear('award_date', $selectedYear);
        }
        if ($selectedFaculty) {
            $awardsQuery->whereHas('staff', function ($q) use ($selectedFaculty) {
                $q->where('faculty_id', $selectedFaculty);
            });
        }
        $totalAwards = (clone $awardsQuery)->count();
        $awardsByLevel = (clone $awardsQuery)
            ->selectRaw("COALESCE(NULLIF(award_name, ''), 'Other') as award_level, COUNT(*) as count")
            ->groupByRaw("COALESCE(NULLIF(award_name, ''), 'Other')")
            ->orderBy('count', 'desc')
            ->get()
            ->map(function ($item) {
                return (object)[
                    'award_level' => ucwords(strtolower($item->award_level)),
                    'count' => (int)$item->count,
                ];
            });
        $recentAwards = (clone $awardsQuery)->orderBy('award_date', 'desc')->limit(5)->get();

        // 2. INTELLECTUAL PROPERTY (IP) MODULE
        $ipQuery = IntellectualProperty::query();
        if ($selectedYear) {
            $ipQuery->where(function ($q) use ($selectedYear) {
                $q->where('tahun', $selectedYear)
                  ->orWhereYear('tarikh_difailkan', $selectedYear)
                  ->orWhereYear('filing_date', $selectedYear);
            });
        }
        if ($selectedFaculty) {
            $facultyObj = Faculty::find($selectedFaculty);
            $fCode = $facultyObj ? $facultyObj->faculty_code : null;
            $fName = $facultyObj ? $facultyObj->faculty_name : null;
            $ipQuery->where(function ($q) use ($selectedFaculty, $fCode, $fName) {
                if ($fCode) $q->orWhere('fakulti', 'LIKE', "%{$fCode}%");
                if ($fName) $q->orWhere('fakulti', 'LIKE', "%{$fName}%");
                $q->orWhereHas('staff', fn($sq) => $sq->where('faculty_id', $selectedFaculty));
            });
        }
        $totalIp = (clone $ipQuery)->count();
        $ipByType = (clone $ipQuery)
            ->selectRaw('COALESCE(jenis_ipr, type, "Other") as type_name, COUNT(*) as count')
            ->groupBy('type_name')
            ->get();
        $recentIp = (clone $ipQuery)->orderBy('tarikh_difailkan', 'desc')->limit(5)->get();

        // 3. COMMUNITY, STEM & INDUSTRY MODULE
        $stemQuery = CommunityStemProgram::query();
        if ($selectedYear) {
            $stemQuery->whereYear('start_date', $selectedYear);
        }
        if ($selectedFaculty) {
            $facultyObj = Faculty::find($selectedFaculty);
            $fCode = $facultyObj ? $facultyObj->faculty_code : null;
            if ($fCode) {
                $stemQuery->where('faculty', 'LIKE', "%{$fCode}%");
            }
        }
        $totalStemPrograms = (clone $stemQuery)->count();
        $stemByClassification = (clone $stemQuery)
            ->selectRaw('classification, COUNT(*) as count')
            ->groupBy('classification')
            ->get();
        $recentStemPrograms = (clone $stemQuery)->orderBy('start_date', 'desc')->limit(5)->get();

        // 4. TRL PRODUCTS MODULE
        $trlQuery = TrlProduct::query();
        if ($selectedYear) {
            $trlQuery->where('product_year', $selectedYear);
        }
        if ($selectedFaculty) {
            $facultyObj = Faculty::find($selectedFaculty);
            $fCode = $facultyObj ? $facultyObj->faculty_code : null;
            if ($fCode) {
                $trlQuery->where('faculty', 'LIKE', "%{$fCode}%");
            }
        }
        $totalProducts = (clone $trlQuery)->count();
        $productsByStatus = (clone $trlQuery)
            ->selectRaw('COALESCE(NULLIF(status, ""), "Unassigned") as development_status, COUNT(*) as count')
            ->groupBy('development_status')
            ->get();
        $productsByCategory = (clone $trlQuery)
            ->selectRaw('COALESCE(NULLIF(category, ""), "General") as product_category, COUNT(*) as count')
            ->groupBy('product_category')
            ->get();
        $recentTrlProducts = (clone $trlQuery)->orderBy('created_at', 'desc')->limit(5)->get();

        // 5. KPI MODULE
        $kpiQuery = KpiYear::query();
        if ($selectedYear) {
            $kpiQuery->where('target_year', $selectedYear);
        }
        $totalKpiStrategies = KpiStrategy::count();
        $avgKpiAchievement = round((clone $kpiQuery)->avg('achievement_percentage') ?? 0, 1);
        $kpiByYear = KpiYear::selectRaw('target_year, AVG(achievement_percentage) as avg_achievement')
            ->groupBy('target_year')
            ->orderBy('target_year')
            ->get();

        // 6. GENERAL METRICS
        $totalProjects = Project::count();
        $totalStaff = Staff::count();
        $totalCompetitions = Competition::count();
        $recentProjects = Project::orderBy('created_at', 'desc')->limit(5)->get();

        return view('super_admin.dashboard', compact(
            'totalAwards',
            'totalIp',
            'totalStemPrograms',
            'totalProducts',
            'totalKpiStrategies',
            'avgKpiAchievement',
            'totalProjects',
            'totalStaff',
            'totalCompetitions',
            'awardsByLevel',
            'ipByType',
            'stemByClassification',
            'productsByStatus',
            'productsByCategory',
            'kpiByYear',
            'recentAwards',
            'recentIp',
            'recentStemPrograms',
            'recentTrlProducts',
            'recentProjects',
            'faculties',
            'availableYears',
            'selectedYear',
            'selectedFaculty',
            'selectedModule'
        ));
    }

    public function getDashboardData(Request $request)
    {
        $selectedYear = $request->get('year');
        $selectedFaculty = $request->get('faculty_id') ?? $request->get('faculty');

        // Awards by level
        $awardsQuery = Award::query();
        if ($selectedYear) $awardsQuery->whereYear('award_date', $selectedYear);
        if ($selectedFaculty) {
            $awardsQuery->whereHas('staff', fn($q) => $q->where('faculty_id', $selectedFaculty));
        }
        $awardsByLevel = $awardsQuery
            ->selectRaw("COALESCE(NULLIF(award_name, ''), 'Other') as level, COUNT(*) as count")
            ->groupByRaw("COALESCE(NULLIF(award_name, ''), 'Other')")
            ->orderBy('count', 'desc')
            ->get()
            ->map(function ($item) {
                return [
                    'level' => ucwords(strtolower($item->level)),
                    'count' => (int)$item->count,
                ];
            });

        // IP by type
        $ipQuery = IntellectualProperty::query();
        if ($selectedYear) {
            $ipQuery->where(fn($q) => $q->where('tahun', $selectedYear)->orWhereYear('tarikh_difailkan', $selectedYear));
        }
        if ($selectedFaculty) {
            $facultyObj = Faculty::find($selectedFaculty);
            $fCode = $facultyObj ? $facultyObj->faculty_code : null;
            if ($fCode) $ipQuery->where('fakulti', 'LIKE', "%{$fCode}%");
        }
        $ipByType = $ipQuery->selectRaw('COALESCE(jenis_ipr, type, "Other") as type_name, COUNT(*) as count')
            ->groupBy('type_name')
            ->get();

        // STEM by classification
        $stemQuery = CommunityStemProgram::query();
        if ($selectedYear) $stemQuery->whereYear('start_date', $selectedYear);
        if ($selectedFaculty) {
            $facultyObj = Faculty::find($selectedFaculty);
            $fCode = $facultyObj ? $facultyObj->faculty_code : null;
            if ($fCode) $stemQuery->where('faculty', 'LIKE', "%{$fCode}%");
        }
        $stemByClassification = $stemQuery->selectRaw('classification, COUNT(*) as count')
            ->groupBy('classification')
            ->get();

        // TRL Products by status
        $trlQuery = TrlProduct::query();
        if ($selectedYear) $trlQuery->where('product_year', $selectedYear);
        if ($selectedFaculty) {
            $facultyObj = Faculty::find($selectedFaculty);
            $fCode = $facultyObj ? $facultyObj->faculty_code : null;
            if ($fCode) $trlQuery->where('faculty', 'LIKE', "%{$fCode}%");
        }
        $productsByStatus = $trlQuery->selectRaw('COALESCE(NULLIF(status, ""), "Unassigned") as status, COUNT(*) as count')
            ->groupBy('status')
            ->get();

        // KPI Achievement by Year
        $kpiByYear = KpiYear::selectRaw('target_year as year, AVG(achievement_percentage) as achievement')
            ->groupBy('target_year')
            ->orderBy('target_year')
            ->get()
            ->map(fn($i) => ['year' => $i->year, 'achievement' => round($i->achievement, 1)]);

        return response()->json([
            'awardsByLevel' => $awardsByLevel,
            'ipByType' => $ipByType,
            'stemByClassification' => $stemByClassification,
            'productsByStatus' => $productsByStatus,
            'kpiByYear' => $kpiByYear,
            'lastUpdated' => now()->toISOString()
        ]);
    }

    public function switchWorkspace($workspace)
    {
        $validWorkspaces = ['superadmin', 'awards', 'kpi', 'projects', 'events', 'ip', 'community', 'products'];

        if (!in_array($workspace, $validWorkspaces)) {
            return redirect()->back()->with('error', 'Invalid workspace');
        }

        session(['workspace' => $workspace]);

        return match ($workspace) {
            'superadmin' => redirect()->route('superadmin.dashboard'),
            'awards' => redirect()->route('admin.dashboard'),
            'kpi' => redirect()->route('admin.kpi.index'),
            'projects' => redirect()->route('admin.projects.index'),
            'events' => redirect()->route('admin.events'),
            'ip' => redirect()->route('admin.ip.dashboard'),
            'community' => redirect()->route('admin.stem.dashboard'),
            'products' => redirect()->route('admin.products.index'),
            default => redirect()->route('superadmin.dashboard'),
        };
    }
}
