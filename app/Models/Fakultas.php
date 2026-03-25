<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Fakultas extends Model
{
    protected $fillable = ['nama_fakultas', 'kode_fk'];
    protected $appends = ['fakultas', 'kode'];

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

    protected function fakultas(): Attribute {
        return Attribute::get(fn() => $this->nama_fakultas);
    }
}
