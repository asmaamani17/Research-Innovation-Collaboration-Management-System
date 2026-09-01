<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KpiPhase extends Model
{
    use HasFactory;

    protected $fillable = [
        'kpi_year_id',
        'phase',
        'achievement',
    ];

    protected $casts = [
        'phase' => 'string',
    ];

    public function kpiYear()
    {
        return $this->belongsTo(KpiYear::class, 'kpi_year_id');
    }

    public function scopeByPhase($query, $phase)
    {
        return $query->where('phase', $phase);
    }
}
