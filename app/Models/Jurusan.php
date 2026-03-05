<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Jurusan extends Model
{
    protected $fillable = ['fakultas_id', 'nama_jurusan'];

    public function fakultas_rel()
    {
        return $this->belongsTo(Fakultas::class, 'fakultas_id');
    }

    public function prodis(): HasMany
    {
        return $this->hasMany(Prodi::class);
    }

    protected function jurusan(): Attribute {
        return Attribute::get(fn() => $this->nama_jurusan);
    }

    protected function fakultas(): Attribute {
        return Attribute::get(fn() => $this->fakultas_rel?->nama_fakultas);
    }
}