<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExhibitionResult extends Model
{
    protected $table = 'exhibition_results';
    protected $primaryKey = 'ExhibitionResultID';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'ExhibitionResultID',
        'Description',
    ];

    /**
     * Get the awards for this exhibition result.
     */
    public function awards()
    {
        return $this->hasMany(Award::class, 'exhibition_result_id', 'ExhibitionResultID');
    }
}
