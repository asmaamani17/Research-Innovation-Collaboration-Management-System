<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KpiRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'strategy_id',
        'kpi_code',
        'initiative',
        'performance_indicator',
        'action_plan',
    ];

    protected $casts = [
        'performance_indicator' => 'array',
        'action_plan' => 'array',
    ];

    public function strategy()
    {
        return $this->belongsTo(KpiStrategy::class, 'strategy_id');
    }

    public function kpiYears()
    {
        return $this->hasMany(KpiYear::class, 'kpi_id');
    }

    public function phases()
    {
        return $this->hasManyThrough(KpiPhase::class, KpiYear::class, 'kpi_id', 'kpi_year_id');
    }
}
