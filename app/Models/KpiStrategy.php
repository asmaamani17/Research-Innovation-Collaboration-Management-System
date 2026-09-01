<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KpiStrategy extends Model
{
    use HasFactory;

    protected $fillable = [
        'strategy_code',
        'strategy_name',
        'display_order',
    ];

    public function kpiRecords()
    {
        return $this->hasMany(KpiRecord::class, 'strategy_id');
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order');
    }
}
