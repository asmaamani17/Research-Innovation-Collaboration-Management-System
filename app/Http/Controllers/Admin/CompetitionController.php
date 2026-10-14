<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Competition;
use App\Models\Award;
use App\Models\Staff;
use App\Models\Project;

class CompetitionController extends Controller
{
    /**
     * Display a listing of events.
     */
    public function index(Request $request)
    {
        $query = Competition::withCount(['awards', 'staff as unique_participants']);

        // Search functionality using model scope
        if ($request->has('search') && $request->search) {
            $query->search($request->search);
        }

        // Filter by level using model scope
        if ($request->has('level') && $request->level) {
            $query->byLevel($request->level);
        }

        // Filter by year using model scope
        if ($request->has('year') && $request->year) {
            $query->byYear($request->year);
        }

        $events = $query->orderBy('start_date', 'desc')->paginate(10);

        // Get available years for filter dropdown
        $availableYears = Competition::query()
            ->whereNotNull('start_date')
            ->selectRaw('DISTINCT YEAR(start_date) as year')
            ->orderByDesc('year')
            ->pluck('year');

        // If no years found, add current year as default
        if ($availableYears->isEmpty()) {
            $availableYears = collect([date('Y')]);
        }

        return view('admin.awards.events.index', compact('events', 'availableYears'));
    }

    /**
     * Show the form for creating a new event.
     */
    public function create()
    {
        return view('admin.awards.events.create');
    }

