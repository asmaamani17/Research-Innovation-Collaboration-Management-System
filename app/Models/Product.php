<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $table = 'legacy_products_archive';

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

    public function awards(): HasMany
    {
        return $this->hasMany(Award::class);
    }
}
