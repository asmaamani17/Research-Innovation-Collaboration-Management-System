<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\Staff;
use App\Models\Award;
use App\Models\Faculty;

class ProjectController extends Controller
{
    /**
     * Display a listing of projects.
     */
    public function index(Request $request)
    {
        $query = Project::withCount('awards');

        // Search functionality using model scope
        if ($request->has('search') && $request->search) {
            $query->search($request->search);
        }

        $projects = $query->orderBy('created_at', 'desc')->paginate(10);

        // Load staff relationships for each project
        foreach ($projects as $project) {
            $project->staff_members = $project->staff()->with('faculty')->get();
            $project->unique_staff_count = $project->staff()->distinct()->count();
        }

        return view('admin.awards.projects.index', compact('projects'));
    }

    /**
     * Show the form for creating a new project.
     */
    public function create()
    {
        return view('admin.awards.projects.create');
    }

    /**
     * Show the form for editing the specified project.
     */
    public function edit($id)
    {
        $project = Project::findOrFail($id);
        return view('admin.awards.projects.edit', compact('project'));
    }

    /**
     * Store a newly created project.
     */
    public function store(Request $request)
    {
        $request->validate([
            'project_id' => 'required|integer|unique:projects,project_id',
            'project_title' => 'required|string',
            'grant_no' => 'nullable|string|max:150',
        ]);

        Project::create([
            'project_id' => $request->project_id,
            'project_title' => $request->project_title,
            'grant_no' => $request->grant_no,
        ]);

        return redirect()->route('admin.projects.index')
            ->with('success', 'Project added successfully!');
    }

    /**
     * Update the specified project.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'project_id' => 'required|integer|unique:projects,project_id,' . $id,
            'project_title' => 'required|string',
            'grant_no' => 'nullable|string|max:150',
        ]);

        $project = Project::findOrFail($id);
        $project->update([
            'project_id' => $request->project_id,
            'project_title' => $request->project_title,
            'grant_no' => $request->grant_no,
        ]);

        return redirect()->route('admin.projects.index')
            ->with('success', 'Project updated successfully!');
    }

    /**
     * Remove the specified project.
     */
    public function destroy($id)
    {
        $project = Project::findOrFail($id);

        // Check if project has awards using model relationship
        if ($project->awards()->exists()) {
            return redirect()->route('admin.projects.index')
                ->with('error', 'Cannot delete project with existing awards!');
        }

        $project->delete();

        return redirect()->route('admin.projects.index')
            ->with('success', 'Project deleted successfully!');
    }

    /**
     * Get project details with awards and staff.
     */
    public function show($id)
    {
        $project = Project::with([
            'awards' => function ($query) {
                $query->with(['staff.faculty', 'event'])->orderBy('created_at', 'desc');
            },
            'staff' => function ($query) {
                $query->with('faculty')->withCount('awards');
            }
        ])->findOrFail($id);

        // Calculate project statistics
        $stats = [
            'total_awards' => $project->awards->count(),
            'unique_staff' => $project->staff->count(),
            'gold_awards' => $project->awards->where('award_level', 'Gold')->count(),
            'silver_awards' => $project->awards->where('award_level', 'Silver')->count(),
            'bronze_awards' => $project->awards->where('award_level', 'Bronze')->count(),
            'faculties_involved' => $project->staff->pluck('faculty.id')->unique()->count(),
        ];

        // Get staff by faculty
        $staff_by_faculty = $project->staff->groupBy('faculty.faculty_name');

        // Get award timeline
        $award_timeline = $project->awards->map(function ($award) {
            return [
                'date' => $award->award_date ? \Carbon\Carbon::parse($award->award_date)->format('M Y') : null,
                'award_name' => $award->award_name,
                'level' => $award->award_level,
                'staff_name' => $award->staff->staff_name,
                'event_name' => $award->event->event_name,
            ];
        });

        return view('admin.awards.projects.show', compact('project', 'stats', 'staff_by_faculty', 'award_timeline'));
    }

