<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prodi extends Model
{
    use HasFactory;

    protected $fillable = [
        'jurusan_id',
        'nama_prodi',
        'kode_pr',
        'nama_strata',
    ];

    protected $appends = ['prodi', 'kode', 'strata', 'jurusan', 'fakultas'];

    public function jurusan_rel()
    {
        return $this->belongsTo(Jurusan::class, 'jurusan_id');
    }

    public function mata_kuliahs()
    {
        return $this->belongsToMany(
            MataKuliah::class,
            'prodi_pivot_mk',
            'prodi_id',
            'mk_id'
        )->withTimestamps();
    }

    protected function prodi(): Attribute
    {
        return Attribute::get(fn () => $this->nama_prodi);
    }

    protected function kode(): Attribute
    {
        return Attribute::get(function () {
            if (! empty($this->attributes['kode_pr'])) {
                return $this->attributes['kode_pr'];
            }
            $kodeJurusan = $this->jurusan_rel?->kode_jr;
            if (! empty($kodeJurusan)) {
                return $kodeJurusan;
            }
            // $kodeFakultas = $this->jurusan_rel?->fakultas_rel?->kode_fk;
            // if (! empty($kodeFakultas)) {
            //     return $kodeFakultas;
            // }
            return null;
        });
    }

    protected function kodeText(): Attribute
    {
        return Attribute::get(function () {
            if (! empty($this->attributes['kode_pr'])) {
                return $this->attributes['kode_pr'];
            }
            $kodeJurusan = $this->jurusan_rel?->kode_jr;
            if (! empty($kodeJurusan)) {
                return $kodeJurusan;
            }
            $kodeFakultas = $this->jurusan_rel?->fakultas_rel?->kode_fk;
            if (! empty($kodeFakultas)) {
                return $kodeFakultas.' (Fakultas)';
            }
            return null;
        });
    }

    protected function strata(): Attribute
    {
        return Attribute::get(fn () => $this->nama_strata);
    }

    protected function jurusan(): Attribute
    {
        return Attribute::get(fn () => $this->jurusan_rel?->nama_jurusan);
    }

    protected function fakultas(): Attribute
    {
        return Attribute::get(fn () => $this->jurusan_rel?->fakultas_rel?->nama_fakultas);
    }
}
