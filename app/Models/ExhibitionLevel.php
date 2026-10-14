<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExhibitionLevel extends Model
{
    protected $table = 'exhibition_levels';
    protected $primaryKey = 'ExhibitionLevelID';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'ExhibitionLevelID',
        'Description',
    ];

    /**
     * Get the competitions for this exhibition level.
     */
    public function competitions()
    {
        return $this->hasMany(Competition::class, 'exhibition_level_id', 'ExhibitionLevelID');
    }
}
