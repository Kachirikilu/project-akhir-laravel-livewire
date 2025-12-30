<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Fakultas extends Model
{
    protected $fillable = ['nama_fakultas'];

    public function jurusans(): HasMany 
    {
        return $this->hasMany(Jurusan::class);
    }

    public function prodis(): HasManyThrough
    {
        return $this->hasManyThrough(Prodi::class, Jurusan::class);
    }
}
