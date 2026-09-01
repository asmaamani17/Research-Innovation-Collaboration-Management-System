<?php

namespace App\Http\Controllers;

use App\Models\Award;
use App\Models\Product;
use App\Models\Project;
use App\Models\Staff;
use App\Models\KpiStrategy;
use App\Models\KpiYear;
use App\Models\Competition;
use Illuminate\Http\Request;

class SuperAdminController extends Controller
{
    public function dashboard()
    {
        // Set workspace to superadmin when accessing this dashboard
        session(['workspace' => 'superadmin']);

        // Get summary statistics
        $totalAwards = Award::count();
        $totalProducts = Product::count();
        $totalProjects = Project::count();
        $totalStaff = Staff::count();
        $totalCompetitions = Competition::count();

        // Get KPI strategies count
        $totalKpiStrategies = KpiStrategy::count();

        // Get awards by level
        $awardsByLevel = Award::selectRaw('award_level, COUNT(*) as count')
            ->groupBy('award_level')
            ->get();

        // Get awards by year
        $awardsByYear = Award::selectRaw('YEAR(award_date) as year, COUNT(*) as count')
            ->whereNotNull('award_date')
            ->groupBy('year')
            ->orderBy('year')
            ->get();

        // Get products by status
        $productsByStatus = Product::selectRaw('development_status, COUNT(*) as count')
            ->groupBy('development_status')
            ->get();

        // Get products by category
        $productsByCategory = Product::selectRaw('product_category, COUNT(*) as count')
            ->groupBy('product_category')
            ->get();

        // Get KPI achievement by year
        $kpiByYear = KpiYear::selectRaw('target_year, AVG(achievement_percentage) as avg_achievement')
            ->groupBy('target_year')
            ->orderBy('target_year')
            ->get();

        // Get staff by faculty
        $staffByFaculty = Staff::with('faculty')
            ->selectRaw('faculty_id, COUNT(*) as count')
            ->groupBy('faculty_id')
            ->get();

        // Get recent awards
        $recentAwards = Award::with(['event', 'staff'])
            ->orderBy('award_date', 'desc')
            ->limit(5)
            ->get();

        // Get recent projects
        $recentProjects = Project::orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('super_admin.dashboard', compact(
            'totalAwards',
            'totalProducts',
            'totalProjects',
            'totalStaff',
            'totalCompetitions',
            'totalKpiStrategies',
            'awardsByLevel',
            'awardsByYear',
            'productsByStatus',
            'productsByCategory',
            'kpiByYear',
            'staffByFaculty',
            'recentAwards',
            'recentProjects'
        ));
    }

    public function getDashboardData()
    {
        // Get awards by level
        $awardsByLevel = Award::selectRaw('award_level, COUNT(*) as count')
            ->groupBy('award_level')
            ->get()
            ->map(function ($item) {
                return [
                    'level' => $item->award_level,
                    'count' => $item->count
                ];
            });

        // Get awards by year
        $awardsByYear = Award::selectRaw('YEAR(award_date) as year, COUNT(*) as count')
            ->whereNotNull('award_date')
            ->groupBy('year')
            ->orderBy('year')
            ->get()
            ->map(function ($item) {
                return [
                    'year' => $item->year,
                    'count' => $item->count
                ];
            });

        // Get products by status
        $productsByStatus = Product::selectRaw('development_status, COUNT(*) as count')
            ->groupBy('development_status')
            ->get()
            ->map(function ($item) {
                return [
                    'status' => $item->development_status,
                    'count' => $item->count
                ];
            });

        // Get products by category
        $productsByCategory = Product::selectRaw('product_category, COUNT(*) as count')
            ->groupBy('product_category')
            ->get()
            ->map(function ($item) {
                return [
                    'category' => $item->product_category,
                    'count' => $item->count
                ];
            });

        // Get KPI achievement by year
        $kpiByYear = KpiYear::selectRaw('target_year, AVG(achievement_percentage) as avg_achievement')
            ->groupBy('target_year')
            ->orderBy('target_year')
            ->get()
            ->map(function ($item) {
                return [
                    'year' => $item->target_year,
                    'achievement' => round($item->avg_achievement, 2)
                ];
            });

        // Get staff by faculty
        $staffByFaculty = Staff::with('faculty')
            ->selectRaw('faculty_id, COUNT(*) as count')
            ->groupBy('faculty_id')
            ->get()
            ->map(function ($item) {
                return [
                    'faculty' => $item->faculty ? $item->faculty->faculty_name : 'Unknown',
                    'count' => $item->count
                ];
            });

        return response()->json([
            'awardsByLevel' => $awardsByLevel,
            'awardsByYear' => $awardsByYear,
            'productsByStatus' => $productsByStatus,
            'productsByCategory' => $productsByCategory,
            'kpiByYear' => $kpiByYear,
            'staffByFaculty' => $staffByFaculty,
            'lastUpdated' => now()->toISOString()
        ]);
    }

    public function switchWorkspace($workspace)
    {
        $validWorkspaces = ['superadmin', 'awards', 'kpi', 'projects', 'events', 'ip'];

        if (!in_array($workspace, $validWorkspaces)) {
            return redirect()->back()->with('error', 'Invalid workspace');
        }

        session(['workspace' => $workspace]);

        // Redirect to appropriate route based on workspace
        return match($workspace) {
            'superadmin' => redirect()->route('superadmin.dashboard'),
            'awards' => redirect()->route('admin.dashboard'),
            'kpi' => redirect()->route('admin.kpi.index'),
            'projects' => redirect()->route('admin.projects.index'),
            'events' => redirect()->route('admin.events'),
            'ip' => redirect()->route('admin.ip.dashboard'),
            default => redirect()->route('superadmin.dashboard'),
        };
    }
}
