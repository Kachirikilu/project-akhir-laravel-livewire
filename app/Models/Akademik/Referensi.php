<?php


namespace App\Models\Akademik;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Referensi extends Model
{
    use SoftDeletes;

    protected $table = 'referensis';
    protected $guarded = ['id'];
    protected $appends = ['kode'];

    protected function kode(): Attribute
    {
        return Attribute::get(function () {
            return preg_replace('/([A-Za-z])([0-9])/', '$1-$2', $this->kode_ref);
        });
    }

    public function rps(): BelongsToMany
    {
        return $this->belongsToMany(Rps::class, 'rps_pivot_referensi', 'rps_id', 'ref_id')
                    ->withPivot('sort_order');
    }
}