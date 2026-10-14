<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IntellectualProperty extends Model
{
    use HasFactory;

    protected $table = 'ip_records';

    protected $fillable = [
        'ip_number',
        'title',
        'type',
        'status',
        'nama_produk',
        'no_pemfailan',
        'jenis_ipr',
        'filing_date',
        'tarikh_difailkan',
        'grant_date',
        'expiry_date',
        'tarikh_luput',
        'country',
        'link_to_evidence',
        'remarks',
        'project_id',
        'category',
        'bil',
        'nama_produk_baru',
        'tahun',
        'tahun_aktiviti',
        'fakulti',
        'coe',
        'jawatan',
        'no_staf',
        'gelaran',
        'nama_penyelidik',
        'penyelidik_bersama',
        'geran',
        'status_produk',
        'ejen',
        'pewartaan',
        'kos',
        'abstrak',
        'kategori',
        'tarikh_notis',
        'tarikh_pewartaan',
        'tarikh_renewal_terakhir',
        'tarikh_renewal_seterusnya',
        'kos_renewal',
    ];

    protected $casts = [
        'filing_date' => 'date',
        'tarikh_difailkan' => 'date',
        'grant_date' => 'date',
        'expiry_date' => 'date',
        'tarikh_luput' => 'date',
        'tarikh_notis' => 'date',
        'tarikh_pewartaan' => 'date',
        'tarikh_renewal_terakhir' => 'date',
        'tarikh_renewal_seterusnya' => 'date',
        'tahun' => 'integer',
        'tahun_aktiviti' => 'integer',
        'kos' => 'decimal:2',
        'kos_renewal' => 'decimal:2',
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