    /**
     * Store a newly created event.
     */
    public function store(Request $request)
    {
        $request->validate([
            'event_name' => 'required|string|max:200',
            'organizer' => 'required|string|max:200',
            'exhibition_place' => 'required|string|max:200',
            'exhibition_level' => 'required|string|max:50|in:International,Regional,National,University',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        Competition::create([
            'event_name' => strtoupper($request->event_name),
            'organizer' => strtoupper($request->organizer),
            'exhibition_place' => strtoupper($request->exhibition_place),
            'exhibition_level' => $request->exhibition_level,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
        ]);

        return redirect()->route('admin.events.index')
            ->with('success', 'Event added successfully!');
    }

    /**
     * Update the specified event.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'event_name' => 'required|string|max:200',
            'organizer' => 'required|string|max:200',
            'exhibition_place' => 'required|string|max:200',
            'exhibition_level' => 'required|string|max:50|in:International,Regional,National,University',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $event = Competition::findOrFail($id);
        $event->update([
            'event_name' => strtoupper($request->event_name),
            'organizer' => strtoupper($request->organizer),
            'exhibition_place' => strtoupper($request->exhibition_place),
            'exhibition_level' => $request->exhibition_level,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
        ]);

        return redirect()->route('admin.events.index')
            ->with('success', 'Event updated successfully!');
    }

    /**
     * Remove the specified event.
     */
    public function destroy($id)
    {
        $event = Competition::findOrFail($id);

        // Check if event has awards using model relationship
        if ($event->awards()->exists()) {
            return redirect()->route('admin.events.index')
                ->with('error', 'Cannot delete event with existing awards!');
        }

        $event->delete();

        return redirect()->route('admin.events.index')
            ->with('success', 'Event deleted successfully!');
    }

    /**
     * Get event details with awards and staff.
     */
    public function show($id)
    {
        $competition = Competition::with([
            'awards' => function ($query) {
                $query->with(['staff.faculty', 'project', 'exhibitionResult'])->orderBy('created_at', 'desc');
            },
            'staff' => function ($query) {
                $query->with('faculty')->distinct();
            },
            'projects' => function ($query) {
                $query->distinct();
            }
        ])->findOrFail($id);

        // Calculate event statistics (case-insensitive matching)
        // Check award_level, award_name, and exhibition_result.Description fields
        // Support both English (Gold, Silver, Bronze) and Malay (Emas, Perak, Gangsa)
        $stats = [
            'total_awards' => $competition->awards->count(),
            'unique_participants' => $competition->staff->count(),
            'unique_projects' => $competition->projects->count(),
            'gold_awards' => $competition->awards->filter(function($a) {
                $level = strtoupper($a->award_level ?? '');
                $name = strtoupper($a->award_name ?? '');
                $result = strtoupper($a->exhibitionResult->Description ?? '');
                return $level === 'GOLD' || str_contains($name, 'GOLD') || str_contains($result, 'EMAS');
            })->count(),
            'silver_awards' => $competition->awards->filter(function($a) {
                $level = strtoupper($a->award_level ?? '');
                $name = strtoupper($a->award_name ?? '');
                $result = strtoupper($a->exhibitionResult->Description ?? '');
                return $level === 'SILVER' || str_contains($name, 'SILVER') || str_contains($result, 'PERAK');
            })->count(),
            'bronze_awards' => $competition->awards->filter(function($a) {
                $level = strtoupper($a->award_level ?? '');
                $name = strtoupper($a->award_name ?? '');
                $result = strtoupper($a->exhibitionResult->Description ?? '');
                return $level === 'BRONZE' || str_contains($name, 'BRONZE') || str_contains($result, 'GANGSA');
            })->count(),
            'platinum_awards' => $competition->awards->filter(function($a) {
                $level = strtoupper($a->award_level ?? '');
                $name = strtoupper($a->award_name ?? '');
                $result = strtoupper($a->exhibitionResult->Description ?? '');
                return $level === 'PLATINUM' || str_contains($name, 'PLATINUM') || str_contains($result, 'PLATINUM');
            })->count(),
            'special_awards' => $competition->awards->filter(function($a) {
                $level = strtoupper($a->award_level ?? '');
                $name = strtoupper($a->award_name ?? '');
                $result = strtoupper($a->exhibitionResult->Description ?? '');
                return $level === 'SPECIAL' || str_contains($name, 'SPECIAL') || str_contains($result, 'ANUGERAH KHAS');
            })->count(),
            'faculties_involved' => $competition->staff->pluck('faculty.id')->unique()->count(),
        ];

        // Get award statistics using model method
        $award_stats = $competition->award_stats;

        // Get award type statistics using model method
        $award_type_stats = $competition->award_type_stats;

        // Group awards by level
        $awards_by_level = $competition->awards->groupBy('award_level');

        // Group staff by faculty
        $staff_by_faculty = $competition->staff->groupBy('faculty.faculty_name');

        return view('admin.awards.events.show', compact('competition', 'stats', 'award_stats', 'award_type_stats', 'awards_by_level', 'staff_by_faculty'));
    }

    /**
     * Get awards for a specific event.
     */
    public function awards($id)
    {
        $event = Competition::findOrFail($id);
        $awards = $event->awards()
            ->with(['staff.faculty', 'project'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.awards.events.show', compact('event', 'awards'))->with('tab', 'awards');
    }

    /**
     * Get staff participants for a specific event.
     */
    public function staff($id)
    {
        $event = Competition::findOrFail($id);
        $staff = $event->staff()
            ->with('faculty')
            ->withCount([
                'awards' => function ($query) use ($id) {
                    $query->where('competition_id', $id);
                }
            ])
            ->orderBy('staff_name')
            ->get();

        return view('admin.awards.events.show', compact('event', 'staff'))->with('tab', 'staff');
    }

    /**
     * Get projects for a specific event.
     */
    public function projects($id)
    {
        $event = Competition::findOrFail($id);
        $projects = $event->projects()
            ->withCount([
                'awards' => function ($query) use ($id) {
                    $query->where('competition_id', $id);
                }
            ])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.awards.events.show', compact('event', 'projects'))->with('tab', 'projects');
    }

    /**
     * Get event statistics for API.
     */
    public function stats($id)
    {
        $event = Competition::withCount(['awards', 'staff as unique_participants', 'projects as unique_projects'])->findOrFail($id);

        $award_levels = $event->award_stats;
        $award_types = $event->award_type_stats;

        return response()->json([
            'event' => $event,
            'award_levels' => $award_levels,
            'award_types' => $award_types,
            'stats' => [
                'total_awards' => $event->awards_count,
                'unique_participants' => $event->unique_participants,
                'unique_projects' => $event->unique_projects,
                'awards_per_participant' => $event->unique_participants > 0 ? round($event->awards_count / $event->unique_participants, 2) : 0,
            ]
        ]);
    }

    /**
     * Get upcoming events.
     */
    public function upcoming()
    {
        $events = Competition::where('start_date', '>', now())
            ->withCount('awards')
            ->orderBy('start_date', 'asc')
            ->limit(5)
            ->get();

        return response()->json($events);
    }

    /**
     * Get ongoing events.
     */
    public function ongoing()
    {
        $events = Competition::where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->withCount('awards')
            ->orderBy('end_date', 'asc')
            ->get();

        return response()->json($events);
    }

    /**
     * Get events by year.
     */
    public function byYear($year)
    {
        $events = Competition::byYear($year)
            ->withCount(['awards', 'staff as unique_participants'])
            ->orderBy('start_date', 'desc')
            ->get();

        return view('admin.awards.events.index', compact('events'))->with('year', $year);
    }

    /**
     * Get events by level.
     */
    public function byLevel($level)
    {
        $events = Competition::byLevel($level)
            ->withCount(['awards', 'staff as unique_participants'])
            ->orderBy('start_date', 'desc')
            ->paginate(15);

        return view('admin.awards.events.index', compact('events'))->with('level', $level);
    }

    /**
     * Show the form for editing the specified event.
     */
    public function edit($id)
    {
        $event = Competition::findOrFail($id);
        return view('admin.awards.events.edit', compact('event'));
    }
}
