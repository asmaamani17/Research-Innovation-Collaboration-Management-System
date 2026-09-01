<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Staff;
use App\Models\Faculty;
use App\Models\Award;

class StaffController extends Controller
{
    /**
     * Display a listing of staff members.
     */
    public function index(Request $request)
    {
        $query = Staff::with('faculty')->withCount('awards');

        // Search functionality using model scope
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        if ($request->filled('faculty_id')) {
            $query->byFaculty($request->faculty_id);
        }

        $staff = $query->orderBy('staff_name')->paginate(10);
        $staff->appends($request->query());

        // Get faculties for filter dropdown
        $faculties = Faculty::orderBy('faculty_name')->get();

        return view('admin.awards.staff', compact('staff', 'faculties'));
    }

    /**
     * Store a newly created staff member.
     */
    public function store(Request $request)
    {
        $request->validate([
            'staff_id' => 'required|string|max:20|unique:staff,staff_id',
            'staff_name' => 'required|string|max:150',
            'faculty_id' => 'required|exists:faculties,id',
        ]);

        Staff::create([
            'staff_id' => trim($request->staff_id),
            'staff_name' => strtoupper(trim($request->staff_name)),
            'faculty_id' => $request->faculty_id,
        ]);

        return redirect()->route('admin.staff.index')
            ->with('success', 'Staff member added successfully!');
    }

    /**
     * Update the specified staff member.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'staff_name' => 'required|string|max:150',
            'faculty_id' => 'required|exists:faculties,id',
        ]);

        $staff = Staff::findOrFail($id);
        $staff->update([
            'staff_name' => strtoupper(trim($request->staff_name)),
            'faculty_id' => $request->faculty_id,
        ]);

        return redirect()->route('admin.staff.index')
            ->with('success', 'Staff member updated successfully!');
    }

    /**
     * Remove the specified staff member.
     */
    public function destroy($id)
    {
        $staff = Staff::findOrFail($id);
        
        // Check if staff has awards using model relationship
        if ($staff->awards()->exists()) {
            return redirect()->route('admin.staff.index')
                ->with('error', 'Cannot delete staff member with existing awards!');
        }

        $staff->delete();

        return redirect()->route('admin.staff.index')
            ->with('success', 'Staff member deleted successfully!');
    }

    /**
     * Show staff details with awards and faculty.
     */
    public function show($id)
    {
        $staff = Staff::with(['faculty', 'awards.project', 'awards.event'])->findOrFail($id);
        
        // Get award statistics for this staff
        $award_stats = [
            'total' => $staff->awards->count(),
            'gold' => $staff->awards->where('award_level', 'Gold')->count(),
            'silver' => $staff->awards->where('award_level', 'Silver')->count(),
            'bronze' => $staff->awards->where('award_level', 'Bronze')->count(),
        ];

        return view('admin.awards.staff-show', compact('staff', 'award_stats'));
    }

    /**
     * Get staff by faculty for AJAX requests.
     */
    public function getByFaculty($facultyId)
    {
        $staff = Staff::where('faculty_id', $facultyId)
            ->orderBy('staff_name')
            ->get(['id', 'staff_name']);

        return response()->json($staff);
    }
}
