<?php


namespace App\Models\Akademik;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Referensi extends Model
{
    use SoftDeletes;

    protected $table = 'referensis';
    protected $guarded = ['id'];

    public function rps(): BelongsToMany
    {
        return $this->belongsToMany(Rps::class, 'rps_pivot_referensi', 'rps_id', 'ref_id')
                    ->withPivot('sort_order');
    }
}