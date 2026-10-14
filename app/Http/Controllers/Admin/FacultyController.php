<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Faculty;
use App\Models\Staff;
use App\Models\Award;
use Illuminate\Support\Facades\DB;

class FacultyController extends Controller
{
    /**
     * Display a listing of faculties.
     */
    public function index(Request $request)
    {
        $query = Faculty::withCount(['staff', 'awards']);

        // Search functionality using model scope
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Use appends to preserve query string parameters on pagination links
        $faculties = $query->orderBy('faculty_name')->paginate(10)->appends($request->query());

        return view('admin.awards.faculty', compact('faculties'));
    }

    /**
     * Store a newly created faculty.
     */
    public function store(Request $request)
    {
        $request->validate([
            'faculty_code' => 'required|string|max:20|unique:faculties,faculty_code',
            'faculty_name' => 'required|string|max:100',
        ]);

        Faculty::create([
            'faculty_code' => strtoupper(trim($request->faculty_code)),
            'faculty_name' => strtoupper(trim($request->faculty_name)),
        ]);

        return redirect()->route('admin.faculty.index')
            ->with('success', 'Faculty added successfully!');
    }

    /**
     * Update the specified faculty.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'faculty_code' => 'required|string|max:20|unique:faculties,faculty_code,'.$id,
            'faculty_name' => 'required|string|max:100',
        ]);

        $faculty = Faculty::findOrFail($id);
        $faculty->update([
            'faculty_code' => strtoupper(trim($request->faculty_code)),
            'faculty_name' => strtoupper(trim($request->faculty_name)),
        ]);

        return redirect()->route('admin.faculty.index')
            ->with('success', 'Faculty updated successfully!');
    }

    /**
     * Remove the specified faculty.
     */
    public function destroy($id)
    {
        $faculty = Faculty::findOrFail($id);
        
        // Check if faculty has staff using model relationship
        if ($faculty->staff()->exists()) {
            return redirect()->route('admin.faculty.index')
                ->with('error', 'Cannot delete faculty with existing staff members!');
        }

        $faculty->delete();

        return redirect()->route('admin.faculty.index')
            ->with('success', 'Faculty deleted successfully!');
    }

    /**
     * Get faculty details with staff and awards.
     */
    public function show($id)
    {
        $faculty = Faculty::with([
            'staff' => function($query) {
                $query->withCount('awards')->orderBy('staff_name');
            },
            'awards' => function($query) {
                $query->with(['staff', 'project', 'competition', 'exhibitionResult'])->orderBy('created_at', 'desc');
            }
        ])->findOrFail($id);

        // Calculate faculty statistics (case-insensitive matching)
        // Check award_level, award_name, and exhibition_result.Description fields
        // Support both English (Gold, Silver, Bronze) and Malay (Emas, Perak, Gangsa)
        $stats = [
            'total_staff' => $faculty->staff->count(),
            'total_awards' => $faculty->awards->count(),
            'gold_awards' => $faculty->awards->filter(function($a) {
                $level = strtoupper($a->award_level ?? '');
                $name = strtoupper($a->award_name ?? '');
                $result = strtoupper($a->exhibitionResult->Description ?? '');
                return $level === 'GOLD' || str_contains($name, 'GOLD') || str_contains($result, 'EMAS');
            })->count(),
            'silver_awards' => $faculty->awards->filter(function($a) {
                $level = strtoupper($a->award_level ?? '');
                $name = strtoupper($a->award_name ?? '');
                $result = strtoupper($a->exhibitionResult->Description ?? '');
                return $level === 'SILVER' || str_contains($name, 'SILVER') || str_contains($result, 'PERAK');
            })->count(),
            'bronze_awards' => $faculty->awards->filter(function($a) {
                $level = strtoupper($a->award_level ?? '');
                $name = strtoupper($a->award_name ?? '');
                $result = strtoupper($a->exhibitionResult->Description ?? '');
                return $level === 'BRONZE' || str_contains($name, 'BRONZE') || str_contains($result, 'GANGSA');
            })->count(),
            'platinum_awards' => $faculty->awards->filter(function($a) {
                $level = strtoupper($a->award_level ?? '');
                $name = strtoupper($a->award_name ?? '');
                $result = strtoupper($a->exhibitionResult->Description ?? '');
                return $level === 'PLATINUM' || str_contains($name, 'PLATINUM') || str_contains($result, 'PLATINUM');
            })->count(),
            'special_awards' => $faculty->awards->filter(function($a) {
                $level = strtoupper($a->award_level ?? '');
                $name = strtoupper($a->award_name ?? '');
                $result = strtoupper($a->exhibitionResult->Description ?? '');
                return $level === 'SPECIAL' || str_contains($name, 'SPECIAL') || str_contains($result, 'ANUGERAH KHAS');
            })->count(),
            'staff_with_awards' => $faculty->staff->where('awards_count', '>', 0)->count(),
        ];

        // Get top performing staff in this faculty
        $top_staff = $faculty->staff->sortByDesc('awards_count')->take(5);

        return view('admin.awards.faculty-show', compact('faculty', 'stats', 'top_staff'));
    }

    /**
     * Get faculty statistics for API.
     */
    public function stats($id)
    {
        $faculty = Faculty::withCount(['staff', 'awards'])->findOrFail($id);
        
        $award_levels = $faculty->awards()
            ->select('award_level', DB::raw('count(*) as count'))
            ->groupBy('award_level')
            ->orderBy('count', 'desc')
            ->get();

        return response()->json([
            'faculty' => $faculty,
            'award_levels' => $award_levels,
            'stats' => [
                'total_staff' => $faculty->staff_count,
                'total_awards' => $faculty->awards_count,
                'awards_per_staff' => $faculty->staff_count > 0 ? round($faculty->awards_count / $faculty->staff_count, 2) : 0,
            ]
        ]);
    }

    /**
     * Get staff list for a specific faculty.
     */
    public function staff($id)
    {
        $faculty = Faculty::findOrFail($id);
        $staff = $faculty->staff()->withCount('awards')->orderBy('staff_name')->get();

        return view('admin.awards.faculty-staff', compact('faculty', 'staff'));
    }

    /**
     * Get awards for a specific faculty.
     */
    public function awards($id)
    {
        $faculty = Faculty::findOrFail($id);
        $awards = $faculty->awards()
            ->with(['staff', 'project', 'competition', 'exhibitionResult'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.awards.faculty-awards', compact('faculty', 'awards'));
    }
}
