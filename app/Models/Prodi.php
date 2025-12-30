<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Prodi extends Model {

    use HasFactory;

    protected $fillable = [
        'jurusan_id',
        'nama_prodi',
        'nama_strata',
    ];
    protected $appends = ['prodi', 'strata', 'jurusan', 'fakultas'];

    public function jurusan_rel() {
        return $this->belongsTo(Jurusan::class, 'jurusan_id');
    }

    protected function prodi(): Attribute {
        return Attribute::get(fn() => $this->nama_prodi);
    }

    protected function strata(): Attribute {
        return Attribute::get(fn() => $this->nama_strata);
    }

    protected function jurusan(): Attribute {
        return Attribute::get(fn() => $this->jurusan_rel?->nama_jurusan);
    }

    protected function fakultas(): Attribute {
        return Attribute::get(fn() => $this->jurusan_rel?->fakultas?->nama_fakultas);
    }
}