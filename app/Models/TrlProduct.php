<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TrlProduct extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'uuid',
        'trl_level',
        'product',
        'researcher',
        'faculty',
        'category',
        'status',
        'action',
        'abstract',
        'product_year',
        'source_sheet',
        'source_no',
    ];

    protected $casts = [
        'trl_level' => 'integer',
        'product_year' => 'integer',
    ];
}
