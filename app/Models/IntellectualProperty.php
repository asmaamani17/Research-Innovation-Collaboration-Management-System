<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IntellectualProperty extends Model
{
    use HasFactory;

    protected $table = 'intellectual_properties';

    protected $fillable = [
        'ip_number',
        'title',
        'type',
        'status',
        'filing_date',
        'grant_date',
        'expiry_date',
        'country',
        'link_to_evidence',
        'remarks',
        'project_id',
    ];

    protected $casts = [
        'filing_date' => 'date',
        'grant_date' => 'date',
        'expiry_date' => 'date',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function staff()
    {
        return $this->belongsToMany(Staff::class, 'ip_staff', 'ip_id', 'staff_id');
    }
}
