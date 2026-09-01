<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Faculty extends Model
{
    use HasFactory;

    protected $fillable = [
        'faculty_code',
        'faculty_name',
    ];

    /**
     * Get the staff members for the faculty.
     */
    public function staff()
    {
        return $this->hasMany(Staff::class);
    }

    /**
     * Get the awards for the faculty through staff.
     */
    public function awards()
    {
        return $this->hasManyThrough(Award::class, Staff::class);
    }

    /**
     * Get the total number of awards for this faculty.
     */
    public function getTotalAwardsAttribute()
    {
        return $this->awards()->count();
    }

    /**
     * Get the total number of staff in this faculty.
     */
    public function getTotalStaffAttribute()
    {
        return $this->staff()->count();
    }

    /**
     * Scope a query to search faculties.
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('faculty_name', 'like', "%{$search}%")
              ->orWhere('faculty_code', 'like', "%{$search}%");
        });
    }

    /**
     * Get faculty with staff and awards count.
     */
    public static function withStats()
    {
        return self::withCount(['staff', 'awards']);
    }
}
