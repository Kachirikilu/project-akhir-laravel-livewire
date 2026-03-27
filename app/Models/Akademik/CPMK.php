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
        return $this->belongsToMany(Rps::class, 'rps_pivot_cpmk', 'cpmk_id', 'rps_id');
    }

    public function subCpmks(): BelongsToMany
    {
        return $this->belongsToMany(SubCpmk::class, 'cpmk_pivot_scpmk', 'cpmk_id', 'scpmk_id')
                    ->withPivot('sort_order')
                    ->orderBy('pivot_sort_order');
    }

    public function cpls(): BelongsToMany
    {
        return $this->belongsToMany(Cpl::class, 'cpmk_pivot_cpl', 'cpl_id', 'cpmk_id');
    }
}