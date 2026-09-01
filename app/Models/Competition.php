<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Competition extends Model
{
    use HasFactory;

    protected $table = 'competitions';

    protected $fillable = [
        'event_name',
        'organizer',
        'exhibition_place',
        'exhibition_level',
        'start_date',
        'end_date',
    ];

    protected $appends = [
        'event_location',
        'national_level',
        'conferring_body',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function getEventLocationAttribute()
    {
        return $this->attributes['exhibition_place'] ?? null;
    }

    public function setEventLocationAttribute($value)
    {
        $this->attributes['exhibition_place'] = $value;
    }

    public function getNationalLevelAttribute()
    {
        return $this->attributes['exhibition_level'] ?? null;
    }

    public function setNationalLevelAttribute($value)
    {
        $this->attributes['exhibition_level'] = $value;
    }

    public function getConferringBodyAttribute()
    {
        return $this->attributes['organizer'] ?? null;
    }

    public function setConferringBodyAttribute($value)
    {
        $this->attributes['organizer'] = $value;
    }

    /**
     * Get the awards for the event.
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
        return $this->hasManyThrough(Staff::class, Award::class, 'competition_id', 'id', 'id', 'staff_id');
    }

    /**
     * Get projects through awards.
     */
    public function projects()
    {
        return $this->hasManyThrough(Project::class, Award::class, 'competition_id', 'id', 'id', 'project_id');
    }

    /**
     * Get the total number of awards for this event.
     */
    public function getTotalAwardsAttribute()
    {
        return $this->awards()->count();
    }

    /**
     * Get the number of unique staff participants.
     */
    public function getUniqueParticipantsAttribute()
    {
        return $this->staff()->distinct()->count();
    }

    /**
     * Get the number of unique projects.
     */
    public function getUniqueProjectsAttribute()
    {
        return $this->projects()->distinct()->count();
    }

    /**
     * Get award statistics for this event.
     */
    public function getAwardStatsAttribute()
    {
        return $this->awards()
            ->select('award_level', DB::raw('count(*) as count'))
            ->groupBy('award_level')
            ->orderBy('count', 'desc')
            ->get();
    }

    /**
     * Get award type statistics for this event.
     */
    public function getAwardTypeStatsAttribute()
    {
        return $this->awards()
            ->select('award_type', DB::raw('count(*) as count'))
            ->groupBy('award_type')
            ->orderBy('count', 'desc')
            ->get();
    }

    /**
     * Scope a query to search events.
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('event_name', 'like', "%{$search}%")
                ->orWhere('organizer', 'like', "%{$search}%")
                ->orWhere('exhibition_place', 'like', "%{$search}%")
                ->orWhere('exhibition_level', 'like', "%{$search}%");
        });
    }

    /**
     * Scope a query to filter by level.
     */
    public function scopeByLevel($query, $level)
    {
        return $query->where('exhibition_level', $level);
    }

    /**
     * Scope a query to filter by year.
     */
    public function scopeByYear($query, $year)
    {
        return $query->whereYear('start_date', $year);
    }

    /**
     * Scope a query to include events with awards.
     */
    public function scopeWithAwards($query)
    {
        return $query->withCount(['awards', 'staff as unique_participants']);
    }

    /**
     * Get events ordered by awards count.
     */
    public static function mostPopular($limit = 10)
    {
        return self::withCount(['awards', 'staff as unique_participants'])
            ->orderBy('awards_count', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Check if event is upcoming.
     */
    public function getIsUpcomingAttribute()
    {
        return $this->start_date > now();
    }

    /**
     * Check if event is ongoing.
     */
    public function getIsOngoingAttribute()
    {
        return $this->start_date <= now() && $this->end_date >= now();
    }

    /**
     * Check if event has ended.
     */
    public function getIsEndedAttribute()
    {
        return $this->end_date < now();
    }

    /**
     * Get event status.
     */
    public function getStatusAttribute()
    {
        if ($this->is_upcoming) {
            return 'Upcoming';
        } elseif ($this->is_ongoing) {
            return 'Ongoing';
        } else {
            return 'Ended';
        }
    }

    /**
     * Get formatted date range.
     */
    public function getDateRangeAttribute()
    {
        if ($this->start_date->format('Y-m-d') === $this->end_date->format('Y-m-d')) {
            return $this->start_date->format('M d, Y');
        }

        return $this->start_date->format('M d') . ' - ' . $this->end_date->format('M d, Y');
    }
}
