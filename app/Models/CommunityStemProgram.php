<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommunityStemProgram extends Model
{
    protected $table = 'community_stem_programs';

    protected $fillable = [
        'source_program_id',
        'project_id',
        'staff_id',
        'faculty',
        'program',
        'kategori',
        'classification',
        'status',
        'start_date',
        'end_date',
        'submitted_at',
        'past_research_id',
        'community_industry_name',
        'organiser',
        'sponsorship_value',
        'brief_benefit',
        'star_rating',
        'remarks',
        'link_evidence',
        'evidence_file',
        'link_borang_a',
        'link_kelulusan_kertas_kerja',
        'link_borang_b',
        'link_borang_c',
        'link_borang_d',
        'combined_evidence_folder',
        'identity_key',
        'sheet_sync_id',
        'sheet_source',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'submitted_at' => 'datetime',
        'star_rating' => 'integer',
    ];

    public function getFacultySingkatAttribute(): string
    {
        return $this->faculty ?? '';
    }

    public function beneficiaries()
    {
        return $this->hasMany(CommunityStemBeneficiary::class);
    }
}
