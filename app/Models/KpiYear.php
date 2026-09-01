<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KpiYear extends Model
{
    use HasFactory;

    protected $fillable = [
        'kpi_id',
        'target_year',
        'target_value',
        'achievement_percentage',
        'achievement_information',
    ];

    protected $casts = [
        'achievement_percentage' => 'decimal:2',
    ];

    public function kpiRecord()
    {
        return $this->belongsTo(KpiRecord::class, 'kpi_id');
    }

    public function phases()
    {
        return $this->hasMany(KpiPhase::class, 'kpi_year_id');
    }

    public function scopeByYear($query, $year)
    {
        return $query->where('target_year', $year);
    }
}
