<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Award;
use App\Models\Staff;
use App\Models\Project;
use App\Models\Event;
use App\Models\Faculty;
use App\Models\Competition;

class AwardController extends Controller
{
    /**
     * Display a listing of awards.
     */
    public function index(Request $request)
    {
        $query = Award::withRelations();

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('award_name', 'like', "%{$search}%")
                    ->orWhere('award_level', 'like', "%{$search}%")
                    ->orWhere('award_type', 'like', "%{$search}%")
                    ->orWhereHas('staff', function ($subQuery) use ($search) {
                        $subQuery->where('staff_name', 'like', "%{$search}%")
                            ->orWhere('staff_id', 'like', "%{$search}%");
                    })
                    ->orWhereHas('project', function ($subQuery) use ($search) {
                        $subQuery->where('project_title', 'like', "%{$search}%")
                            ->orWhere('project_id', 'like', "%{$search}%");
                    })
                    ->orWhereHas('event', function ($subQuery) use ($search) {
                        $subQuery->where('event_name', 'like', "%{$search}%")
                            ->orWhere('exhibition_place', 'like', "%{$search}%")
                            ->orWhere('exhibition_level', 'like', "%{$search}%");
                    });
            });
        }

        // Filter by award level
        if ($request->has('level') && $request->level) {
            $query->byLevel($request->level);
        }

        // Filter by award type
        if ($request->has('type') && $request->type) {
            $query->byType($request->type);
        }

        // Filter by year
        if ($request->has('year') && $request->year) {
            $query->byYear($request->year);
        }

        // Filter by faculty
        if ($request->has('faculty_id') && $request->faculty_id) {
            $query->byFaculty($request->faculty_id);
        }

        // Filter by staff
        if ($request->has('staff_id') && $request->staff_id) {
            $query->byStaff($request->staff_id);
        }

        // Filter by project
        if ($request->has('project_id') && $request->project_id) {
            $query->byProject($request->project_id);
        }

        // Filter by event
        if ($request->has('event_id') && $request->event_id) {
            $query->byEvent($request->event_id);
        }

        $awards = $query->orderBy('award_date', 'desc')->paginate(15);

        // Get filter data
        $staff = Staff::orderBy('staff_name')->get();
        $projects = Project::orderBy('project_title')->get();
        $events = Competition::orderBy('event_name')->get();
        $faculties = Faculty::orderBy('faculty_name')->get();
        $availableYears = Award::selectRaw('YEAR(award_date) as year')
            ->whereNotNull('award_date')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');
        $awardLevels = Award::select('award_level')
            ->whereNotNull('award_level')
            ->distinct()
            ->orderBy('award_level')
            ->pluck('award_level');
        $awardTypes = Award::select('award_type')
            ->whereNotNull('award_type')
            ->distinct()
            ->orderBy('award_type')
            ->pluck('award_type');

        return view('admin.awards.awards.index', compact(
            'awards',
            'staff',
            'projects',
            'events',
            'faculties',
            'availableYears',
            'awardLevels',
            'awardTypes'
        ));
    }

    /**
     * Show the form for creating a new award.
     */
    public function create()
    {
        $staff = Staff::orderBy('staff_name')->get();
        $projects = Project::orderBy('project_title')->get();
        $events = Competition::orderBy('event_name')->get();

        return view('admin.awards.awards.create', compact('staff', 'projects', 'events'));
    }

    /**
     * Show the form for editing the specified award.
     */
    public function edit($id)
    {
        $award = Award::withRelations()->findOrFail($id);
        $staff = Staff::orderBy('staff_name')->get();
        $projects = Project::orderBy('project_title')->get();
        $events = Competition::orderBy('event_name')->get();

        return view('admin.awards.awards.edit', compact('award', 'staff', 'projects', 'events'));
    }

    /**
     * Store a newly created award.
     */
    public function store(Request $request)
    {
        $request->validate([
            'staff_id' => 'required|exists:staff,id',
            'project_id' => 'required|exists:projects,id',
            'event_id' => 'required|exists:competitions,id',
            'award_name' => 'required|string|in:PLATINUM,SPECIAL,GOLD,SILVER,BRONZE,OTHERS',
            'award_level' => 'required|string|in:INDIVIDUAL,INSTITUTIONAL',
            'award_type' => 'required|string|in:AWARD,RECOGNITION,STEWARDSHIP,EXHIBITION,OTHER RESEARCH AWARDS,CLARIVATE HIGHLY AWARD',
            'event_exhibition_level' => 'required|string|in:National,International',
            'award_date' => 'required|date',
            'evidence_document' => 'nullable|file|max:10240|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png,zip',
        ], [
            'staff_id.required' => 'Please select a staff member.',
            'staff_id.exists' => 'Selected staff member does not exist.',
            'project_id.required' => 'Please select a project.',
            'project_id.exists' => 'Selected project does not exist.',
            'event_id.required' => 'Please select an event.',
            'event_id.exists' => 'Selected event does not exist.',
            'award_name.required' => 'Award name is required.',
            'award_level.required' => 'Award level is required.',
            'award_type.required' => 'Award type is required.',
            'event_exhibition_level.required' => 'Exhibition level is required.',
            'award_date.required' => 'Award date is required.',
            'evidence_document.file' => 'Evidence must be a valid file.',
            'evidence_document.max' => 'Evidence document cannot exceed 10MB.',
            'evidence_document.mimes' => 'Evidence document must be one of: PDF, DOC, DOCX, XLS, XLSX, JPG, PNG, ZIP.',
        ]);

        $event = Event::find($request->event_id);
        if ($event && strtoupper($event->national_level ?? '') !== strtoupper($request->event_national_level)) {
            return redirect()->back()
                ->with('error', 'Selected event is marked as ' . ($event->national_level ?? 'Unknown') . ', not ' . $request->event_national_level . '.')
                ->withInput();
        }

        // Prevent exact duplicate records while allowing multiple awards for the same project/event.
        $existingAward = Award::where([
            'staff_id' => $request->staff_id,
            'project_id' => $request->project_id,
            'competition_id' => $request->event_id,
            'award_name' => strtoupper(trim($request->award_name)),
            'award_level' => strtoupper(trim($request->award_level)),
            'award_type' => strtoupper(trim($request->award_type)),
            'award_date' => $request->award_date,
        ])->first();

        if ($existingAward) {
            return redirect()->back()
                ->with('error', 'A similar award already exists for this staff, project, and event combination. 
                          Award: ' . $existingAward->award_name . ' (' . $existingAward->award_level . ')')
                ->withInput();
        }

        // Create award with additional data processing
        $awardData = [
            'staff_id' => $request->staff_id,
            'project_id' => $request->project_id,
            'competition_id' => $request->event_id,
            'award_name' => strtoupper(trim($request->award_name)),
            'award_level' => strtoupper(trim($request->award_level)),
            'award_type' => strtoupper(trim($request->award_type)),
            'award_date' => $request->award_date,
            'evidence_document' => null,
        ];

        // Handle file upload for evidence
        if ($request->hasFile('evidence_document')) {
            $file = $request->file('evidence_document');
            $filename = 'award_' . time() . '_' . str_replace(' ', '_', $request->award_name) . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('evidence', $filename, 'public');
            $awardData['evidence_document'] = $path;
        }

        $award = Award::create($awardData);

        // Log the award creation for audit
        \Log::info('Award created', [
            'award_id' => $award->id,
            'staff_id' => $request->staff_id,
            'project_id' => $request->project_id,
            'event_id' => $request->event_id,
            'created_by' => auth()->id(),
            'created_at' => now()
        ]);

        return redirect()->route('admin.awards')
            ->with('success', 'Award "' . $award->award_name . '" added successfully!');
    }

    /**
     * Validate additional business rules for awards
     */
    private function validateAwardBusinessRules(Request $request)
    {
        $staff = Staff::find($request->staff_id);
        $project = Project::find($request->project_id);
        $event = Event::find($request->event_id);

        // Rule 1: Staff must be from the same faculty as project participants
        if ($staff && $project) {
            $projectStaff = $project->staff()->pluck('faculty_id')->unique();
            if (!$projectStaff->contains($staff->faculty_id)) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'staff_id' => 'Selected staff must be from the same faculty as project participants.'
                ]);
            }
        }

        // Rule 2: Cannot add more than 3 awards for the same project at the same event
        $existingAwardsForProjectEvent = Award::where([
            'project_id' => $request->project_id,
            'competition_id' => $request->event_id,
        ])->count();

        if ($existingAwardsForProjectEvent >= 3) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'project_id' => 'This project already has maximum awards (3) for this event.'
            ]);
        }

        // Rule 3: Cannot add the same award level for the same staff at the same event within 6 months
        $recentSimilarAward = Award::where([
            'staff_id' => $request->staff_id,
            'competition_id' => $request->event_id,
            'award_level' => $request->award_level,
        ])->where('award_date', '>=', now()->subMonths(6))
            ->first();

        if ($recentSimilarAward) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'award_level' => 'This staff already received a ' . $request->award_level . ' award at this event within the last 6 months.'
            ]);
        }
    }

    /**
     * Calculate award priority based on level
     */
    private function calculateAwardPriority($level)
    {
        $priorities = [
            'Gold' => 1,
            'First Place' => 1,
            'Silver' => 2,
            'Second Place' => 2,
            'Bronze' => 3,
            'Third Place' => 3,
            'Excellence' => 2,
            'Merit' => 3,
            'Honorable Mention' => 4,
            'Achievement' => 3
        ];

        return $priorities[$level] ?? 5;
    }

    /**
     * Calculate award score based on level and type
     */
    private function calculateAwardScore($level, $type)
    {
        $levelScores = [
            'Gold' => 100,
            'First Place' => 100,
            'Silver' => 80,
            'Second Place' => 80,
            'Bronze' => 60,
            'Third Place' => 60,
            'Excellence' => 85,
            'Merit' => 70,
            'Honorable Mention' => 40,
            'Achievement' => 50
        ];

        $typeMultipliers = [
            'Research' => 1.2,
            'Innovation' => 1.1,
            'Competition' => 1.0,
            'Excellence' => 1.0,
            'Achievement' => 0.9,
            'Publication' => 1.1,
            'Presentation' => 0.8,
            'Exhibition' => 0.9
        ];

        $baseScore = $levelScores[$level] ?? 50;
        $multiplier = $typeMultipliers[$type] ?? 1.0;

        return (int) round($baseScore * $multiplier);
    }

    /**
     * Update the specified award.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'staff_id' => 'required|exists:staff,id',
            'project_id' => 'required|exists:projects,id',
            'event_id' => 'required|exists:competitions,id',
            'award_name' => 'required|string|in:PLATINUM,SPECIAL,GOLD,SILVER,BRONZE,OTHERS',
            'award_level' => 'required|string|in:INDIVIDUAL,INSTITUTIONAL',
            'award_type' => 'required|string|in:AWARD,RECOGNITION,STEWARDSHIP,EXHIBITION,OTHER RESEARCH AWARDS,CLARIVATE HIGHLY AWARD',
            'award_date' => 'required|date',
            'evidence_document' => [
                'nullable',
                'file',
                'max:10240',
                'mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png,zip',
            ],
        ]);

        try {
            $award = Award::findOrFail($id);

            $updateData = [
                'staff_id' => $request->staff_id,
                'project_id' => $request->project_id,
                'competition_id' => $request->event_id,
                'award_name' => strtoupper(trim($request->award_name)),
                'award_level' => strtoupper(trim($request->award_level)),
                'award_type' => strtoupper(trim($request->award_type)),
                'award_date' => $request->award_date,
            ];

            // Handle file upload for evidence
            if ($request->hasFile('evidence_document')) {
                if ($award->evidence_document && Storage::disk('public')->exists($award->evidence_document)) {
                    Storage::disk('public')->delete($award->evidence_document);
                }

                $file = $request->file('evidence_document');
                $filename = 'award_' . time() . '_' . str_replace(' ', '_', $request->award_name) . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('evidence', $filename, 'public');
                $updateData['evidence_document'] = $path;
            }

            $award->update($updateData);

            return redirect()->route('admin.awards')
                ->with('success', 'Award updated successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to update award: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified award.
     */
    public function destroy($id)
    {
        $award = Award::findOrFail($id);

        if ($award->evidence_document && Storage::disk('public')->exists($award->evidence_document)) {
            Storage::disk('public')->delete($award->evidence_document);
        }

        $award->delete();

        return redirect()->route('admin.awards')
            ->with('success', 'Award deleted successfully!');
    }

    /**
     * Show award details.
     */
    public function show($id)
    {
        $award = Award::withRelations()->findOrFail($id);

        // Get related awards for context
        $relatedAwards = Award::withRelations()
            ->where('id', '!=', $id)
            ->where(function ($query) use ($award) {
                $query->where('staff_id', $award->staff_id)
                    ->orWhere('project_id', $award->project_id)
                    ->orWhere('competition_id', $award->competition_id);
            })
            ->orderBy('award_date', 'desc')
            ->limit(5)
            ->get();

        return view('admin.awards.awards.show', compact('award', 'relatedAwards'));
    }

    /**
     * Get award statistics.
     */
    public function statistics()
    {
        $stats = [
            'total_awards' => Award::count(),
            'gold_awards' => Award::byLevel('Gold')->count(),
            'silver_awards' => Award::byLevel('Silver')->count(),
            'bronze_awards' => Award::byLevel('Bronze')->count(),
            'this_year' => Award::byYear(date('Y'))->count(),
            'last_year' => Award::byYear(date('Y') - 1)->count(),
        ];

        $levelStats = Award::getLevelStats();
        $typeStats = Award::getTypeStats();
        $monthlyStats = Award::getMonthlyStats();

        return view('admin.awards-statistics', compact('stats', 'levelStats', 'typeStats', 'monthlyStats'));
    }

    /**
     * Get awards by staff member.
     */
    public function byStaff($staffId)
    {
        $staff = Staff::with('faculty')->findOrFail($staffId);
        $awards = Award::byStaff($staffId)
            ->withRelations()
            ->orderBy('award_date', 'desc')
            ->paginate(15);

        // Calculate staff statistics
        $staffStats = [
            'total_awards' => Award::byStaff($staffId)->count(),
            'gold_awards' => Award::byStaff($staffId)->where('award_name', 'GOLD')->count(),
            'silver_awards' => Award::byStaff($staffId)->where('award_name', 'SILVER')->count(),
            'bronze_awards' => Award::byStaff($staffId)->where('award_name', 'BRONZE')->count(),
        ];

        return view('admin.awards-staff', compact('staff', 'awards', 'staffStats'));
    }

    /**
     * Get awards by project.
     */
    public function byProject($projectId)
    {
        $project = Project::findOrFail($projectId);
        $awards = Award::byProject($projectId)
            ->withRelations()
            ->orderBy('award_date', 'desc')
            ->paginate(15);

        // Calculate project statistics
        $projectStats = [
            'total_awards' => Award::byProject($projectId)->count(),
            'unique_staff' => Award::byProject($projectId)->distinct('staff_id')->count('staff_id'),
            'gold_awards' => Award::byProject($projectId)->where('award_name', 'GOLD')->count(),
        ];

        return view('admin.awards-project', compact('project', 'awards', 'projectStats'));
    }

    /**
     * Get awards by event.
     */
    public function byEvent($eventId)
    {
        $event = Event::findOrFail($eventId);
        $awards = Award::byEvent($eventId)
            ->withRelations()
            ->orderBy('award_date', 'desc')
            ->paginate(15);

        // Calculate event statistics
        $eventStats = [
            'total_awards' => Award::byEvent($eventId)->count(),
            'unique_staff' => Award::byEvent($eventId)->distinct('staff_id')->count('staff_id'),
            'unique_projects' => Award::byEvent($eventId)->distinct('project_id')->count('project_id'),
        ];

        return view('admin.awards-event', compact('event', 'awards', 'eventStats'));
    }

    /**
     * Get recent awards for dashboard.
     */
    public function recent()
    {
        $awards = Award::recent(10);
        return response()->json($awards);
    }

    /**
     * Export awards to CSV.
     */
    public function export(Request $request)
    {
        $query = Award::withRelations();

        // Apply filters if provided
        if ($request->has('level') && $request->level) {
            $query->byLevel($request->level);
        }
        if ($request->has('year') && $request->year) {
            $query->byYear($request->year);
        }
        if ($request->has('faculty_id') && $request->faculty_id) {
            $query->byFaculty($request->faculty_id);
        }

        $awards = $query->orderBy('award_date', 'desc')->get();

        $filename = "awards_export_" . date('Y-m-d') . ".csv";

        $callback = function () use ($awards) {
            $file = fopen('php://output', 'w');

            // CSV headers
            fputcsv($file, [
                'Award Name',
                'Level',
                'Type',
                'Date',
                'Staff Name',
                'Staff ID',
                'Faculty',
                'Project Title',
                'Project Code',
                'Event Name',
                'Event Location',
                'Evidence URL'
            ]);

            // CSV data
            foreach ($awards as $award) {
                fputcsv($file, [
                    $award->award_name,
                    $award->award_level,
                    $award->award_type,
                    optional($award->award_date)->format('Y-m-d'),
                    $award->staff->staff_name ?? '',
                    $award->staff->staff_id ?? '',
                    $award->staff->faculty->faculty_name ?? '',
                    $award->project->project_title ?? '',
                    $award->project->project_id ?? '',
                    $award->event->event_name ?? '',
                    $award->event->exhibition_place ?? '',
                    $award->evidence_url ?? ''
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
     * Display awards dashboard with statistics and charts.
     */
    public function dashboard()
    {
        // Get summary statistics
        $stats = [
            'total_awards' => Award::count(),
            'total_staff' => Staff::count(),
            'total_projects' => Project::count(),
            'this_year_awards' => Award::whereYear('award_date', date('Y'))->count(),
        ];

        // Get top faculties by awards
        $top_faculties = Faculty::withCount('awards')
            ->orderBy('awards_count', 'desc')
            ->limit(6)
            ->get();
        $max_faculty_awards = $top_faculties->max('awards_count') ?? 1;

        // Get award level distribution
        $awardLevels = Award::selectRaw('award_name, COUNT(*) as count')
            ->groupBy('award_name')
            ->orderBy('count', 'desc')
            ->get();

        // Get award type distribution
        $awardTypes = Award::selectRaw('award_type, COUNT(*) as count')
            ->whereNotNull('award_type')
            ->groupBy('award_type')
            ->orderBy('count', 'desc')
            ->get();

        // Get monthly awards trend (last 12 months)
        $monthlyTrend = Award::selectRaw('YEAR(award_date) as year, MONTH(award_date) as month, COUNT(*) as count')
            ->where('award_date', '>=', now()->subMonths(12))
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        // Get awards by exhibition level
        $exhibitionLevels = Award::join('competitions', 'awards.competition_id', '=', 'competitions.id')
            ->selectRaw('competitions.exhibition_level, COUNT(*) as count')
            ->groupBy('competitions.exhibition_level')
            ->orderBy('count', 'desc')
            ->get();

        // Add university awards (awards with no competition or university-level competitions)
        $universityAwards = Award::whereNull('competition_id')
            ->orWhereHas('event', function($query) {
                $query->where('exhibition_level', 'University');
            })
            ->count();

        if ($universityAwards > 0) {
            $exhibitionLevels->push((object)[
                'exhibition_level' => 'University',
                'count' => $universityAwards
            ]);
        }

        return view('admin.awards.dashboard', compact(
            'stats',
            'top_faculties',
            'max_faculty_awards',
            'awardLevels',
            'awardTypes',
            'monthlyTrend',
            'exhibitionLevels'
        ));
    }

    /**
     * Display the awards import page.
     */
    public function import()
    {
        return view('admin.awards.import.index');
    }

    /**
     * Display the awards reports page.
     */
    public function reports()
    {
        return view('admin.awards.reports.index');
    }

    /**
     * Get live chart data for dashboard.
     */
    public function chartData(Request $request)
    {
        $type = $request->get('type', 'all');
        $data = [];

        switch ($type) {
            case 'levels':
                $data = Award::selectRaw('award_level, COUNT(*) as count')
                    ->whereNotNull('award_level')
                    ->groupBy('award_level')
                    ->orderBy('count', 'desc')
                    ->get();
                break;
            case 'types':
                $data = Award::selectRaw('award_type, COUNT(*) as count')
                    ->whereNotNull('award_type')
                    ->groupBy('award_type')
                    ->orderBy('count', 'desc')
                    ->get();
                break;
            case 'monthly':
                $data = Award::selectRaw('YEAR(award_date) as year, MONTH(award_date) as month, COUNT(*) as count')
                    ->where('award_date', '>=', now()->subMonths(12))
                    ->groupBy('year', 'month')
                    ->orderBy('year')
                    ->orderBy('month')
                    ->get();
                break;
            case 'faculties':
                $data = Faculty::select('faculties.id', 'faculties.faculty_code', 'faculties.faculty_name')
                    ->leftJoin('staff', 'faculties.id', '=', 'staff.faculty_id')
                    ->leftJoin('awards', 'staff.id', '=', 'awards.staff_id')
                    ->leftJoin('exhibition_results', 'awards.exhibition_result_id', '=', 'exhibition_results.ExhibitionResultID')
                    ->selectRaw('faculties.id, faculties.faculty_code, faculties.faculty_name,
                        COALESCE(exhibition_results.Description, "No Result") as result_description,
                        COUNT(*) as count')
                    ->groupBy('faculties.id', 'faculties.faculty_code', 'faculties.faculty_name', 'exhibition_results.ExhibitionResultID', 'exhibition_results.Description')
                    ->orderBy('faculties.faculty_code')
                    ->orderBy('count', 'desc')
                    ->get();
                break;
            case 'exhibition':
                $data = Award::leftJoin('competitions', 'awards.competition_id', '=', 'competitions.id')
                    ->selectRaw('COALESCE(competitions.exhibition_level, "No Competition") as exhibition_level, COUNT(*) as count')
                    ->groupBy('competitions.exhibition_level')
                    ->orderBy('count', 'desc')
                    ->get();
                break;
            case 'results':
                $data = Award::leftJoin('exhibition_results', 'awards.exhibition_result_id', '=', 'exhibition_results.ExhibitionResultID')
                    ->selectRaw('COALESCE(exhibition_results.Description, "No Result") as description, COUNT(*) as count')
                    ->groupBy('exhibition_results.ExhibitionResultID', 'exhibition_results.Description')
                    ->orderBy('count', 'desc')
                    ->get();
                break;
            default:
                $data = [
                    'levels' => Award::selectRaw('award_name, COUNT(*) as count')
                        ->groupBy('award_name')
                        ->orderBy('count', 'desc')
                        ->get(),
                    'types' => Award::selectRaw('award_type, COUNT(*) as count')
                        ->whereNotNull('award_type')
                        ->groupBy('award_type')
                        ->orderBy('count', 'desc')
                        ->get(),
                    'monthly' => Award::selectRaw('YEAR(award_date) as year, MONTH(award_date) as month, COUNT(*) as count')
                        ->where('award_date', '>=', now()->subMonths(12))
                        ->groupBy('year', 'month')
                        ->orderBy('year')
                        ->orderBy('month')
                        ->get(),
                    'faculties' => Faculty::withCount('awards')
                        ->orderBy('awards_count', 'desc')
                        ->limit(10)
                        ->get(),
                    'exhibition' => Award::join('competitions', 'awards.competition_id', '=', 'competitions.id')
                        ->selectRaw('competitions.exhibition_level, COUNT(*) as count')
                        ->groupBy('competitions.exhibition_level')
                        ->orderBy('count', 'desc')
                        ->get(),
                ];
        }

        return response()->json($data);
    }
}
