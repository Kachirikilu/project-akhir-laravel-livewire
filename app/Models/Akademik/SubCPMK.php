<?php

namespace App\Models\Akademik;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class SubCpmk extends Model
{
    use SoftDeletes;

    protected $table = 'sub_cpmks';
    protected $guarded = ['id'];
    protected $appends = ['kode'];

    protected function kode(): Attribute
    {
        return Attribute::get(function () {
            return preg_replace('/([A-Za-z])([0-9])/', '$1-$2', $this->kode_scpmk);
        });
    }

    public function cpmks(): BelongsToMany
    {
        return $this->belongsToMany(Cpmk::class, 'cpmk_pivot_scpmk', 'scpmk_id', 'cpmk_id')
                    ->withPivot('sort_order')
                    ->withTimestamps();
    }
}