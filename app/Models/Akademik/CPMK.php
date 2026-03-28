<?php

namespace App\Models\Akademik;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Cpmk extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    public function rps(): BelongsToMany
    {
        return $this->belongsToMany(Rps::class, 'rps_pivot_cpmk', 'rps_id', 'cpmk_id')
                    ->withTimestamps();
    }

    public function sub_cpmks(): BelongsToMany
    {
        return $this->belongsToMany(SubCpmk::class, 'cpmk_pivot_scpmk', 'cpmk_id', 'scpmk_id')
                    ->withPivot('sort_order')
                    ->withTimestamps();
    }

    public function cpls(): BelongsToMany
    {
        return $this->belongsToMany(Cpl::class, 'cpmk_pivot_cpl', 'cpmk_id', 'cpl_id')
                    ->withPivot('sort_order');
        ;
    }
}