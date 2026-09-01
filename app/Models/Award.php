<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

use App\Models\Competition;

class Award extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'staff_id',
        'event_id',
        'competition_id',
        'award_name',
        'award_level',
        'award_type',
        'award_date',
        'amount',
        'evidence_document',
        'evidence_link',
    ];

    protected $casts = [
        'award_date' => 'date',
    ];

    /**
     * Get the formatted award name with level badge.
     */
    public function getFormattedAwardNameAttribute()
    {
        $levelColors = [
            'Gold' => 'warning',
            'Silver' => 'secondary',
            'Bronze' => 'info',
            'First Place' => 'success',
            'Second Place' => 'primary',
            'Third Place' => 'dark'
        ];

        $color = $levelColors[$this->award_level] ?? 'light';
        $badge = "<span class='badge badge-{$color}'>{$this->award_level}</span>";

        return "{$this->award_name} {$badge}";
    }

    /**
     * Get the award level color for UI display.
     */
    public function getLevelColorAttribute()
    {
        $colors = [
            'Gold' => '#FFD700',
            'Silver' => '#C0C0C0',
            'Bronze' => '#CD7F32',
            'First Place' => '#28a745',
            'Second Place' => '#007bff',
            'Third Place' => '#343a40',
            'Excellence' => '#17a2b8',
            'Merit' => '#6c757d',
            'Honorable Mention' => '#ffc107',
            'Achievement' => '#e83e8c'
        ];

        return $colors[$this->award_level] ?? '#6c757d';
    }

    /**
     * Resolve evidence URL from stored document or existing link.
     */
    public function getEvidenceUrlAttribute()
    {
        if ($this->evidence_document && Storage::disk('public')->exists($this->evidence_document)) {
            return Storage::url($this->evidence_document);
        }

        $originalLink = $this->getOriginal('evidence_link');
        if ($originalLink) {
            return $originalLink;
        }

        return null;
    }

    /**
     * Get the evidence link with proper formatting.
     */
    public function getFormattedEvidenceLinkAttribute()
    {
        if (!$this->evidence_url) {
            return '<span class="text-muted">No evidence provided</span>';
        }

        $domain = parse_url($this->evidence_url, PHP_URL_HOST) ?? '';
        $icon = 'fa-link';

        if (strpos($domain, 'google') !== false) {
            $icon = 'fa-google';
        } elseif (strpos($domain, 'dropbox') !== false) {
            $icon = 'fa-dropbox';
        } elseif (strpos($domain, 'onedrive') !== false) {
            $icon = 'fa-cloud';
        } elseif (strpos($domain, 'youtube') !== false) {
            $icon = 'fa-youtube';
        }

        return "<a href='{$this->evidence_url}' target='_blank' class='btn btn-sm btn-outline-primary'>
                <i class='fab {$icon}'></i> View Evidence
               </a>";
    }

    /**
     * Get the award age in human readable format.
     */
    public function getAgeAttribute()
    {
        return $this->award_date ? Carbon::parse($this->award_date)->diffForHumans() : null;
    }

    /**
     * Get the award status (recent, old, etc.).
     */
    public function getStatusAttribute()
    {
        $daysSince = $this->award_date ? Carbon::parse($this->award_date)->diffInDays(now()) : PHP_INT_MAX;

        if ($daysSince <= 30) {
            return ['status' => 'Recent', 'color' => 'success'];
        } elseif ($daysSince <= 90) {
            return ['status' => 'Current', 'color' => 'info'];
        } elseif ($daysSince <= 365) {
            return ['status' => 'This Year', 'color' => 'primary'];
        } else {
            return ['status' => 'Archive', 'color' => 'secondary'];
        }
    }

    /**
     * Get the award display summary for lists.
     */
    public function getDisplaySummaryAttribute()
    {
        return [
            'id' => $this->id,
            'award_name' => $this->award_name,
            'award_level' => $this->award_level,
            'level_color' => $this->level_color,
            'staff_name' => $this->staff->staff_name ?? 'Unknown Staff',
            'faculty_name' => $this->staff->faculty->faculty_name ?? 'Unknown Faculty',
            'project_title' => $this->project->project_title ?? 'Unknown Project',
            'event_name' => $this->event->event_name ?? 'Unknown Event',
            'award_date' => $this->award_date ? Carbon::parse($this->award_date)->format('M d, Y') : null,
            'age' => $this->age,
            'status' => $this->status,
            'has_evidence' => !empty($this->evidence_url),
        ];
    }

    /**
     * Get the award details for detailed view.
     */
    public function getDetailedInfoAttribute()
    {
        return [
            'basic_info' => [
                'award_name' => $this->award_name,
                'award_level' => $this->award_level,
                'award_type' => $this->award_type,
                'award_date' => $this->award_date ? Carbon::parse($this->award_date)->format('F d, Y') : null,
                'formatted_date' => $this->award_date ? Carbon::parse($this->award_date)->format('Y-m-d') : null,
                'age' => $this->age,
                'level_color' => $this->level_color,
                'status' => $this->status,
                'evidence_link' => $this->formatted_evidence_link,
            ],
            'staff_info' => [
                'id' => $this->staff->id ?? null,
                'name' => $this->staff->staff_name ?? 'Unknown',
                'staff_id' => $this->staff->staff_id ?? 'N/A',
                'faculty' => $this->staff->faculty->faculty_name ?? 'Unknown',
                'faculty_id' => $this->staff->faculty->id ?? null,
                'total_awards' => $this->staff->awards_count ?? 0,
            ],
            'project_info' => [
                'id' => $this->project->id ?? null,
                'title' => $this->project->project_title ?? 'Unknown',
                'code' => $this->project->project_id ?? 'N/A',
                'total_awards' => $this->project->awards_count ?? 0,
            ],
            'event_info' => [
                'id' => $this->event->id ?? null,
                'name' => $this->event->event_name ?? 'Unknown',
                'location' => $this->event->exhibition_place ?? 'Unknown',
                'level' => $this->event->exhibition_level ?? 'Unknown',
                'start_date' => $this->event->start_date?->format('M d, Y') ?? 'Unknown',
                'end_date' => $this->event->end_date?->format('M d, Y') ?? 'Unknown',
                'total_awards' => $this->event->awards_count ?? 0,
            ],
        ];
    }

    /**
     * Get the staff that received the award.
     */
    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }

    /**
     * Get the project for the award.
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Get the event (competition) where the award was given.
     */
    public function event()
    {
        return $this->belongsTo(Competition::class, 'competition_id');
    }

    public function getEventIdAttribute()
    {
        return $this->competition_id;
    }

    public function setEventIdAttribute($value)
    {
        $this->attributes['competition_id'] = $value;
    }

    /**
     * Get the faculty through staff.
     */
    public function faculty()
    {
        return $this->hasOneThrough(Faculty::class, Staff::class, 'id', 'id', 'staff_id', 'faculty_id');
    }

    /**
     * Scope a query to filter by award level.
     */
    public function scopeByLevel($query, $level)
    {
        return $query->where('award_level', $level);
    }

    /**
     * Scope a query to filter by award type.
     */
    public function scopeByType($query, $type)
    {
        return $query->where('award_type', $type);
    }

    /**
     * Scope a query to filter by year.
     */
    public function scopeByYear($query, $year)
    {
        return $query->whereYear('award_date', $year);
    }

    /**
     * Scope a query to filter by faculty.
     */
    public function scopeByFaculty($query, $facultyId)
    {
        return $query->whereHas('staff', function ($q) use ($facultyId) {
            $q->where('faculty_id', $facultyId);
        });
    }

    /**
     * Scope a query to filter by staff.
     */
    public function scopeByStaff($query, $staffId)
    {
        return $query->where('staff_id', $staffId);
    }

    /**
     * Scope a query to filter by project.
     */
    public function scopeByProject($query, $projectId)
    {
        return $query->where('project_id', $projectId);
    }

    /**
     * Scope a query to filter by event.
     */
    public function scopeByEvent($query, $eventId)
    {
        return $query->where('competition_id', $eventId);
    }

    /**
     * Scope a query to include relationships.
     */
    public function scopeWithRelations($query)
    {
        return $query->with(['staff.faculty', 'project', 'event']);
    }

    /**
     * Get awards with all relationships.
     */
    public static function withAllRelations()
    {
        return self::with(['staff.faculty', 'project', 'event']);
    }

    /**
     * Get recent awards.
     */
    public static function recent($limit = 10)
    {
        return self::withAllRelations()
            ->orderBy('award_date', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get award statistics by level.
     */
    public static function getLevelStats()
    {
        return self::select('award_level', DB::raw('count(*) as count'))
            ->groupBy('award_level')
            ->orderBy('count', 'desc')
            ->get();
    }

    /**
     * Get award statistics by type.
     */
    public static function getTypeStats()
    {
        return self::select('award_type', DB::raw('count(*) as count'))
            ->groupBy('award_type')
            ->orderBy('count', 'desc')
            ->get();
    }

    /**
     * Get monthly award statistics.
     */
    public static function getMonthlyStats($year = null)
    {
        $year = $year ?: date('Y');

        return self::whereYear('award_date', $year)
            ->select(DB::raw('MONTH(award_date) as month'), DB::raw('count(*) as count'))
            ->groupBy(DB::raw('MONTH(award_date)'))
            ->orderBy('month')
            ->get();
    }

    /**
     * Get faculty performance statistics.
     */
    public static function getFacultyPerformance()
    {
        return self::with('staff.faculty')
            ->get()
            ->groupBy('staff.faculty.id')
            ->map(function ($awards) {
                $faculty = $awards->first()->staff->faculty;
                return [
                    'faculty' => $faculty,
                    'total_awards' => $awards->count(),
                    'gold_awards' => $awards->where('award_level', 'Gold')->count(),
                    'silver_awards' => $awards->where('award_level', 'Silver')->count(),
                    'bronze_awards' => $awards->where('award_level', 'Bronze')->count(),
                ];
            })
            ->sortByDesc('total_awards')
            ->values();
    }

    /**
     * Get staff performance statistics.
     */
    public static function getStaffPerformance($limit = 20)
    {
        return self::with('staff.faculty')
            ->get()
            ->groupBy('staff.id')
            ->map(function ($awards) {
                $staff = $awards->first()->staff;
                return [
                    'staff' => $staff,
                    'total_awards' => $awards->count(),
                    'gold_awards' => $awards->where('award_level', 'Gold')->count(),
                    'last_award_date' => $awards->max('award_date'),
                ];
            })
            ->sortByDesc('total_awards')
            ->take($limit)
            ->values();
    }

    /**
     * Check if award is gold level.
     */
    public function getIsGoldAttribute()
    {
        return $this->award_level === 'Gold';
    }

    /**
     * Check if award is silver level.
     */
    public function getIsSilverAttribute()
    {
        return $this->award_level === 'Silver';
    }

    /**
     * Check if award is bronze level.
     */
    public function getIsBronzeAttribute()
    {
        return $this->award_level === 'Bronze';
    }

    /**
     * Get award level priority for sorting.
     */
    public function getLevelPriorityAttribute()
    {
        $priorities = [
            'Gold' => 4,
            'Silver' => 3,
            'Bronze' => 2,
            'First Place' => 3,
            'Second Place' => 2,
            'Third Place' => 1,
            'Honorable Mention' => 1,
        ];

        return $priorities[$this->award_level] ?? 0;
    }

    /**
     * Get formatted award date.
     */
    public function getFormattedDateAttribute()
    {
        return $this->award_date ? Carbon::parse($this->award_date)->format('M d, Y') : null;
    }
}
