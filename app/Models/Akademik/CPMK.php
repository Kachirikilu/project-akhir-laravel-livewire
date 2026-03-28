<?php

namespace App\Models\Akademik;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Cpmk extends Model
{
    use SoftDeletes;

    protected $table = 'cpmks';
    protected $guarded = ['id'];
    protected $appends = ['kode'];


    protected function kode(): Attribute
    {
        return Attribute::get(function () {
            return preg_replace('/([A-Za-z])([0-9])/', '$1-$2', $this->kode_cpmk);
        });
    }

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