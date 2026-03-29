<?php

namespace App\Models\ProgramStudi;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class Fakultas extends Model
{
    use SoftDeletes;
    
    protected $fillable = ['kode_fk', 'nama_fakultas'];
    protected $appends = ['kode', 'fakultas'];

    public function jurusans(): HasMany 
    {
        return $this->hasMany(Jurusan::class);
    }

    public function prodis(): HasManyThrough
    {
        return $this->hasManyThrough(Prodi::class, Jurusan::class);
    }

    protected function kode(): Attribute {
        return Attribute::get(function () {
            if (!empty($this->attributes['kode_fk'])) {
                return $this->attributes['kode_fk'];
            }
            return 'UNI';
        });
    }
    protected function tingkatanProdi(): Attribute {
        return Attribute::get(function () {
            if (!empty($this->attributes['kode_fk'])) {
                return 3;
            }
            return 4;
        });
    }

    protected function fakultas(): Attribute {
        return Attribute::get(fn() => $this->nama_fakultas);
    }


    public function scopeSearchFakultas(Builder $query, $searchTerm)
    {
        $searchTerm = '%' . trim($searchTerm) . '%';

        return $query->where(function ($q) use ($searchTerm) {
            $q->where('fakultas.nama_fakultas', 'like', $searchTerm)
                ->orWhere('fakultas.kode_fk', 'like', $searchTerm)
                ->orWhere('fakultas.id', 'like', $searchTerm)
                ->orWhereRaw("CONCAT('Fakultas ', nama_fakultas) LIKE ?", [$searchTerm]);
        });
    }
}
