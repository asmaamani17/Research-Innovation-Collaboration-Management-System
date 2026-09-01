<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'product_name',
        'product_type',
        'product_category',
        'description',
        'development_status',
        'ip_status',
        'commercialization_status',
        'evidence_document',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
