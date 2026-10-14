<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommunityStemBeneficiary extends Model
{
    protected $table = 'community_stem_beneficiaries';

    protected $fillable = [
        'source_beneficiary_id',
        'community_stem_program_id',
        'name',
    ];

    public function program()
    {
        return $this->belongsTo(CommunityStemProgram::class, 'community_stem_program_id');
    }
}
