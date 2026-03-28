<?php

namespace App\Models\Akademik;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

use App\Models\Auth\Dosen;

class Rps extends Model
{
    use SoftDeletes;

    protected $table = 'rps';
    protected $guarded = ['id'];

    public function mataKuliah(): BelongsTo
    {
        return $this->belongsTo(MataKuliah::class, 'mk_id')->withTrashed();
    }

    public function cpmks(): BelongsToMany
    {
        return $this->belongsToMany(Cpmk::class, 'rps_pivot_cpmk', 'rps_id', 'cpmk_id')
                    ->withPivot('sort_order')
                    ->orderBy('pivot_sort_order')
                    ->withTimestamps();
    }

    public function referensis(): BelongsToMany
    {
        return $this->belongsToMany(Referensi::class, 'rps_pivot_referensi', 'rps_id', 'ref_id')
                    ->withPivot(['kategori', 'sort_order']);
    }

    public function dosens(): BelongsToMany
    {
        return $this->belongsToMany(Dosen::class, 'rps_pivot_dosen', 'rps_id', 'dosen_id')
                    ->withPivot(['peran', 'is_ketua', 'sort_order'])
                    ->withTimestamps();
    }
}
