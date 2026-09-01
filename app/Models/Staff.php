<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Staff extends Model
{
    use HasFactory;

    protected $fillable = [
        'staff_id',
        'staff_name',
        'faculty_id',
    ];

    /**
     * Get the faculty that owns the staff.
     */
    public function faculty()
    {
        return $this->belongsTo(Faculty::class);
    }

    /**
     * Get the awards for the staff.
     */
    public function awards()
    {
        return $this->hasMany(Award::class);
    }

    /**
     * Get the intellectual properties for the staff.
     */
    public function intellectualProperties()
    {
        return $this->belongsToMany(IntellectualProperty::class, 'ip_staff', 'staff_id', 'ip_id')->withTimestamps();
    }

    /**
     * Get projects through awards.
     */
    public function projects()
    {
        return $this->hasManyThrough(Project::class, Award::class, 'staff_id', 'id', 'id', 'project_id');
    }

    /**
     * Get events through awards.
     */
    public function events()
    {
        return $this->hasManyThrough(Competition::class, Award::class, 'staff_id', 'id', 'id', 'competition_id');
    }

    /**
     * Get the total number of awards for this staff.
     */
    public function getTotalAwardsAttribute()
    {
        return $this->awards()->count();
    }

    /**
     * Get the number of gold awards.
     */
    public function getGoldAwardsAttribute()
    {
        return $this->awards()->where('award_level', 'Gold')->count();
    }

    /**
     * Get the number of silver awards.
     */
    public function getSilverAwardsAttribute()
    {
        return $this->awards()->where('award_level', 'Silver')->count();
    }

    /**
     * Get the number of bronze awards.
     */
    public function getBronzeAwardsAttribute()
    {
        return $this->awards()->where('award_level', 'Bronze')->count();
    }

    /**
     * Get the most recent award date.
     */
    public function getLastAwardDateAttribute()
    {
        return $this->awards()->max('award_date');
    }

    /**
     * Scope a query to search staff.
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('staff_name', 'like', "%{$search}%")
              ->orWhere('staff_id', 'like', "%{$search}%");
        });
    }

    /**
     * Scope a query to filter by faculty.
     */
    public function scopeByFaculty($query, $facultyId)
    {
        return $query->where('faculty_id', $facultyId);
    }

    /**
     * Scope a query to include staff with awards.
     */
    public function scopeWithAwards($query)
    {
        return $query->withCount('awards')->with('faculty');
    }

    /**
     * Get staff ordered by awards count.
     */
    public static function topPerformers($limit = 10)
    {
        return self::withCount('awards')
            ->with('faculty')
            ->orderBy('awards_count', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get projects with award details for this staff.
     */
    public function projectsWithAwards()
    {
        return $this->projects()
            ->with(['awards' => function($query) {
                $query->where('staff_id', $this->id)->with('event');
            }])
            ->get();
    }

    /**
     * Get unique projects count for this staff.
     */
    public function getUniqueProjectsCountAttribute()
    {
        return $this->projects()->distinct()->count();
    }

    /**
     * Check if staff is involved in a specific project.
     */
    public function isInvolvedInProject($projectId)
    {
        return $this->awards()->where('project_id', $projectId)->exists();
    }
}