    /**
     * Get staff members for a specific project.
     */
    public function staff($id)
    {
        $project = Project::findOrFail($id);
        $staff = $project->staff()
            ->with('faculty')
            ->withCount([
                'awards' => function ($query) use ($id) {
                    $query->where('project_id', $id);
                }
            ])
            ->orderBy('staff_name')
            ->get();

        return view('admin.awards.projects.staff', compact('project', 'staff'));
    }

    /**
     * Add staff member to project (through award creation).
     */
    public function addStaff(Request $request, $id)
    {
        $request->validate([
            'staff_id' => 'required|exists:staff,id',
            'event_id' => 'required|exists:competitions,id',
            'award_name' => 'required|string|max:50',
            'award_level' => 'required|string|max:50',
            'award_type' => 'required|string|max:50',
            'award_date' => 'required|date',
            'evidence_link' => 'nullable|url',
        ]);

        // Check if this exact award already exists
        $existingAward = Award::where([
            'project_id' => $id,
            'staff_id' => $request->staff_id,
            'competition_id' => $request->event_id,
            'award_name' => $request->award_name,
        ])->first();

        if ($existingAward) {
            return redirect()->back()
                ->with('error', 'This award already exists for this project and staff member!');
        }

        Award::create([
            'project_id' => $id,
            'staff_id' => $request->staff_id,
            'competition_id' => $request->event_id,
            'award_name' => strtoupper($request->award_name),
            'award_level' => $request->award_level,
            'award_type' => $request->award_type,
            'award_date' => $request->award_date,
            'evidence_link' => $request->evidence_link,
        ]);

        return redirect()->route('admin.projects.show', $id)
            ->with('success', 'Staff member added to project successfully!');
    }

    /**
     * Remove staff member from project (by deleting related awards).
     */
    public function removeStaff($projectId, $staffId)
    {
        $project = Project::findOrFail($projectId);

        // Delete all awards for this staff member on this project
        $deletedAwards = Award::where([
            'project_id' => $projectId,
            'staff_id' => $staffId,
        ])->delete();

        if ($deletedAwards === 0) {
            return redirect()->back()
                ->with('error', 'No awards found for this staff member on this project!');
        }

        return redirect()->route('admin.projects.show', $projectId)
            ->with('success', "Staff member removed from project ({$deletedAwards} awards deleted)!");
    }

    /**
     * Get project statistics for API.
     */
    public function stats($id)
    {
        $project = Project::withCount(['awards', 'staff'])->findOrFail($id);

        $staff_by_faculty = $project->staff()
            ->join('faculties', 'staff.faculty_id', '=', 'faculties.id')
            ->select('faculties.faculty_name', DB::raw('count(*) as count'))
            ->groupBy('faculties.id', 'faculties.faculty_name')
            ->orderBy('count', 'desc')
            ->get();

        $award_levels = $project->awards()
            ->select('award_level', DB::raw('count(*) as count'))
            ->groupBy('award_level')
            ->orderBy('count', 'desc')
            ->get();

        return response()->json([
            'project' => $project,
            'staff_by_faculty' => $staff_by_faculty,
            'award_levels' => $award_levels,
            'stats' => [
                'total_awards' => $project->awards_count,
                'unique_staff' => $project->staff_count,
                'faculties_involved' => $staff_by_faculty->count(),
                'awards_per_staff' => $project->staff_count > 0 ? round($project->awards_count / $project->staff_count, 2) : 0,
            ]
        ]);
    }

    /**
     * Get projects by staff member.
     */
    public function byStaff($staffId)
    {
        $staff = Staff::findOrFail($staffId);
        $projects = $staff->projects()
            ->withCount('awards')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.awards.staff-projects', compact('staff', 'projects'));
    }

    /**
     * Get projects by faculty.
     */
    public function byFaculty($facultyId)
    {
        $faculty = Faculty::findOrFail($facultyId);
        $projects = Project::whereHas('staff', function ($query) use ($facultyId) {
            $query->where('faculty_id', $facultyId);
        })->withCount(['awards', 'staff'])->get();

        return view('admin.awards.faculty-projects', compact('faculty', 'projects'));
    }
}
