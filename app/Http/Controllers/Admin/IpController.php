<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\IntellectualProperty;
use App\Models\Staff;
use App\Models\Project;
use App\Models\Faculty;

class IpController extends Controller
{
    /**
     * Display a listing of intellectual properties.
     */
    public function index(Request $request)
    {
        $query = IntellectualProperty::with(['project', 'staff']);

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('ip_number', 'like', "%{$search}%")
                    ->orWhere('type', 'like', "%{$search}%")
                    ->orWhere('status', 'like', "%{$search}%")
                    ->orWhere('country', 'like', "%{$search}%")
                    ->orWhereHas('staff', function ($subQuery) use ($search) {
                        $subQuery->where('staff_name', 'like', "%{$search}%")
                            ->orWhere('staff_id', 'like', "%{$search}%");
                    })
                    ->orWhereHas('project', function ($subQuery) use ($search) {
                        $subQuery->where('project_title', 'like', "%{$search}%")
                            ->orWhere('project_id', 'like', "%{$search}%");
                    });
            });
        }

        // Filter by type
        if ($request->has('type') && $request->type) {
            $query->where('type', $request->type);
        }

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Filter by year
        if ($request->has('year') && $request->year) {
            $query->whereYear('filing_date', $request->year);
        }

        // Filter by faculty
        if ($request->has('faculty_id') && $request->faculty_id) {
            $query->whereHas('staff', function ($q) use ($request) {
                $q->where('faculty_id', $request->faculty_id);
            });
        }

        // Filter by staff
        if ($request->has('staff_id') && $request->staff_id) {
            $query->whereHas('staff', function ($q) use ($request) {
                $q->where('staff_id', $request->staff_id);
            });
        }

        // Filter by project
        if ($request->has('project_id') && $request->project_id) {
            $query->where('project_id', $request->project_id);
        }

        $ips = $query->orderBy('filing_date', 'desc')->paginate(15);

        // Get filter data
        $staff = Staff::orderBy('staff_name')->get();
        $projects = Project::orderBy('project_title')->get();
        $faculties = Faculty::orderBy('faculty_name')->get();
        $availableYears = IntellectualProperty::selectRaw('YEAR(filing_date) as year')
            ->whereNotNull('filing_date')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');
        $ipTypes = IntellectualProperty::select('type')
            ->whereNotNull('type')
            ->distinct()
            ->orderBy('type')
            ->pluck('type');
        $ipStatuses = IntellectualProperty::select('status')
            ->whereNotNull('status')
            ->distinct()
            ->orderBy('status')
            ->pluck('status');

        return view('admin.ip.index', compact('ips', 'staff', 'projects', 'faculties', 'availableYears', 'ipTypes', 'ipStatuses'));
    }

    /**
     * Show the form for creating a new intellectual property.
     */
    public function create()
    {
        $staff = Staff::orderBy('staff_name')->get();
        $projects = Project::orderBy('project_title')->get();

        return view('admin.ip.create', compact('staff', 'projects'));
    }

    /**
     * Store a newly created intellectual property.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'ip_number' => 'nullable|string|max:100',
            'title' => 'required|string',
            'type' => 'required|string|in:PATENT,UTILITY_INNOVATION,COPYRIGHT,TRADEMARK,INDUSTRIAL_DESIGN',
            'status' => 'nullable|string|max:20',
            'filing_date' => 'nullable|date',
            'grant_date' => 'nullable|date|after_or_equal:filing_date',
            'expiry_date' => 'nullable|date|after_or_equal:grant_date',
            'country' => 'nullable|string|max:100',
            'link_to_evidence' => 'nullable|url|max:255',
            'remarks' => 'nullable|string',
            'project_id' => 'nullable|exists:projects,id',
            'staff_ids' => 'required|array',
            'staff_ids.*' => 'exists:staff,id',
        ]);

        $ip = IntellectualProperty::create($validated);

        // Attach staff
        if ($request->has('staff_ids')) {
            $ip->staff()->attach($request->staff_ids);
        }

        return redirect()->route('admin.ip.index')
            ->with('success', 'Intellectual Property created successfully.');
    }

    /**
     * Display the specified intellectual property.
     */
    public function show($id)
    {
        $ip = IntellectualProperty::with(['project', 'staff'])->findOrFail($id);

        return view('admin.ip.show', compact('ip'));
    }

    /**
     * Show the form for editing the specified intellectual property.
     */
    public function edit($id)
    {
        $ip = IntellectualProperty::with('staff')->findOrFail($id);
        $staff = Staff::orderBy('staff_name')->get();
        $projects = Project::orderBy('project_title')->get();

        return view('admin.ip.edit', compact('ip', 'staff', 'projects'));
    }

    /**
     * Update the specified intellectual property.
     */
    public function update(Request $request, $id)
    {
        $ip = IntellectualProperty::findOrFail($id);

        $validated = $request->validate([
            'ip_number' => 'nullable|string|max:100',
            'title' => 'required|string',
            'type' => 'required|string|in:PATENT,UTILITY_INNOVATION,COPYRIGHT,TRADEMARK,INDUSTRIAL_DESIGN',
            'status' => 'nullable|string|max:20',
            'filing_date' => 'nullable|date',
            'grant_date' => 'nullable|date|after_or_equal:filing_date',
            'expiry_date' => 'nullable|date|after_or_equal:grant_date',
            'country' => 'nullable|string|max:100',
            'link_to_evidence' => 'nullable|url|max:255',
            'remarks' => 'nullable|string',
            'project_id' => 'nullable|exists:projects,id',
            'staff_ids' => 'required|array',
            'staff_ids.*' => 'exists:staff,id',
        ]);

        $ip->update($validated);

        // Sync staff
        if ($request->has('staff_ids')) {
            $ip->staff()->sync($request->staff_ids);
        }

        return redirect()->route('admin.ip.index')
            ->with('success', 'Intellectual Property updated successfully.');
    }

    /**
     * Remove the specified intellectual property.
     */
    public function destroy($id)
    {
        $ip = IntellectualProperty::findOrFail($id);
        $ip->delete();

        return redirect()->route('admin.ip.index')
            ->with('success', 'Intellectual Property deleted successfully.');
    }

    /**
     * Display the IP dashboard.
     */
    public function dashboard()
    {
        $stats = [
            'total_ips' => IntellectualProperty::count(),
            'total_staff' => Staff::count(),
            'total_projects' => Project::count(),
            'this_year_ips' => IntellectualProperty::whereYear('filing_date', date('Y'))->count(),
        ];

        $faculties = Faculty::withCount(['staff' => function ($q) {
            $q->whereHas('intellectualProperties');
        }])->orderBy('staff_count', 'desc')->limit(10)->get();

        $ipTypes = IntellectualProperty::selectRaw('type, COUNT(*) as count')
            ->groupBy('type')
            ->orderBy('count', 'desc')
            ->get();

        $ipStatuses = IntellectualProperty::selectRaw('status, COUNT(*) as count')
            ->whereNotNull('status')
            ->groupBy('status')
            ->orderBy('count', 'desc')
            ->get();

        $monthlyTrend = IntellectualProperty::selectRaw('YEAR(filing_date) as year, MONTH(filing_date) as month, COUNT(*) as count')
            ->where('filing_date', '>=', now()->subMonths(12))
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        return view('admin.ip.dashboard', compact('stats', 'faculties', 'ipTypes', 'ipStatuses', 'monthlyTrend'));
    }

    /**
     * Display the IP import page.
     */
    public function import()
    {
        return view('admin.ip.import.index');
    }

    /**
     * Display the IP reports page.
     */
    public function reports()
    {
        $summary = [
            'total_ips' => IntellectualProperty::count(),
            'granted_ips' => IntellectualProperty::where('status', 'GRANTED')->count(),
            'pending_ips' => IntellectualProperty::where('status', 'PENDING')->count(),
            'this_year_ips' => IntellectualProperty::whereYear('filing_date', date('Y'))->count(),
        ];

        return view('admin.ip.reports.index', compact('summary'));
    }

    /**
     * Export IPs to CSV.
     */
    public function export(Request $request)
    {
        $query = IntellectualProperty::with(['project', 'staff']);

        // Apply filters if present
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('ip_number', 'like', "%{$search}%")
                    ->orWhere('type', 'like', "%{$search}%")
                    ->orWhere('status', 'like', "%{$search}%")
                    ->orWhere('country', 'like', "%{$search}%")
                    ->orWhereHas('staff', function ($subQuery) use ($search) {
                        $subQuery->where('staff_name', 'like', "%{$search}%")
                            ->orWhere('staff_id', 'like', "%{$search}%");
                    })
                    ->orWhereHas('project', function ($subQuery) use ($search) {
                        $subQuery->where('project_title', 'like', "%{$search}%")
                            ->orWhere('project_id', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->has('type') && $request->type) {
            $query->where('type', $request->type);
        }

        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        if ($request->has('year') && $request->year) {
            $query->whereYear('filing_date', $request->year);
        }

        $ips = $query->orderBy('filing_date', 'desc')->get();

        $filename = 'intellectual_properties_' . date('Y-m-d') . '.csv';

        $callback = function () use ($ips) {
            $file = fopen('php://output', 'w');

            // CSV headers
            fputcsv($file, [
                'IP Number',
                'Title',
                'Type',
                'Status',
                'Filing Date',
                'Grant Date',
                'Expiry Date',
                'Country',
                'Staff Names',
                'Staff IDs',
                'Project Title',
                'Project Code',
                'Evidence URL',
                'Remarks'
            ]);

            // CSV data
            foreach ($ips as $ip) {
                fputcsv($file, [
                    $ip->ip_number ?? '',
                    $ip->title,
                    $ip->type,
                    $ip->status ?? '',
                    optional($ip->filing_date)->format('Y-m-d'),
                    optional($ip->grant_date)->format('Y-m-d'),
                    optional($ip->expiry_date)->format('Y-m-d'),
                    $ip->country ?? '',
                    $ip->staff->pluck('staff_name')->join(', '),
                    $ip->staff->pluck('staff_id')->join(', '),
                    $ip->project->project_title ?? '',
                    $ip->project->project_id ?? '',
                    $ip->link_to_evidence ?? '',
                    $ip->remarks ?? ''
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename={$filename}",
        ]);
    }

    /**
     * Get live chart data for IP dashboard.
     */
    public function chartData(Request $request)
    {
        $type = $request->get('type', 'all');
        $data = [];

        switch ($type) {
            case 'types':
                $data = IntellectualProperty::selectRaw('type, COUNT(*) as count')
                    ->whereNotNull('type')
                    ->groupBy('type')
                    ->orderBy('count', 'desc')
                    ->get();
                break;
            case 'statuses':
                $data = IntellectualProperty::selectRaw('status, COUNT(*) as count')
                    ->whereNotNull('status')
                    ->groupBy('status')
                    ->orderBy('count', 'desc')
                    ->get();
                break;
            case 'monthly':
                $data = IntellectualProperty::selectRaw('YEAR(filing_date) as year, MONTH(filing_date) as month, COUNT(*) as count')
                    ->where('filing_date', '>=', now()->subMonths(12))
                    ->groupBy('year', 'month')
                    ->orderBy('year')
                    ->orderBy('month')
                    ->get();
                break;
            case 'faculties':
                $data = Faculty::select('faculties.id', 'faculties.faculty_code', 'faculties.faculty_name')
                    ->leftJoin('staff', 'faculties.id', '=', 'staff.faculty_id')
                    ->leftJoin('ip_staff', 'staff.id', '=', 'ip_staff.staff_id')
                    ->leftJoin('intellectual_properties', 'ip_staff.ip_id', '=', 'intellectual_properties.id')
                    ->selectRaw('faculties.id, faculties.faculty_code, faculties.faculty_name,
                        COUNT(intellectual_properties.id) as count')
                    ->groupBy('faculties.id', 'faculties.faculty_code', 'faculties.faculty_name')
                    ->orderBy('count', 'desc')
                    ->limit(10)
                    ->get();
                break;
            default:
                $data = [
                    'types' => IntellectualProperty::selectRaw('type, COUNT(*) as count')
                        ->groupBy('type')
                        ->orderBy('count', 'desc')
                        ->get(),
                    'statuses' => IntellectualProperty::selectRaw('status, COUNT(*) as count')
                        ->whereNotNull('status')
                        ->groupBy('status')
                        ->orderBy('count', 'desc')
                        ->get(),
                ];
        }

        return response()->json($data);
    }
}
