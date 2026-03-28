<?php

namespace App\Models\Akademik;

use App\Models\ProgramStudi\Prodi;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Cpl extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    public function prodis(): BelongsToMany
    {
        return $this->belongsToMany(Prodi::class, 'prodi_pivot_cpl', 'prodi_id', 'cpl_id')
                    ->withPivot('sort_order');
    }
    
    public function cpmks(): BelongsToMany
    {
        return $this->belongsToMany(Cpmk::class, 'cpmk_pivot_cpl', 'cpl_id', 'cpmk_id')
                    ->withPivot('sort_order');
    }
}