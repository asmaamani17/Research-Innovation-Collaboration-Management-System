<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'grant_no',
        'project_title',
    ];

    /**
     * Get the awards for the project.
     */
    public function awards()
    {
        return $this->hasMany(Award::class);
    }

    /**
     * Get staff members through awards.
     */
    public function staff()
    {
        return $this->hasManyThrough(Staff::class, Award::class, 'project_id', 'id', 'id', 'staff_id');
    }

    /**
     * Get events through awards.
     */
    public function events()
    {
        return $this->hasManyThrough(Competition::class, Award::class, 'project_id', 'id', 'id', 'competition_id');
    }

    /**
     * Get the total number of awards for this project.
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
     * Get unique staff members involved in this project.
     */
    public function getUniqueStaffAttribute()
    {
        return $this->staff()->distinct()->get();
    }

    /**
     * Get staff names as array.
     */
    public function getStaffNamesAttribute()
    {
        return $this->staff()->pluck('staff_name')->toArray();
    }

    /**
     * Scope a query to search projects.
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('project_title', 'like', "%{$search}%")
              ->orWhere('project_id', 'like', "%{$search}%");
        });
    }

    /**
     * Scope a query to include projects with awards.
     */
    public function scopeWithAwards($query)
    {
        return $query->withCount('awards');
    }

    /**
     * Get projects ordered by awards count.
     */
    public static function topAwarded($limit = 10)
    {
        return self::withCount('awards')
            ->orderBy('awards_count', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Check if project has awards.
     */
    public function hasAwards()
    {
        return $this->awards()->exists();
    }

    /**
     * Get the highest award level for this project.
     */
    public function getHighestAwardAttribute()
    {
        $awards = $this->awards()->pluck('award_level')->toArray();
        
        $priority = ['Gold' => 4, 'Silver' => 3, 'Bronze' => 2, 'First Place' => 3, 'Second Place' => 2, 'Third Place' => 1];
        
        $highest = null;
        $highestPriority = 0;
        
        foreach ($awards as $award) {
            $priority = $priority[$award] ?? 0;
            if ($priority > $highestPriority) {
                $highestPriority = $priority;
                $highest = $award;
            }
        }
        
        return $highest;
    }
}
